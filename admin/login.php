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
    <!-- Bootstrap Icons for password toggle -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter & Kantumruy Pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">

    <style>
        :root {
            --brand-red: #c8102e;
            --brand-red-hover: #a50d25;
            --navy-bg: #0f172a;
        }

        body.login-page {
            font-family: 'Inter', 'Kantumruy Pro', sans-serif;
            background-color: var(--navy-bg);
            color: #1e293b;
            min-height: 100vh;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-top: 3px solid var(--brand-red);
            border-radius: 6px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        .brand-badge {
            width: 48px;
            height: 48px;
            background: var(--brand-red);
            color: #ffffff;
            font-size: 1.25rem;
            font-weight: 800;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            letter-spacing: -0.02em;
        }

        .form-control-clean {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 0.65rem 0.85rem;
            font-size: 0.9rem;
            color: #0f172a;
            transition: all 0.15s ease-in-out;
        }

        .form-control-clean:focus {
            background-color: #ffffff;
            border-color: var(--brand-red);
            box-shadow: 0 0 0 2px rgba(200, 16, 46, 0.12);
        }

        .btn-brand {
            background-color: var(--brand-red);
            border: none;
            color: #ffffff;
            border-radius: 4px;
            padding: 0.7rem 1rem;
            font-weight: 600;
            font-size: 0.9rem;
            width: 100%;
            transition: background-color 0.15s ease-in-out;
        }

        .btn-brand:hover, .btn-brand:focus {
            background-color: var(--brand-red-hover);
            color: #ffffff;
        }

        .toggle-pw-btn {
            background: transparent;
            border: 1px solid #cbd5e1;
            border-left: none;
            border-radius: 0 4px 4px 0;
            color: #64748b;
            padding: 0 0.85rem;
        }
        .toggle-pw-btn:hover {
            color: #0f172a;
            background-color: #f1f5f9;
        }

        .input-pw-clean {
            border-radius: 4px 0 0 4px !important;
        }

        .top-nav-controls {
            position: fixed;
            top: 1rem;
            right: 1.5rem;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }
        .top-nav-controls a, .top-nav-controls button {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.15s ease-in-out;
        }
        .top-nav-controls a:hover, .top-nav-controls button:hover {
            color: #ffffff;
        }
    </style>
</head>
<body class="login-page d-flex align-items-center justify-content-center py-5">

<!-- Top Controls -->
<div class="top-nav-controls">
    <div class="lang-select-box d-inline-flex align-items-center gap-1 px-2.5 py-1 rounded">
        <i class="bi bi-globe2 text-white-50" style="font-size: 0.8rem;"></i>
        <select class="form-select form-select-sm border-0 bg-transparent text-white shadow-none py-0 ps-1 pe-4"
                id="loginTopLangSelect"
                style="font-size: 0.8rem; font-weight: 600; cursor: pointer; background-image: url('data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 16 16\' fill=\'%23ffffff\'%3e%3cpath fill-rule=\'evenodd\' d=\'M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z\'/%3e%3c/svg%3e'); background-size: 10px;"
                onchange="if(this.value) window.location.href=this.value;">
            <option value="<?= lang_url('en') ?>" <?= $currentLang === 'en' ? 'selected' : '' ?> class="text-dark fw-semibold">
                English (EN)
            </option>
            <option value="<?= lang_url('kh') ?>" <?= ($currentLang === 'kh' || $currentLang === 'km') ? 'selected' : '' ?> class="text-dark fw-semibold">
                ភាសាខ្មែរ (KH)
            </option>
        </select>
    </div>
    <span class="text-white-50 opacity-50">|</span>
    <a href="<?= url('public/index.php') ?>" class="text-white-50 text-decoration-none">
        <?= __('live_public_site') ?> &rarr;
    </a>
</div>

<!-- Login Container -->
<div class="login-wrapper w-100 px-3" style="max-width: 400px;">

    <!-- Brand Header -->
    <div class="text-center mb-4">
        <div class="brand-badge mb-3">CMA</div>
        <h1 class="h4 fw-bold text-white mb-1">
            <?= __('admin_portal') ?>
        </h1>
        <p class="text-white-50 small mb-0">
            <?= __('staff_login') ?>
        </p>
    </div>

    <!-- Login Card -->
    <div class="login-card">
        <div class="card-body p-4">

            <!-- Error / Success Messages -->
            <?php if (!empty($errorMsg)) { ?>
                <div class="alert alert-danger border-0 small py-2 px-3 mb-3" style="border-radius: 4px;" role="alert">
                    <?= e(__($errorMsg)) ?>
                </div>
            <?php } ?>

            <?php if (!empty($successMsg)) { ?>
                <div class="alert alert-success border-0 small py-2 px-3 mb-3" style="border-radius: 4px;" role="alert">
                    <?= e(__($successMsg)) ?>
                </div>
            <?php } ?>

            <!-- Form -->
            <form action="<?= url('admin/login.php') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                <!-- Username -->
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold small text-dark">
                        <?= __('username_label') ?>
                    </label>
                    <input type="text"
                           class="form-control form-control-clean"
                           id="username"
                           name="username"
                           placeholder="<?= __('username_label') ?>"
                           required autofocus autocomplete="username">
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold small text-dark">
                        <?= __('password_label') ?>
                    </label>
                    <div class="input-group">
                        <input type="password"
                               class="form-control form-control-clean input-pw-clean"
                               id="password"
                               name="password"
                               placeholder="••••••••"
                               required autocomplete="current-password">
                        <button class="btn toggle-pw-btn" type="button" id="togglePasswordBtn" tabindex="-1">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-brand">
                    <?= __('login_btn') ?>
                </button>
            </form>

        </div>

        <!-- Footer link -->
        <div class="text-center py-2 px-3 border-top bg-light" style="font-size: 0.8rem;">
            <a href="<?= url('public/index.php') ?>" class="text-secondary text-decoration-none">
                &larr; <?= __('live_public_site') ?>
            </a>
        </div>
    </div>

</div>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Password Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Password Toggle
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
