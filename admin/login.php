<?php
/**
 * CMS Staff Login Portal (CMA Authentication)
 * news-platform / admin / login.php
 */


require_once __DIR__ . '/../src/Core/helpers.php';
require_once __DIR__ . '/../languages/common.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/TemplateEngine.php';
require_once __DIR__ . '/../src/Core/Auth.php';

use App\Core\Auth;

$currentLang = $_SESSION['lang'] ?? 'en';

if (Auth::check()) {
    header('Location: ' . url('admin/dashboard.php'));
    exit;
}

$errorMsg   = $_GET['error'] ?? null;
$successMsg = $_GET['msg']   ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $token    = $_POST['csrf_token'] ?? '';

    if (!Auth::verifyCsrfToken($token)) {
        $errorMsg = 'Security validation failed (CSRF token mismatch). Please refresh and try again.';
    } elseif (empty($username) || empty($password)) {
        $errorMsg = 'Please provide both username and password.';
    } else {
        try {
            if (Auth::login($username, $password)) {
                header('Location: ' . url('admin/dashboard.php'));
                exit;
            } else {
                $errorMsg = 'Invalid staff credentials or account disabled.';
            }
        } catch (Throwable $e) {
            $errorMsg = $e->getMessage();
        }
    }
}

$csrfToken = Auth::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="<?= e($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(__('admin_portal')) ?> | Authentication</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300..900;1,14..32,300..900&family=Kantumruy+Pro:ital,wght@0,400..700;1,400..700&family=Noto+Sans+Khmer:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">

    <style>
        /* CNA-Style Login Page */
        :root {
            --cna-red: #c8102e;
            --cna-red-dark: #a50d25;
            --cna-navy: #0f172a;
            --color-accent: #c8102e;
        }

        body.login-page {
            font-family: 'Inter', 'Kantumruy Pro', sans-serif;
            background: var(--cna-navy);
            min-height: 100vh;
        }

        /* CNA subtle crosshatch grid */
        body.login-page::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
            z-index: 0;
        }

        /* Red accent band at top — CNA signature */
        body.login-page::after {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--cna-red);
            z-index: 100;
        }

        .login-wrapper { position: relative; z-index: 1; }

        .login-card {
            background: #ffffff;
            border-radius: 0;                          /* CNA: no rounding */
            border: none;
            border-top: 3px solid var(--cna-red);      /* CNA top border */
            box-shadow: 0 20px 60px rgba(0,0,0,0.45);
            overflow: hidden;
        }

        .login-brand-icon {
            width: 52px; height: 52px;
            border-radius: 2px;                        /* CNA: square */
            background: var(--cna-red);
            color: #fff;
            font-size: 1.4rem;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            letter-spacing: -0.03em;
        }

        .login-submit-btn {
            background: var(--cna-red);
            border: none;
            color: #fff;
            border-radius: 2px;                        /* CNA: sharp */
            padding: 0.75rem;
            font-weight: 700;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            width: 100%;
            transition: background 0.15s ease;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .login-submit-btn:hover { background: var(--cna-red-dark); }

        .login-input {
            background-color: #f9f9f9 !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 2px !important;             /* CNA: sharp */
            padding: 0.65rem 0.9rem !important;
            font-size: 0.9rem !important;
            color: #0f172a !important;
            font-family: 'Inter', sans-serif !important;
            transition: border-color 0.15s !important;
        }
        .login-input:focus {
            background-color: #fff !important;
            border-color: var(--cna-red) !important;
            box-shadow: 0 0 0 2px rgba(200,16,46,0.10) !important;
        }

        .login-input-icon {
            background-color: #f3f4f6 !important;
            border: 1px solid #e5e7eb !important;
            border-right: none !important;
            border-radius: 2px 0 0 2px !important;
            color: #9ca3af;
        }

        .login-input-icon-right {
            background-color: #f3f4f6 !important;
            border: 1px solid #e5e7eb !important;
            border-left: none !important;
            border-radius: 0 2px 2px 0 !important;
            cursor: pointer;
            transition: background 0.12s;
        }
        .login-input-icon-right:hover { background-color: #ebebeb !important; }

        .login-input.has-left-icon {
            border-left: none !important;
            border-radius: 0 2px 2px 0 !important;
        }

        .login-input.has-both-icons {
            border-left: none !important;
            border-right: none !important;
            border-radius: 0 !important;
        }

        .security-badges {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.25rem;
            padding: 0.75rem 0 0;
            border-top: 1px solid #f3f4f6;
        }

        .security-badge-item {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.7rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .login-float-controls {
            position: fixed;
            top: 0; right: 0;
            padding: 0.75rem 1.1rem;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
</head>
<body class="login-page d-flex align-items-center justify-content-center py-5">

<!-- Floating Top-Right Controls -->
<div class="login-float-controls">
    <div class="dropdown">
        <button class="btn btn-outline-light btn-sm rounded-pill px-3 py-1 dropdown-toggle fw-semibold"
                style="font-size:0.78rem; border-color:rgba(255,255,255,0.2);"
                type="button" data-bs-toggle="dropdown">
            <i class="bi bi-translate me-1" style="color:#fbbf24;"></i>
            <?= $currentLang === 'km' ? 'ខ្មែរ' : 'English' ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:160px; border-radius:12px;">
            <li>
                <a class="dropdown-item py-2 <?= $currentLang === 'en' ? 'fw-bold text-danger' : '' ?>" href="?lang=en">
                    <span class="badge bg-light text-dark border me-1" style="font-size:0.65rem;">EN</span> English
                </a>
            </li>
            <li>
                <a class="dropdown-item py-2 <?= $currentLang === 'km' ? 'fw-bold text-danger' : '' ?>" href="?lang=km">
                    <span class="badge bg-light text-dark border me-1" style="font-size:0.65rem;">KH</span> ភាសាខ្មែរ
                </a>
            </li>
        </ul>
    </div>

    <a href="<?= url('public/index.php') ?>"
       class="btn btn-outline-light btn-sm rounded-pill px-3 py-1 d-none d-sm-inline-flex align-items-center gap-1 fw-semibold"
       style="font-size:0.78rem; border-color:rgba(255,255,255,0.2);">
        <i class="bi bi-globe2 me-1" style="color:var(--color-accent);"></i>
        <?= __('live_public_site') ?>
    </a>
</div>

<!-- Login Container -->
<div class="login-wrapper w-100 px-3" style="max-width:440px;">

    <!-- Brand Header -->
    <div class="text-center mb-4">
        <div class="login-brand-icon mb-3">NP</div>
        <h1 class="fw-bold mb-1" style="color:#fff; font-size:1.65rem; letter-spacing:-0.03em;">
            <?= __('admin_portal') ?>
        </h1>
        <p style="color:rgba(255,255,255,0.5); font-size:0.875rem; margin:0;">
            <?= __('staff_login') ?>
        </p>
    </div>

    <!-- Login Card -->
    <div class="login-card">
        <div class="card-body p-4 p-md-5">

            <!-- Error / Success Banners -->
            <?php if (!empty($errorMsg)) { ?>
                <div class="alert border-0 py-3 px-3 d-flex align-items-start gap-2 mb-4"
                     style="background:rgba(200,16,46,0.08); color:#a50d25; border-radius:2px;" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
                    <div class="fw-semibold small"><?= e($errorMsg) ?></div>
                </div>
            <?php } ?>

            <?php if (!empty($successMsg)) { ?>
                <div class="alert border-0 py-3 px-3 d-flex align-items-start gap-2 mb-4"
                     style="background:rgba(22,163,74,0.08); color:#15803d; border-radius:2px;" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 flex-shrink-0"></i>
                    <div class="fw-semibold small"><?= e($successMsg) ?></div>
                </div>
            <?php } ?>

            <!-- Login Form -->
            <form action="<?= url('admin/login.php') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                <!-- Username -->
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold"
                           style="color:#1e293b; font-size:0.875rem; margin-bottom:0.4rem;">
                        <?= __('username_label') ?>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text login-input-icon">
                            <i class="bi bi-person-fill" style="font-size:0.95rem;"></i>
                        </span>
                        <input type="text"
                               class="form-control login-input has-left-icon"
                               id="username"
                               name="username"
                               placeholder="<?= __('username_label') ?>"
                               required autofocus autocomplete="username">
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold"
                           style="color:#1e293b; font-size:0.875rem; margin-bottom:0.4rem;">
                        <?= __('password_label') ?>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text login-input-icon">
                            <i class="bi bi-shield-lock-fill" style="font-size:0.95rem;"></i>
                        </span>
                        <input type="password"
                               class="form-control login-input has-both-icons"
                               id="password"
                               name="password"
                               placeholder="••••••••"
                               required autocomplete="current-password">
                        <button class="btn login-input-icon-right px-3 text-muted"
                                type="button"
                                id="togglePasswordBtn"
                                tabindex="-1">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="login-submit-btn d-flex align-items-center justify-content-center gap-2 mb-2">
                    <i class="bi bi-shield-check fs-5"></i>
                    <span><?= __('login_btn') ?></span>
                </button>

            </form>

            <!-- Security Badges -->
            <div class="security-badges mt-3">
                <div class="security-badge-item">
                    <i class="bi bi-lock-fill" style="color:#22c55e;"></i>
                    BCRYPT
                </div>
                <div class="security-badge-item">
                    <i class="bi bi-shield-check" style="color:#3b82f6;"></i>
                    CSRF 2.0
                </div>
                <div class="security-badge-item">
                    <i class="bi bi-cpu-fill" style="color:var(--color-accent);"></i>
                    RBAC Active
                </div>
            </div>

        </div>

        <!-- Card Footer -->
        <div class="text-center py-3 px-4 border-top" style="background:#f8fafc; font-size:0.82rem;">
            <a href="<?= url('public/index.php') ?>"
               class="text-decoration-none fw-semibold d-inline-flex align-items-center gap-1"
               style="color:#64748b;">
                <i class="bi bi-arrow-left"></i>
                <?= __('live_public_site') ?>
            </a>
        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Password Toggle -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const toggleBtn     = document.getElementById('togglePasswordBtn');
    const toggleIcon    = document.getElementById('togglePasswordIcon');

    if (toggleBtn && passwordInput && toggleIcon) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            toggleIcon.classList.toggle('bi-eye', !isPassword);
            toggleIcon.classList.toggle('bi-eye-slash', isPassword);
        });
    }
});
</script>

</body>
</html>
