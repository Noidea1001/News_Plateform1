<?php
/**
 * Component: Worldwide "All Sections" Mega-Menu Drawer
 * news-platform / templates / components / sections-mega-menu.php
 * Inspired by The New York Times, The Guardian, and BBC News navigation architecture.
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
$megaDb = \App\Core\Database::getInstance();
$megaCategories = $megaDb->fetchAll("SELECT * FROM categories ORDER BY name ASC");
$currentLang = $_SESSION['lang'] ?? 'en';
$megaReader = \App\Core\Auth::reader();
$megaStaff = \App\Core\Auth::check() ? \App\Core\Auth::user() : null;
$megaUser = $megaReader ?: $megaStaff;
?>

<div class="offcanvas offcanvas-start mega-sections-drawer" tabindex="-1" id="sectionsMegaMenu" aria-labelledby="sectionsMegaMenuLabel">
    <div class="offcanvas-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2.5">
            <span class="brand-logo-badge" style="width:34px; height:34px; font-size:0.9rem; border-radius:3px;">NP</span>
            <div>
                <h5 class="offcanvas-title fw-bold text-dark m-0" id="sectionsMegaMenuLabel" style="font-size:1.05rem; letter-spacing:-0.01em;">
                    <?= __('sections_drawer_title') ?>
                </h5>
                <span class="text-muted text-3xs text-uppercase fw-semibold tracking-wider">
                    <?= __('worldwide_news') ?>
                </span>
            </div>
        </div>
        <button type="button" class="btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0">
        <!-- Quick Search Prompt in Drawer -->
        <div class="p-3 bg-light border-bottom drawer-search-box">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control border-start-0 ps-1" id="megaDrawerSearchInput" 
                    placeholder="<?= __('search_placeholder') ?>" 
                    onkeydown="if(event.key==='Enter'){ window.location.href='<?= url('index.php') ?>?q=' + encodeURIComponent(this.value); }">
                <button class="btn btn-danger btn-sm text-xs px-2.5" type="button" 
                    onclick="const val = document.getElementById('megaDrawerSearchInput').value; if(val){ window.location.href='<?= url('index.php') ?>?q=' + encodeURIComponent(val); }">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>

        <div class="p-4">
            <!-- 1. Editorial Blueprints & Desk Layouts -->
            <div class="mb-4">
                <div class="drawer-section-heading">
                    <i class="bi bi-layers-fill text-danger me-1.5"></i>
                    <span><?= __('layout_blueprints_title') ?></span>
                </div>
                <div class="row g-2 mt-1">
                    <div class="col-6">
                        <a href="<?= url('index.php?format=investigative') ?>" class="drawer-format-card d-flex align-items-center gap-2 p-2.5 rounded-2 text-decoration-none">
                            <span class="badge rounded-circle p-2 bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-shield-check"></i>
                            </span>
                            <div class="min-w-0">
                                <div class="fw-bold text-dark text-xs text-truncate"><?= __('tmpl_investigative') ?></div>
                                <div class="text-muted text-3xs"><?= __('footer_tmpl_1') ?></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= url('index.php?format=opinion') ?>" class="drawer-format-card d-flex align-items-center gap-2 p-2.5 rounded-2 text-decoration-none">
                            <span class="badge rounded-circle p-2 bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-chat-square-quote"></i>
                            </span>
                            <div class="min-w-0">
                                <div class="fw-bold text-dark text-xs text-truncate"><?= __('tmpl_opinion') ?></div>
                                <div class="text-muted text-3xs"><?= __('footer_tmpl_3') ?></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Primary News Desks / Categories Grid -->
            <div class="mb-4">
                <div class="drawer-section-heading">
                    <i class="bi bi-grid-fill text-danger me-1.5"></i>
                    <span><?= __('explore_topics') ?></span>
                </div>
                <div class="row row-cols-2 g-2 mt-1">
                    <div class="col">
                        <a href="<?= url('index.php') ?>" class="drawer-category-pill <?= empty($activeCategoryId) ? 'active' : '' ?>">
                            <i class="bi bi-newspaper"></i>
                            <span><?= __('all_stories') ?></span>
                        </a>
                    </div>
                    <?php foreach ($megaCategories as $mCat) { 
                        $isActive = (isset($activeCategoryId) && (int)$activeCategoryId === (int)$mCat['id']);
                    ?>
                        <div class="col">
                            <a href="<?= url('index.php?category=' . $mCat['id']) ?>" class="drawer-category-pill <?= $isActive ? 'active' : '' ?>">
                                <i class="bi bi-chevron-right text-muted" style="font-size:0.7rem;"></i>
                                <span class="text-truncate"><?= e(cat_name($mCat['name'])) ?></span>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <!-- 3. Trending Topics & Live Tags -->
            <div class="mb-4">
                <div class="drawer-section-heading">
                    <i class="bi bi-fire text-danger me-1.5"></i>
                    <span><?= __('trending_topics') ?></span>
                </div>
                <div class="d-flex flex-wrap gap-1.5 mt-2">
                    <a href="<?= url('index.php?q=AI') ?>" class="drawer-tag-chip">#ArtificialIntelligence</a>
                    <a href="<?= url('index.php?q=Economy') ?>" class="drawer-tag-chip">#GlobalMarkets</a>
                    <a href="<?= url('index.php?q=Climate') ?>" class="drawer-tag-chip">#ClimateSummit</a>
                    <a href="<?= url('index.php?q=Tech') ?>" class="drawer-tag-chip">#DeepTech</a>
                    <a href="<?= url('index.php?q=Trade') ?>" class="drawer-tag-chip">#GlobalTrade</a>
                    <a href="<?= url('index.php?q=Diplomacy') ?>" class="drawer-tag-chip">#WorldDiplomacy</a>
                </div>
            </div>

            <!-- 4. Global Financial Snapshot Mini-Pills -->
            <div class="mb-4 p-3 rounded-2 bg-light border drawer-markets-box">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="text-3xs text-uppercase fw-bold text-muted d-flex align-items-center gap-1">
                        <i class="bi bi-graph-up-arrow text-success"></i>
                        <span><?= __('markets_overview') ?></span>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success text-3xs px-1.5 py-0.5">Live Indicative</span>
                </div>
                <div class="row g-2 text-center" style="font-size:0.75rem;">
                    <div class="col-4 p-1.5 bg-white rounded border">
                        <div class="text-3xs text-muted fw-bold">S&P 500</div>
                        <div class="fw-bold text-success text-2xs">▲ +0.48%</div>
                    </div>
                    <div class="col-4 p-1.5 bg-white rounded border">
                        <div class="text-3xs text-muted fw-bold">NASDAQ</div>
                        <div class="fw-bold text-success text-2xs">▲ +0.62%</div>
                    </div>
                    <div class="col-4 p-1.5 bg-white rounded border">
                        <div class="text-3xs text-muted fw-bold">GOLD</div>
                        <div class="fw-bold text-success text-2xs">$2,745</div>
                    </div>
                </div>
            </div>

            <!-- 5. Shortcuts & Platform Architecture -->
            <div class="border-top pt-3">
                <div class="d-flex flex-column gap-2 text-xs">
                    <a href="<?= url('index.php?breaking=1') ?>" class="text-danger text-decoration-none fw-semibold d-flex align-items-center gap-2">
                        <i class="bi bi-lightning-charge-fill"></i>
                        <span><?= __('filter_breaking_only') ?></span>
                    </a>
                    <a href="<?= url('admin/dashboard.php') ?>" class="text-secondary text-decoration-none d-flex align-items-center gap-2">
                        <i class="bi bi-shield-lock"></i>
                        <span>CMS Admin Control Panel</span>
                    </a>
                    <a href="<?= url('manifest.json') ?>" target="_blank" class="text-secondary text-decoration-none d-flex align-items-center gap-2">
                        <i class="bi bi-app-indicator"></i>
                        <span>PWA Web Application Manifest</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Drawer Footer -->
    <div class="offcanvas-footer p-3 border-top bg-light d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm px-2.5 py-1 text-xs d-flex align-items-center gap-1.5 global-theme-toggle-btn">
                <i class="bi bi-moon-stars"></i>
                <span class="theme-text-label"><?= __('theme_dark') ?></span>
            </button>
        </div>
        <div class="text-muted text-3xs">
            &copy; <?= date('Y') ?> <?= __('site_title') ?>
        </div>
    </div>
</div>
