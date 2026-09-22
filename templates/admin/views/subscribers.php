<?php

?>

<div class="container-fluid px-4 py-4">
    <div class="card admin-card-clean">
        <div class="card-header admin-card-header-clean py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <?= __('reader_feed_subscribers') ?>
                </h5>
                <p class="small text-muted mb-0"><?= __('subscribers_list_desc') ?></p>
            </div>
            <a href="<?= url('admin/export-subscribers.php') ?>" class="btn btn-outline-dark btn-sm fw-bold">
                <?= __('export_csv') ?>
            </a>
        </div>
        <div class="table-responsive admin-scroll-table">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th><?= __('subscriber_email') ?></th>
                        <th><?= __('topic_preference') ?></th>
                        <th><?= __('status') ?></th>
                        <th><?= __('subscribed_at') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subscribers)) { ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <?= __('no_subscribers_found') ?>
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
                                        <span class="badge bg-secondary"><?= __('all_topics_option') ?></span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($sub['status'] === 'active') { ?>
                                        <span class="badge bg-success"><?= __('active') ?></span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary"><?= __('unsubscribed') ?></span>
                                    <?php } ?>
                                </td>
                                <td class="small text-muted"><?= \App\Core\TemplateEngine::formatDate($sub['subscribed_at'], 'M j, Y H:i') ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
