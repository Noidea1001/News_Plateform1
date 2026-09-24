<?php
/**
 * CMS Backend Editorial Controller (CMA Control Panel)
 * news-platform / src / Controllers / AdminController.php
 */


namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\TemplateEngine;
use Exception;

class AdminController
{
    private Database $db;
    private TemplateEngine $templateEngine;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->templateEngine = new TemplateEngine();

        // Silent column migration for optional manual drop cap
        try {
            $cols = $this->db->getPdo()->query("SHOW COLUMNS FROM articles LIKE 'has_drop_cap'");
            if ($cols && $cols->rowCount() === 0) {
                $this->db->execute("ALTER TABLE articles ADD COLUMN has_drop_cap TINYINT(1) NOT NULL DEFAULT 0");
            }
        } catch (\Throwable $e) {
            // Column already exists or check ignored
        }
    }

    /**
     * Editorial Dashboard
     */
    public function dashboard(): void
    {
        $user = Auth::requireAuth();

        // 0. Auto-seed real Khmer news articles if database count is under 10
        $totalArticlesCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM articles");
        if ($totalArticlesCount < 10 && file_exists(__DIR__ . '/../../seed_news.php')) {
            ob_start();
            @include __DIR__ . '/../../seed_news.php';
            ob_end_clean();
        }

        // 1. Core Metrics
        $totalArticles = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM articles");
        $publishedCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM articles WHERE status = 'published'");
        $draftCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM articles WHERE status = 'draft'");
        $totalSubscribers = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM subscribers WHERE status = 'active'");

        // 2. Template Blueprint Breakdown Metrics
        $standardCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM articles WHERE template_type = 'standard'");
        $investigativeCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM articles WHERE template_type = 'investigative'");
        $opinionCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM articles WHERE template_type = 'opinion'");

        // 3. Editorial Posts List
        $articles = $this->db->fetchAll(
            "SELECT a.*, c.name as category_name, u.username as author_name 
             FROM articles a 
             JOIN categories c ON a.category_id = c.id 
             JOIN users u ON a.author_id = u.id 
             ORDER BY a.created_at DESC"
        );

        // 4. Recent Feed Subscribers
        $recentSubscribers = $this->db->fetchAll(
            "SELECT s.*, c.name as category_name 
             FROM subscribers s 
             LEFT JOIN categories c ON s.category_preference = c.id 
             ORDER BY s.subscribed_at DESC LIMIT 6"
        );

        $this->templateEngine->renderPage('admin/views/dashboard.php', [
            'pageTitle' => 'CMS Dashboard | Editorial Control Panel',
            'currentUser' => $user,
            'totalArticles' => $totalArticles,
            'publishedCount' => $publishedCount,
            'draftCount' => $draftCount,
            'totalSubscribers' => $totalSubscribers,
            'standardCount' => $standardCount,
            'investigativeCount' => $investigativeCount,
            'opinionCount' => $opinionCount,
            'articles' => $articles,
            'recentSubscribers' => $recentSubscribers,
            'csrfToken' => Auth::generateCsrfToken(),
        ], 'admin');
    }

    /**
     * Create Article Editor View
     */
    public function createArticleForm(): void
    {
        $user = Auth::requireAuth(['admin', 'editor', 'reporter']);
        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name ASC");
        $authors = $this->db->fetchAll("SELECT id, username, role FROM users WHERE is_active = 1 ORDER BY username ASC");

        $this->templateEngine->renderPage('admin/views/article-form.php', [
            'pageTitle' => 'Draft New Article | CMS Control Panel',
            'currentUser' => $user,
            'categories' => $categories,
            'authors' => $authors,
            'article' => null, // New mode
            'csrfToken' => Auth::generateCsrfToken(),
        ], 'admin');
    }

    /**
     * Edit Article Form View
     */
    public function editArticleForm(int $id): void
    {
        $user = Auth::requireAuth(['admin', 'editor', 'reporter']);
        $article = $this->db->fetch("SELECT * FROM articles WHERE id = :id", ['id' => $id]);

        if (!$article) {
            header('Location: ' . url('admin/dashboard.php?error=' . urlencode('Article record not found.')));
            exit;
        }

        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name ASC");
        $authors = $this->db->fetchAll("SELECT id, username, role FROM users WHERE is_active = 1 ORDER BY username ASC");

        $this->templateEngine->renderPage('admin/views/article-form.php', [
            'pageTitle' => 'Edit Post: ' . e($article['title']),
            'currentUser' => $user,
            'categories' => $categories,
            'authors' => $authors,
            'article' => $article,
            'csrfToken' => Auth::generateCsrfToken(),
        ], 'admin');
    }

    /**
     * POST Action Processor: Save/Update Article
     */
    public function saveArticle(array $postData, array $files): void
    {
        $user = Auth::requireAuth(['admin', 'editor', 'reporter']);

        // 1. Verify CSRF
        if (!Auth::verifyCsrfToken($postData['csrf_token'] ?? '')) {
            header('Location: ' . url('admin/dashboard.php?error=' . urlencode('Security validation failed (Invalid CSRF token).')));
            exit;
        }

        $id = !empty($postData['id']) ? (int)$postData['id'] : null;
        $title = trim($postData['title'] ?? '');
        $providedSlug = trim($postData['slug'] ?? '');
        $summary = trim($postData['summary'] ?? '');
        $content = trim($postData['content'] ?? '');
        $categoryId = (int)($postData['category_id'] ?? 0);
        $authorId = !empty($postData['author_id']) ? (int)$postData['author_id'] : $user['id'];
        $templateType = in_array($postData['template_type'] ?? '', ['standard', 'investigative', 'opinion'], true)
            ? $postData['template_type']
            : 'standard';
        $isBreaking = isset($postData['is_breaking']) ? 1 : 0;
        $hasDropCap = isset($postData['has_drop_cap']) ? 1 : 0;
        $status = in_array($postData['status'] ?? '', ['draft', 'published', 'archived'], true)
            ? $postData['status']
            : 'draft';
        $videoEmbedUrl = trim($postData['video_embed_url'] ?? '');
        $audioEmbedUrl = trim($postData['audio_embed_url'] ?? '');
        $galleryImages = trim($postData['gallery_images'] ?? '');
        $referenceUrl = trim($postData['reference_url'] ?? '');
        $referenceSourceName = trim($postData['reference_source_name'] ?? '');

        // Validation
        if (empty($title) || empty($content) || $categoryId <= 0) {
            $redirectUrl = $id 
                ? url("admin/article-edit.php?id={$id}&error=" . urlencode('Title, category, and article body content are required.'))
                : url("admin/article-create.php?error=" . urlencode('Title, category, and article body content are required.'));
            header("Location: {$redirectUrl}");
            exit;
        }

        // Slug generation & sanitization
        $slugBase = !empty($providedSlug) ? $providedSlug : $title;
        $slug = $this->generateUniqueSlug($slugBase, $id);

        // Featured Image Upload Processing
        $featuredImage = $postData['existing_featured_image'] ?? null;
        if (isset($files['featured_image']) && $files['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = $this->handleImageUpload($files['featured_image']);
            if (is_array($uploadedPath) && isset($uploadedPath['error'])) {
                $redirectUrl = $id 
                    ? url("admin/article-edit.php?id={$id}&error=" . urlencode($uploadedPath['error']))
                    : url("admin/article-create.php?error=" . urlencode($uploadedPath['error']));
                header("Location: {$redirectUrl}");
                exit;
            }
            $featuredImage = $uploadedPath;
        }

        // Multiple Gallery Files Upload Processing
        $uploadedGalleryUrls = [];
        if (isset($files['gallery_files']) && isset($files['gallery_files']['name']) && is_array($files['gallery_files']['name'])) {
            $count = count($files['gallery_files']['name']);
            for ($i = 0; $i < $count; $i++) {
                if (isset($files['gallery_files']['error'][$i]) && $files['gallery_files']['error'][$i] === UPLOAD_ERR_OK) {
                    $singleFile = [
                        'name' => $files['gallery_files']['name'][$i],
                        'type' => $files['gallery_files']['type'][$i],
                        'tmp_name' => $files['gallery_files']['tmp_name'][$i],
                        'error' => $files['gallery_files']['error'][$i],
                        'size' => $files['gallery_files']['size'][$i],
                    ];
                    $uploadedPath = $this->handleImageUpload($singleFile);
                    if (is_string($uploadedPath)) {
                        $uploadedGalleryUrls[] = $uploadedPath;
                    }
                }
            }
        }

        // Combine existing gallery_images textarea lines with newly uploaded files
        $existingGalleryLines = array_values(array_filter(array_map('trim', explode("\n", $galleryImages))));
        $allGalleryUrls = array_merge($existingGalleryLines, $uploadedGalleryUrls);
        $galleryImages = implode("\n", array_unique($allGalleryUrls));

        // Published Timestamp Logic
        $publishedAt = null;
        if ($status === 'published') {
            if ($id) {
                $currentArticle = $this->db->fetch("SELECT published_at FROM articles WHERE id = :id", ['id' => $id]);
                $publishedAt = $currentArticle['published_at'] ?? date('Y-m-d H:i:s');
            } else {
                $publishedAt = date('Y-m-d H:i:s');
            }
        }

        if ($id) {
            // Update
            $sql = "UPDATE articles SET 
                        title = :title,
                        slug = :slug,
                        summary = :summary,
                        content = :content,
                        featured_image = :featured_image,
                        video_embed_url = :video_embed_url,
                        audio_embed_url = :audio_embed_url,
                        gallery_images = :gallery_images,
                        reference_url = :reference_url,
                        reference_source_name = :reference_source_name,
                        category_id = :category_id,
                        author_id = :author_id,
                        template_type = :template_type,
                        is_breaking = :is_breaking,
                        has_drop_cap = :has_drop_cap,
                        status = :status,
                        published_at = :published_at
                    WHERE id = :id";

            $this->db->execute($sql, [
                'title' => $title,
                'slug' => $slug,
                'summary' => $summary,
                'content' => $content,
                'featured_image' => $featuredImage,
                'video_embed_url' => $videoEmbedUrl ?: null,
                'audio_embed_url' => $audioEmbedUrl ?: null,
                'gallery_images' => $galleryImages ?: null,
                'reference_url' => $referenceUrl ?: null,
                'reference_source_name' => $referenceSourceName ?: null,
                'category_id' => $categoryId,
                'author_id' => $authorId,
                'template_type' => $templateType,
                'is_breaking' => $isBreaking,
                'has_drop_cap' => $hasDropCap,
                'status' => $status,
                'published_at' => $publishedAt,
                'id' => $id
            ]);

            $msg = ($status === 'draft') ? 'Article draft successfully updated!' : 'Article successfully updated!';
            header("Location: " . url("admin/dashboard.php?msg=" . urlencode($msg)));
            exit;
        } else {
            // Create
            $sql = "INSERT INTO articles 
                    (title, slug, summary, content, featured_image, video_embed_url, audio_embed_url, gallery_images, reference_url, reference_source_name, category_id, author_id, template_type, is_breaking, has_drop_cap, status, published_at, created_at)
                    VALUES
                    (:title, :slug, :summary, :content, :featured_image, :video_embed_url, :audio_embed_url, :gallery_images, :reference_url, :reference_source_name, :category_id, :author_id, :template_type, :is_breaking, :has_drop_cap, :status, :published_at, NOW())";

            $this->db->execute($sql, [
                'title' => $title,
                'slug' => $slug,
                'summary' => $summary,
                'content' => $content,
                'featured_image' => $featuredImage,
                'video_embed_url' => $videoEmbedUrl ?: null,
                'audio_embed_url' => $audioEmbedUrl ?: null,
                'gallery_images' => $galleryImages ?: null,
                'reference_url' => $referenceUrl ?: null,
                'reference_source_name' => $referenceSourceName ?: null,
                'category_id' => $categoryId,
                'author_id' => $authorId,
                'template_type' => $templateType,
                'is_breaking' => $isBreaking,
                'has_drop_cap' => $hasDropCap,
                'status' => $status,
                'published_at' => $publishedAt,
            ]);

            $msg = ($status === 'draft') ? 'New article draft successfully saved!' : 'New article post successfully created!';
            header("Location: " . url("admin/dashboard.php?msg=" . urlencode($msg)));
            exit;
        }
    }

    /**
     * Delete Article Action
     */
    public function deleteArticle(int $id, string $csrfToken): void
    {
        Auth::requireAuth(['admin']);

        if (!Auth::verifyCsrfToken($csrfToken)) {
            header('Location: ' . url('admin/dashboard.php?error=' . urlencode('Security check failed (CSRF).')));
            exit;
        }

        $article = $this->db->fetch("SELECT featured_image FROM articles WHERE id = :id", ['id' => $id]);
        if ($article) {
            // Delete image file if stored locally in uploads/
            if (!empty($article['featured_image']) && str_contains($article['featured_image'], '/uploads/')) {
                $filename = basename($article['featured_image']);
                $filePath = __DIR__ . '/../../public/uploads/' . $filename;
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $this->db->execute("DELETE FROM articles WHERE id = :id", ['id' => $id]);
        }

        header('Location: ' . url('admin/dashboard.php?msg=' . urlencode('Article successfully deleted.')));
        exit;
    }

    /**
     * File Upload Handler with MIME and 5MB validation
     */
    private function handleImageUpload(array $file): string|array
    {
        // 5MB limit validation
        $maxSizeBytes = 5 * 1024 * 1024;
        if ($file['size'] > $maxSizeBytes) {
            return ['error' => 'File size exceeds maximum permitted threshold of 5MB.'];
        }

        // Allowed extension validation
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedExtensions, true)) {
            return ['error' => 'Invalid image file type. Only JPG, PNG, and WEBP uploads are allowed.'];
        }

        // Verify MIME type using finfo object
        $finfoObj = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfoObj->file($file['tmp_name']);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mimeType, $allowedMimes, true)) {
            return ['error' => 'MIME validation failed. Please upload a genuine image file.'];
        }

        $targetDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newFilename = 'cover_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $fileExt;
        $targetFile = $targetDir . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return url('public/uploads/' . $newFilename);
        }

        return ['error' => 'Failed to write uploaded image to media storage directory.'];
    }

    /**
     * Unique Slug Generator
     */
    private function generateUniqueSlug(string $text, ?int $currentId = null): string
    {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        if (empty($slug)) {
            $slug = 'article-' . time();
        }

        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $sql = "SELECT id FROM articles WHERE slug = :slug";
            $params = ['slug' => $slug];
            if ($currentId) {
                $sql .= " AND id != :id";
                $params['id'] = $currentId;
            }

            $existing = $this->db->fetch($sql, $params);
            if (!$existing) {
                break;
            }

            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Categories Management View
     */
    public function categories(): void
    {
        $user = Auth::requireAuth(['admin', 'editor']);
        $categories = $this->db->fetchAll(
            "SELECT c.*, COUNT(a.id) as article_count 
             FROM categories c 
             LEFT JOIN articles a ON c.id = a.category_id 
             GROUP BY c.id 
             ORDER BY c.name ASC"
        );

        $this->templateEngine->renderPage('admin/views/categories.php', [
            'pageTitle' => 'Category Management | CMS Control Panel',
            'currentUser' => $user,
            'categories' => $categories,
            'csrfToken' => Auth::generateCsrfToken(),
        ], 'admin');
    }

    /**
     * Unique Category Slug Generator
     */
    private function generateUniqueCategorySlug(string $name, ?int $currentId = null): string
    {
        // 1. Try to extract English part if format is "Khmer (English)"
        if (preg_match('/\(([^()]+)\)/u', $name, $matches)) {
            $nameForSlug = $matches[1];
        } else {
            // Map common Khmer category names to English for clean URL slugs
            $kmToEnSlug = [
                'បច្ចេកវិទ្យា' => 'technology-ai',
                'នយោបាយ'      => 'global-politics',
                'បរិស្ថាន'      => 'climate-science',
                'សេដ្ឋកិច្ច'    => 'economy-markets',
                'ហេដ្ឋារចនាសម្ព័ន្ធ' => 'infrastructure-logistics',
                'អប់រំ'        => 'education-health',
            ];
            $nameForSlug = $name;
            foreach ($kmToEnSlug as $kmKey => $enVal) {
                if (str_contains($name, $kmKey)) {
                    $nameForSlug = $enVal;
                    break;
                }
            }
        }

        $slug = strtolower(trim($nameForSlug));
        $slug = str_replace('&', 'and', $slug);
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        if (empty($slug) || $slug === 'and') {
            $slug = 'cat-' . ($currentId ?? time());
        }

        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $sql = "SELECT id FROM categories WHERE slug = :slug";
            $params = ['slug' => $slug];
            if ($currentId) {
                $sql .= " AND id != :id";
                $params['id'] = $currentId;
            }

            $existing = $this->db->fetch($sql, $params);
            if (!$existing) {
                break;
            }

            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Save/Update Category POST Action
     */
    public function saveCategory(array $postData): void
    {
        $user = Auth::requireAuth(['admin', 'editor']);
        if (!Auth::verifyCsrfToken($postData['csrf_token'] ?? '')) {
            header('Location: ' . url('admin/categories.php?error=' . urlencode('Invalid CSRF token.')));
            exit;
        }

        $id = !empty($postData['id']) ? (int)$postData['id'] : null;
        $name = trim($postData['name'] ?? '');
        $description = trim($postData['description'] ?? '');

        if (empty($name)) {
            header('Location: ' . url('admin/categories.php?error=' . urlencode('Category name is required.')));
            exit;
        }

        $slug = $this->generateUniqueCategorySlug($name, $id);

        if ($id) {
            $this->db->execute(
                "UPDATE categories SET name = :name, slug = :slug, description = :desc WHERE id = :id",
                ['name' => $name, 'slug' => $slug, 'desc' => $description, 'id' => $id]
            );
            $msg = 'Category successfully updated.';
        } else {
            $this->db->execute(
                "INSERT INTO categories (name, slug, description, created_at) VALUES (:name, :slug, :desc, NOW())",
                ['name' => $name, 'slug' => $slug, 'desc' => $description]
            );
            $msg = 'New Category successfully created.';
        }

        header('Location: ' . url('admin/categories.php?msg=' . urlencode($msg)));
        exit;
    }

    /**
     * Delete Category Action
     */
    public function deleteCategory(int $id, string $csrfToken): void
    {
        Auth::requireAuth(['admin']);
        if (!Auth::verifyCsrfToken($csrfToken)) {
            header('Location: ' . url('admin/categories.php?error=' . urlencode('Invalid CSRF token.')));
            exit;
        }

        $articleCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM articles WHERE category_id = :id", ['id' => $id]);
        if ($articleCount > 0) {
            header('Location: ' . url('admin/categories.php?error=' . urlencode('Cannot delete category: Contains linked articles.')));
            exit;
        }

        $this->db->execute("DELETE FROM categories WHERE id = :id", ['id' => $id]);
        header('Location: ' . url('admin/categories.php?msg=' . urlencode('Category deleted successfully.')));
        exit;
    }

    /**
     * Staff Users Management View
     */
    public function users(): void
    {
        $user = Auth::requireAuth(['admin']);
        $usersList = $this->db->fetchAll(
            "SELECT u.*, COUNT(a.id) as article_count 
             FROM users u 
             LEFT JOIN articles a ON u.id = a.author_id 
             GROUP BY u.id 
             ORDER BY u.created_at DESC"
        );

        $this->templateEngine->renderPage('admin/views/users.php', [
            'pageTitle' => 'Staff User Management | CMS Control Panel',
            'currentUser' => $user,
            'usersList' => $usersList,
            'csrfToken' => Auth::generateCsrfToken(),
        ], 'admin');
    }

    /**
     * Save/Update Staff User POST Action
     */
    public function saveUser(array $postData): void
    {
        Auth::requireAuth(['admin']);
        if (!Auth::verifyCsrfToken($postData['csrf_token'] ?? '')) {
            header('Location: ' . url('admin/users.php?error=' . urlencode('Invalid CSRF token.')));
            exit;
        }

        $id = !empty($postData['id']) ? (int)$postData['id'] : null;
        $username = trim($postData['username'] ?? '');
        $email = trim($postData['email'] ?? '');
        $password = trim($postData['password'] ?? '');
        $role = in_array($postData['role'] ?? '', ['admin', 'editor', 'reporter'], true) ? $postData['role'] : 'reporter';
        $bio = trim($postData['bio'] ?? '');
        $isActive = isset($postData['is_active']) ? 1 : 0;

        if (empty($username) || empty($email)) {
            header('Location: ' . url('admin/users.php?error=' . urlencode('Username and email are required.')));
            exit;
        }

        if ($id) {
            $params = ['username' => $username, 'email' => $email, 'role' => $role, 'bio' => $bio, 'active' => $isActive, 'id' => $id];
            $sql = "UPDATE users SET username = :username, email = :email, role = :role, bio = :bio, is_active = :active";
            if (!empty($password)) {
                $sql .= ", password_hash = :hash";
                $params['hash'] = password_hash($password, PASSWORD_BCRYPT);
            }
            $sql .= " WHERE id = :id";
            $this->db->execute($sql, $params);
            $msg = 'Staff account successfully updated.';
        } else {
            if (empty($password)) {
                header('Location: ' . url('admin/users.php?error=' . urlencode('Password is required for new staff accounts.')));
                exit;
            }
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $this->db->execute(
                "INSERT INTO users (username, email, password_hash, role, bio, is_active, created_at) VALUES (:username, :email, :hash, :role, :bio, :active, NOW())",
                ['username' => $username, 'email' => $email, 'hash' => $hash, 'role' => $role, 'bio' => $bio, 'active' => $isActive]
            );
            $msg = 'New staff user account created.';
        }

        header('Location: ' . url('admin/users.php?msg=' . urlencode($msg)));
        exit;
    }

    /**
     * Subscribers Feed Registrations View
     */
    public function subscribers(): void
    {
        $user = Auth::requireAuth(['admin', 'editor']);
        $subscribers = $this->db->fetchAll(
            "SELECT s.*, c.name as category_name 
             FROM subscribers s 
             LEFT JOIN categories c ON s.category_preference = c.id 
             ORDER BY s.subscribed_at DESC"
        );

        $this->templateEngine->renderPage('admin/views/subscribers.php', [
            'pageTitle' => 'Reader Subscribers Feed | CMS Control Panel',
            'currentUser' => $user,
            'subscribers' => $subscribers,
            'csrfToken' => Auth::generateCsrfToken(),
        ], 'admin');
    }

    /**
     * CSV Exporter for Feed Subscribers
     */
    public function exportSubscribersCsv(): void
    {
        Auth::requireAuth(['admin', 'editor']);
        $subscribers = $this->db->fetchAll(
            "SELECT s.id, s.email, c.name as category_preference, s.status, s.subscribed_at 
             FROM subscribers s 
             LEFT JOIN categories c ON s.category_preference = c.id 
             ORDER BY s.subscribed_at DESC"
        );

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="feed_subscribers_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Subscriber ID', 'Email Address', 'Category Preference', 'Status', 'Subscribed At']);

        foreach ($subscribers as $row) {
            fputcsv($output, [
                $row['id'],
                $row['email'],
                $row['category_preference'] ?? 'All Topics',
                $row['status'],
                $row['subscribed_at']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * AJAX Image Upload Endpoint for WYSIWYG Editor
     */
    public function uploadImageAjax(): void
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');

        if (!Auth::check() || !Auth::hasRole(['admin', 'editor', 'reporter'])) {
            echo json_encode(['error' => 'Session expired or insufficient privileges. Please refresh and log in.']);
            exit;
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['error' => 'No image file uploaded or upload error code: ' . ($_FILES['image']['error'] ?? 'unknown')]);
            exit;
        }

        $uploaded = $this->handleImageUpload($_FILES['image']);
        if (is_array($uploaded) && isset($uploaded['error'])) {
            echo json_encode(['error' => $uploaded['error']]);
            exit;
        }

        echo json_encode(['url' => $uploaded]);
        exit;
    }
}



