<?php
/**
 * Public Reader Layout Header — CNA-Style Design
 * news-platform / templates / layouts / header-public.php
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
require_once __DIR__ . '/../../languages/common.php';
$currentLang = $_SESSION['lang'] ?? 'en';
?>
<!DOCTYPE html>
<html lang="<?= e($currentLang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? __('site_title') . ' | ' . __('site_tagline')) ?></title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Plus Jakarta Sans + Inter + Kantumruy Pro + Noto Sans Khmer -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300..900;1,14..32,300..900&family=Kantumruy+Pro:ital,wght@0,400..700;1,400..700&family=Noto+Sans+Khmer:wght@400;500;600;700&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>

<body class="d-flex flex-column min-vh-100">
    <div id="readingProgressBar"></div>

    <!-- ── CNA-Style Top Utility Bar ──────────────────────────────────────── -->
    <div class="top-utility-header py-1">
        <div class="container-fluid px-3 px-md-4">
            <div class="d-flex justify-content-between align-items-center" style="min-height:30px;">
                <!-- Left: Date -->
                <div class="d-flex align-items-center gap-3" style="font-size:0.72rem; color:rgba(255,255,255,0.6);">
                    <span><?= \App\Core\TemplateEngine::formatDate(date('Y-m-d H:i:s'), 'l, d F Y') ?></span>
                    <span class="opacity-25 d-none d-sm-inline">|</span>
                    <span class="d-none d-md-inline" style="color:rgba(255,255,255,0.45);"><?= __('cda_badge') ?></span>
                </div>

                <!-- Right: Language -->
                <div class="d-flex align-items-center gap-2">
                    <div class="dropdown">
                        <button class="btn btn-link text-white p-0 text-decoration-none dropdown-toggle"
                            style="font-size:0.72rem; font-weight:600; opacity:0.75;" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-globe2 me-1" style="font-size:0.7rem;"></i>
                            <?= ($currentLang === 'kh' || $currentLang === 'km') ? 'ខ្មែរ' : 'EN' ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width:140px;">
                            <li>
                                <a class="dropdown-item <?= $currentLang === 'en' ? 'active' : '' ?>"
                                    href="<?= lang_url('en') ?>">
                                    English (EN)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?= ($currentLang === 'kh' || $currentLang === 'km') ? 'active' : '' ?>"
                                    href="<?= lang_url('kh') ?>">
                                    ភាសាខ្មែរ (KH)
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── CNA-Style Main Navbar ──────────────────────────────────────────── -->
    <nav class="navbar navbar-expand-lg navbar-main sticky-top py-0" style="min-height: 60px;">
        <div class="container-fluid px-3 px-md-4 h-100">

            <!-- Brand — CNA style: red badge + bold name -->
            <a class="navbar-brand d-flex align-items-center gap-2 py-0 text-decoration-none"
                href="<?= url('index.php') ?>">
                <span class="brand-logo-badge"
                    style="width:44px; height:44px; font-size:1.05rem; border-radius:2px;">NP</span>
                <div class="d-none d-sm-block">
                    <div class="brand-title-text" style="font-size:1.1rem; line-height:1.2;"><?= __('site_title') ?>
                    </div>
                    <div
                        style="font-size:0.6rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.1em; line-height:1;">
                        <?= __('site_tagline') ?>
                    </div>
                </div>
            </a>

            <!-- Red divider line (CNA signature) -->
            <div class="d-none d-lg-block mx-3" style="width:1px; height:32px; background:#e5e7eb; flex-shrink:0;">
            </div>

            <!-- Mobile Toggler -->
            <button class="navbar-toggler border-0 shadow-none p-1 ms-auto me-2" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <!-- Inline Search — CNA style with Real-Time Live Filter & Dropdown -->
                <form class="d-flex my-2 my-lg-0 mx-lg-3 position-relative" style="max-width:340px; width:100%;"
                    action="<?= url('index.php') ?>" method="GET" id="headerSearchForm">
                    <div class="input-group input-group-sm w-100 align-items-center"
                        style="background:#f3f4f6; border:1px solid #e5e7eb; border-radius:2px; padding-right:4px;">
                        <span class="input-group-text border-0 bg-transparent pe-1 ps-2.5">
                            <i class="bi bi-search text-muted" style="font-size:0.85rem;"></i>
                        </span>
                        <input class="form-control border-0 bg-transparent shadow-none text-xs pe-1"
                            id="publicSearchInput" type="text" name="q" autocomplete="off"
                            placeholder="<?= __('search_placeholder') ?>" value="<?= e($_GET['q'] ?? '') ?>">
                        <span id="publicSearchClear"
                            class="bootstrap-search-clear text-muted p-1 <?= empty($_GET['q']) ? 'd-none' : '' ?>"
                            title="<?= __('clear_search') ?>">
                            <i class="bi bi-x-circle-fill" style="font-size:0.85rem; color:#9ca3af;"></i>
                        </span>
                    </div>
                    <!-- Live Search Results Dropdown -->
                    <div id="liveSearchDropdown" class="card border-0 shadow-lg position-absolute w-100 mt-1"
                        style="display:none; top:100%; left:0; z-index:1060; max-height:420px; overflow-y:auto; border-radius:2px;">
                        <div class="list-group list-group-flush" id="liveSearchList"></div>
                    </div>
                </form>

                <!-- Subscribe & Saved Reading List CTAs -->
                <div class="d-flex align-items-center gap-3 ms-lg-auto pe-1">
                    <button type="button"
                        class="btn btn-outline-secondary btn-sm p-1.5 d-flex align-items-center justify-content-center position-relative border-0 bg-transparent text-muted me-2"
                        style="width: 36px; height: 36px; border-radius: 2px;"
                        data-bs-toggle="offcanvas" data-bs-target="#savedArticlesModal"
                        title="<?= __('saved_reading_list') ?? 'Saved Reading List' ?>">
                        <i class="bi bi-bookmark-fill text-danger" style="font-size: 1.2rem;"></i>
                        <span id="savedCountBadge" class="position-absolute badge rounded-circle bg-danger p-0 d-flex align-items-center justify-content-center" style="top:-2px; right:-6px; font-size: 0.6rem; width: 18px; height: 18px; display: none;">0</span>
                    </button>

                    <button type="button"
                        class="btn btn-danger btn-sm px-3 py-2 d-flex align-items-center gap-2 fw-bold"
                        style="font-size:0.8rem; border-radius:2px; text-transform:uppercase; letter-spacing:0.04em;"
                        data-bs-toggle="modal" data-bs-target="#subscribeModal">
                        <i class="bi bi-bell-fill" style="font-size:0.75rem;"></i>
                        <span class="d-none d-sm-inline"><?= __('get_feed_cta') ?></span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function setupLiveSearch(inputId, clearId, dropdownId, listId) {
                const searchInput = document.getElementById(inputId);
                const clearBtn = document.getElementById(clearId);
                const dropdown = document.getElementById(dropdownId);
                const searchList = document.getElementById(listId);
                let debounceTimer = null;

                if (!searchInput) return;

                function updateClearBtn() {
                    if (!clearBtn) return;
                    if (searchInput.value.trim().length > 0) {
                        clearBtn.classList.remove('d-none');
                    } else {
                        clearBtn.classList.add('d-none');
                    }
                }

                if (clearBtn) {
                    clearBtn.addEventListener('click', function () {
                        searchInput.value = '';
                        updateClearBtn();
                        searchInput.dispatchEvent(new Event('input'));
                        if (dropdown) dropdown.style.display = 'none';
                    });
                }

                searchInput.addEventListener('input', function () {
                    updateClearBtn();
                    const query = this.value.trim();

                    // 1. Real-time DOM filter for articles rendered on current page
                    const feedRows = document.querySelectorAll('.cna-feed-row, .secondary-grid-card');
                    const heroCard = document.querySelector('.lead-article-card') || document.querySelector('.cna-hero-card');

                    let visibleCount = 0;

                    if (feedRows.length > 0 || heroCard) {
                        const lowerQ = query.toLowerCase();
                        feedRows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            if (!query || text.includes(lowerQ)) {
                                row.style.display = '';
                                visibleCount++;
                            } else {
                                row.style.display = 'none';
                            }
                        });

                        if (heroCard) {
                            const heroText = heroCard.textContent.toLowerCase();
                            if (!query || heroText.includes(lowerQ)) {
                                heroCard.style.display = '';
                                visibleCount++;
                            } else {
                                heroCard.style.display = 'none';
                            }
                        }

                        // Dynamic Live "Not Found" Message Toggle
                        let liveNoResultsMsg = document.getElementById('liveNoResultsMsg');
                        if (!liveNoResultsMsg) {
                            const container = document.querySelector('.cna-news-list') || document.querySelector('.homepage-body');
                            if (container) {
                                liveNoResultsMsg = document.createElement('div');
                                liveNoResultsMsg.id = 'liveNoResultsMsg';
                                liveNoResultsMsg.className = 'text-center py-5 bg-white border my-3';
                                liveNoResultsMsg.style.borderRadius = '2px';
                                liveNoResultsMsg.style.display = 'none';
                                liveNoResultsMsg.innerHTML = `
                                    <div class="mb-3" style="font-size:3rem; color:#d1d5db;"><i class="bi bi-search"></i></div>
                                    <h4 class="fw-bold mb-2" style="color:#0f172a;"><?= addslashes(__('no_articles_found')) ?></h4>
                                    <p class="text-muted mb-0"><?= addslashes(__('no_articles_desc')) ?></p>
                                `;
                                container.parentNode.insertBefore(liveNoResultsMsg, container);
                            }
                        }

                        if (liveNoResultsMsg) {
                            if (query && visibleCount === 0) {
                                liveNoResultsMsg.style.display = 'block';
                            } else {
                                liveNoResultsMsg.style.display = 'none';
                            }
                        }
                    }

                    // 2. Real-time AJAX search preview dropdown
                    clearTimeout(debounceTimer);
                    if (query.length < 2) {
                        if (dropdown) dropdown.style.display = 'none';
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        fetch('<?= url("search_api.php") ?>?q=' + encodeURIComponent(query))
                            .then(res => res.json())
                            .then(data => {
                                if (data.success && data.articles && data.articles.length > 0) {
                                    let html = '';
                                    data.articles.forEach(art => {
                                        html += `
                                    <a href="${art.url}" class="list-group-item list-group-item-action p-2.5 d-flex gap-2.5 align-items-start border-bottom text-decoration-none">
                                        ${art.featured_image ? `<img src="${art.featured_image}" style="width:52px;height:38px;object-fit:cover;border-radius:2px;flex-shrink:0;">` : ''}
                                        <div class="min-w-0 flex-grow-1">
                                            <div style="font-size:0.62rem;font-weight:700;color:#c8102e;text-transform:uppercase;letter-spacing:0.04em;">${art.category_display}</div>
                                            <div style="font-size:0.83rem;font-weight:700;color:#0f172a;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">${art.title}</div>
                                        </div>
                                    </a>
                                `;
                                    });
                                    searchList.innerHTML = html;
                                    dropdown.style.display = 'block';
                                } else {
                                    searchList.innerHTML = '<div class="p-3 text-center text-muted small"><?= __('no_articles_found') ?></div>';
                                    dropdown.style.display = 'block';
                                }
                            })
                            .catch(() => {
                                if (dropdown) dropdown.style.display = 'none';
                            });
                    }, 150);
                });

                document.addEventListener('click', function (e) {
                    if (dropdown && searchInput && !searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.style.display = 'none';
                    }
                });
            }

            // Initialize real-time search on both Navbar and Hero Search bars
            setupLiveSearch('publicSearchInput', 'publicSearchClear', 'liveSearchDropdown', 'liveSearchList');
            setupLiveSearch('heroSearchInput', 'heroSearchClear', 'heroLiveSearchDropdown', 'heroLiveSearchList');
        });
    </script>

    <!-- ── CNA-Style Category Navigation — Tab Underline Style ─────────────── -->
    <div class="nav-category-toolbar" style="background:#fff; border-bottom:1px solid #e5e7eb;">
        <div class="container-fluid px-3 px-md-4">
            <div class="nav-category-scroll">
                <a href="<?= url('index.php') ?>"
                    class="nav-category-link <?= empty($activeCategoryId) ? 'active' : '' ?>">
                    <?= __('all_stories') ?>
                </a>
                <?php
                $catNavDb = \App\Core\Database::getInstance();
                $catNavList = $catNavDb->fetchAll("SELECT * FROM categories ORDER BY name ASC");
                foreach ($catNavList as $cItem) {
                    ?>
                    <a href="<?= url('index.php?category=' . $cItem['id']) ?>"
                        class="nav-category-link <?= (isset($activeCategoryId) && (int) $activeCategoryId === (int) $cItem['id']) ? 'active' : '' ?>">
                        <?= e(cat_name($cItem['name'])) ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- ── Breaking News Ticker ───────────────────────────────────────────── -->
    <?php if (!empty($breakingNews)) { ?>
        <div class="breaking-ticker-bar py-2">
            <div class="container-fluid px-3 px-md-4 d-flex align-items-center gap-3">
                <span class="breaking-badge badge text-uppercase flex-shrink-0">
                    <i class="bi bi-lightning-fill me-1"></i><?= __('breaking') ?>
                </span>
                <div class="ticker-wrapper flex-grow-1 overflow-hidden">
                    <div class="ticker-track">
                        <!-- Set 1 -->
                        <div class="ticker-content d-inline-flex align-items-center">
                            <?php foreach ($breakingNews as $bItem) { ?>
                                <a href="<?= url('article.php?slug=' . urlencode($bItem['slug'])) ?>"
                                    class="text-white text-decoration-none fw-semibold me-5 text-nowrap ticker-link">
                                    <span
                                        style="color:#f87171; font-weight:700;">[<?= e(cat_name($bItem['category_name'])) ?>]</span>
                                    <?= e(article_title($bItem['title'])) ?>
                                </a>
                            <?php } ?>
                        </div>
                        <!-- Set 2 (Duplicated for seamless infinite loop) -->
                        <div class="ticker-content d-inline-flex align-items-center" aria-hidden="true">
                            <?php foreach ($breakingNews as $bItem) { ?>
                                <a href="<?= url('article.php?slug=' . urlencode($bItem['slug'])) ?>"
                                    class="text-white text-decoration-none fw-semibold me-5 text-nowrap ticker-link">
                                    <span
                                        style="color:#f87171; font-weight:700;">[<?= e(cat_name($bItem['category_name'])) ?>]</span>
                                    <?= e(article_title($bItem['title'])) ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <main class="flex-grow-1" style="background:#f9f9f9;">