<?php
/**
 * Modern Clean Editorial Action & Social Share Strip
 * news-platform / templates / components / share-buttons.php
 *
 * Minimalist, seamless toolbar styled like top-tier digital news publications.
 */

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
$currentUrl = ($isHttps ? "https://" : "http://") . $host . $uri;
$shareTitle = article_title($article['title'] ?? 'NewsPlatform Story');
?>

<!-- ── Modern Clean Editorial Action & Social Share Strip ────── -->
<div class="editorial-actions-bar py-2 my-2 border-top border-bottom d-flex align-items-center justify-content-between gap-3 flex-wrap" style="background: transparent;">
    
    <!-- Left: Fast News Archive Link -->
    <div class="d-flex align-items-center gap-2">
        <a href="<?= url('archive.php?breaking=1') ?>" class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 text-2xs fw-bold text-decoration-none d-inline-flex align-items-center gap-1.5" title="<?= __('filter_breaking_only') ?>">
            <i class="bi bi-lightning-fill text-danger" style="font-size: 0.75rem;"></i>
            <span><?= __('filter_breaking_only') ?></span>
            <i class="bi bi-arrow-right" style="font-size: 0.7rem;"></i>
        </a>
    </div>

    <!-- Right: Bookmark & Minimal Social Share Icons -->
    <div class="d-flex align-items-center gap-2 ms-auto">
        
        <!-- Bookmark / Save Button -->
        <?php if (isset($article) && isset($artData)) { ?>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 bookmark-toggle-btn d-inline-flex align-items-center gap-1.5 shadow-none text-xs fw-semibold"
                data-id="<?= (int)$article['id'] ?>" data-article='<?= $artData ?>' title="<?= __('save_for_later') ?? 'Save' ?>" style="height:32px;">
                <i class="bi bi-bookmark text-danger"></i>
                <span class="bookmark-label"><?= __('save_for_later') ?? 'Save' ?></span>
            </button>
        <?php } ?>

        <span class="vr mx-0.5 text-secondary opacity-25" style="height: 18px;"></span>

        <!-- Minimal Circular Social Icons -->
        <div class="d-inline-flex align-items-center gap-1.5">
            <!-- Telegram Share -->
            <a href="https://t.me/share/url?url=<?= urlencode($currentUrl) ?>&text=<?= urlencode($shareTitle) ?>" target="_blank" rel="noopener"
                class="btn-social-icon social-icon-tg" title="Share on Telegram">
                <i class="bi bi-telegram"></i>
            </a>

            <!-- Facebook Share -->
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>" target="_blank" rel="noopener"
                class="btn-social-icon social-icon-fb" title="Share on Facebook">
                <i class="bi bi-facebook"></i>
            </a>

            <!-- X / Twitter Share -->
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($currentUrl) ?>&text=<?= urlencode($shareTitle) ?>" target="_blank" rel="noopener"
                class="btn-social-icon social-icon-x" title="Share on X">
                <i class="bi bi-twitter-x"></i>
            </a>

            <!-- Copy Link Button -->
            <button type="button" class="btn-social-icon copy-link-btn"
                onclick="copyArticleLink(this)" title="<?= __('copy_link') ?>">
                <i class="bi bi-link-45deg"></i>
            </button>
        </div>
    </div>
</div>

<style>
.editorial-actions-bar {
    border-color: #e5e7eb !important;
}
.btn-social-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #475569;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    font-size: 0.85rem;
    text-decoration: none !important;
    transition: all 0.15s ease;
}
.btn-social-icon:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}
.social-icon-tg:hover {
    color: #0088cc !important;
    background: #e0f2fe !important;
    border-color: #7dd3fc !important;
}
.social-icon-fb:hover {
    color: #1877f2 !important;
    background: #dbeafe !important;
    border-color: #93c5fd !important;
}
.social-icon-x:hover {
    color: #0f172a !important;
    background: #f1f5f9 !important;
    border-color: #94a3b8 !important;
}
.copy-link-btn:hover {
    color: #c8102e !important;
    background: #fef2f2 !important;
    border-color: #fca5a5 !important;
}
</style>

<script>
if (typeof window.copyArticleLink !== 'function') {
    window.copyArticleLink = function(btn) {
        const urlToCopy = window.location.href;
        navigator.clipboard.writeText(urlToCopy).then(function() {
            const icon = btn.querySelector('i');
            if (icon) {
                const prevClass = icon.className;
                icon.className = 'bi bi-check2 text-success';
                btn.style.borderColor = '#22c55e';
                setTimeout(function() {
                    icon.className = prevClass;
                    btn.style.borderColor = '';
                }, 2000);
            }
        }).catch(function() {
            prompt('Copy URL:', urlToCopy);
        });
    };
}
</script>
