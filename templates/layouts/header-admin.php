<?php
/**
 * CMS Admin Header Layout — CNA-Style Professional
 * news-platform / templates / layouts / header-admin.php
 */
require_once __DIR__ . '/../../languages/common.php';
$currentLang = $_SESSION['lang'] ?? 'en';
?>
<!DOCTYPE html>
<html lang="<?= e($currentLang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? __('admin_portal') . ' | ' . __('editorial_overview')) ?></title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Plus Jakarta Sans + Inter + Kantumruy Pro + Noto Sans Khmer -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300..900;1,14..32,300..900&family=Kantumruy+Pro:ital,wght@0,400..700;1,400..700&family=Noto+Sans+Khmer:wght@400;500;600;700&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= url('public/assets/css/style.css') ?>">

    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', 'Inter', 'Kantumruy Pro', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* CNA Admin Topbar — thin navy line like CNA utility bar */
        .admin-topbar {
            background: #0f172a;
            color: rgba(255, 255, 255, 0.60);
            font-size: 0.72rem;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            min-height: 30px;
        }

        /* Make page body use full height */
        .admin-content-area {
            flex: 1;
            overflow: hidden;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- ── Reusable Professional Delete Confirmation Modal Card ───────────────── -->
    <div class="modal fade" id="globalDeleteModal" tabindex="-1" aria-labelledby="globalDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content border-0 shadow-lg"
                style="border-radius:8px; overflow:hidden; background:#ffffff;">
                <div class="modal-body p-4 text-center">
                    <h5 class="fw-bold text-dark mb-2" id="globalDeleteModalLabel"><?= __('confirm_delete_title') ?>
                    </h5>
                    <p class="text-secondary small mb-4" id="globalDeleteModalText">
                        <?= __('confirm_delete_msg', ['item' => 'item']) ?>
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary px-4 fw-semibold text-xs"
                            style="border-radius:4px;" data-bs-dismiss="modal">
                            <?= __('btn_cancel_delete') ?>
                        </button>
                        <a href="#" id="globalDeleteConfirmBtn" class="btn btn-danger px-4 fw-bold text-xs"
                            style="border-radius:4px;">
                            <?= __('btn_confirm_delete') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDeleteCard(deleteUrl, itemName) {
            const modalEl = document.getElementById('globalDeleteModal');
            if (!modalEl) {
                if (confirm("Delete " + itemName + "?")) { window.location.href = deleteUrl; }
                return;
            }
            const textEl = document.getElementById('globalDeleteModalText');
            const btnEl = document.getElementById('globalDeleteConfirmBtn');

            if (textEl && itemName) {
                const template = <?= json_encode(__('confirm_delete_msg')) ?>;
                textEl.innerText = template.replace(':item', itemName);
            }
            if (btnEl) {
                btnEl.setAttribute('href', deleteUrl);
            }
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        }
    </script>

    <!-- ── CNA Admin Topbar ────────────────────────────────────────────────── -->
    <div class="admin-topbar d-flex align-items-center px-3 px-md-4 py-1">
        <div class="d-flex align-items-center gap-3 flex-grow-1">
            <span
                style="color:rgba(255,255,255,0.40);"><?= \App\Core\TemplateEngine::formatDate(date('Y-m-d H:i:s'), 'l, d F Y') ?></span>
            <span class="opacity-25">|</span>
            <span>CMS Editorial Platform</span>
        </div>
        <div>
            <a href="<?= url('public/index.php') ?>" target="_blank"
                class="d-inline-flex align-items-center gap-1 text-decoration-none"
                style="color:rgba(255,255,255,0.55); font-size:0.72rem; font-weight:600;">
                <span class="d-none d-sm-inline"><?= __('live_public_site') ?></span> &rarr;
            </a>
        </div>
    </div>

    <!-- ── CNA Admin Navbar — white bar ────────────────────────────────────── -->
    <nav class="navbar navbar-expand-xl admin-navbar py-0"
        style="min-height:56px; position:sticky; top:0; z-index:1020; border-bottom: 1px solid #e5e7eb;">
        <div class="container-fluid px-3 px-md-4 h-100">

            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center gap-2 py-3 text-decoration-none"
                href="<?= url('admin/dashboard.php') ?>">
                <span class="brand-logo-badge"
                    style="width:42px; height:36px; font-size:0.95rem; border-radius:2px; font-weight:900;">CMA</span>
                <span class="d-none d-sm-inline fw-bold" style="color:#0f172a; font-size:1rem; letter-spacing:-0.02em;">
                    <?= __('admin_portal') ?>
                </span>
            </a>

            <!-- Divider -->
            <div class="d-none d-xl-block mx-3" style="width:1px; height:28px; background:#e5e7eb; flex-shrink:0;">
            </div>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0 shadow-none p-1 ms-auto me-2" type="button" data-bs-toggle="collapse"
                data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <!-- Nav links — CNA style: red bottom border on active -->
                <ul class="navbar-nav me-auto mb-0 align-items-xl-stretch">
                    <li class="nav-item">
                        <a class="nav-link py-0 px-3 d-flex align-items-center <?= str_contains($_SERVER['PHP_SELF'], 'dashboard') ? 'active' : '' ?>"
                            style="height:56px;" href="<?= url('admin/dashboard.php') ?>">
                            <span><?= __('editorial_overview') ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-0 px-3 d-flex align-items-center <?= str_contains($_SERVER['PHP_SELF'], 'article-create') ? 'active' : '' ?>"
                            style="height:56px;" href="<?= url('admin/article-create.php') ?>">
                            <span><?= __('draft_new_article') ?></span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link py-0 px-3 d-flex align-items-center dropdown-toggle <?= (str_contains($_SERVER['PHP_SELF'], 'categories') || str_contains($_SERVER['PHP_SELF'], 'users') || str_contains($_SERVER['PHP_SELF'], 'subscribers')) ? 'active' : '' ?>"
                            style="height:56px;" href="#" id="adminMgmtDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <span><?= __('management') ?></span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminMgmtDropdown">
                            <li>
                                <a class="dropdown-item <?= str_contains($_SERVER['PHP_SELF'], 'categories') ? 'active' : '' ?>"
                                    href="<?= url('admin/categories.php') ?>">
                                    <?= __('categories') ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?= str_contains($_SERVER['PHP_SELF'], 'users') ? 'active' : '' ?>"
                                    href="<?= url('admin/users.php') ?>">
                                    <?= __('staff_users') ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?= str_contains($_SERVER['PHP_SELF'], 'subscribers') ? 'active' : '' ?>"
                                    href="<?= url('admin/subscribers.php') ?>">
                                    <?= __('subscribers') ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <!-- Right controls -->
                <div class="d-flex flex-wrap align-items-center gap-2 my-2 my-xl-0">

                    <!-- Language Switcher -->
                    <div class="dropdown me-2 position-relative">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle fw-bold text-dark"
                            style="font-size:0.8rem; border-radius:2px; padding:0.35rem 0.75rem; background:#ffffff; border:1px solid #cbd5e1;"
                            type="button" id="adminLangDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span><?= ($currentLang === 'kh' || $currentLang === 'km') ? 'KH' : 'EN' ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border mt-1"
                            aria-labelledby="adminLangDropdown" style="min-width:150px; z-index:2000 !important;">
                            <li>
                                <a class="dropdown-item py-2 fw-bold <?= $currentLang === 'en' ? 'active bg-danger text-white' : '' ?>"
                                    href="<?= lang_url('en') ?>">
                                    English (EN)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 fw-bold <?= ($currentLang === 'kh' || $currentLang === 'km') ? 'active bg-danger text-white' : '' ?>"
                                    href="<?= lang_url('kh') ?>">
                                    ភាសាខ្មែរ (KH)
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- New Article CTA -->
                    <a href="<?= url('admin/article-create.php') ?>"
                        class="btn btn-danger btn-sm d-inline-flex align-items-center gap-1 fw-bold text-nowrap"
                        style="font-size:0.8rem; border-radius:2px; padding:0.4rem 0.85rem; text-transform:uppercase; letter-spacing:0.04em;">
                        <span>+ <?= __('draft_new_article') ?></span>
                    </a>

                    <!-- User menu -->
                    <?php if (isset($currentUser)) { ?>
                        <div class="dropdown">
                            <button
                                class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2 fw-semibold text-nowrap"
                                style="font-size:0.8rem; border:1px solid #e5e7eb; border-radius:2px; padding:0.4rem 0.75rem; color:#0f172a;"
                                type="button" data-bs-toggle="dropdown">
                                <span class="d-flex align-items-center justify-content-center rounded fw-bold text-white"
                                    style="width:22px;height:22px;font-size:0.68rem;background:#c8102e;border-radius:2px;flex-shrink:0;">
                                    <?= strtoupper(substr($currentUser['username'], 0, 1)) ?>
                                </span>
                                <span><?= e($currentUser['username']) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" style="min-width:200px;">
                                <li>
                                    <div class="px-3 py-2 border-bottom" style="font-size:0.78rem;">
                                        <div class="fw-semibold text-truncate" style="color:#0f172a; max-width:170px;">
                                            <?= e($currentUser['email']) ?></div>
                                        <span class="badge mt-1 text-uppercase"
                                            style="background:rgba(200,16,46,0.08);color:#c8102e;border:1px solid rgba(200,16,46,0.2);font-size:0.62rem;border-radius:2px;">
                                            <?= e($currentUser['role']) ?>
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        style="color:#c8102e; font-size:0.85rem; font-weight:600; padding:0.6rem 0.9rem;"
                                        href="<?= url('admin/logout.php') ?>">
                                        <?= __('logout') ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- CNA-style red border bottom already on the nav — the 3px solid red line is the signature -->

    <!-- ── Global Professional Toast Notifications (Top-Right, Auto-Close 3s) ── -->
    <div class="toast-container position-fixed top-0 mt-4 end-0 p-3" style="z-index: 3000;">
        <?php if (!empty($_GET['msg'])) { ?>
            <div id="adminToastMsg" class="toast align-items-center border-0 shadow-lg show" role="alert"
                aria-live="assertive" aria-atomic="true"
                style="background:#ffffff !important; border:1px solid #e2e8f0 !important; border-radius:6px; min-width:280px; max-width:380px;">
                <div class="d-flex p-3 align-items-center justify-content-between">
                    <div class="toast-body p-0 fw-semibold text-dark small" style="font-size:0.85rem; line-height:1.4;">
                        <?= e($_GET['msg']) ?>
                    </div>
                    <button type="button" class="btn-close ms-3 me-0 shadow-none" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($_GET['error'])) { ?>
            <div id="adminToastErr" class="toast align-items-center border-0 shadow-lg show" role="alert"
                aria-live="assertive" aria-atomic="true"
                style="background:#ffffff !important; border:1px solid #e2e8f0 !important; border-radius:6px; min-width:280px; max-width:380px;">
                <div class="d-flex p-3 align-items-center justify-content-between">
                    <div class="toast-body p-0 fw-semibold text-danger small" style="font-size:0.85rem; line-height:1.4;">
                        <?= e($_GET['error']) ?>
                    </div>
                    <button type="button" class="btn-close ms-3 me-0 shadow-none" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        <?php } ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Universal Dropdown Toggle Handler for all dropdowns (Management, Language, User Profile)
            document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function (toggleEl) {
                toggleEl.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const parent = toggleEl.closest('.dropdown');
                    const menu = parent ? parent.querySelector('.dropdown-menu') : toggleEl.nextElementSibling;

                    if (menu) {
                        const isOpen = menu.classList.contains('show');
                        // Close all open dropdown menus first
                        document.querySelectorAll('.dropdown-menu.show').forEach(function (m) {
                            m.classList.remove('show');
                        });
                        if (!isOpen) {
                            menu.classList.add('show');
                        }
                    }
                });
            });

            // Close open dropdowns when clicking outside
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu.show').forEach(function (m) {
                        m.classList.remove('show');
                    });
                }
            });

            // Auto close toast notifications after 3 seconds (3000ms)
            const toastElems = document.querySelectorAll('.toast');
            toastElems.forEach(function (toastEl) {
                setTimeout(function () {
                    toastEl.classList.remove('show');
                    setTimeout(function () { if (toastEl.parentNode) toastEl.parentNode.removeChild(toastEl); }, 300);
                }, 3000);
            });
        });
    </script>

    <main class="flex-grow-1">