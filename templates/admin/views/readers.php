<?php
/**
 * CMS Admin Reader User Management View
 * news-platform / templates / admin / views / readers.php
 */
?>

<div class="container-fluid px-4 py-4">

    <!-- Page Header & Action Bar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= url('admin/dashboard.php') ?>" class="text-decoration-none text-muted"><?= __('admin_portal') ?></a></li>
                    <li class="breadcrumb-item"><a href="<?= url('admin/users.php') ?>" class="text-decoration-none text-muted"><?= __('admin_nav_staff') ?></a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= __('admin_nav_readers') ?></li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span><?= __('readers_mgmt_title') ?></span>
                <span class="badge bg-danger rounded-pill fs-6"><?= km_num($totalReaders) ?></span>
            </h3>
            <p class="text-muted small mb-0"><?= __('readers_mgmt_desc') ?></p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Link to Staff Users Management for quick toggle -->
            <a href="<?= url('admin/users.php') ?>" class="btn btn-outline-secondary btn-sm rounded-2 d-inline-flex align-items-center gap-1.5 fw-semibold">
                <i class="bi bi-person-badge text-danger"></i>
                <span><?= __('admin_nav_staff') ?></span>
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

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="<?= url('admin/readers.php') ?>" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5 col-lg-4">
                    <div class="input-group input-group-sm">
                        <input type="text" name="q" class="form-control rounded-start-2"
                            placeholder="<?= e(__('search_readers_placeholder')) ?>"
                            value="<?= e($searchQuery) ?>">
                        <button type="submit" class="btn btn-danger rounded-end-2 px-3">
                            <i class="bi bi-search me-1"></i> <?= __('search') ?>
                        </button>
                    </div>
                </div>

                <?php if (!empty($searchQuery)) { ?>
                    <div class="col-auto">
                        <a href="<?= url('admin/readers.php') ?>" class="btn btn-outline-secondary btn-sm rounded-2">
                            <?= __('reset_filters') ?>
                        </a>
                    </div>
                <?php } ?>

                <div class="col text-md-end text-muted small">
                    <?= __('total_registered_readers') ?>: <strong class="text-dark"><?= km_num(count($readersList)) ?></strong>
                </div>
            </form>
        </div>
    </div>

    <!-- Readers Table Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th style="width: 70px;" class="ps-3">#</th>
                        <th><?= __('reader_account') ?></th>
                        <th><?= __('email_address') ?></th>
                        <th class="text-center"><?= __('reader_comments_count') ?></th>
                        <th><?= __('registered_at') ?></th>
                        <th style="width: 100px;" class="text-end pe-3"><?= __('actions') ?? 'Actions' ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($readersList)) { ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                <p class="mb-0 fw-semibold"><?= __('no_readers_found') ?></p>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($readersList as $idx => $reader) { ?>
                            <tr>
                                <td class="ps-3 text-muted text-xs font-monospace">
                                    <?= km_num($reader['id']) ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                            style="width: 36px; height: 36px; font-size: 0.9rem;">
                                            <?= strtoupper(mb_substr($reader['name'] ?? 'R', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= e($reader['name']) ?></div>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary text-3xs">
                                                <?= __('verified_reader_badge') ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-secondary small"><?= e($reader['email']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border text-2xs fw-semibold px-2 py-1">
                                        <?= km_num((int)$reader['comment_count']) ?>
                                    </span>
                                </td>
                                <td class="text-muted text-xs">
                                    <?= \App\Core\TemplateEngine::formatDate($reader['created_at']) ?>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-outline-danger btn-sm p-1 px-2 rounded-2"
                                        onclick="confirmDeleteReader(<?= (int)$reader['id'] ?>, '<?= e(addslashes($reader['name'])) ?>')"
                                        title="<?= __('delete_reader') ?>">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteReaderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= __('delete_reader') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="text-dark mb-2"><?= __('delete_reader_confirm') ?></p>
                <div class="p-2 bg-light rounded-2 fw-bold text-danger" id="deleteReaderName"></div>
            </div>
            <div class="modal-footer border-top bg-light px-4 py-2 justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-2" data-bs-dismiss="modal">
                    <?= __('cancel') ?? 'Cancel' ?>
                </button>
                <a href="#" id="deleteReaderConfirmBtn" class="btn btn-danger btn-sm px-3 rounded-2 fw-bold">
                    <?= __('delete') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDeleteReader(readerId, readerName) {
    document.getElementById('deleteReaderName').textContent = readerName;
    const confirmBtn = document.getElementById('deleteReaderConfirmBtn');
    confirmBtn.href = '<?= url("admin/actions/delete-reader.php") ?>?id=' + readerId + '&csrf_token=<?= e($csrfToken) ?>';
    
    const modal = new bootstrap.Modal(document.getElementById('deleteReaderModal'));
    modal.show();
}
</script>
