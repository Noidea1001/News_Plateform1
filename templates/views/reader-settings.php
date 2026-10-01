<?php
/**
 * Reader Settings / Account Page (Full Format Layout)
 * news-platform / templates / views / reader-settings.php
 */

$avatarInitial = strtoupper(mb_substr(strip_tags($reader['name'] ?? 'R'), 0, 1));
$avatarUrl     = $reader['avatar_url'] ?? '';
$memberSince   = !empty($reader['created_at'])
    ? date('d M Y', strtotime($reader['created_at']))
    : '—';
?>

<div class="settings-page py-4 py-lg-5">
    <div class="container">

        <!-- ── Breadcrumb Navigation ──────────────────────────────────── -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0 text-muted">
                <li class="breadcrumb-item">
                    <a href="<?= url('index.php') ?>" class="text-decoration-none text-muted">
                        <i class="bi bi-house-door me-1"></i><?= __('nav_home') ?>
                    </a>
                </li>
                <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">
                    <?= __('settings_page_title') ?>
                </li>
            </ol>
        </nav>

        <!-- ── System Feedback Alerts ─────────────────────────────────── -->
        <?php if (!empty($success)) { ?>
            <div class="alert alert-success d-flex align-items-center gap-2.5 py-2.5 px-3 rounded-2 small mb-4 border-0 shadow-sm">
                <i class="bi bi-check-circle-fill text-success fs-6 flex-shrink-0"></i>
                <div class="fw-medium"><?= e($success) ?></div>
            </div>
        <?php } ?>
        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger d-flex align-items-center gap-2.5 py-2.5 px-3 rounded-2 small mb-4 border-0 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-6 flex-shrink-0"></i>
                <div class="fw-medium"><?= e($error) ?></div>
            </div>
        <?php } ?>

        <!-- ── Top Account Overview Hero Card ────────────────────────── -->
        <div class="settings-hero-card p-4 p-md-4 mb-4">
            <div class="row align-items-center g-3">
                <!-- User Profile & Avatar -->
                <div class="col-lg-6 col-md-7">
                    <div class="d-flex align-items-center gap-3">
                        <div class="position-relative flex-shrink-0" style="width:76px; height:76px;">
                            <?php if (!empty($avatarUrl)) { ?>
                                <img src="<?= e($avatarUrl) ?>" alt="Avatar" id="heroAvatarImg"
                                     class="rounded-circle border border-2 border-danger shadow-sm"
                                     style="width:76px; height:76px; object-fit:cover;">
                            <?php } else { ?>
                                <div id="heroAvatarFallback"
                                     class="rounded-circle bg-danger d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                     style="width:76px; height:76px; font-size:1.85rem;">
                                    <?= e($avatarInitial) ?>
                                </div>
                            <?php } ?>
                            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-1"
                                  style="width:14px; height:14px;" title="Active"></span>
                        </div>
                        <div class="overflow-hidden">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <h4 class="fw-bold text-dark mb-0 editorial-title"><?= e(strip_tags($reader['name'])) ?></h4>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-3xs px-2 py-0.5 rounded-pill">
                                    <i class="bi bi-patch-check-fill me-1"></i><?= __('verified_reader_badge') ?>
                                </span>
                            </div>
                            <div class="text-muted text-xs text-truncate mb-1">
                                <i class="bi bi-envelope me-1"></i><?= e($reader['email']) ?>
                            </div>
                            <div class="text-3xs text-muted">
                                <i class="bi bi-shield-check me-1 text-success"></i><?= __('account_active') ?? 'Active Account' ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Metrics Chips -->
                <div class="col-lg-6 col-md-5">
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                        <div class="settings-stat-chip">
                            <div class="text-3xs text-muted text-uppercase fw-semibold mb-0.5">
                                <i class="bi bi-calendar3 me-1 text-danger"></i><?= __('settings_member_since') ?>
                            </div>
                            <div class="fw-bold text-dark text-xs"><?= $memberSince ?></div>
                        </div>
                        <div class="settings-stat-chip">
                            <div class="text-3xs text-muted text-uppercase fw-semibold mb-0.5">
                                <i class="bi bi-chat-left-text me-1 text-danger"></i><?= __('settings_comments') ?>
                            </div>
                            <div class="fw-bold text-dark text-xs"><?= km_num($commentCount) ?></div>
                        </div>
                        <div class="settings-stat-chip">
                            <div class="text-3xs text-muted text-uppercase fw-semibold mb-0.5">
                                <i class="bi bi-bookmark-star me-1 text-danger"></i><?= __('settings_topics_sub') ?>
                            </div>
                            <div class="fw-bold text-dark text-xs"><?= km_num(count($subscribedCatIds)) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Main 2-Column Settings Body ───────────────────────────── -->
        <div class="row g-4">

            <!-- ── Left Column: Nav Pills & Quick Links ──────────────── -->
            <div class="col-lg-3 col-md-4">
                <div class="settings-nav-card p-3 mb-3">
                    <div class="text-3xs text-muted text-uppercase fw-bold px-2 mb-2">
                        <?= __('settings_page_title') ?>
                    </div>
                    <div class="nav flex-column gap-1" id="settingsTabs" role="tablist">
                        <button class="nav-link active" id="tab-profile-btn"
                                data-bs-toggle="pill" data-bs-target="#tab-profile" type="button" role="tab">
                            <i class="bi bi-person-circle fs-6"></i>
                            <span><?= __('settings_tab_profile') ?></span>
                        </button>
                        <button class="nav-link" id="tab-password-btn"
                                data-bs-toggle="pill" data-bs-target="#tab-password" type="button" role="tab">
                            <i class="bi bi-shield-lock fs-6"></i>
                            <span><?= __('settings_tab_password') ?></span>
                        </button>
                        <button class="nav-link" id="tab-topics-btn"
                                data-bs-toggle="pill" data-bs-target="#tab-topics" type="button" role="tab">
                            <i class="bi bi-tags fs-6"></i>
                            <span><?= __('settings_tab_topics') ?></span>
                        </button>
                        <button class="nav-link text-danger" id="tab-danger-btn"
                                data-bs-toggle="pill" data-bs-target="#tab-danger" type="button" role="tab">
                            <i class="bi bi-trash3 fs-6"></i>
                            <span><?= __('settings_tab_danger') ?></span>
                        </button>
                    </div>

                    <hr class="my-3 opacity-15">

                    <div class="px-1">
                        <a href="<?= url('logout.php') ?>" class="btn btn-outline-danger btn-sm w-100 rounded-2 py-1.5 text-xs fw-semibold d-flex align-items-center justify-content-center gap-1.5">
                            <i class="bi bi-box-arrow-right"></i>
                            <span><?= __('sign_out') ?></span>
                        </a>
                    </div>
                </div>

                <!-- Helpful Security Info Note -->
                <div class="p-3 bg-light rounded-3 border small text-muted text-3xs">
                    <div class="fw-semibold text-dark mb-1">
                        <i class="bi bi-info-circle-fill text-danger me-1"></i><?= __('editorial_integrity') ?? 'Reader Privacy' ?>
                    </div>
                    <div><?= __('privacy_guaranteed') ?? 'Your email address is confidential and never shown publicly on comments.' ?></div>
                </div>
            </div>

            <!-- ── Right Column: Tabbed Panels ────────────────────────── -->
            <div class="col-lg-9 col-md-8">
                <div class="tab-content">

                    <!-- ── Tab 1: Profile Information ──────────────────── -->
                    <div class="tab-pane fade show active" id="tab-profile" role="tabpanel">
                        <div class="settings-content-card p-4 p-md-4">
                            <div class="border-bottom pb-3 mb-4">
                                <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                    <i class="bi bi-person-circle text-danger"></i>
                                    <span><?= __('settings_tab_profile') ?></span>
                                </h5>
                                <p class="text-muted text-xs mb-0"><?= __('register_reader_subtitle') ?></p>
                            </div>

                            <form action="<?= url('settings.php') ?>" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                <input type="hidden" name="action" value="profile">

                                <div class="row g-3 mb-4">
                                    <!-- Full Name -->
                                    <div class="col-md-6">
                                        <label for="s_name" class="form-label small fw-semibold text-dark mb-1.5">
                                            <?= __('full_name') ?> <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light text-muted border-end-0">
                                                <i class="bi bi-person"></i>
                                            </span>
                                            <input type="text" class="form-control rounded-end-2 border-start-0 ps-1"
                                                   id="s_name" name="name"
                                                   value="<?= e(strip_tags($reader['name'])) ?>"
                                                   placeholder="<?= e(__('name_placeholder')) ?>" required>
                                        </div>
                                    </div>

                                    <!-- Email Address -->
                                    <div class="col-md-6">
                                        <label for="s_email" class="form-label small fw-semibold text-dark mb-1.5">
                                            <?= __('email_address') ?> <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light text-muted border-end-0">
                                                <i class="bi bi-envelope"></i>
                                            </span>
                                            <input type="email" class="form-control rounded-end-2 border-start-0 ps-1"
                                                   id="s_email" name="email"
                                                   value="<?= e($reader['email']) ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Avatar URL with Live Preview -->
                                <div class="mb-4">
                                    <label for="s_avatar" class="form-label small fw-semibold text-dark mb-1.5">
                                        <?= __('settings_avatar_url') ?>
                                        <span class="text-muted fw-normal text-3xs">(<?= __('settings_optional') ?>)</span>
                                    </label>
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text bg-light text-muted border-end-0">
                                            <i class="bi bi-image"></i>
                                        </span>
                                        <input type="url" class="form-control border-start-0 ps-1 rounded-end-2"
                                               id="s_avatar" name="avatar_url"
                                               value="<?= e($reader['avatar_url'] ?? '') ?>"
                                               placeholder="https://example.com/photo.jpg">
                                    </div>
                                    <div class="text-muted text-3xs mb-3">
                                        <i class="bi bi-lightbulb me-1"></i><?= __('settings_avatar_hint') ?>
                                    </div>

                                    <!-- Live Preview Box -->
                                    <div class="p-3 bg-light rounded-2 border d-flex align-items-center gap-3">
                                        <div class="flex-shrink-0" style="width:48px; height:48px;">
                                            <img id="avatarLivePreviewImg"
                                                 src="<?= !empty($avatarUrl) ? e($avatarUrl) : 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="%23d90429"><rect width="48" height="48" rx="24"/></svg>' ?>"
                                                 alt="Live Preview"
                                                 class="rounded-circle border"
                                                 style="width:48px; height:48px; object-fit:cover; display:<?= !empty($avatarUrl) ? 'block' : 'none' ?>;">
                                            <div id="avatarLivePreviewFallback"
                                                 class="rounded-circle bg-danger text-white fw-bold d-flex align-items-center justify-content-center"
                                                 style="width:48px; height:48px; font-size:1.25rem; display:<?= empty($avatarUrl) ? 'flex' : 'none' ?>;">
                                                <?= e($avatarInitial) ?>
                                            </div>
                                        </div>
                                        <div class="small">
                                            <div class="fw-semibold text-dark text-xs"><?= __('current_image') ?? 'Avatar Preview' ?></div>
                                            <div class="text-muted text-3xs" id="avatarPreviewStatus">
                                                <?= !empty($avatarUrl) ? 'Custom image loaded' : 'Default initials avatar' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-end pt-3 border-top">
                                    <button type="submit" class="btn btn-danger btn-sm px-4 py-2 fw-semibold rounded-2 shadow-2xs d-inline-flex align-items-center gap-1.5">
                                        <i class="bi bi-check-lg"></i>
                                        <span><?= __('settings_save_profile') ?></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- ── Tab 2: Password & Security ──────────────────── -->
                    <div class="tab-pane fade" id="tab-password" role="tabpanel">
                        <div class="settings-content-card p-4 p-md-4">
                            <div class="border-bottom pb-3 mb-4">
                                <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-lock text-danger"></i>
                                    <span><?= __('settings_tab_password') ?></span>
                                </h5>
                                <p class="text-muted text-xs mb-0"><?= __('password_min_hint') ?? 'Update your password securely' ?></p>
                            </div>

                            <form action="<?= url('settings.php') ?>" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                <input type="hidden" name="action" value="password">

                                <!-- Current Password -->
                                <div class="mb-3" style="max-width:440px;">
                                    <label for="s_cur_pw" class="form-label small fw-semibold text-dark mb-1.5">
                                        <?= __('settings_current_password') ?> <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted border-end-0">
                                            <i class="bi bi-key"></i>
                                        </span>
                                        <input type="password" class="form-control border-start-0 border-end-0 ps-1"
                                               id="s_cur_pw" name="current_password" placeholder="••••••••" required>
                                        <button class="btn btn-outline-secondary border-start-0 toggle-pw-btn" type="button" data-target="s_cur_pw">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- New Password -->
                                <div class="mb-3" style="max-width:440px;">
                                    <label for="s_new_pw" class="form-label small fw-semibold text-dark mb-1.5">
                                        <?= __('settings_new_password') ?> <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted border-end-0">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                        <input type="password" class="form-control border-start-0 border-end-0 ps-1"
                                               id="s_new_pw" name="new_password" placeholder="••••••••" minlength="6" required>
                                        <button class="btn btn-outline-secondary border-start-0 toggle-pw-btn" type="button" data-target="s_new_pw">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="text-muted text-3xs mt-1">
                                        <i class="bi bi-info-circle me-1"></i><?= __('password_min_length_hint') ?>
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-4" style="max-width:440px;">
                                    <label for="s_conf_pw" class="form-label small fw-semibold text-dark mb-1.5">
                                        <?= __('confirm_password_label') ?> <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted border-end-0">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                        <input type="password" class="form-control border-start-0 border-end-0 ps-1"
                                               id="s_conf_pw" name="confirm_password" placeholder="••••••••" minlength="6" required>
                                        <button class="btn btn-outline-secondary border-start-0 toggle-pw-btn" type="button" data-target="s_conf_pw">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-end pt-3 border-top">
                                    <button type="submit" class="btn btn-danger btn-sm px-4 py-2 fw-semibold rounded-2 shadow-2xs d-inline-flex align-items-center gap-1.5">
                                        <i class="bi bi-shield-check"></i>
                                        <span><?= __('settings_change_password') ?></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- ── Tab 3: Topic Subscriptions ──────────────────── -->
                    <div class="tab-pane fade" id="tab-topics" role="tabpanel">
                        <div class="settings-content-card p-4 p-md-4">
                            <div class="border-bottom pb-3 mb-4">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                            <i class="bi bi-tags text-danger"></i>
                                            <span><?= __('settings_tab_topics') ?></span>
                                        </h5>
                                        <p class="text-muted text-xs mb-0"><?= __('topic_subscriptions_desc') ?></p>
                                    </div>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 text-2xs rounded-pill" id="subCountBadge">
                                        <i class="bi bi-check-circle me-1"></i><span id="subCountNum"><?= count($subscribedCatIds) ?></span> <?= __('subscribed_topic') ?>
                                    </span>
                                </div>
                            </div>

                            <div class="row g-2.5 mb-3" id="settingsTopicList">
                                <?php foreach ($categories as $cat) {
                                    $catId   = (int)$cat['id'];
                                    $subbed  = in_array($catId, $subscribedCatIds, true);
                                    $catName = cat_name($cat['name']);
                                ?>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="p-3 rounded-2 border d-flex align-items-center justify-content-between gap-2 topic-card <?= $subbed ? 'border-danger bg-danger bg-opacity-5' : 'bg-white' ?>"
                                             id="cat-card-<?= $catId ?>">
                                            <div class="overflow-hidden">
                                                <div class="fw-semibold text-dark text-xs text-truncate"><?= e($catName) ?></div>
                                                <div class="text-3xs text-muted">
                                                    <?= $subbed ? __('subscribed_topic') : __('subscribe_topic') ?>
                                                </div>
                                            </div>
                                            <button type="button"
                                                class="btn btn-sm rounded-pill topic-toggle-btn flex-shrink-0 <?= $subbed ? 'btn-danger' : 'btn-outline-secondary' ?>"
                                                data-cat-id="<?= $catId ?>"
                                                data-subscribed="<?= $subbed ? '1' : '0' ?>"
                                                style="font-size:0.75rem; padding: 0.25rem 0.75rem;">
                                                <i class="bi <?= $subbed ? 'bi-check2' : 'bi-plus-lg' ?> me-0.5"></i>
                                                <span class="btn-label"><?= $subbed ? __('subscribed_topic') : __('subscribe_topic') ?></span>
                                            </button>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div id="topicsMsg" class="alert alert-info py-2 px-3 text-xs mb-0 d-none rounded-2 border-0 shadow-2xs"></div>
                        </div>
                    </div>

                    <!-- ── Tab 4: Danger Zone ──────────────────────────── -->
                    <div class="tab-pane fade" id="tab-danger" role="tabpanel">
                        <div class="settings-content-card p-4 p-md-4">
                            <div class="border-bottom pb-3 mb-4">
                                <h5 class="fw-bold text-danger mb-1 d-flex align-items-center gap-2">
                                    <i class="bi bi-exclamation-octagon text-danger"></i>
                                    <span><?= __('settings_tab_danger') ?></span>
                                </h5>
                                <p class="text-muted text-xs mb-0"><?= __('settings_delete_warning') ?></p>
                            </div>

                            <div class="card border-danger border-opacity-25 rounded-3 p-4 bg-danger bg-opacity-5">
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-circle flex-shrink-0">
                                        <i class="bi bi-trash3-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1"><?= __('settings_delete_account') ?></h6>
                                        <p class="text-muted text-xs mb-0"><?= __('settings_delete_desc') ?></p>
                                    </div>
                                </div>

                                <form action="<?= url('settings.php') ?>" method="POST"
                                      onsubmit="return confirm('<?= __('settings_delete_confirm_js') ?>')">
                                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                    <input type="hidden" name="action" value="delete_account">

                                    <div class="mb-3" style="max-width:380px;">
                                        <label for="s_del_confirm" class="form-label small fw-semibold text-dark mb-1.5">
                                            <?= __('settings_type_delete') ?>
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control rounded-start-2 border-danger"
                                                   id="s_del_confirm" name="confirm_delete"
                                                   placeholder="delete" autocomplete="off" required>
                                            <button type="submit" class="btn btn-danger rounded-end-2 fw-bold px-3">
                                                <i class="bi bi-trash3 me-1"></i><?= __('settings_delete_btn') ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div><!-- /.tab-content -->
            </div><!-- /.col-lg-9 -->
        </div><!-- /.row -->

    </div><!-- /.container -->
</div>

<script>
// ── Live Avatar URL Previewer ──────────────────────────────────────────────
const avatarInput = document.getElementById('s_avatar');
const liveImg = document.getElementById('avatarLivePreviewImg');
const liveFallback = document.getElementById('avatarLivePreviewFallback');
const liveStatus = document.getElementById('avatarPreviewStatus');

if (avatarInput) {
    avatarInput.addEventListener('input', function () {
        const url = this.value.trim();
        if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
            liveImg.src = url;
            liveImg.onload = function () {
                liveImg.style.display = 'block';
                liveFallback.style.display = 'none';
                if (liveStatus) liveStatus.textContent = 'Custom image valid';
            };
            liveImg.onerror = function () {
                liveImg.style.display = 'none';
                liveFallback.style.display = 'flex';
                if (liveStatus) liveStatus.textContent = 'Image failed to load';
            };
        } else {
            liveImg.style.display = 'none';
            liveFallback.style.display = 'flex';
            if (liveStatus) liveStatus.textContent = 'Default initials avatar';
        }
    });
}

