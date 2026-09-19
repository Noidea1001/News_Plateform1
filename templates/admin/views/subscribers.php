<?php
/**
 * CMS Admin Subscribers Feed View
 * news-platform / templates / admin / views / subscribers.php
 */
declare(strict_types=1);
?>

<div class="container-fluid px-4 py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-envelope-paper-fill me-2 text-primary"></i> Reader Feed Subscribers
                </h5>
                <p class="small text-muted mb-0">List of registered emails receiving editorial dispatches and breaking alerts.</p>
            </div>
            <a href="<?= url('admin/export-subscribers.php') ?>" class="btn btn-success fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i> Export to CSV
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Subscriber Email</th>
                        <th>Topic Preference</th>
                        <th>Status</th>
                        <th>Subscribed At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subscribers)) { ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i> No feed subscribers registered yet.
                            </td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($subscribers as $sub) { ?>
                            <tr>
                                <td class="fw-bold"><?= $sub['id'] ?></td>
                                <td class="fw-semibold text-dark"><?= e($sub['email']) ?></td>
                                <td>
                                    <?php if (!empty($sub['category_name'])) { ?>
                                        <span class="badge bg-light text-dark border"><?= e(cat_name($sub['category_name'])) ?></span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary">All Topics & Breaking</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($sub['status'] === 'active') { ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary">Unsubscribed</span>
                                    <?php } ?>
                                </td>
                                <td class="small text-muted"><?= date('M j, Y H:i', strtotime($sub['subscribed_at'])) ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
