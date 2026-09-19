<?php
/**
 * CMS Admin Staff User Management View
 * news-platform / templates / admin / views / users.php
 */
declare(strict_types=1);
?>

<div class="container-fluid px-4 py-4">

    <!-- Flash Messages -->
    <?php if (isset($_GET['msg'])) { ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= e($_GET['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>
    <?php if (isset($_GET['error'])) { ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>

    <div class="row g-4">
        <!-- Add / Edit Staff Form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold py-3">
                    <i class="bi bi-person-plus me-2 text-success"></i> Staff Account Management
                </div>
                <div class="card-body p-4">
                    <form action="<?= url('admin/users.php') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                        <input type="hidden" name="id" id="userId" value="">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="userName" class="form-control" placeholder="e.g. john_doe" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="userEmail" class="form-control" placeholder="staff@newsplatform.local" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password <span class="text-muted small" id="pwHelp">(Required for new user)</span></label>
                            <input type="password" name="password" id="userPassword" class="form-control" placeholder="••••••••">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Staff Role</label>
                            <select name="role" id="userRole" class="form-select">
                                <option value="reporter">Reporter / Author</option>
                                <option value="editor">Editor</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bio / Editorial Profile</label>
                            <textarea name="bio" id="userBio" class="form-control" rows="2" placeholder="Brief author bio for opinion blueprints..."></textarea>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="userActive" value="1" checked>
                            <label class="form-check-label fw-semibold" for="userActive">Account Active</label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success flex-grow-1 fw-semibold">
                                <i class="bi bi-save me-1"></i> Save Staff User
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetUserForm()">Clear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Staff List Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title fw-bold mb-0 text-dark"><i class="bi bi-people me-2 text-primary"></i> CMS Staff Members</h5>
                    <span class="badge bg-secondary"><?= count($usersList) ?> Total Staff</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Staff Member</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Authored</th>
                                <th class="text-end">Actions</th>
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
                                                <div class="text-xs text-muted">Created <?= date('M j, Y', strtotime($u['created_at'])) ?></div>
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
                                            <span class="badge bg-success">Active</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">Disabled</span>
                                        <?php } ?>
                                    </td>
                                    <td class="fw-bold text-dark"><?= number_format((int)$u['article_count']) ?> posts</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary" onclick="editUser(<?= htmlspecialchars(json_encode($u), ENT_QUOTES, 'UTF-8') ?>)">
                                            <i class="bi bi-pencil"></i> Edit
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
    document.getElementById('pwHelp').innerText = '(Leave blank to keep current password)';
}
function resetUserForm() {
    document.getElementById('userId').value = '';
    document.getElementById('userName').value = '';
    document.getElementById('userEmail').value = '';
    document.getElementById('userPassword').value = '';
    document.getElementById('userRole').value = 'reporter';
    document.getElementById('userBio').value = '';
    document.getElementById('userActive').checked = true;
    document.getElementById('pwHelp').innerText = '(Required for new user)';
}
</script>
