<?php
/**
 * Public Reader Layout Header — CNA-Style Design
 * news-platform / templates / layouts / header-public.php
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
require_once __DIR__ . '/../../languages/common.php';
$currentLang = $_SESSION['lang'] ?? 'en';
$currentReader = \App\Core\Auth::reader();
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

    <!-- Progressive Web App (PWA) Manifest & Meta -->
    <link rel="manifest" href="<?= url('manifest.json') ?>">
    <meta name="theme-color" content="#c8102e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="<?= url('assets/icons/icon-192.svg') ?>">

    <style>
        .header-clean-icon-btn {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
            cursor: pointer;
            width: 36px;
            height: 36px;
            border-radius: 4px;
            color: #334155 !important;
            transition: transform 0.15s ease, opacity 0.15s ease;
        }
        .header-clean-icon-btn:hover,
        .header-clean-icon-btn:focus,
        .header-clean-icon-btn:active {
            background: transparent !important;
            box-shadow: none !important;
            outline: none !important;
            color: #c8102e !important;
            transform: scale(1.1);
        }
        .navbar-main {
            z-index: 1035 !important;
        }
        .nav-category-toolbar {
            position: relative;
            z-index: 8;
        }
        .breaking-ticker-bar {
            position: relative;
            z-index: 5 !important;
        }
        #liveSearchDropdown {
            width: 440px !important;
            max-width: 92vw !important;
            max-height: min(360px, 68vh) !important;
            overflow-y: auto !important;
            border-radius: 6px !important;
            box-shadow: 0 14px 40px rgba(0, 0, 0, 0.16) !important;
            z-index: 1060 !important;
        }
        .live-search-item {
            transition: background 0.12s ease;
        }
        .live-search-item:hover {
            background: #f8fafc !important;
        }
    </style>
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

                <!-- Right: PWA Install & Language -->
                <div class="d-flex align-items-center gap-2.5">
                    <button type="button" id="pwaInstallBtn"
                        class="btn btn-outline-light btn-sm py-0 px-2 d-none align-items-center gap-1"
                        style="font-size:0.7rem; border-radius:2px; border-color:rgba(255,255,255,0.3); opacity:0.85;"
                        title="<?= __('pwa_install') ?>">
                        <i class="bi bi-download" style="font-size:0.68rem;"></i>
                        <span class="d-none d-sm-inline"><?= __('pwa_install') ?></span>
                    </button>

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

                <!-- Reader Controls, Notifications & CTAs -->
                <div class="d-flex align-items-center gap-2 ms-lg-auto pe-1">
                    
                    <!-- Saved Reading List Offcanvas Trigger -->
                    <button type="button"
                        class="header-clean-icon-btn d-flex align-items-center justify-content-center position-relative"
                        data-bs-toggle="offcanvas" data-bs-target="#savedArticlesModal"
                        title="<?= __('saved_reading_list') ?? 'Saved Reading List' ?>">
                        <i class="bi bi-bookmark-fill text-danger" style="font-size: 1.2rem;"></i>
                        <span id="savedCountBadge" class="position-absolute badge rounded-circle bg-danger p-0 d-flex align-items-center justify-content-center" style="top:-2px; right:-4px; font-size: 0.6rem; width: 17px; height: 17px; display: none;">0</span>
                    </button>

                    <!-- Real-Time Notification Bell Dropdown -->
                    <div class="dropdown position-relative">
                        <button type="button"
                            class="header-clean-icon-btn d-flex align-items-center justify-content-center position-relative"
                            id="notifBellBtn" data-bs-toggle="dropdown" aria-expanded="false"
                            title="<?= __('notifications_title') ?>">
                            <i class="bi bi-bell-fill text-dark" style="font-size: 1.2rem;"></i>
                            <span id="notifBadge" class="position-absolute badge rounded-circle bg-danger p-0 d-flex align-items-center justify-content-center"
                                style="top:-2px; right:-4px; font-size: 0.6rem; width: 17px; height: 17px; display: none;">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 mt-2" style="width: 340px; max-height: 420px; overflow-y: auto; border-radius: 6px; z-index: 1080;">
                            <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light">
                                <span class="fw-bold small text-dark"><i class="bi bi-bell-fill me-1 text-danger"></i> <?= __('notifications_title') ?></span>
                                <span class="badge bg-danger text-white text-2xs" id="notifCountLabel">0</span>
                            </div>
                            <div id="notifList" class="list-group list-group-flush small">
                                <div class="p-3 text-center text-muted text-xs">
                                    <span class="spinner-border spinner-border-sm me-1"></span> <?= __('loading') ?? 'Loading...' ?>
                                </div>
                            </div>
                            <div class="p-2 border-top text-center bg-light">
                                <a href="<?= url('index.php?breaking=1') ?>" class="text-xs text-danger text-decoration-none fw-semibold">
                                    <?= __('filter_breaking_only') ?> &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Reader Auth Dropdown / Buttons -->
                    <?php if ($currentReader) { ?>
                        <div class="dropdown ms-1">
                            <button class="btn btn-outline-danger btn-sm px-2.5 py-1.5 d-flex align-items-center gap-1.5 rounded-2 dropdown-toggle text-nowrap"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.78rem;">
                                <i class="bi bi-person-circle text-danger"></i>
                                <span class="fw-bold"><?= e($currentReader['name']) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="min-width: 200px;">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-bold small text-dark"><?= e($currentReader['name']) ?></div>
                                    <div class="text-muted text-2xs"><?= e($currentReader['email']) ?></div>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-3xs mt-1">
                                        <?= __('verified_reader_badge') ?>
                                    </span>
                                </li>
                                <li>
                                    <a class="dropdown-item small py-2 text-danger" href="<?= url('logout.php') ?>">
                                        <i class="bi bi-box-arrow-right me-1.5"></i> <?= __('sign_out') ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php } else { ?>
                        <div class="d-flex align-items-center gap-1.5 ms-1">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-2.5 py-1.5 fw-semibold text-xs rounded-2 text-dark text-nowrap"
                                data-bs-toggle="modal" data-bs-target="#readerAuthModal" data-auth-tab="login">
                                <?= __('sign_in') ?>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm px-2.5 py-1.5 fw-semibold text-xs rounded-2 shadow-2xs text-nowrap d-none d-sm-inline-block"
                                data-bs-toggle="modal" data-bs-target="#readerAuthModal" data-auth-tab="register">
                                <?= __('create_account') ?>
                            </button>
                        </div>
                    <?php } ?>

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

                    // Real-time AJAX search preview dropdown
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
                                        const img = art.image_url || art.featured_image || '';
                                        const imgHtml = img 
                                            ? `<div style="width:68px;height:52px;flex-shrink:0;border-radius:4px;overflow:hidden;background:#f1f5f9;"><img src="${escapeHtml(img)}" alt="${escapeHtml(art.title)}" style="width:100%;height:100%;object-fit:cover;" onerror="this.parentNode.style.display='none'"></div>`
                                            : `<div style="width:68px;height:52px;flex-shrink:0;border-radius:4px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#94a3b8;"><i class="bi bi-newspaper fs-5"></i></div>`;
                                        
                                        const timeHtml = art.time_ago ? `<span class="text-muted text-3xs"><i class="bi bi-clock me-1"></i>${escapeHtml(art.time_ago)}</span>` : '';
                                        const snippetHtml = art.summary_snippet ? `<div class="text-muted text-2xs mt-0.5" style="line-height:1.3;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;">${escapeHtml(art.summary_snippet)}</div>` : '';

                                        html += `
                                            <a href="${art.url}" class="list-group-item list-group-item-action p-2.5 d-flex gap-2.5 align-items-center border-bottom text-decoration-none live-search-item">
                                                ${imgHtml}
                                                <div class="min-w-0 flex-grow-1">
                                                    <div class="d-flex align-items-center justify-content-between gap-1 mb-0.5">
                                                        <span class="badge bg-danger bg-opacity-10 text-danger text-3xs px-1.5 py-0.5 rounded-1 fw-bold text-uppercase">${escapeHtml(art.category_display || '')}</span>
                                                        ${timeHtml}
                                                    </div>
                                                    <div style="font-size:0.84rem;font-weight:700;color:#0f172a;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                                        ${escapeHtml(art.title)}
                                                    </div>
                                                    ${snippetHtml}
                                                </div>
                                            </a>
                                        `;
                                    });
                                    html += `
                                        <div class="p-2 border-top bg-light text-center">
                                            <a href="<?= url('index.php') ?>?q=${encodeURIComponent(query)}" class="text-xs text-danger text-decoration-none fw-bold">
                                                <?= __('view_all_results') ?? 'View all results' ?> &rarr;
                                            </a>
                                        </div>
                                    `;
                                    searchList.innerHTML = html;
                                    dropdown.style.display = 'block';
                                } else {
                                    searchList.innerHTML = '<div class="p-3 text-center text-muted small"><i class="bi bi-search me-1"></i> <?= addslashes(__('no_articles_found')) ?></div>';
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

            function escapeHtml(str) {
                if (!str) return '';
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            // Initialize real-time search on Navbar search bar
            setupLiveSearch('publicSearchInput', 'publicSearchClear', 'liveSearchDropdown', 'liveSearchList');

            function fetchNotifications() {
                fetch('<?= url("api/v1/notifications.php") ?>')
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.success && Array.isArray(data.notifications)) {
                            const badge = document.getElementById('notifBadge');
                            const countLabel = document.getElementById('notifCountLabel');
                            const list = document.getElementById('notifList');
                            
                            const count = data.count || data.notifications.length;
                            if (badge) {
                                badge.textContent = count > 99 ? '99+' : count;
                                badge.style.display = count > 0 ? 'flex' : 'none';
                            }
                            if (countLabel) countLabel.textContent = count;
                            
                            if (list) {
                                if (data.notifications.length === 0) {
                                    list.innerHTML = '<div class="p-3 text-center text-muted text-xs"><?= addslashes(__('no_notifications')) ?></div>';
                                } else {
                                    list.innerHTML = data.notifications.map(n => {
                                        const isBreaking = n.type === 'breaking';
                                        const isSubTopic = n.is_subscribed_topic;
                                        let badgeHtml = '';
                                        if (isBreaking) {
                                            badgeHtml = '<span class="badge bg-danger text-white text-3xs px-1.5 py-0.5 rounded-1 fw-bold me-1">HOT</span>';
                                        } else if (isSubTopic) {
                                            badgeHtml = '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-3xs px-1.5 py-0.5 rounded-1 fw-bold me-1">✓ <?= addslashes(__('subscribed_topic_badge')) ?></span>';
                                        }
                                        const catLabel = n.category_display ? `<span class="badge bg-light text-secondary text-3xs px-1.5 py-0.5 rounded-1">${escapeHtml(n.category_display)}</span>` : '';

                                        return `
                                        <a href="${n.article_url}" class="list-group-item list-group-item-action p-2.5 border-bottom">
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="mt-0.5 flex-shrink-0">
                                                    ${isBreaking 
                                                        ? '<span class="badge bg-danger rounded-circle p-1 d-inline-flex"><i class="bi bi-lightning-fill text-white" style="font-size:0.75rem;"></i></span>' 
                                                        : (isSubTopic 
                                                            ? '<span class="badge bg-danger bg-opacity-15 text-danger rounded-circle p-1 d-inline-flex"><i class="bi bi-bookmark-star-fill text-danger" style="font-size:0.75rem;"></i></span>'
                                                            : '<span class="badge bg-primary bg-opacity-10 text-primary rounded-circle p-1 d-inline-flex"><i class="bi bi-newspaper" style="font-size:0.75rem;"></i></span>')}
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden min-w-0">
                                                    <div class="d-flex align-items-center gap-1 mb-0.5 flex-wrap">
                                                        ${badgeHtml}
                                                        ${catLabel}
                                                    </div>
                                                    <div class="fw-bold text-dark text-xs text-truncate" style="line-height:1.35;">${escapeHtml(n.title)}</div>
                                                    <div class="text-muted text-2xs text-truncate mt-0.5">${escapeHtml(n.message || '')}</div>
                                                    <div class="text-2xs text-secondary mt-1"><i class="bi bi-clock me-1"></i>${escapeHtml(n.time_ago || '')}</div>
                                                </div>
                                            </div>
                                        </a>
                                    `;
                                    }).join('');
                                }
                            }
                        }
                    })
                    .catch(e => console.debug('Notifications poll:', e));
            }

            fetchNotifications();
            setInterval(fetchNotifications, 30000);
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

    <!-- ── Breaking News Ticker (CNA Live Stream) ────────────────────────── -->
    <?php
    if (!isset($breakingNews)) {
        $headerDb = \App\Core\Database::getInstance();
        $breakingNews = $headerDb->fetchAll(
            "SELECT a.id, a.title, a.slug, c.name as category_name 
             FROM articles a 
             JOIN categories c ON a.category_id = c.id 
             WHERE a.status = 'published' AND a.is_breaking = 1 
             ORDER BY a.published_at DESC LIMIT 5"
        );
    }
    if (!empty($breakingNews)) {
    ?>
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