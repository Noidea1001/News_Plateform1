<?php
/**
 * Live Editorial Article Preview Controller Endpoint
 * news-platform / preview.php
 *
 * Renders the real article exactly through the real public templates (Standard, Investigative, Opinion)
 * using POSTed form draft data (or session draft), complete with real header, navbar, sidebar widgets,
 * and footer layout 100% identically to a published article on the live site.
 */

require_once __DIR__ . '/src/Core/helpers.php';
require_once __DIR__ . '/src/Core/Database.php';
require_once __DIR__ . '/src/Core/TemplateEngine.php';
require_once __DIR__ . '/src/Core/Auth.php';
require_once __DIR__ . '/src/Controllers/PublicController.php';

use App\Core\Auth;
use App\Core\Database;
use App\Core\TemplateEngine;

// Allow editors/admins/reporters to preview drafts
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Language switch via ?lang=
if (isset($_GET['lang'])) {
    $reqLang = strtolower(trim($_GET['lang']));
    if (in_array($reqLang, ['kh', 'km', 'en'], true)) {
        $_SESSION['lang'] = ($reqLang === 'km' || $reqLang === 'kh') ? 'kh' : 'en';
    }
}

// If draft data was posted via form, store in session for live previewing/refreshing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['preview_draft'] = $_POST;
    if (!empty($_FILES['featured_image']['tmp_name'])) {
        // Handle temporary upload preview
        $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
        $tmpName = 'tmp_preview_' . time() . '.' . $ext;
        $dest = __DIR__ . '/public/uploads/' . $tmpName;
        if (!is_dir(__DIR__ . '/public/uploads')) {
            @mkdir(__DIR__ . '/public/uploads', 0777, true);
        }
        if (@move_uploaded_file($_FILES['featured_image']['tmp_name'], $dest)) {
            $_SESSION['preview_draft']['featured_image_url'] = 'uploads/' . $tmpName;
        }
    }
}

$draft = $_SESSION['preview_draft'] ?? $_POST ?? [];

$db = Database::getInstance();
$templateEngine = new TemplateEngine();

// 1. Resolve Title & Content
$titleKh = trim($draft['title_kh'] ?? '');
$titleEn = trim($draft['title_en'] ?? '');
$title = trim($draft['title'] ?? '');
if (!$title) {
    $title = $titleKh ?: ($titleEn ?: 'Untitled Draft Article');
}
if (!$titleKh) $titleKh = $title;
if (!$titleEn) $titleEn = $title;

$summaryKh = trim($draft['summary_kh'] ?? '');
$summaryEn = trim($draft['summary_en'] ?? '');
$summary = trim($draft['summary'] ?? '');
if (!$summary) {
    $summary = $summaryKh ?: $summaryEn;
}

$contentKh = trim($draft['content_kh'] ?? '');
$contentEn = trim($draft['content_en'] ?? '');
$content = trim($draft['content'] ?? '');
if (!$content) {
    $content = $contentKh ?: $contentEn;
}

$templateType = trim($draft['template_type'] ?? 'standard');
if (!in_array($templateType, ['standard', 'investigative', 'opinion'], true)) {
    $templateType = 'standard';
}

$categoryId = (int) ($draft['category_id'] ?? 0);
$category = $db->fetch("SELECT * FROM categories WHERE id = :id LIMIT 1", ['id' => $categoryId]);
if (!$category) {
    $category = $db->fetch("SELECT * FROM categories ORDER BY id ASC LIMIT 1") ?: [
        'id' => 1,
        'name' => 'General News',
        'slug' => 'general'
    ];
}

// Author resolution
$currentUser = Auth::user();
$authorName = $currentUser['username'] ?? 'Editorial Author';
$authorRole = ucfirst($currentUser['role'] ?? 'Reporter');
$authorAvatar = !empty($currentUser['avatar_url']) ? $currentUser['avatar_url'] : null;

// Featured image resolution
$featuredImage = $draft['featured_image_url'] ?? $draft['existing_featured_image'] ?? $draft['featured_image_url_input'] ?? '';

// Word count & Reading time
$plainText = strip_tags($summary . ' ' . $content);
$words = preg_split('/\s+/u', trim($plainText));
$wordCount = count(array_filter($words));
$mins = max(1, (int) ceil($wordCount / 180));
$readingTime = __('min_read', ['min' => $mins]);

