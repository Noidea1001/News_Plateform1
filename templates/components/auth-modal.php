<?php
/**
 * Clean Reader Authentication Modal (Popup)
 * news-platform / templates / components / auth-modal.php
 */
$csrfToken = \App\Core\Auth::generateCsrfToken();
$currentRequestUri = $_SERVER['REQUEST_URI'] ?? url('index.php');
?>

<div class="modal fade" id="readerAuthModal" tabindex="-1" aria-labelledby="readerAuthModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 430px;">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            
            <!-- Modal Header: Compact Clean Tabs -->
            <div class="modal-header border-bottom bg-light px-4 py-3 align-items-center justify-content-between">
                <ul class="nav nav-pills gap-1 flex-nowrap" id="authModalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-1.5 px-3 fw-bold small text-nowrap rounded-2"
                                id="tab-login-btn" data-bs-toggle="pill" data-bs-target="#tab-login-pane"
                                type="button" role="tab" aria-controls="tab-login-pane" aria-selected="true">
                            <?= __('tab_sign_in') ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-1.5 px-3 fw-bold small text-nowrap rounded-2 text-dark"
                                id="tab-register-btn" data-bs-toggle="pill" data-bs-target="#tab-register-pane"
                                type="button" role="tab" aria-controls="tab-register-pane" aria-selected="false">
                            <?= __('tab_register') ?>
                        </button>
                    </li>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <!-- Alert Feedback Box -->
                <div id="authModalAlert" class="alert d-none py-2 px-3 small rounded-2 mb-3" role="alert"></div>

                <div class="tab-content" id="authModalTabContent">
                    
                    <!-- ── TAB 1: SIGN IN ─────────────────────────────────────────── -->
                    <div class="tab-pane fade show active" id="tab-login-pane" role="tabpanel" aria-labelledby="tab-login-btn">
                        <div class="mb-3">
                            <h5 class="fw-bold text-dark mb-1"><?= __('login_reader_title') ?></h5>
                            <p class="text-muted small mb-0"><?= __('sign_in_reader_subtitle') ?></p>
                        </div>

                        <form id="modalLoginForm" action="<?= url('login.php') ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                            <input type="hidden" name="redirect_to" value="<?= e($currentRequestUri) ?>">
                            <input type="hidden" name="ajax" value="1">

                            <div class="mb-3">
                                <label for="modalLoginEmail" class="form-label small fw-semibold text-dark mb-1">
                                    <?= __('email_address') ?> <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control rounded-2" id="modalLoginEmail" name="email"
                                    placeholder="name@example.com" required autocomplete="email">
                            </div>

                            <div class="mb-3">
                                <label for="modalLoginPassword" class="form-label small fw-semibold text-dark mb-1">
                                    <?= __('password_label') ?> <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control rounded-2" id="modalLoginPassword" name="password"
                                    placeholder="••••••••" required autocomplete="current-password">
                            </div>

                            <div class="d-grid mb-3 pt-1">
                                <button type="submit" id="modalLoginSubmit" class="btn btn-danger py-2 fw-bold rounded-2">
                                    <?= __('sign_in') ?>
                                </button>
                            </div>

                            <div class="text-center small text-muted">
                                <?= __('no_account_yet') ?>
                                <a href="javascript:void(0)" onclick="switchAuthTab('register')" class="text-danger fw-bold text-decoration-none">
                                    <?= __('create_account') ?>
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- ── TAB 2: REGISTER ───────────────────────────────────────── -->
                    <div class="tab-pane fade" id="tab-register-pane" role="tabpanel" aria-labelledby="tab-register-btn">
                        <div class="mb-3">
                            <h5 class="fw-bold text-dark mb-1"><?= __('create_account_title') ?></h5>
                            <p class="text-muted small mb-0"><?= __('register_reader_subtitle') ?></p>
                        </div>

                        <form id="modalRegisterForm" action="<?= url('register.php') ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                            <input type="hidden" name="redirect_to" value="<?= e($currentRequestUri) ?>">
                            <input type="hidden" name="ajax" value="1">

                            <div class="mb-2.5">
                                <label for="modalRegName" class="form-label small fw-semibold text-dark mb-1">
                                    <?= __('full_name') ?> <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control rounded-2" id="modalRegName" name="name"
                                    placeholder="<?= e(__('name_placeholder')) ?>" required autocomplete="name">
                            </div>

                            <div class="mb-2.5">
                                <label for="modalRegEmail" class="form-label small fw-semibold text-dark mb-1">
                                    <?= __('email_address') ?> <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control rounded-2" id="modalRegEmail" name="email"
                                    placeholder="name@example.com" required autocomplete="email">
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label for="modalRegPassword" class="form-label small fw-semibold text-dark mb-1">
                                        <?= __('password_label') ?> <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control rounded-2" id="modalRegPassword" name="password"
                                        placeholder="••••••••" minlength="6" required autocomplete="new-password">
                                </div>
                                <div class="col-sm-6">
                                    <label for="modalRegPasswordConfirm" class="form-label small fw-semibold text-dark mb-1">
                                        <?= __('confirm_password_label') ?> <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control rounded-2" id="modalRegPasswordConfirm" name="password_confirm"
                                        placeholder="••••••••" minlength="6" required autocomplete="new-password">
                                </div>
                                <div class="col-12">
                                    <span class="text-muted text-3xs"><?= __('password_min_length_hint') ?></span>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" id="modalRegSubmit" class="btn btn-danger py-2 fw-bold rounded-2">
                                    <?= __('create_account') ?>
                                </button>
                            </div>

                            <div class="text-center small text-muted">
                                <?= __('already_have_account') ?>
                                <a href="javascript:void(0)" onclick="switchAuthTab('login')" class="text-danger fw-bold text-decoration-none">
                                    <?= __('sign_in') ?>
                                </a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
