<?php
/**
 * Social Media Share Buttons Component
 * news-platform / templates / components / share-buttons.php
 */

require_once __DIR__ . '/../../src/Core/helpers.php';
$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$shareTitle = article_title($article['title'] ?? 'NewsPlatform Story');
?>

<div class="share-buttons-widget p-2.5 p-sm-3 bg-light rounded border my-4 shadow-sm">
    <div class="d-flex align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
            <i class="bi bi-share-fill text-danger fs-6 fs-sm-5"></i>
            <span class="fw-bold text-dark text-xs text-sm-normal"><?= __('share_story') ?></span>
        </div>
        <div class="d-flex align-items-center gap-2 gap-sm-2.5 flex-wrap justify-content-end ms-auto">
            <!-- Telegram -->
            <a href="https://t.me/share/url?url=<?= urlencode($currentUrl) ?>&text=<?= urlencode($shareTitle) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-primary rounded-pill px-2.5 px-sm-3 py-1.5 shadow-xs d-inline-flex align-items-center justify-content-center gap-1" style="background-color: #0088cc; border: none;" title="Telegram">
                <i class="bi bi-telegram"></i> <span class="d-none d-md-inline small">Telegram</span>
            </a>
            <!-- Facebook -->
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-primary rounded-pill px-2.5 px-sm-3 py-1.5 shadow-xs d-inline-flex align-items-center justify-content-center gap-1" style="background-color: #1877f2; border: none;" title="Facebook">
                <i class="bi bi-facebook"></i> <span class="d-none d-md-inline small">Facebook</span>
            </a>
            <!-- X / Twitter -->
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($currentUrl) ?>&text=<?= urlencode($shareTitle) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-dark rounded-pill px-2.5 px-sm-3 py-1.5 shadow-xs d-inline-flex align-items-center justify-content-center gap-1" title="X">
                <i class="bi bi-twitter-x"></i> <span class="d-none d-md-inline small">X</span>
            </a>
            <!-- Copy Link Button -->
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 px-sm-3 py-1.5 shadow-xs d-inline-flex align-items-center justify-content-center gap-1" id="copyShareBtn" onclick="copyArticleLink()" title="<?= __('copy_link') ?>">
                <i class="bi bi-link-45deg fs-6"></i> <span id="copyBtnText" class="d-none d-md-inline small"><?= __('copy_link') ?></span>
            </button>
            <!-- Save Button at the end -->
            <?php if (isset($article) && isset($artData)) { ?>
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 px-sm-3 py-1.5 shadow-xs bookmark-toggle-btn d-inline-flex align-items-center justify-content-center gap-1" data-id="<?= $article['id'] ?>" data-article='<?= $artData ?>' title="<?= __('save_for_later') ?? 'Save' ?>">
                    <i class="bi bi-bookmark"></i> <span class="d-none d-md-inline small"><?= __('save_for_later') ?? 'Save' ?></span>
                </button>
            <?php } ?>
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
