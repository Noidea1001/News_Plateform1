<?php

?>

<div class="container-fluid px-4 py-4">
    <div class="card admin-card-clean">
        <div class="card-header admin-card-header-clean py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="card-title fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <span><?= __('reader_feed_subscribers') ?></span>
                    <span class="badge bg-danger rounded-pill fs-6"><?= km_num(count($subscribers)) ?></span>
                </h5>
                <p class="small text-muted mb-0"><?= __('subscribers_list_desc') ?></p>
            </div>
            <a href="<?= url('admin/export-subscribers.php') ?>" class="btn btn-outline-dark btn-sm fw-bold">
                <i class="bi bi-file-earmark-arrow-down me-1"></i> <?= __('export_csv') ?>
            </a>
        </div>
        <div class="table-responsive admin-scroll-table">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">#</th>
                        <th><?= __('subscriber_email') ?></th>
                        <th><?= __('topic_preference') ?></th>
                        <th><?= __('status') ?></th>
                        <th><?= __('subscribed_at') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subscribers)) { ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-2 text-muted d-block mb-1"></i>
                                <?= __('no_subscribers_found') ?>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($subscribers as $sub) { ?>
                            <tr>
                                <td class="fw-bold text-muted"><?= km_num($sub['id']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger fw-bold d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:34px;height:34px;font-size:0.8rem;">
                                            <?= mb_strtoupper(mb_substr($sub['reader_name'] ?: $sub['email'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark"><?= e($sub['email']) ?></div>
                                            <?php if (!empty($sub['reader_name'])) { ?>
                                                <div class="text-muted text-xs"><i class="bi bi-person me-1"></i><?= e($sub['reader_name']) ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($sub['category_name'])) { ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1.5"><?= e(cat_name($sub['category_name'])) ?></span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary"><?= __('all_topics_option') ?></span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($sub['status'] === 'active') { ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><?= __('active') ?></span>
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
