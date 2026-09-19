<?php
/**
 * CMS Admin Categories Management View
 * news-platform / templates / admin / views / categories.php
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
        <!-- Add / Edit Category Form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold py-3">
                    <i class="bi bi-tags me-2 text-info"></i> Create / Update Category
                </div>
                <div class="card-body p-4">
                    <form action="<?= url('admin/categories.php') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                        <input type="hidden" name="id" id="catId" value="">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="catName" class="form-control" placeholder="e.g. Technology & AI" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="catDesc" class="form-control" rows="3" placeholder="Brief overview of topic coverage..."></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger flex-grow-1 fw-semibold">
                                <i class="bi bi-save me-1"></i> Save Category
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetCatForm()">Clear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Category Table List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title fw-bold mb-0 text-dark"><i class="bi bi-list-nested me-2 text-danger"></i> Topic Categories</h5>
                    <span class="badge bg-secondary"><?= count($categories) ?> Total</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Category Name</th>
                                <th>URL Slug</th>
                                <th>Description</th>
                                <th>Articles</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat) { ?>
                                <tr>
                                    <td class="fw-bold"><?= $cat['id'] ?></td>
                                    <td class="fw-bold text-dark"><?= e(cat_name($cat['name'])) ?></td>
                                    <td><code class="text-xs bg-light px-2 py-1 border rounded"><?= e($cat['slug']) ?></code></td>
                                    <td class="text-muted small"><?= e($cat['description'] ?? 'No description') ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark rounded-pill px-2.5 py-1">
                                            <?= number_format((int)$cat['article_count']) ?> posts
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editCategory(<?= htmlspecialchars(json_encode($cat), ENT_QUOTES, 'UTF-8') ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <?php if ((int)$cat['article_count'] === 0) { ?>
                                            <a href="<?= url('admin/categories.php?action=delete&id=' . $cat['id'] . '&csrf_token=' . e($csrfToken)) ?>" 
                                               class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete category?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php } else { ?>
                                            <button class="btn btn-sm btn-outline-secondary" disabled title="Cannot delete: linked articles exist">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                        <?php } ?>
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
function editCategory(cat) {
    document.getElementById('catId').value = cat.id;
    document.getElementById('catName').value = cat.name;
    document.getElementById('catDesc').value = cat.description || '';
}
function resetCatForm() {
    document.getElementById('catId').value = '';
    document.getElementById('catName').value = '';
    document.getElementById('catDesc').value = '';
}
</script>
