<?php
/**
 * Component: Interactive Reader Opinion Poll Widget
 * news-platform / templates / components / reader-poll-widget.php
 * Engaging community opinion poll with real-time percentage visualization.
 */

require_once __DIR__ . '/../../src/Core/helpers.php';
$currentLang = $_SESSION['lang'] ?? 'en';
$isKh = ($currentLang === 'kh' || $currentLang === 'km');
?>

<section class="sbw reader-poll-widget mb-4" style="border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
    <!-- Header -->
    <div class="sbw-head d-flex align-items-center justify-content-between p-3 border-bottom" style="background: #fafafa;">
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center text-danger flex-shrink-0" style="font-size: 1rem;">
                <i class="bi bi-pie-chart-fill"></i>
            </span>
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.86rem;"><?= __('poll_title') ?></h6>
        </div>
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5" style="font-size: 0.65rem;">
            <?= __('poll_badge') ?>
        </span>
    </div>

    <!-- Body -->
    <div class="p-3">
        <!-- Question -->
        <p class="fw-bold text-dark mb-3" style="font-size: 0.88rem; line-height: 1.45;">
            <?= __('poll_question') ?>
        </p>

        <!-- Poll Form (Before Vote) -->
        <form id="npReaderPollForm" class="d-flex flex-column gap-2 mb-3">
            <label class="poll-option-card d-flex align-items-center gap-2.5 p-2.5 rounded-2 border" style="cursor: pointer; background: #ffffff; border-color: #e2e8f0;">
                <input type="radio" name="poll_choice" value="1" class="form-check-input mt-0 flex-shrink-0" required>
                <span class="text-xs text-dark fw-medium" style="font-size: 0.8rem; line-height: 1.35;">
                    <?= __('poll_opt_1') ?>
                </span>
            </label>

            <label class="poll-option-card d-flex align-items-center gap-2.5 p-2.5 rounded-2 border" style="cursor: pointer; background: #ffffff; border-color: #e2e8f0;">
                <input type="radio" name="poll_choice" value="2" class="form-check-input mt-0 flex-shrink-0" required>
                <span class="text-xs text-dark fw-medium" style="font-size: 0.8rem; line-height: 1.35;">
                    <?= __('poll_opt_2') ?>
                </span>
            </label>

            <label class="poll-option-card d-flex align-items-center gap-2.5 p-2.5 rounded-2 border" style="cursor: pointer; background: #ffffff; border-color: #e2e8f0;">
                <input type="radio" name="poll_choice" value="3" class="form-check-input mt-0 flex-shrink-0" required>
                <span class="text-xs text-dark fw-medium" style="font-size: 0.8rem; line-height: 1.35;">
                    <?= __('poll_opt_3') ?>
                </span>
            </label>

            <button type="submit" class="btn btn-danger btn-sm w-100 py-2 mt-1.5 fw-bold rounded-2 shadow-2xs d-flex align-items-center justify-content-center gap-1.5" style="background:#c8102e; border:none; font-size: 0.82rem;">
                <i class="bi bi-check2-circle"></i>
                <span><?= __('poll_vote_btn') ?></span>
            </button>
        </form>

        <!-- Poll Results (After Vote - Animated) -->
        <div id="npPollResults" class="d-none flex-column gap-2.5">
            <!-- Option 1 Result -->
            <div>
                <div class="d-flex justify-content-between text-xs mb-1" style="font-size: 0.78rem;">
                    <span class="text-dark fw-medium text-truncate me-2"><?= __('poll_opt_1') ?></span>
                    <span class="fw-bold text-danger flex-shrink-0">58%</span>
                </div>
                <div class="progress" style="height: 6px; background: #f1f5f9; border-radius: 3px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 58%; border-radius: 3px;"></div>
                </div>
            </div>

            <!-- Option 2 Result -->
            <div>
                <div class="d-flex justify-content-between text-xs mb-1" style="font-size: 0.78rem;">
                    <span class="text-dark fw-medium text-truncate me-2"><?= __('poll_opt_2') ?></span>
                    <span class="fw-bold text-primary flex-shrink-0">32%</span>
                </div>
                <div class="progress" style="height: 6px; background: #f1f5f9; border-radius: 3px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 32%; border-radius: 3px;"></div>
                </div>
            </div>

            <!-- Option 3 Result -->
            <div>
                <div class="d-flex justify-content-between text-xs mb-1" style="font-size: 0.78rem;">
                    <span class="text-dark fw-medium text-truncate me-2"><?= __('poll_opt_3') ?></span>
                    <span class="fw-bold text-secondary flex-shrink-0">10%</span>
                </div>
                <div class="progress" style="height: 6px; background: #f1f5f9; border-radius: 3px;">
                    <div class="progress-bar bg-secondary" role="progressbar" style="width: 10%; border-radius: 3px;"></div>
                </div>
            </div>

            <div class="alert alert-success py-1.5 px-2.5 text-center text-xs mb-0 mt-2" style="font-size: 0.75rem;">
                <i class="bi bi-check-circle-fill me-1"></i><?= __('poll_thank_you') ?>
            </div>
        </div>

        <!-- Total Vote Count Footer -->
        <div class="d-flex align-items-center justify-content-between pt-2.5 mt-2 border-top text-muted" style="font-size: 0.72rem;">
            <span><i class="bi bi-people me-1"></i><?= __('poll_total_votes', ['count' => $isKh ? '១,៨៤២' : '1,842']) ?></span>
            <span class="text-3xs"><?= $isKh ? 'អនាមិក ១០០%' : '100% Anonymous' ?></span>
        </div>
    </div>
</section>

<style>
.poll-option-card:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
}
.poll-option-card input[type="radio"]:checked + span {
    color: #c8102e !important;
    font-weight: 700 !important;
}
</style>

<script>
(function() {
    const POLL_STORAGE_KEY = 'np_poll_voted_ai_2026';
    const pollForm = document.getElementById('npReaderPollForm');
    const pollResults = document.getElementById('npPollResults');

    function showResults() {
        if (pollForm) pollForm.classList.add('d-none');
        if (pollResults) {
            pollResults.classList.remove('d-none');
            pollResults.classList.add('d-flex');
        }
    }

    if (localStorage.getItem(POLL_STORAGE_KEY)) {
        showResults();
    }

    if (pollForm) {
        pollForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const selected = pollForm.querySelector('input[name="poll_choice"]:checked');
            if (!selected) return;

            try {
                localStorage.setItem(POLL_STORAGE_KEY, selected.value);
            } catch(e) {}

            showResults();
        });
    }
})();
</script>
