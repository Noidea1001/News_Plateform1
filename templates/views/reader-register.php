<?php
/**
 * Public Reader Registration View
 * news-platform / templates / views / reader-register.php
 */
?>

<div class="py-4 py-md-5" style="background-color: #f8fafc; min-height: 75vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5" style="max-width: 440px;">

                <div class="text-center mb-3">
                    <a href="<?= url('index.php') ?>" class="d-inline-flex align-items-center gap-2 text-decoration-none mb-2">
                        <span class="fs-4 fw-black text-danger font-serif">NEWS</span>
                        <span class="badge bg-danger text-white fs-6 py-1 px-2">PLATFORM</span>
                    </a>
                    <h3 class="editorial-title fw-bold text-dark mb-1"><?= __('create_account') ?></h3>
                    <p class="text-muted small mb-0"><?= __('register_reader_subtitle') ?></p>
                </div>

                <div class="card border-0 shadow-sm rounded-3 bg-white p-4">

                    <?php if (!empty($error)) { ?>
                        <div class="alert alert-danger py-2 px-3 rounded-2 small d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                            <div><?= e($error) ?></div>
                        </div>
                    <?php } ?>

                    <form action="<?= url('register.php') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label small fw-semibold text-dark mb-1">
                                <?= __('full_name') ?> <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control rounded-2" id="name" name="name"
                                placeholder="<?= e(__('name_placeholder')) ?>" value="<?= e($_POST['name'] ?? '') ?>" required autofocus>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-semibold text-dark mb-1">
                                <?= __('email_address') ?> <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control rounded-2" id="email" name="email"
                                placeholder="name@example.com" value="<?= e($_POST['email'] ?? '') ?>" required>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label small fw-semibold text-dark mb-1">
                                <?= __('password_label') ?> <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control rounded-2" id="password" name="password"
                                placeholder="••••••••" minlength="6" required>
                            <div class="text-muted text-3xs mt-1"><?= __('password_min_length_hint') ?></div>
                        </div>

                        <!-- Password Confirmation -->
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label small fw-semibold text-dark mb-1">
                                <?= __('confirm_password_label') ?> <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control rounded-2" id="password_confirm" name="password_confirm"
                                placeholder="••••••••" minlength="6" required>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mb-3 pt-1">
                            <button type="submit" class="btn btn-danger py-2 fw-bold rounded-2 shadow-2xs">
                                <?= __('create_account') ?>
                            </button>
                        </div>

                        <!-- Sign In Link -->
                        <div class="text-center small text-muted">
                            <?= __('already_have_account') ?> 
                            <a href="<?= url('login.php') ?>" class="text-danger fw-bold text-decoration-none">
                                <?= __('sign_in') ?>
                            </a>
                        </div>
                    </form>

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
