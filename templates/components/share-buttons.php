<?php
/**
 * Social Media Share Buttons Component
 * news-platform / templates / components / share-buttons.php
 */

require_once __DIR__ . '/../../src/Core/helpers.php';
$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$shareTitle = article_title($article['title'] ?? 'NewsPlatform Story');
?>

<div class="share-buttons-widget p-3 bg-light rounded border my-4 shadow-sm">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-share-fill text-danger fs-5"></i>
            <span class="fw-bold text-dark small"><?= __('share_story') ?></span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Telegram -->
            <a href="https://t.me/share/url?url=<?= urlencode($currentUrl) ?>&text=<?= urlencode($shareTitle) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs" style="background-color: #0088cc; border: none;">
                <i class="bi bi-telegram me-1"></i> Telegram
            </a>
            <!-- Facebook -->
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs" style="background-color: #1877f2; border: none;">
                <i class="bi bi-facebook me-1"></i> Facebook
            </a>
            <!-- X / Twitter -->
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($currentUrl) ?>&text=<?= urlencode($shareTitle) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-dark rounded-pill px-3 shadow-xs">
                <i class="bi bi-twitter-x me-1"></i> X
            </a>
            <!-- LinkedIn -->
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($currentUrl) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs" style="background-color: #0a66c2; border: none;">
                <i class="bi bi-linkedin me-1"></i> LinkedIn
            </a>
            <!-- Copy Link Button -->
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-xs" id="copyShareBtn" onclick="copyArticleLink()">
                <i class="bi bi-link-45deg me-1"></i> <span id="copyBtnText"><?= __('copy_link') ?></span>
            </button>
        </div>
    </div>
</div>

<script>
function copyArticleLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        var btnText = document.getElementById('copyBtnText');
        var original = btnText.innerText;
        btnText.innerText = '<?= __('copied') ?>';
        setTimeout(function() {
            btnText.innerText = original;
        }, 2000);
    });
}
</script>
