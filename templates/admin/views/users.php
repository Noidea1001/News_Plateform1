<?php
/**
 * CMS Admin Staff User Management View
 * news-platform / templates / admin / views / users.php
 */
?>

<div class="container-fluid px-4 py-4">

    <!-- Flash Messages -->
    <?php if (isset($_GET['msg'])) { ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <?= e($_GET['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>
    <?php if (isset($_GET['error'])) { ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <?= e($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                    <form action="<?= url('admin/users.php') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                        <input type="hidden" name="id" id="userId" value="">

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?= __('username') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="userName" class="form-control" placeholder="e.g. john_doe" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?= __('email_address') ?> <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="userEmail" class="form-control" placeholder="staff@newsplatform.local" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?= __('password') ?> <span class="text-muted small" id="pwHelp"><?= __('req_new_user') ?></span></label>
                            <input type="password" name="password" id="userPassword" class="form-control" placeholder="••••••••">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?= __('staff_role') ?></label>
                            <select name="role" id="userRole" class="form-select">
                                <option value="reporter"><?= __('reporter') ?></option>
                                <option value="editor"><?= __('editor') ?></option>
                                <option value="admin"><?= __('administrator') ?></option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?= __('bio_profile') ?></label>
                            <textarea name="bio" id="userBio" class="form-control" rows="2" placeholder="Brief author bio for opinion blueprints..."></textarea>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="userActive" value="1" checked>
                            <label class="form-check-label fw-semibold" for="userActive"><?= __('account_active') ?></label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger flex-grow-1 fw-semibold">
                                <?= __('save_staff_user') ?>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetUserForm()"><?= __('clear_search') ?></button>
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
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 fw-bold"><?= count($usersList) ?> <?= __('total_staff') ?></span>
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
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:36px; height:36px;">
                                                <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= e($u['username']) ?></div>
                                                <div class="text-xs text-muted"><?= __('created') ?> <?= \App\Core\TemplateEngine::formatDate($u['created_at'], 'M j, Y') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small text-muted"><?= e($u['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : ($u['role'] === 'editor' ? 'warning text-dark' : 'primary') ?> text-uppercase">
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
                                    <td class="fw-bold text-dark"><?= number_format((int)$u['article_count']) ?> <?= __('posts') ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary px-2 py-0.5" style="font-size:0.75rem;" onclick="editUser(<?= htmlspecialchars(json_encode($u), ENT_QUOTES, 'UTF-8') ?>)">
                                            <?= __('edit') ?>
                                        </button>
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
    document.getElementById('userRole').value = u.role;
    document.getElementById('userBio').value = u.bio || '';
    document.getElementById('userActive').checked = (parseInt(u.is_active) === 1);
    document.getElementById('pwHelp').innerText = '<?= addslashes(__('blank_keep_pw')) ?>';
}
function resetUserForm() {
    document.getElementById('userId').value = '';
    document.getElementById('userName').value = '';
    document.getElementById('userEmail').value = '';
    document.getElementById('userPassword').value = '';
    document.getElementById('userRole').value = 'reporter';
    document.getElementById('userBio').value = '';
    document.getElementById('userActive').checked = true;
    document.getElementById('pwHelp').innerText = '<?= addslashes(__('req_new_user')) ?>';
}
</script>