// ── Password Visibility Toggles ────────────────────────────────────────────
document.querySelectorAll('.toggle-pw-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const targetId = this.dataset.target;
        const input = document.getElementById(targetId);
        if (!input) return;
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        this.querySelector('i').className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
});

// ── Topic Subscribe/Unsubscribe via REST API ───────────────────────────────
document.querySelectorAll('.topic-toggle-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const catId    = this.dataset.catId;
        const isSubbed = this.dataset.subscribed === '1';
        const msgEl    = document.getElementById('topicsMsg');
        const cardEl   = document.getElementById('cat-card-' + catId);
        const countNum = document.getElementById('subCountNum');
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
                const labelSpan = this.querySelector('.btn-label');
                const icon = this.querySelector('i');

                if (nowSubbed) {
                    this.className = 'btn btn-sm rounded-pill topic-toggle-btn flex-shrink-0 btn-danger';
                    if (icon) icon.className = 'bi bi-check2 me-0.5';
                    if (labelSpan) labelSpan.textContent = '<?= addslashes(__('subscribed_topic')) ?>';
                    if (cardEl) {
                        cardEl.classList.add('border-danger', 'bg-danger', 'bg-opacity-5');
                        cardEl.classList.remove('bg-white');
                    }
                } else {
                    this.className = 'btn btn-sm rounded-pill topic-toggle-btn flex-shrink-0 btn-outline-secondary';
                    if (icon) icon.className = 'bi bi-plus-lg me-0.5';
                    if (labelSpan) labelSpan.textContent = '<?= addslashes(__('subscribe_topic')) ?>';
                    if (cardEl) {
                        cardEl.classList.remove('border-danger', 'bg-danger', 'bg-opacity-5');
                        cardEl.classList.add('bg-white');
                    }
                }

                if (countNum && typeof data.total_subscriptions !== 'undefined') {
                    countNum.textContent = data.total_subscriptions;
                }

                if (msgEl) {
                    msgEl.classList.remove('d-none');
                    msgEl.textContent = data.message;
                    setTimeout(() => { msgEl.classList.add('d-none'); }, 3000);
                }
            }
            this.disabled = false;
        })
        .catch(() => { this.disabled = false; });
    });
});

// ── Restore Active Tab via URL Hash ────────────────────────────────────────
(function () {
    const hash = window.location.hash;
    if (hash) {
        const tabBtn = document.querySelector('[data-bs-target="' + hash + '"]');
        if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();
    }
})();
</script>
