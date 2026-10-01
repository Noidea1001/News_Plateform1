<?php
/**
 * Social Media Share & Quick Reader Actions Toolbar
 * news-platform / templates / components / share-buttons.php
 */

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
$currentUrl = ($isHttps ? "https://" : "http://") . $host . $uri;
$shareTitle = article_title($article['title'] ?? 'NewsPlatform Story');
?>

<div class="editorial-actions-bar p-2.5 p-sm-3 bg-white rounded-2 border my-3 shadow-2xs">
    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
        
        <!-- Left: Action Labels & Fast News Link -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 text-2xs fw-bold d-inline-flex align-items-center gap-1">
                <span class="live-dot" style="width: 6px; height: 6px;"></span>
                <?= __('live_coverage') ?? 'LIVE' ?>
            </span>
            <a href="<?= url('archive.php?breaking=1') ?>" class="text-decoration-none text-dark small fw-bold hover-danger d-none d-md-inline-flex align-items-center gap-1" title="<?= __('filter_breaking_only') ?>">
                <i class="bi bi-lightning-charge-fill text-warning"></i>
                <span class="text-xs"><?= __('filter_breaking_only') ?></span> &rarr;
            </a>
        </div>

        <!-- Right: Bookmark & Social Share Buttons -->
        <div class="d-flex align-items-center gap-1.5 gap-sm-2 flex-wrap justify-content-end ms-auto">
            
            <!-- 1. Bookmark / Save Button -->
            <?php if (isset($article) && isset($artData)) { ?>
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 bookmark-toggle-btn d-inline-flex align-items-center gap-1.5 shadow-2xs fw-semibold"
                    data-id="<?= (int)$article['id'] ?>" data-article='<?= $artData ?>' title="<?= __('save_for_later') ?? 'Bookmark Story' ?>" style="font-size:0.8rem;">
                    <i class="bi bi-bookmark fs-6"></i>
                    <span class="bookmark-label d-none d-sm-inline"><?= __('save_for_later') ?? 'Save' ?></span>
                </button>
            <?php } ?>

            <!-- 2. Telegram Share -->
            <a href="https://t.me/share/url?url=<?= urlencode($currentUrl) ?>&text=<?= urlencode($shareTitle) ?>" target="_blank" rel="noopener"
                class="btn btn-sm text-white rounded-pill px-2.5 px-sm-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-2xs fw-semibold"
                style="background-color: #0088cc; font-size:0.8rem;" title="Share on Telegram">
                <i class="bi bi-telegram fs-6"></i>
                <span class="d-none d-md-inline">Telegram</span>
            </a>

            <!-- 3. Facebook Share -->
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>" target="_blank" rel="noopener"
                class="btn btn-sm text-white rounded-pill px-2.5 px-sm-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-2xs fw-semibold"
                style="background-color: #1877f2; font-size:0.8rem;" title="Share on Facebook">
                <i class="bi bi-facebook fs-6"></i>
                <span class="d-none d-md-inline">Facebook</span>
            </a>

            <!-- 4. X / Twitter Share -->
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($currentUrl) ?>&text=<?= urlencode($shareTitle) ?>" target="_blank" rel="noopener"
                class="btn btn-sm btn-dark rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1 shadow-2xs"
                style="font-size:0.8rem;" title="Share on X">
                <i class="bi bi-twitter-x"></i>
            </a>

            <!-- 5. Copy Link Button -->
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 px-sm-3 py-1.5 d-inline-flex align-items-center gap-1 shadow-2xs copy-link-btn"
                onclick="copyArticleLink(this)" title="<?= __('copy_link') ?>" style="font-size:0.8rem;">
                <i class="bi bi-link-45deg fs-6"></i>
                <span class="copy-label d-none d-sm-inline"><?= __('copy_link') ?></span>
            </button>
        </div>

    </div>
</div>

<script>
if (typeof window.copyArticleLink !== 'function') {
    window.copyArticleLink = function(btn) {
        const urlToCopy = window.location.href;
        navigator.clipboard.writeText(urlToCopy).then(function() {
            const labelEl = btn.querySelector('.copy-label') || btn;
            const originalText = labelEl.innerText;
            labelEl.innerText = '<?= addslashes(__('copied') ?? 'Copied!') ?>';
            btn.classList.add('btn-success');
            btn.classList.remove('btn-outline-secondary');
            setTimeout(function() {
                labelEl.innerText = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-secondary');
            }, 2000);
        }).catch(function() {
            prompt('Copy URL:', urlToCopy);
        });
    };
}
</script>