function switchAuthTab(tab) {
    const loginBtn = document.getElementById('tab-login-btn');
    const regBtn = document.getElementById('tab-register-btn');
    const alertBox = document.getElementById('authModalAlert');
    if (alertBox) {
        alertBox.classList.add('d-none');
        alertBox.textContent = '';
    }

    if (tab === 'register') {
        const tabTrigger = new bootstrap.Tab(regBtn);
        tabTrigger.show();
        loginBtn.classList.remove('active', 'text-white');
        loginBtn.classList.add('text-dark');
        regBtn.classList.add('active');
        regBtn.classList.remove('text-dark');
    } else {
        const tabTrigger = new bootstrap.Tab(loginBtn);
        tabTrigger.show();
        regBtn.classList.remove('active', 'text-white');
        regBtn.classList.add('text-dark');
        loginBtn.classList.add('active');
        loginBtn.classList.remove('text-dark');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('readerAuthModal');
    if (!modalEl) return;

    modalEl.addEventListener('show.bs.modal', function(event) {
        const triggerBtn = event.relatedTarget;
        const requestedTab = triggerBtn ? triggerBtn.getAttribute('data-auth-tab') : null;
        if (requestedTab === 'register') {
            switchAuthTab('register');
        } else {
            switchAuthTab('login');
        }
    });

    function setupAuthForm(formId, submitBtnId) {
        const form = document.getElementById(formId);
        const submitBtn = document.getElementById(submitBtnId);
        const alertBox = document.getElementById('authModalAlert');
        if (!form || !submitBtn) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> ...';

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(res) {
                return res.json().catch(function() {
                    // Fallback to reload if not JSON
                    window.location.reload();
                });
            })
            .then(function(data) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;

                if (!data) return;

                if (data.success) {
                    alertBox.className = 'alert alert-success py-2 px-3 small rounded-2 mb-3';
                    alertBox.textContent = data.message || 'Success!';
                    alertBox.classList.remove('d-none');
                    setTimeout(function() {
                        window.location.href = data.redirect || window.location.href;
                    }, 600);
                } else {
                    alertBox.className = 'alert alert-danger py-2 px-3 small rounded-2 mb-3';
                    alertBox.textContent = data.message || 'Authentication error occurred.';
                    alertBox.classList.remove('d-none');
                }
            })
            .catch(function(err) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                if (alertBox) {
                    alertBox.className = 'alert alert-danger py-2 px-3 small rounded-2 mb-3';
                    alertBox.textContent = 'Connection error. Please try again.';
                    alertBox.classList.remove('d-none');
                }
            });
        });
    }

    setupAuthForm('modalLoginForm', 'modalLoginSubmit');
    setupAuthForm('modalRegisterForm', 'modalRegSubmit');
});
</script>
