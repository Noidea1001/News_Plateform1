<?php
/**
 * Modern Clean Editorial Action & Dropdown Share Strip
 * news-platform / templates / components / share-buttons.php
 *
 * Minimalist, seamless toolbar with Bookmark and a single Share dropdown menu.
 */

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
$currentUrl = ($isHttps ? "https://" : "http://") . $host . $uri;
$shareTitle = article_title($article['title'] ?? 'NewsPlatform Story');
?>

<!-- ── Modern Clean Editorial Action & Dropdown Share Strip ────── -->
<div class="editorial-actions-bar py-2 my-2 border-top border-bottom d-flex align-items-center justify-content-between gap-2 flex-wrap" style="background: transparent;">
    
    <!-- Left: Fast News Archive Link -->
    <div class="d-flex align-items-center gap-2">
        <a href="<?= url('archive.php?breaking=1') ?>" class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 text-2xs fw-bold text-decoration-none d-inline-flex align-items-center gap-1.5" title="<?= __('filter_breaking_only') ?>">
            <i class="bi bi-lightning-fill text-danger" style="font-size: 0.75rem;"></i>
            <span><?= __('filter_breaking_only') ?></span>
            <i class="bi bi-arrow-right" style="font-size: 0.7rem;"></i>
        </a>
    </div>

    <!-- Right: Bookmark & Clean Share Dropdown -->
    <div class="d-flex align-items-center gap-2 ms-auto">
        
        <!-- 1. Bookmark / Save Button (Only for Registered Readers) -->
        <?php if ((\App\Core\Auth::readerCheck() || \App\Core\Auth::check()) && isset($article) && isset($artData)) { ?>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 bookmark-toggle-btn d-inline-flex align-items-center gap-1.5 shadow-none text-xs fw-semibold"
                data-id="<?= (int)$article['id'] ?>" data-article='<?= $artData ?>' title="<?= __('save_for_later') ?? 'Save' ?>" style="height:32px;">
                <i class="bi bi-bookmark text-danger"></i>
                <span class="bookmark-label"><?= __('save_for_later') ?? 'Save' ?></span>
            </button>
        <?php } ?>

        <!-- 2. Clean Share Dropdown Button -->
        <div class="dropdown position-relative">
            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1.5 text-xs fw-semibold dropdown-toggle shadow-none"
                type="button" id="articleShareDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="height:32px;">
                <i class="bi bi-share text-danger"></i>
                <span><?= __('share_story') ?? 'Share' ?></span>
            </button>
            
            <!-- Share Options Dropdown Menu -->
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-2 mt-1" aria-labelledby="articleShareDropdownBtn" style="border-radius: 8px; min-width: 200px; z-index: 1050;">
                <li><h6 class="dropdown-header text-2xs text-uppercase fw-bold text-muted"><?= __('share_story') ?? 'Share Story' ?></h6></li>
                
                <!-- Telegram -->
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2.5 py-2 text-xs fw-semibold"
                        href="https://t.me/share/url?url=<?= urlencode($currentUrl) ?>&text=<?= urlencode($shareTitle) ?>"
                        target="_blank" rel="noopener">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white flex-shrink-0" style="width:26px; height:26px; background:#0088cc;">
                            <i class="bi bi-telegram" style="font-size:0.8rem;"></i>
                        </span>
                        <span>Telegram</span>
                    </a>
                </li>

                <!-- Facebook -->
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2.5 py-2 text-xs fw-semibold"
                        href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>"
                        target="_blank" rel="noopener">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white flex-shrink-0" style="width:26px; height:26px; background:#1877f2;">
                            <i class="bi bi-facebook" style="font-size:0.8rem;"></i>
                        </span>
                        <span>Facebook</span>
                    </a>
                </li>

                <!-- X / Twitter -->
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2.5 py-2 text-xs fw-semibold"
                        href="https://twitter.com/intent/tweet?url=<?= urlencode($currentUrl) ?>&text=<?= urlencode($shareTitle) ?>"
                        target="_blank" rel="noopener">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white bg-dark flex-shrink-0" style="width:26px; height:26px;">
                            <i class="bi bi-twitter-x" style="font-size:0.75rem;"></i>
                        </span>
                        <span>X (Twitter)</span>
                    </a>
                </li>

                <li><hr class="dropdown-divider my-1"></li>

                <!-- Copy Link Button -->
                <li>
                    <button type="button" class="dropdown-item d-flex align-items-center gap-2.5 py-2 text-xs fw-semibold w-100 bg-transparent border-0 text-start"
                        onclick="copyArticleLinkDropdown(this)">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-secondary border flex-shrink-0 copy-icon-box" style="width:26px; height:26px;">
                            <i class="bi bi-link-45deg" style="font-size:0.85rem;"></i>
                        </span>
                        <span class="copy-text-label"><?= __('copy_link') ?? 'Copy Link' ?></span>
                    </button>
                </li>
            </ul>
        </div>

    </div>
</div>

<style>
.editorial-actions-bar {
    border-color: #e5e7eb !important;
}
.editorial-actions-bar .dropdown-item {
    transition: background 0.12s ease, color 0.12s ease;
}
.editorial-actions-bar .dropdown-item:hover {
    background-color: #f8fafc;
    color: #c8102e;
}
</style>

<script>
function copyArticleLinkDropdown(btn) {
    const urlToCopy = window.location.href;
    navigator.clipboard.writeText(urlToCopy).then(function() {
        const label = btn.querySelector('.copy-text-label');
        const iconBox = btn.querySelector('.copy-icon-box');
        if (label) label.textContent = '<?= addslashes(__('copied') ?? 'Copied!') ?>';
        if (iconBox) iconBox.innerHTML = '<i class="bi bi-check2 text-success" style="font-size:0.85rem;"></i>';
        setTimeout(function() {
            if (label) label.textContent = '<?= addslashes(__('copy_link') ?? 'Copy Link') ?>';
            if (iconBox) iconBox.innerHTML = '<i class="bi bi-link-45deg" style="font-size:0.85rem;"></i>';
        }, 2000);
    }).catch(function() {
        prompt('Copy URL:', urlToCopy);
    });
}
</script>
