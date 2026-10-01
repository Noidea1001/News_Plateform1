<?php
/**
 * Public Reader Login View
 * news-platform / templates / views / reader-login.php
 */
?>

<div class="py-5" style="background-color: #f8fafc; min-height: 75vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <div class="text-center mb-4">
                    <a href="<?= url('index.php') ?>" class="d-inline-flex align-items-center gap-2 text-decoration-none mb-2">
                        <span class="fs-3 fw-black text-danger font-serif">NEWS</span>
                        <span class="badge bg-danger text-white fs-6 py-1 px-2">PLATFORM</span>
                    </a>
                    <h2 class="editorial-title fw-bold text-dark mb-1"><?= __('sign_in') ?></h2>
                    <p class="text-muted small"><?= __('sign_in_reader_subtitle') ?></p>
                </div>

                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 p-md-5">

                    <?php if (!empty($error)) { ?>
                        <div class="alert alert-danger py-2.5 px-3 rounded-2 small d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                            <div><?= e($error) ?></div>
                        </div>
                    <?php } ?>

                    <?php if (!empty($msg)) { ?>
                        <div class="alert alert-success py-2.5 px-3 rounded-2 small d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                            <div><?= e($msg) ?></div>
                        </div>
                    <?php } ?>

                    <form action="<?= url('login.php') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold text-dark"><?= __('email_address') ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control border-start-0" id="email" name="email"
                                    placeholder="name@example.com" value="<?= e($_POST['email'] ?? '') ?>" required autofocus>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label small fw-bold text-dark"><?= __('password_label') ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-lock"></i></span>
                                <input type="password" class="form-control border-start-0" id="password" name="password"
                                    placeholder="••••••••" required>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-danger py-2.5 fw-bold rounded-2 shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i> <?= __('sign_in') ?>
                            </button>
                        </div>

                        <!-- Register Link -->
                        <div class="text-center small text-muted">
                            <?= __('no_account_yet') ?> 
                            <a href="<?= url('register.php') ?>" class="text-danger fw-bold text-decoration-none">
                                <?= __('create_account') ?>
                            </a>
                        </div>
                    </form>

                    <!-- CMS Staff Portal notice -->
                    <div class="border-top pt-3 mt-4 text-center">
                        <span class="text-2xs text-muted">
                            <i class="bi bi-shield-check text-secondary me-1"></i>
                            <?= __('cms_staff_link_prompt') ?> 
                            <a href="<?= url('admin/login.php') ?>" class="text-secondary fw-semibold text-decoration-underline">
                                <?= __('cms_staff_login_link') ?>
                            </a>
                        </span>
                    </div>

                </div>

                <div class="text-center mt-3">
                    <a href="<?= url('index.php') ?>" class="small text-muted text-decoration-none">
                        &larr; <?= __('nav_home') ?>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
