<?php
/**
 * Component: Clean Editorial "All Sections" Drawer
 * news-platform / templates / components / sections-mega-menu.php
 * Clean, pure-white editorial directory inspired by BBC News and The New York Times.
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
$megaDb = \App\Core\Database::getInstance();
$megaCategories = $megaDb->fetchAll("SELECT * FROM categories ORDER BY name ASC");
$currentLang = $_SESSION['lang'] ?? 'en';
$megaReader = \App\Core\Auth::reader();
$megaStaff = \App\Core\Auth::check() ? \App\Core\Auth::user() : null;
$megaUser = $megaReader ?: $megaStaff;
?>

<div class="offcanvas offcanvas-start mega-sections-drawer" tabindex="-1" id="sectionsMegaMenu" aria-labelledby="sectionsMegaMenuLabel" style="width: 320px; max-width: 85vw; background:#ffffff; border-right:1px solid #e5e7eb; z-index:1075;">
    <!-- Drawer Header -->
    <div class="offcanvas-header border-bottom py-3 px-3.5 d-flex align-items-center justify-content-between" style="background:#ffffff; border-left: 4px solid #c8102e;">
        <div class="d-flex align-items-center gap-2">
            <span class="brand-logo-badge" style="width:32px; height:32px; font-size:0.85rem; border-radius:2px;">NP</span>
            <div>
                <h5 class="offcanvas-title fw-bold text-dark m-0" id="sectionsMegaMenuLabel" style="font-size:0.95rem; line-height:1.2;">
                    <?= __('sections_drawer_title') ?>
                </h5>
                <span class="text-muted" style="font-size:0.65rem; text-transform:uppercase; letter-spacing:0.06em; font-weight:600;">
                    <?= __('site_title') ?>
                </span>
            </div>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Drawer Body -->
    <div class="offcanvas-body p-0 d-flex flex-column" style="background:#ffffff;">
        <!-- Quick Search Form -->
        <div class="p-3 border-bottom" style="background:#fafafa;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted ps-2.5" style="border-color:#e5e7eb;">
                    <i class="bi bi-search" style="font-size:0.8rem;"></i>
                </span>
                <input type="text" class="form-control border-start-0 ps-1" id="megaDrawerSearchInput" 
                    placeholder="<?= __('search_placeholder') ?>" style="border-color:#e5e7eb; font-size:0.8rem;"
                    onkeydown="if(event.key==='Enter'){ window.location.href='<?= url('index.php') ?>?q=' + encodeURIComponent(this.value); }">
                <button class="btn btn-danger btn-sm text-xs px-2.5" type="button" 
                    onclick="const val = document.getElementById('megaDrawerSearchInput').value; if(val){ window.location.href='<?= url('index.php') ?>?q=' + encodeURIComponent(val); }">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>

        <div class="p-3.5 flex-grow-1 overflow-y-auto">
            <!-- 1. Categories Section -->
            <div class="mb-4">
                <div class="text-uppercase fw-bold text-muted mb-2 px-1" style="font-size:0.68rem; letter-spacing:0.08em;">
                    <?= __('explore_topics') ?>
                </div>
                <div class="d-flex flex-column gap-1">
                    <a href="<?= url('index.php') ?>" class="sections-clean-link <?= empty($activeCategoryId) ? 'active' : '' ?>">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-newspaper text-danger" style="font-size:0.9rem;"></i>
                            <span><?= __('all_stories') ?></span>
                        </span>
                        <i class="bi bi-chevron-right text-muted" style="font-size:0.7rem;"></i>
                    </a>
                    <?php foreach ($megaCategories as $mCat) { 
                        $isActive = (isset($activeCategoryId) && (int)$activeCategoryId === (int)$mCat['id']);
                    ?>
                        <a href="<?= url('index.php?category=' . $mCat['id']) ?>" class="sections-clean-link <?= $isActive ? 'active' : '' ?>">
                            <span><?= e(cat_name($mCat['name'])) ?></span>
                            <i class="bi bi-chevron-right text-muted" style="font-size:0.7rem;"></i>
                        </a>
                    <?php } ?>
                </div>
            </div>

            <!-- 2. Special Formats Section -->
            <div class="mb-4 pt-2 border-top">
                <div class="text-uppercase fw-bold text-muted mb-2 px-1" style="font-size:0.68rem; letter-spacing:0.08em;">
                    <?= __('layout_blueprints_title') ?>
                </div>
                <div class="d-flex flex-column gap-1">
                    <a href="<?= url('index.php?format=investigative') ?>" class="sections-clean-link">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-danger" style="font-size:0.9rem;"></i>
                            <span><?= __('tmpl_investigative') ?></span>
                        </span>
                        <span class="badge bg-danger bg-opacity-10 text-danger" style="font-size:0.62rem;"><?= __('footer_tmpl_1') ?></span>
                    </a>
                    <a href="<?= url('index.php?format=opinion') ?>" class="sections-clean-link">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-chat-square-quote text-primary" style="font-size:0.9rem;"></i>
                            <span><?= __('tmpl_opinion') ?></span>
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:0.62rem;"><?= __('footer_tmpl_3') ?></span>
                    </a>
                </div>
            </div>

            <!-- 3. Quick Editorial Links -->
            <div class="pt-2 border-top">
                <div class="text-uppercase fw-bold text-muted mb-2 px-1" style="font-size:0.68rem; letter-spacing:0.08em;">
                    <?= __('sections_quick_links') ?>
                </div>
                <div class="d-flex flex-column gap-1">
                    <a href="<?= url('index.php?breaking=1') ?>" class="sections-clean-link text-danger">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-lightning-fill"></i>
                            <span class="fw-bold"><?= __('filter_breaking_only') ?></span>
                        </span>
                        <i class="bi bi-arrow-right" style="font-size:0.75rem;"></i>
                    </a>
                    <a href="#" class="sections-clean-link" data-bs-dismiss="offcanvas" data-bs-toggle="offcanvas" data-bs-target="#savedArticlesModal">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-bookmark text-secondary"></i>
                            <span><?= __('saved_reading_list') ?></span>
                        </span>
                        <i class="bi bi-arrow-right text-muted" style="font-size:0.75rem;"></i>
                    </a>
                    <a href="<?= url('admin/dashboard.php') ?>" class="sections-clean-link">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-shield-lock text-secondary"></i>
                            <span><?= __('cma_link') ?></span>
                        </span>
                        <i class="bi bi-box-arrow-up-right text-muted" style="font-size:0.7rem;"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Drawer Footer -->
        <div class="p-3 border-top d-flex align-items-center justify-content-between" style="background:#fafafa; font-size:0.72rem; color:#6b7280;">
            <span>&copy; <?= date('Y') ?> <?= __('site_title') ?></span>
            <span class="badge bg-light text-secondary border" style="font-size:0.65rem;"><?= __('cda_badge') ?></span>
        </div>
    </div>
</div>
