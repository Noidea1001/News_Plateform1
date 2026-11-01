<?php
/**
 * Component: Mobile App-Style Bottom Navigation Bar
 * news-platform / templates / components / mobile-bottom-nav.php
 * Native mobile app-style floating bottom bar for fast finger navigation.
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
$mobileBottomReader = \App\Core\Auth::reader();
$mobileBottomStaff = \App\Core\Auth::check() ? \App\Core\Auth::user() : null;
$mobileBottomUser = $mobileBottomReader ?: $mobileBottomStaff;
?>

<nav class="mobile-bottom-app-bar d-lg-none" aria-label="Mobile Navigation">
    <div class="mobile-bottom-bar-inner">
        <!-- Home -->
        <a href="<?= url('index.php') ?>" class="mobile-tab-btn <?= (empty($_GET) || (isset($_GET['page']) && count($_GET) === 1)) ? 'active' : '' ?>">
            <i class="bi bi-house-door-fill"></i>
            <span><?= __('mobile_nav_home') ?></span>
        </a>

        <!-- Sections (Mega Drawer) -->
        <button type="button" class="mobile-tab-btn" data-bs-toggle="offcanvas" data-bs-target="#sectionsMegaMenu" aria-controls="sectionsMegaMenu">
            <i class="bi bi-grid-fill"></i>
            <span><?= __('mobile_nav_sections') ?></span>
        </button>

        <!-- Search Bar Trigger -->
        <button type="button" class="mobile-tab-btn" data-bs-toggle="collapse" data-bs-target="#mobileSearchCollapse" aria-expanded="false" aria-controls="mobileSearchCollapse" onclick="setTimeout(()=>document.getElementById('mobileSearchInput')?.focus(), 150)">
            <i class="bi bi-search"></i>
            <span><?= __('mobile_nav_search') ?></span>
        </button>

        <!-- Saved Reading List -->
        <button type="button" class="mobile-tab-btn position-relative" data-bs-toggle="offcanvas" data-bs-target="#savedArticlesModal" aria-controls="savedArticlesModal">
            <i class="bi bi-bookmark-fill"></i>
            <span><?= __('mobile_nav_saved') ?></span>
            <span id="bottomNavSavedBadge" class="position-absolute badge rounded-circle bg-danger p-0 d-none align-items-center justify-content-center" 
                style="top: 4px; right: 22%; font-size: 0.55rem; width: 14px; height: 14px; pointer-events: none;"></span>
        </button>

        <!-- Account / Notifications -->
        <?php if ($mobileBottomUser): ?>
            <button type="button" class="mobile-tab-btn position-relative" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-expanded="false" aria-controls="mainNavbar">
                <i class="bi bi-person-circle"></i>
                <span><?= __('mobile_nav_account') ?></span>
            </button>
        <?php else: ?>
            <button type="button" class="mobile-tab-btn" data-bs-toggle="modal" data-bs-target="#readerAuthModal" data-auth-tab="login">
                <i class="bi bi-person"></i>
                <span><?= __('sign_in') ?></span>
            </button>
        <?php endif; ?>
    </div>
</nav>

<script>
    // Sync Bottom Bar Saved Badge
    (function() {
        function updateBottomSavedBadge() {
            try {
                const raw = localStorage.getItem('np_saved_articles_v1') || localStorage.getItem('np_saved_articles') || '[]';
                const list = JSON.parse(raw);
                const count = Array.isArray(list) ? list.length : 0;
                const badge = document.getElementById('bottomNavSavedBadge');
                if (badge) {
                    if (count > 0) {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.classList.remove('d-none');
                        badge.classList.add('d-flex');
                    } else {
                        badge.classList.remove('d-flex');
                        badge.classList.add('d-none');
                    }
                }
            } catch(e) {}
        }
        document.addEventListener('DOMContentLoaded', updateBottomSavedBadge);
        window.addEventListener('storage', updateBottomSavedBadge);
        window.addEventListener('np:bookmarks_updated', updateBottomSavedBadge);
    })();
</script>
