<?php
/**
 * Progressive Web App (PWA) Install Modal Component
 * news-platform / templates / components / pwa-install-modal.php
 */
?>
<div class="modal fade" id="pwaInstallModal" tabindex="-1" aria-labelledby="pwaInstallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px; overflow: hidden; background: #ffffff;">
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="brand-logo-badge" style="width: 38px; height: 38px; font-size: 0.95rem; border-radius: 4px;">NP</span>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="pwaInstallModalLabel" style="font-size: 1.05rem;">
                            <?= __('pwa_install_modal_title') ?>
                        </h6>
                        <span class="text-2xs text-muted"><?= __('site_tagline') ?></span>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body px-4 py-3">
                <p class="text-secondary small mb-3" style="line-height: 1.6;">
                    <?= __('pwa_install_desc') ?>
                </p>

                <!-- Dynamic Install Trigger Area -->
                <div id="pwaNativeInstallArea" class="mb-3">
                    <button type="button" id="pwaModalInstallBtn" class="btn btn-danger w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" style="border-radius: 4px; font-size: 0.9rem;">
                        <i class="bi bi-download"></i>
                        <span><?= __('pwa_install_btn_action') ?></span>
                    </button>
                </div>

                <!-- Platform Specific Guidance -->
                <div class="bg-light p-3 rounded-2 border text-start" style="font-size: 0.8rem;">
                    <div class="fw-bold text-dark mb-1 d-flex align-items-center gap-1.5">
                        <i class="bi bi-info-circle text-danger"></i>
                        <span>Manual Installation Guide:</span>
                    </div>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 text-muted" style="font-size: 0.76rem;">
                        <li class="d-flex align-items-start gap-1.5">
                            <i class="bi bi-apple text-dark mt-0.5"></i>
                            <span><?= __('pwa_ios_instructions') ?></span>
                        </li>
                        <li class="d-flex align-items-start gap-1.5">
                            <i class="bi bi-laptop text-dark mt-0.5"></i>
                            <span><?= __('pwa_desktop_instructions') ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 pb-3 px-4 justify-content-center">
                <button type="button" class="btn btn-sm btn-link text-muted text-decoration-none" data-bs-dismiss="modal" style="font-size: 0.8rem;">
                    <?= __('btn_cancel') ?? 'Close' ?>
                </button>
            </div>
        </div>
    </div>
</div>
