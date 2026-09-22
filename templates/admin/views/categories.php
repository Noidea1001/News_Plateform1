<?php
/**
 * CMS Admin Categories Management View
 * news-platform / templates / admin / views / categories.php
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
        <!-- Add / Edit Category Form -->
        <div class="col-lg-4">
            <div class="card admin-card-clean">
                <div class="card-header admin-card-header-clean fw-bold py-3 text-dark">
                    <?= __('create_update_category') ?>
                </div>
                <div class="card-body p-4">
                    <form action="<?= url('admin/categories.php') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                        <input type="hidden" name="id" id="catId" value="">

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?= __('category_name') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="catName" class="form-control" placeholder="e.g. Technology & AI" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?= __('description') ?></label>
                            <textarea name="description" id="catDesc" class="form-control" rows="3" placeholder="Brief overview of topic coverage..."></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger flex-grow-1 fw-semibold">
                                <?= __('save_category') ?>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetCatForm()"><?= __('clear_search') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Category Table List -->
        <div class="col-lg-8">
            <div class="card admin-card-clean">
                <div class="card-header admin-card-header-clean py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title fw-bold mb-0 text-dark"><?= __('topic_categories') ?></h5>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 fw-bold"><?= count($categories) ?> <?= __('total') ?></span>
                </div>
                <div class="table-responsive admin-scroll-table">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th><?= __('category_name') ?></th>
                                <th><?= __('url_slug') ?></th>
                                <th><?= __('description') ?></th>
                                <th><?= __('total_articles') ?></th>
                                <th class="text-end"><?= __('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat) { ?>
                                <tr>
                                    <td class="fw-bold"><?= $cat['id'] ?></td>
                                    <td class="fw-bold text-dark"><?= e(cat_name($cat['name'])) ?></td>
                                    <td><code class="text-xs bg-light px-2 py-1 border rounded"><?= e($cat['slug']) ?></code></td>
                                    <td class="text-muted small"><?= e($cat['description'] ?? __('no_description')) ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark rounded-pill px-2.5 py-1">
                                            <?= number_format((int)$cat['article_count']) ?> <?= __('posts') ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary me-1 px-2 py-0.5" style="font-size:0.75rem;" onclick="editCategory(<?= htmlspecialchars(json_encode($cat), ENT_QUOTES, 'UTF-8') ?>)">
                                            <?= __('edit') ?>
                                        </button>
                                        <?php if ((int)$cat['article_count'] === 0) { ?>
                                            <a href="<?= url('admin/categories.php?action=delete&id=' . $cat['id'] . '&csrf_token=' . e($csrfToken)) ?>" 
                                               class="btn btn-sm btn-outline-danger px-2 py-0.5" style="font-size:0.75rem;" onclick="return confirm('Delete category?')">
                                                <?= __('delete') ?>
                                            </a>
                                        <?php } else { ?>
                                            <button class="btn btn-sm btn-outline-secondary px-2 py-0.5" style="font-size:0.75rem;" disabled title="Cannot delete: linked articles exist">
                                                <?= __('locked') ?>
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
