<?php
/**
 * Reader Settings / Account Page
 * news-platform / templates / views / reader-settings.php
 */

// Avatar initial helper
$avatarInitial = strtoupper(mb_substr(strip_tags($reader['name'] ?? 'R'), 0, 1));
$avatarUrl     = $reader['avatar_url'] ?? '';
$memberSince   = !empty($reader['created_at'])
    ? date('d M Y', strtotime($reader['created_at']))
    : '—';
?>

<div style="background:#f8fafc; min-height:85vh;" class="py-4 py-md-5">
<div class="container" style="max-width:780px;">

    <!-- Page Header -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= url('index.php') ?>" class="text-muted text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i><?= __('nav_home') ?>
        </a>
        <span class="text-muted opacity-40">|</span>
        <span class="small fw-semibold text-dark"><?= __('settings_page_title') ?></span>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($success)) { ?>
        <div class="alert alert-success d-flex align-items-center gap-2 py-2 px-3 rounded-2 small mb-4 border-0 shadow-2xs">
            <i class="bi bi-check-circle-fill text-success flex-shrink-0"></i>
            <div><?= e($success) ?></div>
        </div>
    <?php } ?>
    <?php if (!empty($error)) { ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 rounded-2 small mb-4 border-0 shadow-2xs">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
            <div><?= e($error) ?></div>
        </div>
    <?php } ?>

    <div class="row g-4">

        <!-- ── Left: Profile Card ────────────────────────────────────── -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white text-center p-4 mb-3">
                <!-- Avatar -->
                <div class="mb-3 mx-auto position-relative" style="width:80px; height:80px;">
                    <?php if ($avatarUrl) { ?>
                        <img src="<?= e($avatarUrl) ?>" alt="Avatar"
                             class="rounded-circle border border-2 border-danger"
                             style="width:80px; height:80px; object-fit:cover;">
                    <?php } else { ?>
                        <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width:80px; height:80px; font-size:2rem;">
                            <?= e($avatarInitial) ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="fw-bold text-dark mb-0" style="font-size:1rem;"><?= e(strip_tags($reader['name'])) ?></div>
                <div class="text-muted text-xs mb-2"><?= e($reader['email']) ?></div>
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-2xs">
                    <?= __('verified_reader_badge') ?>
                </span>

                <hr class="my-3 opacity-15">

                <div class="text-xs text-muted">
                    <div class="d-flex justify-content-between mb-1">
                        <span><?= __('settings_member_since') ?></span>
                        <span class="fw-semibold text-dark"><?= $memberSince ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span><?= __('settings_comments') ?></span>
                        <span class="fw-semibold text-dark"><?= km_num($commentCount) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><?= __('settings_topics_sub') ?></span>
                        <span class="fw-semibold text-dark"><?= km_num(count($subscribedCatIds)) ?></span>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="<?= url('logout.php') ?>" class="btn btn-outline-danger btn-sm w-100 rounded-2 py-1.5 text-xs fw-semibold">
                        <i class="bi bi-box-arrow-right me-1"></i><?= __('sign_out') ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- ── Right: Settings Tabs ──────────────────────────────────── -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white overflow-hidden">

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs border-bottom px-3 pt-2 bg-white gap-1" id="settingsTabs" role="tablist" style="font-size:0.82rem;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-3 py-2 fw-semibold" id="tab-profile-btn"
                                data-bs-toggle="tab" data-bs-target="#tab-profile" type="button">
                            <i class="bi bi-person-fill me-1"></i><?= __('settings_tab_profile') ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-3 py-2 fw-semibold" id="tab-password-btn"
                                data-bs-toggle="tab" data-bs-target="#tab-password" type="button">
                            <i class="bi bi-shield-lock-fill me-1"></i><?= __('settings_tab_password') ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-3 py-2 fw-semibold" id="tab-topics-btn"
                                data-bs-toggle="tab" data-bs-target="#tab-topics" type="button">
                            <i class="bi bi-bookmark-fill me-1"></i><?= __('settings_tab_topics') ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-3 py-2 fw-semibold text-danger" id="tab-danger-btn"
                                data-bs-toggle="tab" data-bs-target="#tab-danger" type="button">
                            <i class="bi bi-trash3-fill me-1"></i><?= __('settings_tab_danger') ?>
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-4">

                    <!-- ── Tab: Profile ────────────────────────────────────── -->
                    <div class="tab-pane fade show active" id="tab-profile" role="tabpanel">
                        <h6 class="fw-bold text-dark mb-3"><?= __('settings_tab_profile') ?></h6>
                        <form action="<?= url('settings.php') ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                            <input type="hidden" name="action" value="profile">

                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="s_name" class="form-label small fw-semibold text-dark mb-1">
                                    <?= __('full_name') ?> <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm rounded-2"
                                       id="s_name" name="name"
                                       value="<?= e(strip_tags($reader['name'])) ?>"
                                       placeholder="<?= e(__('name_placeholder')) ?>" required>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="s_email" class="form-label small fw-semibold text-dark mb-1">
                                    <?= __('email_address') ?> <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control form-control-sm rounded-2"
                                       id="s_email" name="email"
                                       value="<?= e($reader['email']) ?>" required>
                            </div>

                            <!-- Avatar URL -->
                            <div class="mb-4">
                                <label for="s_avatar" class="form-label small fw-semibold text-dark mb-1">
                                    <?= __('settings_avatar_url') ?>
                                    <span class="text-muted fw-normal">(<?= __('settings_optional') ?>)</span>
                                </label>
                                <input type="url" class="form-control form-control-sm rounded-2"
                                       id="s_avatar" name="avatar_url"
                                       value="<?= e($reader['avatar_url'] ?? '') ?>"
                                       placeholder="https://...">
                                <div class="text-muted text-3xs mt-1"><?= __('settings_avatar_hint') ?></div>
                            </div>

                            <button type="submit" class="btn btn-danger btn-sm px-4 py-2 fw-bold rounded-2">
                                <?= __('settings_save_profile') ?>
                            </button>
                        </form>
                    </div>

                    <!-- ── Tab: Password ───────────────────────────────────── -->
                    <div class="tab-pane fade" id="tab-password" role="tabpanel">
                        <h6 class="fw-bold text-dark mb-3"><?= __('settings_tab_password') ?></h6>
                        <form action="<?= url('settings.php') ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                            <input type="hidden" name="action" value="password">

                            <div class="mb-3">
                                <label for="s_cur_pw" class="form-label small fw-semibold text-dark mb-1">
                                    <?= __('settings_current_password') ?> <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control form-control-sm rounded-2"
                                       id="s_cur_pw" name="current_password" placeholder="••••••••" required>
                            </div>

                            <div class="mb-3">
                                <label for="s_new_pw" class="form-label small fw-semibold text-dark mb-1">
                                    <?= __('settings_new_password') ?> <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control form-control-sm rounded-2"
                                       id="s_new_pw" name="new_password" placeholder="••••••••" minlength="6" required>
                                <div class="text-muted text-3xs mt-1"><?= __('password_min_length_hint') ?></div>
                            </div>

                            <div class="mb-4">
                                <label for="s_conf_pw" class="form-label small fw-semibold text-dark mb-1">
                                    <?= __('confirm_password_label') ?> <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control form-control-sm rounded-2"
                                       id="s_conf_pw" name="confirm_password" placeholder="••••••••" minlength="6" required>
                            </div>

                            <button type="submit" class="btn btn-danger btn-sm px-4 py-2 fw-bold rounded-2">
                                <?= __('settings_change_password') ?>
                            </button>
                        </form>
                    </div>

                    <!-- ── Tab: Topic Subscriptions ────────────────────────── -->
                    <div class="tab-pane fade" id="tab-topics" role="tabpanel">
                        <h6 class="fw-bold text-dark mb-1"><?= __('settings_tab_topics') ?></h6>
                        <p class="text-muted text-xs mb-3"><?= __('topic_subscriptions_desc') ?></p>

                        <div class="d-flex flex-wrap gap-2" id="settingsTopicList">
                            <?php foreach ($categories as $cat) {
                                $catId   = (int)$cat['id'];
                                $subbed  = in_array($catId, $subscribedCatIds, true);
                                $catName = cat_name($cat['name']);
                            ?>
                                <button type="button"
                                    class="btn btn-sm rounded-pill topic-toggle-btn <?= $subbed ? 'btn-danger' : 'btn-outline-secondary' ?>"
                                    data-cat-id="<?= $catId ?>"
                                    data-subscribed="<?= $subbed ? '1' : '0' ?>"
                                    style="font-size:0.78rem;">
                                    <?= $subbed ? '<i class="bi bi-check2 me-1"></i>' : '' ?>
                                    <?= e($catName) ?>
                                </button>
                            <?php } ?>
                        </div>

                        <div id="topicsMsg" class="mt-3 text-xs text-muted d-none"></div>
                    </div>

                    <!-- ── Tab: Danger Zone ────────────────────────────────── -->
                    <div class="tab-pane fade" id="tab-danger" role="tabpanel">
                        <h6 class="fw-bold text-danger mb-1"><?= __('settings_tab_danger') ?></h6>
                        <p class="text-muted text-xs mb-3"><?= __('settings_delete_warning') ?></p>

                        <div class="card border-danger border-opacity-25 rounded-2 p-3 bg-danger bg-opacity-5">
                            <div class="fw-semibold text-dark small mb-2"><?= __('settings_delete_account') ?></div>
                            <p class="text-muted text-xs mb-3"><?= __('settings_delete_desc') ?></p>

                            <form action="<?= url('settings.php') ?>" method="POST"
                                  onsubmit="return confirm('<?= __('settings_delete_confirm_js') ?>')">
                                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                <input type="hidden" name="action" value="delete_account">

                                <label for="s_del_confirm" class="form-label small fw-semibold text-dark mb-1">
                                    <?= __('settings_type_delete') ?>
                                </label>
                                <div class="input-group input-group-sm" style="max-width:280px;">
                                    <input type="text" class="form-control rounded-start-2 border-danger"
                                           id="s_del_confirm" name="confirm_delete"
                                           placeholder="delete" autocomplete="off">
                                    <button type="submit" class="btn btn-danger rounded-end-2 fw-bold px-3">
                                        <?= __('settings_delete_btn') ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div><!-- /.tab-content -->
            </div><!-- /.card -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.container -->
</div>

<script>
// ── Topic Subscribe/Unsubscribe via API ─────────────────────────────────────
document.querySelectorAll('.topic-toggle-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const catId    = this.dataset.catId;
        const subbed   = this.dataset.subscribed === '1';
        const msgEl    = document.getElementById('topicsMsg');
        this.disabled  = true;

        fetch('<?= url('api/v1/subscription.php') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ category_id: parseInt(catId) })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const nowSubbed = data.subscribed;
                this.dataset.subscribed = nowSubbed ? '1' : '0';
                if (nowSubbed) {
                    this.className = 'btn btn-sm rounded-pill topic-toggle-btn btn-danger';
                    this.innerHTML = '<i class="bi bi-check2 me-1"></i>' + this.textContent.trim();
                } else {
                    this.className = 'btn btn-sm rounded-pill topic-toggle-btn btn-outline-secondary';
                    this.innerHTML = this.textContent.trim().replace(/^✓\s*/, '');
                }
                if (msgEl) {
                    msgEl.classList.remove('d-none');
                    msgEl.textContent = data.message;
                }
            }
            this.disabled = false;
        })
        .catch(() => { this.disabled = false; });
    });
});

// ── Active Tab Hash Restore ─────────────────────────────────────────────────
(function () {
    const hash = window.location.hash;
    if (hash) {
        const tabBtn = document.querySelector('[data-bs-target="' + hash + '"]');
        if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();
    }
})();
</script>
