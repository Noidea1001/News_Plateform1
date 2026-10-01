<?php
/**
 * NewsPlatform RESTful Public API v1 — Articles Endpoint
 * news-platform / public / api / v1 / articles.php
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Only GET is supported.']);
    exit;
}

require_once __DIR__ . '/../../../src/Core/helpers.php';
require_once __DIR__ . '/../../../src/Core/Database.php';
require_once __DIR__ . '/../../../src/Core/TemplateEngine.php';
require_once __DIR__ . '/../../../src/Core/Sanitizer.php';

use App\Core\Database;
use App\Core\TemplateEngine;
use App\Core\Sanitizer;

try {
    $db = Database::getInstance();

    // ── 1. Single Article Detail Mode ──────────────────────────────────────────
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

    if ($id > 0 || !empty($slug)) {
        $whereClause = ($id > 0) ? 'a.id = :id' : 'a.slug = :slug';
        $params = ($id > 0) ? ['id' => $id] : ['slug' => $slug];

        $article = $db->fetch(
            "SELECT a.*, c.name as category_name, c.slug as category_slug,
                    u.username as author_name, u.role as author_role, u.avatar_url as author_avatar, u.bio as author_bio
             FROM articles a
             JOIN categories c ON a.category_id = c.id
             JOIN users u ON a.author_id = u.id
             WHERE {$whereClause} AND a.status = 'published' LIMIT 1",
            $params
        );

        if (!$article) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Article not found or not published.']);
            exit;
        }

        // Increment views count
        $db->execute("UPDATE articles SET views_count = views_count + 1 WHERE id = :id", ['id' => $article['id']]);

        // Calculate reading time
        $rawText = strip_tags(($article['summary'] ?? '') . ' ' . ($article['content'] ?? ''));
        $words = preg_split('/\s+/u', trim($rawText));
        $mins = max(1, (int)ceil(count(array_filter($words)) / 180));

        // Fetch comments for this article
        $comments = $db->fetchAll(
            "SELECT id, parent_id, user_name, content, likes_count, created_at 
             FROM comments 
             WHERE article_id = :aid AND status = 'approved' 
             ORDER BY created_at ASC",
            ['aid' => $article['id']]
        );

        foreach ($comments as &$com) {
            $com['time_ago'] = TemplateEngine::timeAgo($com['created_at']);
        }
        unset($com);

        // Fetch related articles
        $related = $db->fetchAll(
            "SELECT a.id, a.title, a.slug, a.summary, a.featured_image, a.views_count, a.published_at, c.name as category_name
             FROM articles a
             JOIN categories c ON a.category_id = c.id
             WHERE a.category_id = :cid AND a.id != :aid AND a.status = 'published'
             ORDER BY a.published_at DESC LIMIT 3",
            ['cid' => $article['category_id'], 'aid' => $article['id']]
        );

        foreach ($related as &$r) {
            $r['title'] = article_title($r['title']);
            $r['summary'] = article_summary($r['summary']);
            $r['category_display'] = cat_name($r['category_name']);
            $r['url'] = url('article.php?slug=' . urlencode($r['slug']));
        }
        unset($r);

        $responseData = [
            'id' => (int)$article['id'],
            'title' => article_title($article['title']),
            'slug' => $article['slug'],
            'summary' => article_summary($article['summary']),
            'content_raw' => $article['content'],
            'content_html' => Sanitizer::parseArticleMedia(article_content($article['content']), $article['gallery_images'] ?? null, $article['featured_image'] ?? null, $article['video_embed_url'] ?? null),
            'featured_image' => !empty($article['featured_image']) ? image_url($article['featured_image']) : null,
            'video_embed_url' => $article['video_embed_url'] ?? null,
            'audio_embed_url' => $article['audio_embed_url'] ?? null,
            'reference_url' => $article['reference_url'] ?? null,
            'reference_source_name' => $article['reference_source_name'] ?? null,
            'template_type' => $article['template_type'],
            'is_breaking' => (int)$article['is_breaking'] === 1,
            'views_count' => (int)$article['views_count'] + 1,
            'reading_time_mins' => $mins,
            'published_at' => $article['published_at'],
            'time_ago' => TemplateEngine::timeAgo($article['published_at']),
            'category' => [
                'id' => (int)$article['category_id'],
                'name' => cat_name($article['category_name']),
                'slug' => $article['category_slug']
            ],
            'author' => [
                'name' => $article['author_name'],
                'role' => $article['author_role'],
                'bio' => $article['author_bio'] ?? '',
                'avatar' => $article['author_avatar'] ?? null
            ],
            'comments' => $comments,
            'related_articles' => $related,
            'canonical_url' => url('article.php?slug=' . urlencode($article['slug']))
        ];

        echo json_encode([
            'status' => 'success',
            'data' => $responseData
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ── 2. Paginated Articles List Stream ───────────────────────────────────────
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min(50, max(1, (int)($_GET['limit'] ?? 10)));
    $offset = ($page - 1) * $limit;

    $where = ["a.status = 'published'"];
    $params = [];

    // Filter: Category (ID or Slug)
    if (!empty($_GET['category'])) {
        $catParam = trim($_GET['category']);
        if (is_numeric($catParam)) {
            $where[] = "a.category_id = :cat_id";
            $params['cat_id'] = (int)$catParam;
        } else {
            $where[] = "c.slug = :cat_slug";
            $params['cat_slug'] = $catParam;
        }
    }

    // Filter: Search query
    if (!empty($_GET['q'])) {
        $term = '%' . trim($_GET['q']) . '%';
        $where[] = "(a.title LIKE :s1 OR a.summary LIKE :s2 OR a.content LIKE :s3)";
        $params['s1'] = $term;
        $params['s2'] = $term;
        $params['s3'] = $term;
    }

    // Filter: Template type
    if (!empty($_GET['template']) && in_array($_GET['template'], ['standard', 'investigative', 'opinion'], true)) {
        $where[] = "a.template_type = :template_type";
        $params['template_type'] = $_GET['template'];
    }

    // Filter: Breaking
    if (isset($_GET['breaking']) && ($_GET['breaking'] === '1' || $_GET['breaking'] === '0')) {
        $where[] = "a.is_breaking = :is_breaking";
        $params['is_breaking'] = (int)$_GET['breaking'];
    }

    // Sort order
    $sort = $_GET['sort'] ?? 'latest';
    $orderBy = ($sort === 'views') ? 'a.views_count DESC, a.published_at DESC' : 'a.published_at DESC';

    $whereSql = implode(' AND ', $where);

    // Total count
    $totalCount = (int)$db->fetchColumn(
        "SELECT COUNT(*) FROM articles a JOIN categories c ON a.category_id = c.id WHERE {$whereSql}",
        $params
    );

    $totalPages = max(1, (int)ceil($totalCount / $limit));

    // Fetch articles
    $articles = $db->fetchAll(
        "SELECT a.id, a.title, a.slug, a.summary, a.featured_image, a.template_type, a.is_breaking,
                a.views_count, a.published_at,
                c.id as category_id, c.name as category_name, c.slug as category_slug,
                u.username as author_name, u.role as author_role
         FROM articles a
         JOIN categories c ON a.category_id = c.id
         JOIN users u ON a.author_id = u.id
         WHERE {$whereSql}
         ORDER BY {$orderBy}
         LIMIT {$limit} OFFSET {$offset}",
        $params
    );

    $items = [];
    foreach ($articles as $art) {
        $rawText = strip_tags(($art['summary'] ?? ''));
        $words = preg_split('/\s+/u', trim($rawText));
        $mins = max(1, (int)ceil(count(array_filter($words)) / 180));

        $items[] = [
            'id' => (int)$art['id'],
            'title' => article_title($art['title']),
            'slug' => $art['slug'],
            'summary' => article_summary($art['summary']),
            'featured_image' => !empty($art['featured_image']) ? image_url($art['featured_image']) : null,
            'template_type' => $art['template_type'],
            'is_breaking' => (int)$art['is_breaking'] === 1,
            'views_count' => (int)$art['views_count'],
            'reading_time_mins' => $mins,
            'published_at' => $art['published_at'],
            'time_ago' => TemplateEngine::timeAgo($art['published_at']),
            'category' => [
                'id' => (int)$art['category_id'],
                'name' => cat_name($art['category_name']),
                'slug' => $art['category_slug']
            ],
            'author' => [
                'name' => $art['author_name'],
                'role' => $art['author_role']
            ],
            'url' => url('article.php?slug=' . urlencode($art['slug']))
        ];
    }

    echo json_encode([
        'status' => 'success',
        'data' => $items,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total_items' => $totalCount,
            'total_pages' => $totalPages,
            'has_more' => ($page < $totalPages)
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
}