$article = [
    'id' => 0,
    'title' => $title,
    'title_kh' => $titleKh,
    'title_en' => $titleEn,
    'summary' => $summary,
    'summary_kh' => $summaryKh,
    'summary_en' => $summaryEn,
    'content' => $content,
    'content_kh' => $contentKh,
    'content_en' => $contentEn,
    'slug' => $draft['slug'] ?? 'preview-draft',
    'featured_image' => $featuredImage,
    'category_id' => $category['id'],
    'category_name' => $category['name'],
    'category_slug' => $category['slug'],
    'author_id' => $currentUser['id'] ?? 1,
    'author_name' => $authorName,
    'author_role' => $authorRole,
    'author_avatar' => $authorAvatar,
    'author_bio' => 'Editorial contributor & senior journalist.',
    'published_at' => date('Y-m-d H:i:s'),
    'created_at' => date('Y-m-d H:i:s'),
    'views_count' => 0,
    'is_breaking' => !empty($draft['is_breaking']) ? 1 : 0,
    'template_type' => $templateType,
    'has_drop_cap' => !empty($draft['has_drop_cap']) ? 1 : 0,
    'audio_embed_url' => trim($draft['audio_embed_url'] ?? ''),
    'video_embed_url' => trim($draft['video_embed_url'] ?? ''),
    'reference_url' => trim($draft['reference_url'] ?? ''),
    'reference_source_name' => trim($draft['reference_source_name'] ?? ''),
    'reading_time_mins' => $mins,
    'reading_time' => $readingTime,
    'status' => 'draft',
];

// Fetch real sidebar & related data
$relatedArticles = $db->fetchAll(
    "SELECT a.*, c.name as category_name 
     FROM articles a 
     JOIN categories c ON a.category_id = c.id 
     WHERE a.status = 'published' 
     ORDER BY a.published_at DESC LIMIT 3"
);

$trendingArticles = $db->fetchAll(
    "SELECT a.id, a.title, a.title_en, a.title_kh, a.slug, a.views_count, a.published_at, c.name as category_name 
     FROM articles a 
     JOIN categories c ON a.category_id = c.id 
     WHERE a.status = 'published' 
     ORDER BY a.views_count DESC LIMIT 5"
);

$categories = $db->fetchAll("SELECT * FROM categories ORDER BY name ASC");
$comments = [];

// Inject top Live Preview Control Bar into rendered output
ob_start();
$templateEngine->renderArticleView($templateType, [
    'pageTitle' => '[PREVIEW] ' . e($article['title']) . ' | NewsPlatform',
    'article' => $article,
    'relatedArticles' => $relatedArticles,
    'trendingArticles' => $trendingArticles,
    'categories' => $categories,
    'comments' => $comments,
]);
$fullHtml = ob_get_clean();

$activeLang = $_SESSION['lang'] ?? 'kh';
$khActive = ($activeLang === 'kh' || $activeLang === 'km');

$previewControlBar = '
<!-- Real-Time Live Preview Sticky Top Bar -->
<div id="livePreviewToolbar" style="position:fixed; top:0; left:0; right:0; z-index:99999; background:#0f172a; color:#fff; padding:6px 16px; box-shadow:0 4px 20px rgba(0,0,0,0.35); font-family:system-ui,sans-serif; display:flex; align-items:center; justify-content:space-between; gap:12px; border-bottom:2px solid #c8102e;">
    <div style="display:flex; align-items:center; gap:10px;">
        <span style="background:#c8102e; color:#fff; font-size:11px; font-weight:800; padding:3px 8px; border-radius:3px; text-transform:uppercase; letter-spacing:0.05em;">
            LIVE PREVIEW
        </span>
        <span style="font-size:12px; color:rgba(255,255,255,0.7); font-weight:600;">
            ' . strtoupper($templateType) . ' BLUEPRINT (100% REAL TEMPLATE)
        </span>
    </div>
    <div style="display:flex; align-items:center; gap:8px;">
        <div style="display:inline-flex; border-radius:4px; overflow:hidden; border:1px solid rgba(255,255,255,0.2);">
            <a href="?lang=kh" style="padding:3px 12px; font-size:12px; text-decoration:none; font-weight:bold; ' . ($khActive ? 'background:#c8102e; color:#fff;' : 'background:transparent; color:rgba(255,255,255,0.7);') . '">ខ្មែរ (KH)</a>
            <a href="?lang=en" style="padding:3px 12px; font-size:12px; text-decoration:none; font-weight:bold; ' . (!$khActive ? 'background:#c8102e; color:#fff;' : 'background:transparent; color:rgba(255,255,255,0.7);') . '">EN</a>
        </div>
        <button type="button" onclick="window.close()" style="background:rgba(255,255,255,0.15); border:none; color:#fff; padding:4px 12px; font-size:12px; font-weight:600; border-radius:4px; cursor:pointer;">
            &larr; Back to Editor
        </button>
    </div>
</div>
<style>
    body { padding-top: 42px !important; }
</style>
';

// Inject bar right after opening <body>
if (stripos($fullHtml, '<body') !== false) {
    $fullHtml = preg_replace('/(<body[^>]*>)/i', '$1' . $previewControlBar, $fullHtml, 1);
} else {
    $fullHtml = $previewControlBar . $fullHtml;
}

echo $fullHtml;
