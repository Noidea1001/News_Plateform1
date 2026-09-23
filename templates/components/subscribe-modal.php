<?php
/**
 * Component: Public Reader Feed Subscription Modal (AJAX Handler)
 * news-platform / templates / components / subscribe-modal.php
 */
?>

<div class="modal fade" id="subscribeModal" tabindex="-1" aria-labelledby="subscribeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="overflow: hidden !important; border-radius: 4px !important;">
            <div class="modal-header bg-brand-navy text-white p-4 position-relative" style="border-top-left-radius: inherit; border-top-right-radius: inherit;">
                <div>
                    <span class="badge bg-danger text-uppercase px-2 py-1 mb-2"><?= __('feed_sub_title') ?></span>
                    <h5 class="modal-title fw-bold editorial-title h4 mb-0" id="subscribeModalLabel"><?= __('modal_headline') ?></h5>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary small mb-4">
                    <?= __('modal_desc') ?>
                </p>

                <!-- Dynamic Alert Feedback Box -->
                <div id="subscribeAlert" class="alert d-none py-2 px-3 small mb-3" role="alert"></div>

                <form id="subscribeForm" action="<?= url('public/subscribe.php') ?>" method="POST">
                    <div class="mb-3">
                        <label for="subEmail" class="form-label fw-semibold small text-dark"><?= __('email_label') ?> <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="subEmail" name="email" placeholder="name@example.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="subCategory" class="form-label fw-semibold small text-dark"><?= __('topic_pref_label') ?></label>
                        <select class="form-select" id="subCategory" name="category_preference">
                            <option value=""><?= __('all_topics_option') ?></option>
                            <?php 
                            $mCatDb = \App\Core\Database::getInstance();
                            $mCats = $mCatDb->fetchAll("SELECT * FROM categories ORDER BY name ASC");
                            foreach ($mCats as $mC) {
                            ?>
                                <option value="<?= (int)$mC['id'] ?>"><?= e(cat_name($mC['name'])) ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" id="subSubmitBtn" class="btn btn-danger py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= __('btn_confirm_sub') ?></span>
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light py-2 px-4 justify-content-between text-muted small">
                <span><i class="bi bi-shield-check text-success me-1"></i> <?= __('privacy_guaranteed') ?></span>
                <span><?= __('direct_feed_endpoint') ?></span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subForm = document.getElementById('subscribeForm');
    const subAlert = document.getElementById('subscribeAlert');
    const subSubmitBtn = document.getElementById('subSubmitBtn');

    if (subForm) {
        subForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('subEmail').value.trim();
            const category = document.getElementById('subCategory').value;

            if (!email) {
                showAlert('danger', 'Please enter a valid email address.');
                return;
            }

            // Disable button during transit
            subSubmitBtn.disabled = true;
            subSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Submitting...';

            const formData = new FormData();
            formData.append('email', email);
            formData.append('category_preference', category);

            const subscribeUrl = subForm.getAttribute('action') || '<?= url('public/subscribe.php') ?>';

            fetch(subscribeUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                subSubmitBtn.disabled = false;
                subSubmitBtn.innerHTML = '<i class="bi bi-check-circle-fill"></i> <span><?= __('btn_confirm_sub') ?></span>';

                if (data.success) {
                    showAlert('success', data.message);
                    subForm.reset();
                    setTimeout(() => {
                        const modalEl = document.getElementById('subscribeModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        subAlert.classList.add('d-none');
                    }, 2500);
                } else {
                    showAlert('danger', data.message || 'Subscription failed.');
                }
            })
            .catch(error => {
                subSubmitBtn.disabled = false;
                subSubmitBtn.innerHTML = '<i class="bi bi-check-circle-fill"></i> <span><?= __('btn_confirm_sub') ?></span>';
                showAlert('danger', 'Network error encountered during registration.');
            });
        });
    }

    function showAlert(type, message) {
        subAlert.className = `alert alert-${type} py-2 px-3 small mb-3`;
        subAlert.textContent = message;
        subAlert.classList.remove('d-none');
    }
});
</script>
