<?php
/**
 * CMS Backend Editorial Controller (CMA Control Panel)
 * news-platform / src / Controllers / AdminController.php
 */


namespace App\Controllers;

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/Auth.php';
require_once __DIR__ . '/../Core/TemplateEngine.php';
require_once __DIR__ . '/../Core/WebPush.php';

use App\Core\Auth;
use App\Core\Database;
use App\Core\TemplateEngine;
use App\Core\WebPush;
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
            $this->db->execute("ALTER TABLE articles MODIFY COLUMN featured_image VARCHAR(500) NULL");
        } catch (\Throwable $e) {
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
        $totalSubscribers = (int)$this->db->fetchColumn(
            "SELECT (
                (SELECT COUNT(*) FROM reader_subscriptions) + 
                (SELECT COUNT(*) FROM subscribers WHERE status = 'active')
            )"
        );
        $totalViews = (int)$this->db->fetchColumn("SELECT COALESCE(SUM(views_count), 0) FROM articles");
        $totalComments = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM comments");
        $avgViewsPerStory = $publishedCount > 0 ? (int)round($totalViews / $publishedCount) : 0;

        // 2. Template Blueprint Breakdown Metrics
        $standardCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM articles WHERE template_type = 'standard'");
        $investigativeCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM articles WHERE template_type = 'investigative'");
        $opinionCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM articles WHERE template_type = 'opinion'");

        // 3. Category Readership Distribution Analytics
        $categoryStats = $this->db->fetchAll(
            "SELECT c.id, c.name, COUNT(a.id) as article_count, COALESCE(SUM(a.views_count), 0) as total_views 
             FROM categories c 
             LEFT JOIN articles a ON a.category_id = c.id 
             GROUP BY c.id, c.name 
             ORDER BY total_views DESC"
        );

        // 4. Editorial Posts List
        $articles = $this->db->fetchAll(
            "SELECT a.*, c.name as category_name, u.username as author_name 
             FROM articles a 
             JOIN categories c ON a.category_id = c.id 
             JOIN users u ON a.author_id = u.id 
             ORDER BY a.created_at DESC"
        );

        // 5. Recent Feed Subscribers (including reader topic subscriptions)
        $recentSubscribers = $this->db->fetchAll(
            "(
                SELECT 
                    rs.id, 
                    r.email, 
                    r.name as reader_name, 
                    c.name as category_name, 
                    'active' as status, 
                    rs.created_at as subscribed_at 
                 FROM reader_subscriptions rs 
                 JOIN readers r ON rs.reader_id = r.id 
                 JOIN categories c ON rs.category_id = c.id 
             )
             UNION ALL
             (
                SELECT 
                    s.id, 
                    s.email, 
                    NULL as reader_name, 
                    c.name as category_name, 
                    s.status, 
                    s.subscribed_at 
                 FROM subscribers s 
                 LEFT JOIN categories c ON s.category_preference = c.id 
             )
             ORDER BY subscribed_at DESC LIMIT 6"
        );

        $totalStaff = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM users");
        $totalReaders = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM readers");

        $this->templateEngine->renderPage('admin/views/dashboard.php', [
            'pageTitle' => 'CMS Dashboard | Editorial Control Panel',
            'currentUser' => $user,
            'totalArticles' => $totalArticles,
            'publishedCount' => $publishedCount,
            'draftCount' => $draftCount,
            'totalSubscribers' => $totalSubscribers,
            'totalViews' => $totalViews,
            'totalComments' => $totalComments,
            'avgViewsPerStory' => $avgViewsPerStory,
            'totalStaff' => $totalStaff,
            'totalReaders' => $totalReaders,
            'categoryStats' => $categoryStats,
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
        
        // Bilingual Processing: Khmer & English with smart dual-language auto-split
        $titleKh = trim($postData['title_kh'] ?? '');
        $titleEn = trim($postData['title_en'] ?? '');
        $title = trim($postData['title'] ?? '');

        // Auto-split if user input two languages into title_kh, title_en, or title
        if (!empty($titleKh)) {
            $split = split_dual_language($titleKh);
            if (!empty($split['kh']) && !empty($split['en'])) {
                $titleKh = $split['kh'];
                if (empty($titleEn)) $titleEn = $split['en'];
            }
        }
        if (!empty($titleEn)) {
            $split = split_dual_language($titleEn);
            if (!empty($split['kh']) && !empty($split['en'])) {
                if (empty($titleKh)) $titleKh = $split['kh'];
                $titleEn = $split['en'];
            }
        }
        if (!empty($title) && (empty($titleKh) || empty($titleEn))) {
            $split = split_dual_language($title);
            if (empty($titleKh) && !empty($split['kh'])) $titleKh = $split['kh'];
            if (empty($titleEn) && !empty($split['en'])) $titleEn = $split['en'];
        }
        if (empty($title)) {
            $title = $titleKh !== '' ? $titleKh : $titleEn;
        }
        if ($titleKh === '') {
            $titleKh = $title;
        }

        $summaryKh = trim($postData['summary_kh'] ?? '');
        $summaryEn = trim($postData['summary_en'] ?? '');
        $summary = trim($postData['summary'] ?? '');

        if (!empty($summaryKh)) {
            $split = split_dual_language($summaryKh);
            if (!empty($split['kh']) && !empty($split['en'])) {
                $summaryKh = $split['kh'];
                if (empty($summaryEn)) $summaryEn = $split['en'];
            }
        }
        if (!empty($summaryEn)) {
            $split = split_dual_language($summaryEn);
            if (!empty($split['kh']) && !empty($split['en'])) {
                if (empty($summaryKh)) $summaryKh = $split['kh'];
                $summaryEn = $split['en'];
            }
        }
        if (!empty($summary) && (empty($summaryKh) || empty($summaryEn))) {
            $split = split_dual_language($summary);
            if (empty($summaryKh) && !empty($split['kh'])) $summaryKh = $split['kh'];
            if (empty($summaryEn) && !empty($split['en'])) $summaryEn = $split['en'];
        }
        if (empty($summary)) {
            $summary = $summaryKh !== '' ? $summaryKh : $summaryEn;
        }
        if ($summaryKh === '') {
            $summaryKh = $summary;
        }

        $contentKh = trim($postData['content_kh'] ?? '');
        $contentEn = trim($postData['content_en'] ?? '');
        $content = trim($postData['content'] ?? '');

        if (!empty($contentKh)) {
            $split = split_dual_language($contentKh);
            if (!empty($split['kh']) && !empty($split['en'])) {
                $contentKh = $split['kh'];
                if (empty($contentEn)) $contentEn = $split['en'];
            }
        }
        if (!empty($contentEn)) {
            $split = split_dual_language($contentEn);
            if (!empty($split['kh']) && !empty($split['en'])) {
                if (empty($contentKh)) $contentKh = $split['kh'];
                $contentEn = $split['en'];
            }
        }
        if (!empty($content) && (empty($contentKh) || empty($contentEn))) {
            $split = split_dual_language($content);
            if (empty($contentKh) && !empty($split['kh'])) $contentKh = $split['kh'];
            if (empty($contentEn) && !empty($split['en'])) $contentEn = $split['en'];
        }
        if (empty($content)) {
            $content = $contentKh !== '' ? $contentKh : $contentEn;
        }
        if ($contentKh === '') {
            $contentKh = $content;
        }

        $providedSlug = trim($postData['slug'] ?? '');
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

        // Slug generation & sanitization (prefer English title for clean ASCII URL slug)
        $slugBase = !empty($providedSlug) ? $providedSlug : ($titleEn ?: $title);
        $slug = $this->generateUniqueSlug($slugBase, $id);

        // Featured Image Upload Processing
        $featuredImageUrl = trim($postData['featured_image_url'] ?? '');
        $featuredImage = $postData['existing_featured_image'] ?? null;

        if (!empty($featuredImageUrl)) {
            $featuredImage = $featuredImageUrl;
        }

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
                        title_kh = :title_kh,
                        title_en = :title_en,
                        slug = :slug,
                        summary = :summary,
                        summary_kh = :summary_kh,
                        summary_en = :summary_en,
                        content = :content,
                        content_kh = :content_kh,
                        content_en = :content_en,
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
                'title_kh' => $titleKh,
                'title_en' => $titleEn,
                'slug' => $slug,
                'summary' => $summary,
                'summary_kh' => $summaryKh,
                'summary_en' => $summaryEn,
                'content' => $content,
                'content_kh' => $contentKh,
                'content_en' => $contentEn,
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

            // Real-time reader notification if published
            if ($status === 'published') {
                $this->dispatchArticleNotification($id, $titleKh, $titleEn, $title, $summaryKh, $summaryEn, $content, (bool)$isBreaking);
            }

            $msg = ($status === 'draft') ? 'Article draft successfully updated!' : 'Article successfully updated!';
            header("Location: " . url("admin/dashboard.php?msg=" . urlencode($msg)));
            exit;
        } else {
            // Create
            $sql = "INSERT INTO articles 
                    (title, title_kh, title_en, slug, summary, summary_kh, summary_en, content, content_kh, content_en, featured_image, video_embed_url, audio_embed_url, gallery_images, reference_url, reference_source_name, category_id, author_id, template_type, is_breaking, has_drop_cap, status, published_at, created_at)
                    VALUES
                    (:title, :title_kh, :title_en, :slug, :summary, :summary_kh, :summary_en, :content, :content_kh, :content_en, :featured_image, :video_embed_url, :audio_embed_url, :gallery_images, :reference_url, :reference_source_name, :category_id, :author_id, :template_type, :is_breaking, :has_drop_cap, :status, :published_at, NOW())";

            $this->db->execute($sql, [
                'title' => $title,
                'title_kh' => $titleKh,
                'title_en' => $titleEn,
                'slug' => $slug,
                'summary' => $summary,
                'summary_kh' => $summaryKh,
                'summary_en' => $summaryEn,
                'content' => $content,
                'content_kh' => $contentKh,
                'content_en' => $contentEn,
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

            $newArticleId = (int)$this->db->lastInsertId();

            // Real-time reader notification if published
            if ($status === 'published' && $newArticleId > 0) {
                $this->dispatchArticleNotification($newArticleId, $titleKh, $titleEn, $title, $summaryKh, $summaryEn, $content, (bool)$isBreaking);
            }

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
            'msg' => $_GET['msg'] ?? null,
            'error' => $_GET['error'] ?? null,
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

        // Validate unique username
        $dupUserSql = "SELECT id FROM users WHERE username = :u" . ($id ? " AND id != :id" : "");
        $dupUserParams = ['u' => $username];
        if ($id) $dupUserParams['id'] = $id;
        $dupUser = $this->db->fetch($dupUserSql, $dupUserParams);
        if ($dupUser) {
            header('Location: ' . url('admin/users.php?error=' . urlencode("Username '{$username}' is already taken. Please choose another username.")));
            exit;
        }

        // Validate unique email
        $dupEmailSql = "SELECT id FROM users WHERE email = :e" . ($id ? " AND id != :id" : "");
        $dupEmailParams = ['e' => $email];
        if ($id) $dupEmailParams['id'] = $id;
        $dupEmail = $this->db->fetch($dupEmailSql, $dupEmailParams);
        if ($dupEmail) {
            header('Location: ' . url('admin/users.php?error=' . urlencode("Email address '{$email}' is already registered. Please choose another email.")));
            exit;
        }

        try {
            if ($id) {
                $existingUser = $this->db->fetch("SELECT * FROM users WHERE id = :id", ['id' => $id]);
                if (!$existingUser) {
                    header('Location: ' . url('admin/users.php?error=' . urlencode('Staff user not found.')));
                    exit;
                }

                // Security Rule: Administrator accounts and roles are strictly protected. Cannot demote or change role.
                if ($existingUser['role'] === 'admin') {
                    $role = 'admin';
                    $isActive = 1; // Cannot deactivate an administrator account
                }

                if (!empty($password) && strlen($password) < 8) {
                    header('Location: ' . url('admin/users.php?error=' . urlencode('Password must be at least 8 characters long.')));
                    exit;
                }
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
                if (strlen($password) < 8) {
                    header('Location: ' . url('admin/users.php?error=' . urlencode('Password must be at least 8 characters long.')));
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
        } catch (\Throwable $e) {
            $errorMsg = str_contains($e->getMessage(), 'Duplicate entry')
                ? "A user with this username or email already exists."
                : "Database error saving staff account: " . $e->getMessage();
            header('Location: ' . url('admin/users.php?error=' . urlencode($errorMsg)));
            exit;
        }
    }

    /**
     * Delete Staff User Action
     */
    public function deleteUser(int $id, string $csrfToken): void
    {
        $currentUser = Auth::requireAuth(['admin']);

        if (!Auth::verifyCsrfToken($csrfToken)) {
            header('Location: ' . url('admin/users.php?error=' . urlencode('Security validation failed (Invalid CSRF).')));
            exit;
        }

        if ($id <= 0) {
            header('Location: ' . url('admin/users.php?error=' . urlencode('Invalid staff user ID.')));
            exit;
        }

        $targetUser = $this->db->fetch("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        if (!$targetUser) {
            header('Location: ' . url('admin/users.php?error=' . urlencode('Staff user not found.')));
            exit;
        }

        // STRICT PROTECTION: Admin cannot be deleted!
        if ($targetUser['role'] === 'admin') {
            header('Location: ' . url('admin/users.php?error=' . urlencode('Security Protection: Administrator accounts cannot be deleted.')));
            exit;
        }

        // Prevent deleting self
        if ((int)$targetUser['id'] === (int)$currentUser['id']) {
            header('Location: ' . url('admin/users.php?error=' . urlencode('Security Protection: You cannot delete your own account.')));
            exit;
        }

        // Reassign authored articles to current admin so articles are preserved
        $articleCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM articles WHERE author_id = :id", ['id' => $id]);
        if ($articleCount > 0) {
            $this->db->execute("UPDATE articles SET author_id = :admin_id WHERE author_id = :old_id", [
                'admin_id' => $currentUser['id'],
                'old_id' => $id
            ]);
        }

        $this->db->execute("DELETE FROM users WHERE id = :id", ['id' => $id]);
        header('Location: ' . url('admin/users.php?msg=' . urlencode('Staff user account successfully deleted.')));
        exit;
    }

    /**
     * Reader Users Management View (Separate from Staff)
     */
    public function readers(): void
    {
        $currentUser = Auth::requireAuth(['admin']);
        $searchQuery = trim($_GET['q'] ?? '');

        if (!empty($searchQuery)) {
            $term = '%' . $searchQuery . '%';
            $readersList = $this->db->fetchAll(
                "SELECT r.*, COUNT(c.id) as comment_count 
                 FROM readers r 
                 LEFT JOIN comments c ON c.user_email = r.email 
                 WHERE r.name LIKE :q1 OR r.email LIKE :q2 
                 GROUP BY r.id 
                 ORDER BY r.created_at DESC",
                ['q1' => $term, 'q2' => $term]
            );
        } else {
            $readersList = $this->db->fetchAll(
                "SELECT r.*, COUNT(c.id) as comment_count 
                 FROM readers r 
                 LEFT JOIN comments c ON c.user_email = r.email 
                 GROUP BY r.id 
                 ORDER BY r.created_at DESC"
            );
        }

        $totalReaders = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM readers");

        $this->templateEngine->renderPage('admin/views/readers.php', [
            'pageTitle' => __('readers_mgmt_title') . ' | CMS Control Panel',
            'currentUser' => $currentUser,
            'readersList' => $readersList,
            'totalReaders' => $totalReaders,
            'searchQuery' => $searchQuery,
            'csrfToken' => Auth::generateCsrfToken(),
            'msg' => $_GET['msg'] ?? null,
            'error' => $_GET['error'] ?? null,
        ], 'admin');
    }

    /**
     * Delete Reader User Action
     */
    public function deleteReader(int $id, string $csrfToken): void
    {
        Auth::requireAuth(['admin']);

        if (!Auth::verifyCsrfToken($csrfToken)) {
            header('Location: ' . url('admin/readers.php?error=' . urlencode('Security validation failed (Invalid CSRF).')));
            exit;
        }

        if ($id <= 0) {
            header('Location: ' . url('admin/readers.php?error=' . urlencode('Invalid reader ID.')));
            exit;
        }

        $targetReader = $this->db->fetch("SELECT * FROM readers WHERE id = :id", ['id' => $id]);
        if (!$targetReader) {
            header('Location: ' . url('admin/readers.php?error=' . urlencode('Reader account not found.')));
            exit;
        }

        $this->db->execute("DELETE FROM readers WHERE id = :id", ['id' => $id]);
        header('Location: ' . url('admin/readers.php?msg=' . urlencode(__('reader_deleted_success'))));
        exit;
    }

    /**
     * Admin News Archive ("បណ្ណសារព័ត៌មាន") Management View
     */
    public function archive(): void
    {
        $currentUser = Auth::requireAuth(['admin', 'editor', 'reporter']);

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

        $categoryId = !empty($_GET['category']) ? (int)$_GET['category'] : 0;
        $year = !empty($_GET['year']) ? (int)$_GET['year'] : 0;
        $month = !empty($_GET['month']) ? (int)$_GET['month'] : 0;
        $blueprint = trim($_GET['blueprint'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $isBreaking = isset($_GET['breaking']) && $_GET['breaking'] === '1' ? 1 : null;
        $searchQuery = trim($_GET['q'] ?? '');
        $sort = trim($_GET['sort'] ?? 'newest');
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;

        $where = ["1=1"];
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

        if (in_array($status, ['published', 'draft', 'archived'], true)) {
            $where[] = "a.status = :st";
            $params['st'] = $status;
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

        $orderBy = match ($sort) {
            'oldest' => "a.published_at ASC",
            'views' => "a.views_count DESC, a.published_at DESC",
            'alpha' => "a.title ASC",
            default => "a.published_at DESC, a.created_at DESC",
        };

        $totalCount = (int)$this->db->fetchColumn(
            "SELECT COUNT(*) FROM articles a {$whereSql}",
            $params
        );
        $totalPages = max(1, (int)ceil($totalCount / $perPage));
        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }
        $offset = ($currentPage - 1) * $perPage;

        $articles = $this->db->fetchAll(
            "SELECT a.*, c.name as category_name, c.slug as category_slug, u.username as author_name 
             FROM articles a 
             JOIN categories c ON a.category_id = c.id 
             JOIN users u ON a.author_id = u.id 
             {$whereSql} 
             ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $this->templateEngine->renderPage('admin/views/archive.php', [
            'pageTitle' => __('archive_page_title') . ' | CMS Control Panel',
            'currentUser' => $currentUser,
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
                'status' => $status,
                'breaking' => $isBreaking,
                'q' => $searchQuery,
                'sort' => $sort,
            ],
            'csrfToken' => Auth::generateCsrfToken(),
        ], 'admin');
    }

    /**
     * Subscribers Feed Registrations View
     */
    public function subscribers(): void
    {
        $user = Auth::requireAuth(['admin', 'editor']);
        $subscribers = $this->db->fetchAll(
            "(
                SELECT 
                    rs.id, 
                    r.id as reader_id,
                    r.email, 
                    r.name as reader_name, 
                    c.name as category_name, 
                    'active' as status, 
                    rs.created_at as subscribed_at 
                 FROM reader_subscriptions rs 
                 JOIN readers r ON rs.reader_id = r.id 
                 JOIN categories c ON rs.category_id = c.id 
             )
             UNION ALL
             (
                SELECT 
                    s.id, 
                    NULL as reader_id,
                    s.email, 
                    NULL as reader_name, 
                    c.name as category_name, 
                    s.status, 
                    s.subscribed_at 
                 FROM subscribers s 
                 LEFT JOIN categories c ON s.category_preference = c.id 
             )
             ORDER BY subscribed_at DESC"
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
            "(
                SELECT 
                    rs.id, 
                    r.email, 
                    r.name as reader_name,
                    c.name as category_preference, 
                    'active' as status, 
                    rs.created_at as subscribed_at 
                 FROM reader_subscriptions rs 
                 JOIN readers r ON rs.reader_id = r.id
                 JOIN categories c ON rs.category_id = c.id 
             )
             UNION ALL
             (
                SELECT 
                    s.id, 
                    s.email, 
                    NULL as reader_name,
                    c.name as category_preference, 
                    s.status, 
                    s.subscribed_at 
                 FROM subscribers s 
                 LEFT JOIN categories c ON s.category_preference = c.id 
             )
             ORDER BY subscribed_at DESC"
        );

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="feed_subscribers_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Excel
        fputs($output, "\xEF\xBB\xBF");
        fputcsv($output, ['Subscriber ID', 'Email Address', 'Reader Name', 'Topic Preference', 'Status', 'Subscribed At']);

        foreach ($subscribers as $row) {
            fputcsv($output, [
                $row['id'],
                $row['email'],
                $row['reader_name'] ?? 'N/A',
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

    /**
     * Helper to dispatch real-time reader notifications upon article publication
     */
    private function dispatchArticleNotification(int $articleId, string $titleKh, string $titleEn, string $title, string $summaryKh, string $summaryEn, string $content, bool $isBreaking): void
    {
        try {
            $notifTitle = !empty($titleKh) && !empty($titleEn) && $titleKh !== $titleEn
                ? "{$titleKh} | {$titleEn}"
                : ($titleKh ?: ($titleEn ?: $title));
            $notifMsg = !empty($summaryKh) 
                ? $summaryKh 
                : (!empty($summaryEn) ? $summaryEn : mb_substr(strip_tags($content), 0, 150));
            $notifType = $isBreaking ? 'breaking' : 'news';

            if ($articleId > 0) {
                $existingNotif = $this->db->fetch("SELECT id FROM notifications WHERE article_id = :aid", ['aid' => $articleId]);
                if (!$existingNotif) {
                    $this->db->execute(
                        "INSERT INTO notifications (article_id, title, message, type, created_at) VALUES (:aid, :title, :message, :type, NOW())",
                        [
                            'aid' => $articleId,
                            'title' => mb_substr($notifTitle, 0, 250),
                            'message' => mb_substr($notifMsg, 0, 250),
                            'type' => $notifType
                        ]
                    );
                }

                // Web Push API: Send real-time browser notifications to opted-in subscribers instantly
                $art = $this->db->fetch("SELECT slug, title, title_en, title_kh FROM articles WHERE id = :id", ['id' => $articleId]);
                $slug = $art['slug'] ?? '';
                $pushHeadline = !empty($art['title_en']) ? $art['title_en'] : ($art['title'] ?? $notifTitle);
                $finalTitleKh = $art['title_kh'] ?? $titleKh;
                $finalTitleEn = $art['title_en'] ?? $titleEn;

                WebPush::sendBreakingNewsNotification(
                    $articleId,
                    $pushHeadline,
                    $notifMsg,
                    $slug,
                    $finalTitleKh,
                    $finalTitleEn,
                    $summaryKh,
                    $summaryEn
                );
            }
        } catch (\Throwable $ne) {
            error_log("Failed to dispatch notification for article {$articleId}: " . $ne->getMessage());
        }
    }
}



