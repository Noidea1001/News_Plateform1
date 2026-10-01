<?php
/**
 * Public Reader CDA Controller
 * news-platform / src / Controllers / PublicController.php
 */


namespace App\Controllers;

use App\Core\Database;
use App\Core\TemplateEngine;
use App\Core\Auth;
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

        $noSearchResults = false;
        if ($searchQuery !== '' && $totalCount === 0) {
            $noSearchResults = true;
            $articles = $this->db->fetchAll(
                "SELECT a.*, c.name as category_name, c.slug as category_slug, u.username as author_name, u.role as author_role 
                 FROM articles a 
                 JOIN categories c ON a.category_id = c.id 
                 JOIN users u ON a.author_id = u.id 
                 WHERE a.status = 'published' 
                 ORDER BY a.published_at DESC LIMIT {$perPage}"
            );
        }

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
            'noSearchResults' => $noSearchResults,
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

        // Fetch reader comments
        $comments = $this->db->fetchAll(
            "SELECT * FROM comments WHERE article_id = :art_id AND status = 'approved' ORDER BY created_at ASC",
            ['art_id' => $article['id']]
        );

        // Route automatically through assigned template_type blueprint
        $this->templateEngine->renderArticleView($article['template_type'], [
            'pageTitle' => e($article['title']) . ' | NewsPlatform',
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'trendingArticles' => $trendingArticles,
            'categories' => $categories,
            'comments' => $comments,
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
            $rawSummary = strip_tags(article_summary($art['summary']));
            $art['summary'] = $rawSummary;
            $art['summary_snippet'] = mb_strimwidth($rawSummary, 0, 95, '...');
            $art['category_display'] = cat_name($art['category_name']);
            $art['image_url'] = !empty($art['featured_image']) ? image_url($art['featured_image']) : '';
            $art['url'] = url('article.php?slug=' . urlencode($art['slug']));
            $art['time_ago'] = TemplateEngine::timeAgo($art['published_at']);
        }
        unset($art);

        return ['success' => true, 'articles' => $articles];
    }

    /**
     * Add Reader Comment
     */
    public function addComment(array $postData): array
    {
        $articleId = (int)($postData['article_id'] ?? 0);
        $parentId = !empty($postData['parent_id']) ? (int)$postData['parent_id'] : null;
        $userName = trim($postData['user_name'] ?? '');
        $userEmail = trim($postData['user_email'] ?? '');
        $content = trim($postData['content'] ?? '');

        // Validation
        if ($articleId <= 0) {
            return ['success' => false, 'message' => __('comment_error')];
        }

        // Verify article exists
        $articleExists = (bool)$this->db->fetchColumn("SELECT id FROM articles WHERE id = :id AND status = 'published'", ['id' => $articleId]);
        if (!$articleExists) {
            return ['success' => false, 'message' => 'Article not found.'];
        }

        if (mb_strlen($userName) < 2 || mb_strlen($userName) > 80) {
            return ['success' => false, 'message' => 'Please provide a valid name (2-80 characters).'];
        }

        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Please provide a valid email address.'];
        }

        if (mb_strlen($content) < 3 || mb_strlen($content) > 3000) {
            return ['success' => false, 'message' => 'Comment must be between 3 and 3000 characters.'];
        }

        // Parent comment verification if reply
        if ($parentId !== null) {
            $parentExists = (bool)$this->db->fetchColumn(
                "SELECT id FROM comments WHERE id = :pid AND article_id = :aid", 
                ['pid' => $parentId, 'aid' => $articleId]
            );
            if (!$parentExists) {
                $parentId = null;
            }
        }

        // Insert comment
        $this->db->execute(
            "INSERT INTO comments (article_id, parent_id, user_name, user_email, content, likes_count, status, created_at) 
             VALUES (:aid, :pid, :name, :email, :content, 0, 'approved', NOW())",
            [
                'aid' => $articleId,
                'pid' => $parentId,
                'name' => htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'),
                'email' => strtolower($userEmail),
                'content' => htmlspecialchars($content, ENT_QUOTES, 'UTF-8')
            ]
        );

        $newId = (int)$this->db->lastInsertId();
        $newComment = $this->db->fetch("SELECT * FROM comments WHERE id = :id", ['id' => $newId]);
        if ($newComment) {
            $newComment['time_ago'] = TemplateEngine::timeAgo($newComment['created_at']);
        }

        return [
            'success' => true,
            'message' => __('comment_success'),
            'comment' => $newComment
        ];
    }

    /**
     * Like/Upvote Reader Comment
     */
    public function likeComment(int $commentId): array
    {
        if ($commentId <= 0) {
            return ['success' => false, 'message' => 'Invalid comment ID.'];
        }

        $comment = $this->db->fetch("SELECT id, likes_count FROM comments WHERE id = :id", ['id' => $commentId]);
        if (!$comment) {
            return ['success' => false, 'message' => 'Comment not found.'];
        }

        $this->db->execute("UPDATE comments SET likes_count = likes_count + 1 WHERE id = :id", ['id' => $commentId]);
        $newLikes = (int)$comment['likes_count'] + 1;

        return [
            'success' => true,
            'likes_count' => $newLikes
        ];
    }

    /**
     * News Archive Page ("បណ្ណសារព័ត៌មាន") View with Multi-Filtering
     */
    public function archive(): void
    {
        // 1. Fetch available filter options
        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name ASC");
        
        $yearsRaw = $this->db->fetchAll(
            "SELECT DISTINCT YEAR(published_at) as yr 
             FROM articles 
             WHERE status = 'published' AND published_at IS NOT NULL 
             ORDER BY yr DESC"
        );
        $years = array_filter(array_map(fn($r) => (int)$r['yr'], $yearsRaw));
        if (empty($years)) {
            $years = [(int)date('Y')];
        }

        // 2. Parse active filter parameters
        $categoryId = !empty($_GET['category']) ? (int)$_GET['category'] : 0;
        $year = !empty($_GET['year']) ? (int)$_GET['year'] : 0;
        $month = !empty($_GET['month']) ? (int)$_GET['month'] : 0;
        $blueprint = trim($_GET['blueprint'] ?? '');
        $isBreaking = isset($_GET['breaking']) && $_GET['breaking'] === '1' ? 1 : null;
        $searchQuery = trim($_GET['q'] ?? '');
        $sort = trim($_GET['sort'] ?? 'newest');
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 12;

        // 3. Dynamic SQL Query Construction
        $where = ["a.status = 'published'"];
        $params = [];

        if ($categoryId > 0) {
            $where[] = "a.category_id = :cat_id";
            $params['cat_id'] = $categoryId;
        }

        if ($year > 0) {
            $where[] = "YEAR(a.published_at) = :yr";
            $params['yr'] = $year;
        }

        if ($month >= 1 && $month <= 12) {
            $where[] = "MONTH(a.published_at) = :mo";
            $params['mo'] = $month;
        }

        if (in_array($blueprint, ['standard', 'investigative', 'opinion'], true)) {
            $where[] = "a.template_type = :tpl";
            $params['tpl'] = $blueprint;
        }

        if ($isBreaking === 1) {
            $where[] = "a.is_breaking = 1";
        }

        if ($searchQuery !== '') {
            $where[] = "(a.title LIKE :s1 OR a.summary LIKE :s2 OR a.content LIKE :s3 OR a.title_kh LIKE :s4 OR a.title_en LIKE :s5)";
            $term = "%{$searchQuery}%";
            $params['s1'] = $term;
            $params['s2'] = $term;
            $params['s3'] = $term;
            $params['s4'] = $term;
            $params['s5'] = $term;
        }

        $whereSql = "WHERE " . implode(" AND ", $where);

        // Sorting
        $orderBy = match ($sort) {
            'oldest' => "a.published_at ASC",
            'views' => "a.views_count DESC, a.published_at DESC",
            'alpha' => "a.title ASC",
            default => "a.published_at DESC",
        };

        // 4. Pagination & Count
        $totalCount = (int)$this->db->fetchColumn(
            "SELECT COUNT(*) FROM articles a {$whereSql}",
            $params
        );
        $totalPages = max(1, (int)ceil($totalCount / $perPage));
        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }
        $offset = ($currentPage - 1) * $perPage;

        // 5. Fetch Paginated Records
        $articles = $this->db->fetchAll(
            "SELECT a.*, c.name as category_name, c.slug as category_slug, u.username as author_name, u.role as author_role 
             FROM articles a 
             JOIN categories c ON a.category_id = c.id 
             JOIN users u ON a.author_id = u.id 
             {$whereSql} 
             ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        // Attach reading time estimation and time ago
        foreach ($articles as &$art) {
            $words = str_word_count(strip_tags($art['content'] ?? ''));
            $art['reading_time'] = max(1, (int)ceil($words / 200));
            $art['time_ago'] = TemplateEngine::timeAgo($art['published_at'] ?? $art['created_at']);
        }
        unset($art);

        $this->templateEngine->renderPage('views/archive.php', [
            'pageTitle' => __('archive_page_title') . ' | ' . __('app_name'),
            'categories' => $categories,
            'years' => $years,
            'articles' => $articles,
            'totalCount' => $totalCount,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'filters' => [
                'category' => $categoryId,
                'year' => $year,
                'month' => $month,
                'blueprint' => $blueprint,
                'breaking' => $isBreaking,
                'q' => $searchQuery,
                'sort' => $sort,
            ],
            'csrfToken' => Auth::generateCsrfToken(),
        ], 'public');
    }

    /**
     * Reader Notifications JSON API (for real-time bell dropdown with topic filtering)
     */
    public function notificationsApi(): void
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');

        $currentReader = Auth::reader();
        $subscribedCatIds = [];

        if ($currentReader) {
            $subs = $this->db->fetchAll(
                "SELECT category_id FROM reader_subscriptions WHERE reader_id = :rid",
                ['rid' => (int)$currentReader['id']]
            );
            $subscribedCatIds = array_map(fn($s) => (int)$s['category_id'], $subs);
        }

        if (!empty($subscribedCatIds)) {
            // User has customized subscriptions: prioritize breaking news + articles in their subscribed topics
            $inClause = implode(',', $subscribedCatIds);
            $notifications = $this->db->fetchAll(
                "SELECT n.*, a.slug as article_slug, a.featured_image, a.category_id, c.name as category_name
                 FROM notifications n 
                 LEFT JOIN articles a ON n.article_id = a.id 
                 LEFT JOIN categories c ON a.category_id = c.id
                 WHERE n.type = 'breaking' OR a.category_id IN ($inClause)
                 ORDER BY n.created_at DESC LIMIT 20"
            );
        } else {
            // General stream: breaking news + latest published
            $notifications = $this->db->fetchAll(
                "SELECT n.*, a.slug as article_slug, a.featured_image, a.category_id, c.name as category_name
                 FROM notifications n 
                 LEFT JOIN articles a ON n.article_id = a.id 
                 LEFT JOIN categories c ON a.category_id = c.id
                 ORDER BY n.created_at DESC LIMIT 15"
            );
        }

        foreach ($notifications as &$n) {
            $n['time_ago'] = TemplateEngine::timeAgo($n['created_at']);
            $n['article_url'] = !empty($n['article_slug']) ? url('article.php?slug=' . urlencode($n['article_slug'])) : url('index.php');
            $n['is_subscribed_topic'] = !empty($n['category_id']) && in_array((int)$n['category_id'], $subscribedCatIds, true);
            $n['category_display'] = !empty($n['category_name']) ? cat_name($n['category_name']) : '';
        }
        unset($n);

        echo json_encode([
            'success' => true,
            'is_logged_in' => !empty($currentReader),
            'has_subscriptions' => !empty($subscribedCatIds),
            'count' => count($notifications),
            'notifications' => $notifications
        ]);
        exit;
    }

    /**
     * Reader Topic Subscriptions JSON API
     */
    public function subscriptionApi(): void
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');

        $currentReader = Auth::reader();
        if (!$currentReader) {
            echo json_encode([
                'success' => false,
                'require_login' => true,
                'message' => __('subscribe_login_prompt') ?? 'Please sign in or register to customize your news feed'
            ]);
            exit;
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($method === 'GET') {
            $subs = $this->db->fetchAll(
                "SELECT category_id FROM reader_subscriptions WHERE reader_id = :rid",
                ['rid' => (int)$currentReader['id']]
            );
            $catIds = array_map(fn($s) => (int)$s['category_id'], $subs);

            echo json_encode([
                'success' => true,
                'reader' => ['id' => $currentReader['id'], 'name' => $currentReader['name']],
                'subscriptions' => $catIds
            ]);
            exit;
        }

        if ($method === 'POST') {
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true) ?: $_POST;
            $catId = (int)($input['category_id'] ?? 0);

            if ($catId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
                exit;
            }

            // Verify category exists
            $category = $this->db->fetch("SELECT id, name FROM categories WHERE id = :cid", ['cid' => $catId]);
            if (!$category) {
                echo json_encode(['success' => false, 'message' => 'Category not found']);
                exit;
            }

            // Check existing subscription
            $existing = $this->db->fetch(
                "SELECT id FROM reader_subscriptions WHERE reader_id = :rid AND category_id = :cid",
                ['rid' => (int)$currentReader['id'], 'cid' => $catId]
            );

            if ($existing) {
                // Unsubscribe
                $this->db->execute(
                    "DELETE FROM reader_subscriptions WHERE reader_id = :rid AND category_id = :cid",
                    ['rid' => (int)$currentReader['id'], 'cid' => $catId]
                );
                $isSubscribed = false;
                $msg = 'Unsubscribed from ' . cat_name($category['name']);
            } else {
                // Subscribe
                $this->db->execute(
                    "INSERT INTO reader_subscriptions (reader_id, category_id, created_at) VALUES (:rid, :cid, NOW())",
                    ['rid' => (int)$currentReader['id'], 'cid' => $catId]
                );
                $isSubscribed = true;
                $msg = 'Subscribed to ' . cat_name($category['name']);
            }

            $count = (int)$this->db->fetchColumn(
                "SELECT COUNT(*) FROM reader_subscriptions WHERE reader_id = :rid",
                ['rid' => (int)$currentReader['id']]
            );

            echo json_encode([
                'success' => true,
                'subscribed' => $isSubscribed,
                'category_id' => $catId,
                'category_name' => cat_name($category['name']),
                'total_subscriptions' => $count,
                'message' => $msg
            ]);
            exit;
        }

        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    /**
     * Public Reader Registration Page View
     */
    public function showRegister(): void
    {
        if (Auth::readerCheck()) {
            header('Location: ' . url('index.php'));
            exit;
        }

        $this->templateEngine->renderPage('views/reader-register.php', [
            'pageTitle' => __('create_account') . ' | ' . __('app_name'),
            'csrfToken' => Auth::generateCsrfToken(),
            'error' => $_GET['error'] ?? null,
            'msg' => $_GET['msg'] ?? null,
        ], 'public');
    }

    /**
     * Public Reader Registration POST Action
     */
    public function registerReader(array $postData): void
    {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || !empty($postData['ajax']);
        $redirectTo = !empty($postData['redirect_to']) ? $postData['redirect_to'] : url('index.php');
        if (!str_starts_with($redirectTo, '/') && !str_starts_with($redirectTo, url(''))) {
            $redirectTo = url('index.php');
        }

        if (!Auth::verifyCsrfToken($postData['csrf_token'] ?? '')) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Security validation failed (Invalid CSRF token).']);
                exit;
            }
            header('Location: ' . url('register.php?error=' . urlencode('Security validation failed (Invalid CSRF token).')));
            exit;
        }

        $name = trim($postData['name'] ?? '');
        $email = trim($postData['email'] ?? '');
        $password = $postData['password'] ?? '';
        $confirmPassword = $postData['password_confirm'] ?? '';

        if ($password !== $confirmPassword) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
                exit;
            }
            header('Location: ' . url('register.php?error=' . urlencode('Passwords do not match.')));
            exit;
        }

        $result = Auth::readerRegister($name, $email, $password);
        if (!$result['success']) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $result['message']]);
                exit;
            }
            header('Location: ' . url('register.php?error=' . urlencode($result['message'])));
            exit;
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Welcome, ' . $name . '! Your reader account has been created.',
                'redirect' => $redirectTo
            ]);
            exit;
        }

        header('Location: ' . $redirectTo);
        exit;
    }

    /**
     * Public Reader Login Page View
     */
    public function showLogin(): void
    {
        if (Auth::readerCheck()) {
            header('Location: ' . url('index.php'));
            exit;
        }

        $this->templateEngine->renderPage('views/reader-login.php', [
            'pageTitle' => __('sign_in') . ' | ' . __('app_name'),
            'csrfToken' => Auth::generateCsrfToken(),
            'error' => $_GET['error'] ?? null,
            'msg' => $_GET['msg'] ?? null,
        ], 'public');
    }

    /**
     * Public Reader Login POST Action
     */
    public function loginReader(array $postData): void
    {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || !empty($postData['ajax']);
        $redirectTo = !empty($postData['redirect_to']) ? $postData['redirect_to'] : url('index.php');
        if (!str_starts_with($redirectTo, '/') && !str_starts_with($redirectTo, url(''))) {
            $redirectTo = url('index.php');
        }

        if (!Auth::verifyCsrfToken($postData['csrf_token'] ?? '')) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Security validation failed (Invalid CSRF token).']);
                exit;
            }
            header('Location: ' . url('login.php?error=' . urlencode('Security validation failed (Invalid CSRF token).')));
            exit;
        }

        $email = trim($postData['email'] ?? '');
        $password = $postData['password'] ?? '';

        if (empty($email) || empty($password)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
                exit;
            }
            header('Location: ' . url('login.php?error=' . urlencode('Email and password are required.')));
            exit;
        }

        $success = Auth::readerLogin($email, $password);
        if (!$success) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid email or password. Please try again.']);
                exit;
            }
            header('Location: ' . url('login.php?error=' . urlencode('Invalid email or password. Please try again.')));
            exit;
        }

        $reader = Auth::reader();
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Welcome back, ' . ($reader['name'] ?? 'Reader') . '!',
                'redirect' => $redirectTo
            ]);
            exit;
        }

        header('Location: ' . $redirectTo);
        exit;
    }

    /**
     * Public Reader Logout Action
     */
    public function logoutReader(): void
    {
        Auth::readerLogout();
        header('Location: ' . url('index.php?msg=' . urlencode('You have been logged out successfully.')));
        exit;
    }
}
?>
