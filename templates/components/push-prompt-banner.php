<?php
/**
 * Real-Time Push Notification Permission Opt-In Banner & iOS Guide Modal
 * news-platform / templates / components / push-prompt-banner.php
 */
?>
<!-- ── Floating Push Notification Opt-In Prompt Card ────── -->
<div id="npPushNotificationPrompt"
     class="position-fixed bottom-0 end-0 m-3 p-3 bg-white shadow-lg border rounded-3 d-none transition-all"
     style="z-index: 9999; max-width: 380px; box-shadow: 0 10px 30px rgba(0,0,0,0.18) !important;">
    <div class="d-flex align-items-start gap-3">
        <div class="rounded-circle bg-danger bg-opacity-10 p-2.5 text-danger flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-bell-fill fs-5"></i>
        </div>
        <div class="flex-grow-1 min-w-0">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <h6 class="fw-bold text-dark mb-0 fs-6"><?= __('push_alerts_title') ?></h6>
                <button type="button" class="btn-close btn-close-sm" id="dismissPushPromptBtn" aria-label="Close" style="font-size: 0.65rem;"></button>
            </div>
            <p class="text-secondary small mb-2" style="font-size: 0.8rem; line-height: 1.45;">
                <?= __('push_alerts_enable_prompt') ?>
            </p>
            <div id="iosSafariPushHint" class="d-none alert alert-light border py-1.5 px-2 mb-2 text-2xs text-muted">
                <i class="bi bi-apple me-1 text-dark"></i><?= __('push_alerts_ios_hint') ?>
            </div>
            <div class="d-flex align-items-center gap-2 mt-2">
                <button type="button" id="acceptPushPromptBtn" class="btn btn-sm btn-danger fw-bold px-3 py-1 text-xs d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="bi bi-bell"></i>
                    <span><?= __('push_alerts_prompt_btn') ?></span>
                </button>
                <button type="button" id="laterPushPromptBtn" class="btn btn-sm btn-outline-secondary px-2.5 py-1 text-xs">
                    <?= __('push_alerts_later_btn') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── iOS Home Screen & Notification Guide Modal ────── -->
<div class="modal fade" id="iosPushGuideModal" tabindex="-1" aria-labelledby="iosPushGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header border-0 pb-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="brand-logo-badge" style="width: 36px; height: 36px; font-size: 0.9rem; border-radius: 4px;">NP</span>
                    <h6 class="modal-title fw-bold text-dark mb-0 fs-6" id="iosPushGuideModalLabel">
                        <?= __('push_alerts_ios_title') ?>
                    </h6>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="text-secondary small mb-3" style="line-height: 1.5;">
                    <?= __('push_alerts_enable_prompt') ?>
                </p>
                <div class="list-group list-group-flush rounded-3 border mb-3">
                    <div class="list-group-item d-flex align-items-start gap-2.5 py-2.5">
                        <span class="badge bg-danger rounded-circle p-1.5 text-white fw-bold" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                        <div class="small text-dark">
                            <strong><?= __('push_alerts_ios_step1') ?></strong>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-start gap-2.5 py-2.5">
                        <span class="badge bg-danger rounded-circle p-1.5 text-white fw-bold" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                        <div class="small text-dark">
                            <strong><?= __('push_alerts_ios_step2') ?></strong>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-start gap-2.5 py-2.5">
                        <span class="badge bg-danger rounded-circle p-1.5 text-white fw-bold" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                        <div class="small text-dark">
                            <strong><?= __('push_alerts_ios_step3') ?></strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-3 px-4 justify-content-center">
                <button type="button" class="btn btn-sm btn-secondary px-4 fw-semibold" data-bs-dismiss="modal">
                    <?= __('btn_cancel') ?? 'OK' ?>
                </button>
            </div>
        </div>
    </div>
</div>
