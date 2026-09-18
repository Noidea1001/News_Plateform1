<?php
/**
 * CMS Staff Login Portal (CMA Authentication)
 * news-platform / admin / login.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../src/Core/helpers.php';
require_once __DIR__ . '/../languages/common.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/TemplateEngine.php';
require_once __DIR__ . '/../src/Core/Auth.php';

use App\Core\Auth;

$currentLang = $_SESSION['lang'] ?? 'en';

// If already logged in, redirect to dashboard
if (Auth::check()) {
    header('Location: ' . url('admin/dashboard.php'));
    exit;
}

$errorMsg = $_GET['error'] ?? null;
$successMsg = $_GET['msg'] ?? null;

// Handle POST authentication request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $token = $_POST['csrf_token'] ?? '';

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
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Sans+Khmer:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('public/assets/css/style.css') ?>">
    <style>
        body.login-bg {
            background: radial-gradient(circle at 50% 20%, #1e293b 0%, #0f172a 70%, #020617 100%);
            font-family: 'Inter', 'Noto Sans Khmer', sans-serif;
        }
        .login-card-glass {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
        }
        .security-badge-grid {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }
    </style>
</head>
<body class="login-bg d-flex align-items-center justify-content-center min-vh-100 py-5">

<!-- Floating Top-Right Language & CDA Controls -->
<div class="position-absolute top-0 end-0 p-3 p-md-4 d-flex align-items-center gap-2">
    <div class="dropdown">
        <button class="btn btn-outline-light btn-sm rounded-pill px-3 py-1.5 dropdown-toggle border-secondary shadow-sm fw-semibold" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-translate text-warning me-1"></i>
            <span><?= $currentLang === 'km' ? 'ភាសាខ្មែរ' : 'English' ?></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0 text-sm mt-2">
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2 <?= $currentLang === 'en' ? 'active fw-bold bg-danger text-white' : '' ?>" href="?lang=en">
                    <span class="badge bg-secondary-subtle text-dark border me-1">EN</span> English
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2 <?= $currentLang === 'km' ? 'active fw-bold bg-danger text-white' : '' ?>" href="?lang=km">
                    <span class="badge bg-secondary-subtle text-dark border me-1">KM</span> ភាសាខ្មែរ (Khmer)
                </a>
            </li>
        </ul>
    </div>

    <a href="<?= url('public/index.php') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 text-white border-secondary d-none d-sm-inline-flex align-items-center gap-1 shadow-sm">
        <i class="bi bi-globe2 text-danger"></i>
        <span><?= __('live_public_site') ?></span>
    </a>
</div>

<div class="container px-3" style="max-width: 450px;">
    
    <!-- Branding Header -->
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-danger text-white fw-extrabold rounded-4 shadow-lg mb-2" style="width:58px; height:58px; font-size: 1.6rem;">
            NP
        </div>
        <h2 class="fw-bold text-white editorial-title mb-1 fs-3"><?= __('admin_portal') ?></h2>
        <p class="text-slate-400 text-white-50 small mb-0"><?= __('staff_login') ?></p>
    </div>

    <!-- Glassmorphic Card -->
    <div class="card login-card-glass border-0 overflow-hidden">
        <div class="card-body p-4 p-md-4.5">

            <!-- Security Alert Banners -->
            <?php if (!empty($errorMsg)) { ?>
                <div class="alert alert-danger rounded-3 py-2.5 px-3 small d-flex align-items-center gap-2.5 mb-3 border-0 bg-danger bg-opacity-10 text-danger" role="alert">
                    <i class="bi bi-shield-x fs-5 flex-shrink-0"></i>
                    <div class="fw-semibold"><?= e($errorMsg) ?></div>
                </div>
            <?php } ?>

            <?php if (!empty($successMsg)) { ?>
                <div class="alert alert-success rounded-3 py-2.5 px-3 small d-flex align-items-center gap-2.5 mb-3 border-0 bg-success bg-opacity-10 text-success" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 flex-shrink-0"></i>
                    <div class="fw-semibold"><?= e($successMsg) ?></div>
                </div>
            <?php } ?>

            <!-- Login Credentials Form -->
            <form action="<?= url('admin/login.php') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                <!-- Username Input -->
                <div class="mb-3">
                    <label for="username" class="form-label fw-bold small text-dark"><?= __('username_label') ?></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 ps-3 text-muted"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 py-2.5 text-dark fw-medium" 
                               id="username" name="username" placeholder="<?= __('username_label') ?>" required autofocus>
                    </div>
                </div>

                <!-- Password Input with Interactive Eye Toggle -->
                <div class="mb-4">
                    <label for="password" class="form-label fw-bold small text-dark"><?= __('password_label') ?></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 ps-3 text-muted"><i class="bi bi-shield-lock-fill"></i></span>
                        <input type="password" class="form-control bg-light border-start-0 border-end-0 py-2.5 text-dark fw-medium" 
                               id="password" name="password" placeholder="••••••••" required>
                        <button class="btn btn-light border border-start-0 pe-3 text-muted" type="button" id="togglePasswordBtn">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn gradient-btn-danger text-white py-2.5 fw-bold rounded-3 d-flex align-items-center justify-content-center gap-2 shadow">
                        <i class="bi bi-shield-check fs-5"></i>
                        <span><?= __('login_btn') ?></span>
                    </button>
                </div>

            </form>

            <!-- Security Assurance Indicators Bar -->
            <div class="row g-2 mt-2 pt-2 border-top text-center text-xs text-muted">
                <div class="col-4">
                    <i class="bi bi-lock-fill text-success me-1"></i> BCRYPT
                </div>
                <div class="col-4">
                    <i class="bi bi-shield-check text-primary me-1"></i> CSRF 2.0
                </div>
                <div class="col-4">
                    <i class="bi bi-cpu-fill text-danger me-1"></i> RBAC Active
                </div>
            </div>

        </div>
        <div class="card-footer bg-light py-2.5 text-center text-muted text-xs border-top">
            <a href="<?= url('public/index.php') ?>" class="text-secondary text-decoration-none fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> <?= __('live_public_site') ?>
            </a>
        </div>
    </div>

</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Interactive Password Eye Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const toggleIcon = document.getElementById('togglePasswordIcon');

    if (toggleBtn && passwordInput && toggleIcon) {
        toggleBtn.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            if (type === 'text') {
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        });
    }
});
</script>

</body>
</html>
