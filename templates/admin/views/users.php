<?php
/**
 * CMS Admin Staff User Management View
 * news-platform / templates / admin / views / users.php
 */
?>

<div class="container-fluid px-4 py-4">

    <!-- Page Header & Action Bar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= url('admin/dashboard.php') ?>" class="text-decoration-none text-muted"><?= __('admin_portal') ?></a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= __('admin_nav_staff') ?></li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span><?= __('staff_account_mgmt') ?></span>
                <span class="badge bg-danger rounded-pill fs-6"><?= km_num(count($usersList)) ?></span>
            </h3>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= url('admin/readers.php') ?>" class="btn btn-outline-secondary btn-sm rounded-2 d-inline-flex align-items-center gap-1.5 fw-semibold">
                <i class="bi bi-people text-primary"></i>
                <span><?= __('admin_nav_readers') ?></span>
            </a>
        </div>
    </div>

    <!-- Alert Feedback -->
    <?php if (!empty($msg)) { ?>
        <div class="alert alert-success alert-dismissible fade show py-2.5 px-3 rounded-2 small d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <div><?= e($msg) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <?php if (!empty($error)) { ?>
        <div class="alert alert-danger alert-dismissible fade show py-2.5 px-3 rounded-2 small d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
            <div><?= e($error) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <div class="row g-4">
        <!-- Add / Edit Staff Form -->
        <div class="col-lg-4">
            <div class="card admin-card-clean">
                <div class="card-header admin-card-header-clean fw-bold py-3 text-dark">
                    <?= __('staff_account_mgmt') ?>
                </div>
                <div class="card-body p-4">
                    <form action="<?= url('admin/users.php') ?>" method="POST" id="staffUserForm">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                        <input type="hidden" name="id" id="userId" value="">

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?= __('username') ?> <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="username" id="userName" class="form-control"
                                placeholder="e.g. john_doe" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?= __('email_address') ?> <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" id="userEmail" class="form-control"
                                placeholder="staff@newsplatform.local" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?= __('password') ?> <span class="text-muted small"
                                    id="pwHelp"><?= __('req_new_user') ?></span></label>
                            <input type="password" name="password" id="userPassword" class="form-control"
                                placeholder="••••••••" minlength="8">
                            <div class="form-text text-muted text-xs mt-1" id="pwSubHelp">
                                <?= __('password_min_hint') ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?= __('staff_role') ?></label>
                            <select name="role" id="userRole" class="form-select">
                                <option value="reporter"><?= __('reporter') ?></option>
                                <option value="editor"><?= __('editor') ?></option>
                                <option value="admin"><?= __('administrator') ?></option>
                            </select>
                            <div id="adminProtectedNotice" class="text-danger text-2xs mt-1 d-none">
                                <i class="bi bi-shield-lock-fill me-1"></i> Administrator role is protected and cannot be modified or downgraded.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?= __('bio_profile') ?></label>
                            <textarea name="bio" id="userBio" class="form-control" rows="2"
                                placeholder="Brief author bio for opinion blueprints..."></textarea>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="userActive" value="1"
                                checked>
                            <label class="form-check-label fw-semibold"
                                for="userActive"><?= __('account_active') ?></label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger flex-grow-1 fw-semibold">
                                <?= __('save_staff_user') ?>
                            </button>
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="resetUserForm()"><?= __('clear_search') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Staff List Table -->
        <div class="col-lg-8">
            <div class="card admin-card-clean">
                <div class="card-header admin-card-header-clean py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title fw-bold mb-0 text-dark"><?= __('cms_staff_members') ?></h5>
                    <span
                        class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 fw-bold"><?= count($usersList) ?>
                        <?= __('total_staff') ?></span>
                </div>
                <div class="table-responsive admin-scroll-table">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><?= __('staff_member') ?></th>
                                <th><?= __('email_address') ?></th>
                                <th><?= __('role') ?></th>
                                <th><?= __('status') ?></th>
                                <th><?= __('authored') ?></th>
                                <th class="text-end"><?= __('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usersList as $u) { ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                                style="width:36px; height:36px;">
                                                <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= e($u['username']) ?></div>
                                                <div class="text-xs text-muted"><?= __('created') ?>
                                                    <?= \App\Core\TemplateEngine::formatDate($u['created_at'], 'M j, Y') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small text-muted"><?= e($u['email']) ?></td>
                                    <td>
                                        <span
                                            class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : ($u['role'] === 'editor' ? 'warning text-dark' : 'primary') ?> text-uppercase">
                                            <?= e($u['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($u['is_active'])) { ?>
                                            <span class="badge bg-success"><?= __('active') ?></span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary"><?= __('unsubscribed') ?></span>
                                        <?php } ?>
                                    </td>
                                    <td class="fw-bold text-dark"><?= number_format((int) $u['article_count']) ?>
                                        <?= __('posts') ?></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary px-2 py-0.5"
                                                style="font-size:0.75rem;"
                                                onclick="editUser(<?= htmlspecialchars(json_encode($u), ENT_QUOTES, 'UTF-8') ?>)">
                                                <i class="bi bi-pencil-square me-1"></i><?= __('edit') ?>
                                            </button>
                                            <?php if ($u['role'] === 'admin') { ?>
                                                <button type="button" class="btn btn-outline-secondary px-2 py-0.5 opacity-50"
                                                    style="font-size:0.75rem;" disabled title="Administrator accounts are protected and cannot be deleted">
                                                    <i class="bi bi-shield-lock-fill text-danger me-1"></i>Protected
                                                </button>
                                            <?php } else { ?>
                                                <a href="#" class="btn btn-outline-danger px-2 py-0.5"
                                                    style="font-size:0.75rem;"
                                                    onclick="confirmDeleteCard('<?= url('admin/actions/delete-user.php?id=' . $u['id'] . '&csrf_token=' . e($csrfToken)) ?>', <?= htmlspecialchars(json_encode($u['username']), ENT_QUOTES, 'UTF-8') ?>); return false;"
                                                    title="<?= __('delete') ?>">
                                                    <i class="bi bi-trash3 me-1"></i><?= __('delete') ?>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function editUser(u) {
        document.getElementById('userId').value = u.id;
        document.getElementById('userName').value = u.username;
        document.getElementById('userEmail').value = u.email;
        
        const roleSelect = document.getElementById('userRole');
        const protectedNotice = document.getElementById('adminProtectedNotice');
        const activeSwitch = document.getElementById('userActive');
        
        roleSelect.value = u.role;
        if (u.role === 'admin') {
            roleSelect.setAttribute('disabled', 'disabled');
            if (protectedNotice) protectedNotice.classList.remove('d-none');
            activeSwitch.setAttribute('disabled', 'disabled');
        } else {
            roleSelect.removeAttribute('disabled');
            if (protectedNotice) protectedNotice.classList.add('d-none');
            activeSwitch.removeAttribute('disabled');
        }

        document.getElementById('userBio').value = u.bio || '';
        activeSwitch.checked = (parseInt(u.is_active) === 1);
        document.getElementById('pwHelp').innerText = '<?= addslashes(__('blank_keep_pw')) ?>';
    }

    function resetUserForm() {
        document.getElementById('userId').value = '';
        document.getElementById('userName').value = '';
        document.getElementById('userEmail').value = '';
        document.getElementById('userPassword').value = '';
        
        const roleSelect = document.getElementById('userRole');
        roleSelect.removeAttribute('disabled');
        roleSelect.value = 'reporter';
        
        const protectedNotice = document.getElementById('adminProtectedNotice');
        if (protectedNotice) protectedNotice.classList.add('d-none');
        
        const activeSwitch = document.getElementById('userActive');
        activeSwitch.removeAttribute('disabled');
        activeSwitch.checked = true;

        document.getElementById('userBio').value = '';
        document.getElementById('pwHelp').innerText = '<?= addslashes(__('req_new_user')) ?>';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('staffUserForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const uId = document.getElementById('userId').value;
                const pw = document.getElementById('userPassword').value;

                if (!uId && !pw) {
                    e.preventDefault();
                    if (typeof window.showAdminToast === 'function') {
                        window.showAdminToast('Password is required for new staff accounts.', 'error');
                    } else {
                        alert('Password is required for new staff accounts.');
                    }
                    return false;
                }

                if (pw && pw.length < 8) {
                    e.preventDefault();
                    if (typeof window.showAdminToast === 'function') {
                        window.showAdminToast('Password must be at least 8 characters long.', 'error');
                    } else {
                        alert('Password must be at least 8 characters long.');
                    }
                    return false;
                }
            });
        }
    });
</script>