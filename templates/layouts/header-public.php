<?php
/**
 * Public Reader Layout Header — CNA-Style Design
 * news-platform / templates / layouts / header-public.php
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
require_once __DIR__ . '/../../languages/common.php';
$currentLang   = $_SESSION['lang'] ?? 'en';
$currentReader = \App\Core\Auth::reader();
$staffUser     = \App\Core\Auth::check() ? \App\Core\Auth::user() : null;
$currentUser   = $currentReader ?: $staffUser;
$headerDb      = \App\Core\Database::getInstance();
$catNavList = $headerDb->fetchAll("SELECT * FROM categories ORDER BY name ASC");
$activeCatId = (int) ($activeCategoryId ?? ($_GET['category'] ?? 0));
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

    <!-- Progressive Web App (PWA) Manifest & Standard Mobile Meta -->
    <link rel="manifest" href="<?= url('manifest.json') ?>">
    <meta name="theme-color" content="#c8102e">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?= __('site_title') ?>">
    <link rel="apple-touch-icon" href="<?= url('assets/icons/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= url('assets/icons/icon-192.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= url('assets/icons/icon-192.png') ?>">

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
        .live-search-dropdown-menu {
            border-radius: 6px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 14px 40px rgba(0, 0, 0, 0.16) !important;
            background: #ffffff !important;
            z-index: 1060 !important;
            display: none;
            flex-direction: column;
            overflow: hidden !important;
        }
        #liveSearchDropdown {
            width: 440px !important;
            max-width: 92vw !important;
        }
        #mobileLiveSearchDropdown {
            width: 100% !important;
            left: 0 !important;
            right: 0 !important;
        }
        #mobileSearchCollapse.show {
            overflow: visible !important;
        }
        #mobileSearchForm {
            position: relative;
            overflow: visible !important;
        }
        .live-search-scroll-container {
            max-height: 340px;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            overscroll-behavior-y: contain !important;
            touch-action: pan-y !important;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
        }
        @media (max-width: 991.98px) {
            .live-search-scroll-container {
                max-height: min(280px, 50vh) !important;
            }
        }
        .live-search-scroll-container::-webkit-scrollbar {
            width: 6px;
        }
        .live-search-scroll-container::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        .live-search-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .live-search-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .live-search-item {
            transition: background 0.12s ease;
        }
        .live-search-item:hover {
            background: #f8fafc !important;
        }

        /* ── Mobile Top Utility Bar ── */
        @media (max-width: 991.98px) {
            .top-utility-header .mobile-hide-text {
                display: none !important;
            }
            .top-utility-header {
                justify-content: flex-end !important;
            }
        }

        /* ── Mobile: Hide inline search in navbar collapse (use dedicated mobile search instead) ── */
        @media (max-width: 991.98px) {
            #headerSearchForm {
                display: none !important;
            }
        }

        /* ── Mobile Navbar Collapse — Professional Slide-Down Panel ── */
        @media (max-width: 991.98px) {
            #mainNavbar {
                background: #ffffff;
                border-top: 1px solid #e5e7eb;
                border-bottom: 1px solid #e5e7eb;
                box-shadow: 0 8px 24px rgba(15, 23, 42, 0.10);
                padding: 0.5rem 0 !important;
                margin-top: 0;
            }
            /* Mobile menu items container */
            #mainNavbar .mobile-menu-section {
                padding: 0.5rem 0.75rem;
            }
            #mainNavbar .mobile-menu-divider {
                height: 1px;
                background: #f1f5f9;
                margin: 0.35rem 0.75rem;
            }
            #mainNavbar .mobile-menu-label {
                font-size: 0.65rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #94a3b8;
                padding: 0.5rem 0.75rem 0.25rem;
            }
            #mainNavbar .mobile-menu-item {
                display: flex;
                align-items: center;
                gap: 0.65rem;
                padding: 0.6rem 0.75rem;
                border-radius: 6px;
                color: #334155;
                font-size: 0.88rem;
                font-weight: 600;
                text-decoration: none;
                transition: background 0.15s ease;
            }
            #mainNavbar .mobile-menu-item:hover,
            #mainNavbar .mobile-menu-item:active {
                background: #f8fafc;
                color: #c8102e;
            }
            #mainNavbar .mobile-menu-item i {
                font-size: 1.05rem;
                width: 20px;
                text-align: center;
                flex-shrink: 0;
            }
        }

        /* ── Notification dropdown — full-width on mobile ── */
        @media (max-width: 575.98px) {
            #notifBellBtn + .dropdown-menu,
            .mobile-notif-dropdown .dropdown-menu {
                position: fixed !important;
                top: auto !important;
                left: 8px !important;
                right: 8px !important;
                width: calc(100vw - 16px) !important;
                max-width: none !important;
                transform: none !important;
                border-radius: 10px !important;
                box-shadow: 0 16px 48px rgba(0,0,0,0.18) !important;
            }
        }

        /* ── Mobile Icon Bar (between brand and hamburger) ── */
        .mobile-icon-bar {
            display: flex;
            align-items: center;
            gap: 2px;
        }
        .mobile-icon-bar .header-clean-icon-btn {
            width: 34px;
            height: 34px;
        }
        .mobile-icon-bar .divider-dot {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: #cbd5e1;
            margin: 0 2px;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <div id="readingProgressBar"></div>

    <!-- ── CNA-Style Top Utility Bar (Matches Admin Site Fit) ──────────── -->
    <div class="top-utility-header d-flex align-items-center justify-content-between px-3 px-md-4 py-1">
        <!-- Left: Date & CDA Badge (hidden on mobile) -->
        <div class="d-flex align-items-center gap-2.5 mobile-hide-text" style="font-size:0.72rem; font-weight:600;">
            <span style="color:rgba(255,255,255,0.40);"><?= \App\Core\TemplateEngine::formatDate(date('Y-m-d H:i:s'), 'l, d F Y') ?></span>
            <span class="opacity-25">|</span>
            <span style="color:rgba(255,255,255,0.60);"><?= __('cda_badge') ?></span>
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

            <!-- Mobile Controls (Icons + Search + Hamburger) — Always visible on mobile -->
            <div class="d-flex align-items-center ms-auto d-lg-none mobile-icon-bar">

                <!-- Mobile: Saved Reading List Icon & Notification Bell (Only for Logged-In Users) -->
                <?php if ($currentUser) { ?>
                    <button type="button"
                        class="header-clean-icon-btn d-flex align-items-center justify-content-center position-relative p-0"
                        data-bs-toggle="offcanvas" data-bs-target="#savedArticlesModal"
                        title="<?= __('saved_reading_list') ?? 'Saved Reading List' ?>">
                        <i class="bi bi-bookmark-fill text-danger" style="font-size: 1.1rem;"></i>
                        <span id="mobileSavedCountBadge" class="position-absolute badge rounded-circle bg-danger p-0 d-flex align-items-center justify-content-center" style="top:-1px; right:-3px; font-size: 0.55rem; width: 15px; height: 15px; display: none;">0</span>
                    </button>

                    <div class="dropdown position-relative mobile-notif-dropdown">
                        <button type="button"
                            class="header-clean-icon-btn d-flex align-items-center justify-content-center position-relative p-0"
                            id="mobileNotifBellBtn" data-bs-toggle="dropdown" aria-expanded="false"
                            title="<?= __('notifications_title') ?>">
                            <i class="bi bi-bell-fill text-dark" style="font-size: 1.1rem;"></i>
                            <span id="mobileNotifBadge" class="position-absolute badge rounded-circle bg-danger p-0 d-flex align-items-center justify-content-center"
                                style="top:-1px; right:-3px; font-size: 0.55rem; width: 15px; height: 15px; display: none;">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 mt-2" style="width: 340px; max-height: 380px; overflow-y: auto; border-radius: 8px; z-index: 1080;">
                            <div class="p-2.5 border-bottom d-flex align-items-center justify-content-between" style="background:#f8fafc;">
                                <span class="fw-bold small text-dark"><i class="bi bi-bell-fill me-1 text-danger"></i> <?= __('notifications_title') ?></span>
                                <span class="badge bg-danger text-white text-2xs" id="mobileNotifCountLabel">0</span>
                            </div>
                            <div class="p-2 border-bottom bg-light-subtle px-3 text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100 py-1 fw-bold text-xs d-flex align-items-center justify-content-center gap-1.5 push-toggle-action-btn">
                                    <i class="bi bi-bell-fill push-toggle-icon"></i>
                                    <span class="push-toggle-text"><?= __('push_alerts_subscribe') ?></span>
                                </button>
                            </div>
                            <div id="mobileNotifList" class="list-group list-group-flush small">
                                <div class="p-3 text-center text-muted text-xs">
                                    <span class="spinner-border spinner-border-sm me-1"></span> <?= __('loading') ?? 'Loading...' ?>
                                </div>
                            </div>
                            <div class="p-2 border-top text-center" style="background:#f8fafc;">
                                <a href="<?= url('index.php?breaking=1') ?>" class="text-xs text-danger text-decoration-none fw-semibold">
                                    <?= __('filter_breaking_only') ?> &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <span class="divider-dot"></span>

                <!-- Mobile Search Toggle Button -->
                <button class="header-clean-icon-btn d-flex align-items-center justify-content-center p-0"
                    type="button" data-bs-toggle="collapse" data-bs-target="#mobileSearchCollapse"
                    aria-expanded="false" aria-controls="mobileSearchCollapse" title="<?= __('search') ?>">
                    <i class="bi bi-search text-dark" style="font-size: 1.05rem;"></i>
                </button>

                <!-- Mobile Hamburger Toggler -->
                <button class="navbar-toggler border-0 shadow-none p-1" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" title="Menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

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
                    <div id="liveSearchDropdown" class="card border-0 shadow-lg position-absolute mt-1 live-search-dropdown-menu"
                        style="top:100%; left:0;">
                        <div id="liveSearchHeader" class="py-1 px-2.5 bg-light border-bottom d-flex align-items-center justify-content-between text-muted text-3xs fw-bold" style="background:#f8fafc !important; display:none;"></div>
                        <div class="list-group list-group-flush live-search-scroll-container" id="liveSearchList"></div>
                        <div id="liveSearchFooter" class="py-1.5 px-2 border-top bg-white text-center" style="background:#ffffff !important; display:none;"></div>
                    </div>
                </form>

                <!-- Desktop: Reader Controls, Notifications & CTAs (hidden on mobile — icons moved to mobile bar) -->
                <div class="d-none d-lg-flex align-items-center gap-2 ms-lg-auto pe-1">
                    
                    <?php if ($currentUser) { ?>
                        <!-- Saved Reading List Offcanvas Trigger (Only for Logged-in Users) -->
                        <button type="button"
                            class="header-clean-icon-btn d-flex align-items-center justify-content-center position-relative"
                            data-bs-toggle="offcanvas" data-bs-target="#savedArticlesModal"
                            title="<?= __('saved_reading_list') ?? 'Saved Reading List' ?>">
                            <i class="bi bi-bookmark-fill text-danger" style="font-size: 1.2rem;"></i>
                            <span id="savedCountBadge" class="position-absolute badge rounded-circle bg-danger p-0 d-flex align-items-center justify-content-center" style="top:-2px; right:-4px; font-size: 0.6rem; width: 17px; height: 17px; display: none;">0</span>
                        </button>

                        <!-- Real-Time Notification Bell Dropdown (Only for Logged-in Users) -->
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
                                <div class="p-2 border-bottom bg-light-subtle px-3 text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100 py-1 fw-bold text-xs d-flex align-items-center justify-content-center gap-1.5 push-toggle-action-btn">
                                        <i class="bi bi-bell-fill push-toggle-icon"></i>
                                        <span class="push-toggle-text"><?= __('push_alerts_subscribe') ?></span>
                                    </button>
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

                        <!-- User Profile Dropdown (Desktop) -->
                        <div class="dropdown ms-1">
                            <button class="btn btn-outline-danger btn-sm px-2.5 py-1.5 d-flex align-items-center gap-1.5 rounded-2 dropdown-toggle text-nowrap"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.78rem;">
                                <i class="bi bi-person-circle text-danger"></i>
                                <span class="fw-bold"><?= e($currentReader ? $currentReader['name'] : ($staffUser['username'] ?? 'Staff Admin')) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="min-width: 200px;">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-bold small text-dark"><?= e($currentReader ? $currentReader['name'] : ($staffUser['username'] ?? 'Staff Admin')) ?></div>
                                    <div class="text-muted text-2xs"><?= e($currentReader ? $currentReader['email'] : ($staffUser['email'] ?? '')) ?></div>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-3xs mt-1">
                                        <?= $currentReader ? __('verified_reader_badge') : 'Staff Admin' ?>
                                    </span>
                                </li>
                                <?php if ($currentReader) { ?>
                                <li>
                                    <a class="dropdown-item small py-2" href="<?= url('settings.php') ?>">
                                        <i class="bi bi-gear-fill me-1.5 text-muted"></i> <?= __('settings_page_title') ?>
                                    </a>
                                </li>
                                <?php } else { ?>
                                <li>
                                    <a class="dropdown-item small py-2" href="<?= url('admin/dashboard.php') ?>">
                                        <i class="bi bi-speedometer2 me-1.5 text-muted"></i> Admin Dashboard
                                    </a>
                                </li>
                                <?php } ?>
                                <li>
                                    <a class="dropdown-item small py-2 text-danger" href="<?= url('logout.php') ?>">
                                        <i class="bi bi-box-arrow-right me-1.5"></i> <?= __('sign_out') ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php } else { ?>
                        <!-- Guest: Create Account CTA Button -->
                        <div class="d-flex align-items-center ms-1">
                            <button type="button" class="btn btn-danger btn-sm px-2.5 py-1.5 fw-semibold text-xs rounded-2 shadow-2xs text-nowrap"
                                data-bs-toggle="modal" data-bs-target="#readerAuthModal" data-auth-tab="register">
                                <?= __('create_account') ?>
                            </button>
                        </div>
                    <?php } ?>
                </div>

                <!-- Mobile: Clean Professional Menu Panel (replaces messy list) -->
                <div class="d-lg-none">
                    <div class="mobile-menu-divider"></div>

                    <!-- User Account Section (Mobile) -->
                    <?php if ($currentUser) { ?>
                        <div class="mobile-menu-section">
                            <div class="d-flex align-items-center gap-2.5 py-2">
                                <div class="d-flex align-items-center justify-content-center" style="width:36px; height:36px; border-radius:50%; background:rgba(217,4,41,0.08);">
                                    <i class="bi bi-person-fill text-danger" style="font-size:1.1rem;"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-bold text-dark" style="font-size:0.88rem;"><?= e($currentReader ? $currentReader['name'] : ($staffUser['username'] ?? 'Staff Admin')) ?></div>
                                    <div class="text-muted" style="font-size:0.72rem;"><?= e($currentReader ? $currentReader['email'] : ($staffUser['email'] ?? '')) ?></div>
                                </div>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" style="font-size:0.6rem;">
                                    <?= $currentReader ? __('verified_reader_badge') : 'Staff Admin' ?>
                                </span>
                            </div>
                        </div>
                        <div class="mobile-menu-divider"></div>
                        <?php if ($currentReader) { ?>
                        <a class="mobile-menu-item" href="<?= url('settings.php') ?>">
                            <i class="bi bi-gear-fill text-muted"></i>
                            <?= __('settings_page_title') ?>
                        </a>
                        <?php } else { ?>
                        <a class="mobile-menu-item" href="<?= url('admin/dashboard.php') ?>">
                            <i class="bi bi-speedometer2 text-muted"></i>
                            Admin Dashboard
                        </a>
                        <?php } ?>
                        <a class="mobile-menu-item text-danger" href="<?= url('logout.php') ?>">
                            <i class="bi bi-box-arrow-right"></i>
                            <?= __('sign_out') ?>
                        </a>
                    <?php } else { ?>
                        <div class="mobile-menu-section">
                            <button type="button" class="btn btn-danger btn-sm w-100 py-2 fw-semibold rounded-2 shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#readerAuthModal" data-auth-tab="register"
                                style="font-size:0.85rem;">
                                <i class="bi bi-person-plus-fill me-1.5"></i>
                                <?= __('create_account') ?>
                            </button>
                        </div>
                    <?php } ?>

                    <!-- Mobile PWA Install Trigger -->
                    <div class="mobile-menu-section">
                        <button type="button" id="mobilePwaInstallBtn"
                            class="btn btn-outline-danger btn-sm w-100 d-none align-items-center justify-content-center gap-1.5 py-1.5 fw-semibold text-xs rounded-2"
                            title="<?= __('pwa_install') ?>">
                            <i class="bi bi-download"></i>
                            <span><?= __('pwa_install') ?></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- ── Mobile Collapsible Search Bar (Smooth Dropdown) ────────────────── -->
    <div class="collapse d-lg-none bg-white border-bottom shadow-sm" id="mobileSearchCollapse" style="position:relative; z-index:1034;">
        <div class="container-fluid px-3 py-2">
            <form action="<?= url('index.php') ?>" method="GET" class="position-relative" id="mobileSearchForm">
                <div class="input-group input-group-sm align-items-center" style="background:#f3f4f6; border:1px solid #e5e7eb; border-radius:4px; padding-right:4px;">
                    <span class="input-group-text border-0 bg-transparent pe-1 ps-2.5">
                        <i class="bi bi-search text-muted" style="font-size:0.85rem;"></i>
                    </span>
                    <input class="form-control border-0 bg-transparent shadow-none text-xs pe-1"
                        id="mobileSearchInput" type="text" name="q" autocomplete="off"
                        placeholder="<?= __('search_placeholder') ?>" value="<?= e($_GET['q'] ?? '') ?>">
                    <span id="mobileSearchClear"
                        class="bootstrap-search-clear text-muted p-1 <?= empty($_GET['q']) ? 'd-none' : '' ?>"
                        title="<?= __('clear_search') ?>">
                        <i class="bi bi-x-circle-fill" style="font-size:0.85rem; color:#9ca3af;"></i>
                    </span>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-2.5 py-1 text-2xs fw-bold ms-1">
                        <?= __('search') ?>
                    </button>
                </div>
                <!-- Mobile Live Search Dropdown -->
                <div id="mobileLiveSearchDropdown" class="card border-0 shadow-lg position-absolute w-100 mt-1 live-search-dropdown-menu"
                    style="top:100%; left:0;">
                    <div id="mobileLiveSearchHeader" class="py-1 px-2.5 bg-light border-bottom d-flex align-items-center justify-content-between text-muted text-3xs fw-bold" style="background:#f8fafc !important; display:none;"></div>
                    <div class="list-group list-group-flush live-search-scroll-container" id="mobileLiveSearchList"></div>
                    <div id="mobileLiveSearchFooter" class="py-1.5 px-2 border-top bg-white text-center" style="background:#ffffff !important; display:none;"></div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function setupLiveSearch(inputId, clearId, dropdownId, listId, headerId, footerId) {
                const searchInput = document.getElementById(inputId);
                const clearBtn = document.getElementById(clearId);
                const dropdown = document.getElementById(dropdownId);
                const searchList = document.getElementById(listId);
                const header = document.getElementById(headerId);
                const footer = document.getElementById(footerId);
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

                function closeDropdown() {
                    if (dropdown) dropdown.style.display = 'none';
                    if (header) header.style.display = 'none';
                    if (footer) footer.style.display = 'none';
                }

                if (clearBtn) {
                    clearBtn.addEventListener('click', function () {
                        searchInput.value = '';
                        updateClearBtn();
                        searchInput.dispatchEvent(new Event('input'));
                        closeDropdown();
                    });
                }

                searchInput.addEventListener('input', function () {
                    updateClearBtn();
                    const query = this.value.trim();

                    // Real-time AJAX search preview dropdown
                    clearTimeout(debounceTimer);
                    if (query.length < 2) {
                        closeDropdown();
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        fetch('<?= url("search_api.php") ?>?q=' + encodeURIComponent(query) + '&lang=<?= $currentLang ?>')
                            .then(res => res.json())
                            .then(data => {
                                if (data.success && data.articles && data.articles.length > 0) {
                                    const isKhmer = '<?= $currentLang ?>' === 'kh' || '<?= $currentLang ?>' === 'km';
                                    const khmerDigits = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
                                    const countFormatted = isKhmer 
                                        ? String(data.articles.length).replace(/[0-9]/g, d => khmerDigits[d]) 
                                        : String(data.articles.length);
                                    const countText = countFormatted + ' ' + '<?= addslashes(__('articles_count_label')) ?>';

                                    if (header) {
                                        header.innerHTML = `
                                            <span class="d-flex align-items-center gap-1.5"><i class="bi bi-file-earmark-text text-danger"></i>${countText}</span>
                                            <span class="text-secondary d-flex align-items-center gap-1"><i class="bi bi-arrow-down-up" style="font-size:0.7rem;"></i><?= addslashes(__('scroll_for_more')) ?></span>
                                        `;
                                        header.style.display = 'flex';
                                    }

                                    let html = '';
                                    data.articles.forEach(art => {
                                        const img = art.image_url || art.featured_image || '';
                                        const imgHtml = img
                                            ? `<div class="ls-thumb"><img src="${escapeHtml(img)}" alt="${escapeHtml(art.title)}" onerror="this.parentNode.style.display='none'"></div>`
                                            : `<div class="ls-thumb d-flex align-items-center justify-content-center text-muted"><i class="bi bi-newspaper" style="font-size:0.85rem;"></i></div>`;

                                        const timeHtml = art.time_ago
                                            ? `<span class="ls-time"><i class="bi bi-clock me-1" style="font-size:0.6rem;vertical-align:middle;"></i>${escapeHtml(art.time_ago)}</span>`
                                            : '';
                                        const snippetHtml = art.summary_snippet
                                            ? `<div class="ls-snippet">${escapeHtml(art.summary_snippet)}</div>`
                                            : '';

                                        html += `
                                            <a href="${art.url}" class="list-group-item list-group-item-action text-decoration-none live-search-item d-flex gap-2">
                                                ${imgHtml}
                                                <div class="min-w-0 flex-grow-1">
                                                    <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                        <span class="ls-cat badge bg-danger bg-opacity-10 text-danger">${escapeHtml(art.category_display || '')}</span>
                                                        ${timeHtml}
                                                    </div>
                                                    <div class="ls-title">${escapeHtml(art.title)}</div>
                                                    ${snippetHtml}
                                                </div>
                                            </a>
                                        `;
                                    });

                                    searchList.innerHTML = html;

                                    if (footer) {
                                        footer.innerHTML = `
                                            <a href="<?= url('index.php') ?>?q=${encodeURIComponent(query)}" class="text-xs text-danger text-decoration-none fw-bold d-inline-flex align-items-center gap-1">
                                                <span><?= addslashes(__('view_all_results')) ?></span>
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        `;
                                        footer.style.display = 'block';
                                    }

                                    dropdown.style.display = 'flex';
                                    searchList.scrollTop = 0;
                                } else {
                                    if (header) header.style.display = 'none';
                                    if (footer) footer.style.display = 'none';
                                    searchList.innerHTML = '<div class="p-3 text-center text-muted small"><i class="bi bi-search me-1"></i> <?= addslashes(__('no_articles_found')) ?></div>';
                                    dropdown.style.display = 'flex';
                                }
                            })
                            .catch(() => {
                                closeDropdown();
                            });
                    }, 150);
                });

                document.addEventListener('click', function (e) {
                    if (dropdown && searchInput && !searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                        closeDropdown();
                    }
                });
            }

            function escapeHtml(str) {
                if (!str) return '';
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            // Initialize real-time search on Navbar & Mobile search bars
            setupLiveSearch('publicSearchInput', 'publicSearchClear', 'liveSearchDropdown', 'liveSearchList', 'liveSearchHeader', 'liveSearchFooter');
            setupLiveSearch('mobileSearchInput', 'mobileSearchClear', 'mobileLiveSearchDropdown', 'mobileLiveSearchList', 'mobileLiveSearchHeader', 'mobileLiveSearchFooter');

            const mobileCollapse = document.getElementById('mobileSearchCollapse');
            const mainNav = document.getElementById('mainNavbar');
            if (mobileCollapse) {
                mobileCollapse.addEventListener('shown.bs.collapse', function () {
                    const input = document.getElementById('mobileSearchInput');
                    if (input) input.focus();
                });
            }
            if (mainNav && mobileCollapse) {
                mainNav.addEventListener('show.bs.collapse', function () {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                        const bsCollapse = bootstrap.Collapse.getInstance(mobileCollapse);
                        if (bsCollapse) bsCollapse.hide();
                    }
                });
                mobileCollapse.addEventListener('show.bs.collapse', function () {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                        const bsMain = bootstrap.Collapse.getInstance(mainNav);
                        if (bsMain) bsMain.hide();
                    }
                });
            }

            function fetchNotifications() {
                <?php if (!$currentUser) { ?>
                    return; // Guests don't fetch notifications
                <?php } ?>
                fetch('<?= url("api/v1/notifications.php") ?>?lang=<?= $currentLang ?>')
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.success && Array.isArray(data.notifications)) {
                            // Desktop elements
                            const badge = document.getElementById('notifBadge');
                            const countLabel = document.getElementById('notifCountLabel');
                            const list = document.getElementById('notifList');
                            // Mobile elements
                            const mobileBadge = document.getElementById('mobileNotifBadge');
                            const mobileCountLabel = document.getElementById('mobileNotifCountLabel');
                            const mobileList = document.getElementById('mobileNotifList');
                            
                            const count = data.count || data.notifications.length;
                            const countText = count > 99 ? '99+' : count;

                            // Update both desktop and mobile badges
                            [badge, mobileBadge].forEach(b => {
                                if (b) {
                                    b.textContent = countText;
                                    b.style.display = count > 0 ? 'flex' : 'none';
                                }
                            });
                            [countLabel, mobileCountLabel].forEach(cl => {
                                if (cl) cl.textContent = count;
                            });
                            
                            // Build notification HTML
                            let notifHtml = '';
                            if (data.notifications.length === 0) {
                                notifHtml = '<div class="p-3 text-center text-muted text-xs"><?= addslashes(__('no_notifications')) ?></div>';
                            } else {
                                notifHtml = data.notifications.map(n => {
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
                                    <a href="${n.article_url}" class="list-group-item list-group-item-action py-2 px-2.5 border-bottom">
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

                            // Apply to both desktop and mobile lists
                            [list, mobileList].forEach(l => {
                                if (l) l.innerHTML = notifHtml;
                            });

                            // Trigger OS System Pop-up Banner for new notifications
                            if (data.notifications && data.notifications.length > 0) {
                                const latest = data.notifications[0];
                                const lastSeenId = parseInt(localStorage.getItem('np_last_notif_id') || '0', 10);
                                if (latest.id > lastSeenId) {
                                    localStorage.setItem('np_last_notif_id', latest.id);
                                    if (lastSeenId > 0 && 'Notification' in window && Notification.permission === 'granted') {
                                        const notifTitle = (latest.type === 'breaking' ? '🚨 BREAKING NEWS: ' : '📰 ') + latest.title;
                                        if ('serviceWorker' in navigator) {
                                            navigator.serviceWorker.ready.then(reg => {
                                                reg.showNotification(notifTitle, {
                                                    body: latest.message,
                                                    icon: '<?= url("assets/icons/icon-192.png") ?>',
                                                    badge: '<?= url("assets/icons/icon-192.png") ?>',
                                                    tag: 'notif-' + latest.id,
                                                    requireInteraction: true,
                                                    data: { url: latest.article_url }
                                                });
                                            }).catch(() => {
                                                try {
                                                    new Notification(notifTitle, { body: latest.message, icon: '<?= url("assets/icons/icon-192.png") ?>' });
                                                } catch(e) {}
                                            });
                                        }
                                    }
                                }
                            }
                        }
                    })
                    .catch(e => console.debug('Notifications poll:', e));
            }

            <?php if ($currentUser) { ?>
            fetchNotifications();
            setInterval(fetchNotifications, 10000);
            <?php } ?>
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

                <!-- Breaking News Browser Push Alerts Opt-In Button -->
                <button type="button" id="pushSubscribeBtn"
                    class="btn btn-sm py-0.5 px-2 d-none align-items-center gap-1.5 flex-shrink-0 text-white"
                    style="font-size:0.7rem; background:rgba(255,255,255,0.14); border:1px solid rgba(255,255,255,0.22); border-radius:2px; height:24px; transition:all 0.2s;"
                    title="<?= __('push_alerts_title') ?>">
                    <i class="bi bi-bell-fill text-warning" id="pushSubscribeIcon" style="font-size:0.75rem;"></i>
                    <span id="pushSubscribeText" class="d-none d-sm-inline fw-semibold"><?= __('push_alerts_subscribe') ?></span>
                </button>
            </div>
        </div>
    <?php } ?>

    <main class="flex-grow-1" style="background:#f9f9f9;">