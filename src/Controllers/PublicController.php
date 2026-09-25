<?php
/**
 * Public Reader CDA Controller
 * news-platform / src / Controllers / PublicController.php
 */


namespace App\Controllers;

use App\Core\Database;
use App\Core\TemplateEngine;
use Exception;

require_once __DIR__ . '/../Core/helpers.php';

class PublicController
{
    private Database $db;
    private TemplateEngine $templateEngine;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->templateEngine = new TemplateEngine();
    }

    /**
     * Homepage Reader View
     */
    public function home(): void
    {
        // 0. Auto-seed real Khmer news articles if database count is under 10
        try {
            $totalArticlesCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM articles");
            if ($totalArticlesCount < 10 && file_exists(__DIR__ . '/../../seed_news.php')) {
                ob_start();
                @include __DIR__ . '/../../seed_news.php';
                ob_end_clean();
            }
        } catch (\Throwable $seedEx) {
            error_log("Auto-seed error: " . $seedEx->getMessage());
        }

        // 1. Fetch breaking news
        $breakingNews = $this->db->fetchAll(
            "SELECT a.*, c.name as category_name, c.slug as category_slug 
             FROM articles a 
             JOIN categories c ON a.category_id = c.id 
             WHERE a.status = 'published' AND a.is_breaking = 1 
             ORDER BY a.published_at DESC LIMIT 3"
        );

        // 2. Fetch categories
        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name ASC");

        // 3. Category Filter
        $activeCategoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
        
        $whereSql = "WHERE a.status = 'published'";
        $params = [];
        if ($activeCategoryId) {
            $whereSql .= " AND a.category_id = :cat_id";
            $params['cat_id'] = $activeCategoryId;
        }

        // Search query filter
        $searchQuery = trim($_GET['q'] ?? '');
        if ($searchQuery !== '') {
            $whereSql .= " AND (a.title LIKE :s1 OR a.summary LIKE :s2 OR a.content LIKE :s3)";
            $searchTerm = "%{$searchQuery}%";
            $params['s1'] = $searchTerm;
            $params['s2'] = $searchTerm;
            $params['s3'] = $searchTerm;
        }

        // 4. Pagination Calculation
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $totalCount = (int)$this->db->fetchColumn(
            "SELECT COUNT(*) FROM articles a JOIN categories c ON a.category_id = c.id {$whereSql}",
            $params
        );
        $totalPages = max(1, (int)ceil($totalCount / $perPage));
        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }
        $offset = ($currentPage - 1) * $perPage;

        // 5. Fetch paginated article feed
        $articles = $this->db->fetchAll(
            "SELECT a.*, c.name as category_name, c.slug as category_slug, u.username as author_name, u.role as author_role 
             FROM articles a 
             JOIN categories c ON a.category_id = c.id 
             JOIN users u ON a.author_id = u.id 
             {$whereSql} 
             ORDER BY a.published_at DESC LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        // Attach reading time estimation
        foreach ($articles as &$art) {
            $text = strip_tags(($art['summary'] ?? '') . ' ' . ($art['content'] ?? ''));
            $words = preg_split('/\s+/u', trim($text));
            $wordCount = count(array_filter($words));
            $mins = max(1, (int)ceil($wordCount / 180));
            $art['reading_time_mins'] = $mins;
            $art['reading_time'] = __('min_read', ['min' => $mins]);
        }
        unset($art);

        // 6. Fetch trending articles for sidebar
        $trendingArticles = $this->db->fetchAll(
            "SELECT a.id, a.title, a.slug, a.views_count, a.published_at, c.name as category_name 
             FROM articles a 
             JOIN categories c ON a.category_id = c.id 
             WHERE a.status = 'published' 
             ORDER BY a.views_count DESC, a.published_at DESC LIMIT 5"
        );

        // Render homepage view
        $this->templateEngine->renderPage('views/home.php', [
            'pageTitle' => 'NewsPlatform | Decoupled Enterprise News System',
            'breakingNews' => $breakingNews,
            'categories' => $categories,
            'articles' => $articles,
            'trendingArticles' => $trendingArticles,
            'activeCategoryId' => $activeCategoryId,
            'searchQuery' => $searchQuery,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'perPage' => $perPage,
        ], 'public');
    }

    /**
     * Detailed Article View (Dispatches template_type to dynamic view engine)
     */
    public function article(string $slug): void
    {
        if (empty($slug)) {
            header('Location: /index.php');
            exit;
        }

        // Fetch article by slug
        $article = $this->db->fetch(
            "SELECT a.*, c.name as category_name, c.slug as category_slug, 
                    u.username as author_name, u.bio as author_bio, u.avatar_url as author_avatar, u.role as author_role 
             FROM articles a 
             JOIN categories c ON a.category_id = c.id 
             JOIN users u ON a.author_id = u.id 
             WHERE a.slug = :slug AND a.status = 'published' LIMIT 1",
            ['slug' => $slug]
        );

        if (!$article) {
            http_response_code(404);
            $this->templateEngine->renderPage('views/404.php', [
                'pageTitle' => '404 - Article Not Found',
                'message' => 'The article you are looking for does not exist or has been archived.'
            ], 'public');
            return;
        }

        // Attach reading time
        $text = strip_tags(($article['summary'] ?? '') . ' ' . ($article['content'] ?? ''));
        $words = preg_split('/\s+/u', trim($text));
        $wordCount = count(array_filter($words));
        $mins = max(1, (int)ceil($wordCount / 180));
        $article['reading_time_mins'] = $mins;
        $article['reading_time'] = __('min_read', ['min' => $mins]);

        // Increment article view counter
        $this->db->execute(
            "UPDATE articles SET views_count = views_count + 1 WHERE id = :id",
            ['id' => $article['id']]
        );

        // Fetch related articles in same category
        $relatedArticles = $this->db->fetchAll(
            "SELECT a.*, c.name as category_name 
             FROM articles a 
             JOIN categories c ON a.category_id = c.id 
             WHERE a.category_id = :cat_id AND a.id != :art_id AND a.status = 'published' 
             ORDER BY a.published_at DESC LIMIT 3",
            ['cat_id' => $article['category_id'], 'art_id' => $article['id']]
        );

        // Fetch trending articles for sidebar
        $trendingArticles = $this->db->fetchAll(
            "SELECT a.id, a.title, a.slug, a.views_count, a.published_at, c.name as category_name 
             FROM articles a 
             JOIN categories c ON a.category_id = c.id 
             WHERE a.status = 'published' 
             ORDER BY a.views_count DESC LIMIT 5"
        );

        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name ASC");

        // Route automatically through assigned template_type blueprint
        $this->templateEngine->renderArticleView($article['template_type'], [
            'pageTitle' => e($article['title']) . ' | NewsPlatform',
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'trendingArticles' => $trendingArticles,
            'categories' => $categories,
        ]);
    }

    /**
     * Reader AJAX Subscription Handler
     */
    public function subscribe(array $postData): array
    {
        $email = trim($postData['email'] ?? '');
        $categoryId = !empty($postData['category_preference']) ? (int)$postData['category_preference'] : null;

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Please provide a valid email address.'
            ];
        }

        try {
            // Check if already subscribed
            $existing = $this->db->fetch("SELECT id, status FROM subscribers WHERE email = :email", ['email' => $email]);
            
            if ($existing) {
                if ($existing['status'] === 'active') {
                    return [
                        'success' => true,
                        'message' => 'You are already registered to receive feed updates! Thank you.'
                    ];
                } else {
                    // Reactivate
                    $this->db->execute(
                        "UPDATE subscribers SET status = 'active', category_preference = :cat, subscribed_at = NOW() WHERE id = :id",
                        ['cat' => $categoryId, 'id' => $existing['id']]
                    );
                    return [
                        'success' => true,
                        'message' => 'Welcome back! Your feed subscription has been reactivated.'
                    ];
                }
            }

            // Insert new subscriber
            $this->db->execute(
                "INSERT INTO subscribers (email, category_preference, status, subscribed_at) VALUES (:email, :cat, 'active', NOW())",
                ['email' => $email, 'cat' => $categoryId]
            );

            return [
                'success' => true,
                'message' => 'Subscription successful! You will now receive editorial alerts and breaking feed digests.'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Server error processing subscription. Please try again later.'
            ];
        }
    }

    /**
     * Real-Time Search API JSON Endpoint
     */
    public function searchApi(string $q): array
    {
        $q = trim($q);
        if (mb_strlen($q) < 1) {
            return ['success' => true, 'articles' => []];
        }

        $term = "%{$q}%";
        $articles = $this->db->fetchAll(
            "SELECT a.id, a.title, a.slug, a.summary, a.featured_image, a.published_at, c.name as category_name
             FROM articles a
             JOIN categories c ON a.category_id = c.id
             WHERE a.status = 'published'
               AND (a.title LIKE :q1 OR a.summary LIKE :q2 OR c.name LIKE :q3)
             ORDER BY a.published_at DESC LIMIT 8",
            ['q1' => $term, 'q2' => $term, 'q3' => $term]
        );

        foreach ($articles as &$art) {
            $art['title'] = article_title($art['title']);
            $art['summary'] = article_summary($art['summary']);
            $art['category_display'] = cat_name($art['category_name']);
            $art['url'] = url('article.php?slug=' . urlencode($art['slug']));
            $art['time_ago'] = TemplateEngine::timeAgo($art['published_at']);
        }
        unset($art);

        return ['success' => true, 'articles' => $articles];
    }
}
?>
