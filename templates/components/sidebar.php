<?php
/**
 * Component: Public Reader Sidebar — CNA-Style Clean Design
 * news-platform / templates / components / sidebar.php
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
?>

<aside class="d-flex flex-column gap-4">

    <!-- Widget 1: Newsletter Dispatch Callout -->
    <div class="card border-0 sidebar-newsletter-card text-white overflow-hidden">
        <div class="card-body p-4">
            <span class="badge text-uppercase px-3 py-1 mb-2 fw-bold"
                  style="font-size:0.65rem; background:#c8102e; letter-spacing:0.06em;"><?= __('daily_digest') ?></span>
            <h5 class="fw-bold text-white mb-2" style="font-size:1rem; letter-spacing:-0.02em;"><?= __('editorial_dispatch') ?></h5>
            <p class="small text-light opacity-85 mb-3 lh-base">
                <?= __('digest_desc') ?>
            </p>
            <button type="button"
                    class="btn btn-danger btn-sm w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2"
                    style="border-radius:2px; text-transform:uppercase; letter-spacing:0.04em; font-size:0.82rem;"
                    data-bs-toggle="modal" data-bs-target="#subscribeModal">
                <?= __('join_free_feed') ?>
            </button>
        </div>
    </div>

    <!-- Widget 2: Most Read Stories -->
    <?php if (!empty($trendingArticles)) { ?>
    <div class="card border-0 overflow-hidden most-read-card">
        <!-- Header — CNA red left-border style -->
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between px-3"
             style="border-left:3px solid #c8102e;">
            <h6 class="mb-0 fw-bold d-flex align-items-center gap-2"
                style="font-size:0.9rem; color:#0f172a; letter-spacing:-0.01em;">
                <?= __('most_read') ?>
            </h6>
            <span style="font-size:0.72rem; color:#9ca3af; font-weight:600;"><?= __('live_traffic') ?></span>
        </div>
        <div class="list-group list-group-flush">
            <?php foreach ($trendingArticles as $tIndex => $tItem) { ?>
                <a href="<?= url('article.php?slug=' . urlencode($tItem['slug'])) ?>"
                   class="list-group-item list-group-item-action py-3 px-3 border-bottom trending-list-item text-decoration-none">
                    <div class="d-flex gap-3 align-items-start">
                        <!-- Number — ALL uniform red -->
                        <div class="trending-rank-num flex-shrink-0">
                            <?= km_num(sprintf('%02d', $tIndex + 1)) ?>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <!-- Category pill -->
                            <div class="mb-1">
                                <span style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#c8102e;">
                                    <?= e(cat_name($tItem['category_name'])) ?>
                                </span>
                            </div>
                            <!-- Title -->
                            <h6 class="mb-1 fw-bold trending-title"
                                style="font-size:0.875rem; line-height:1.45; color:#0f172a; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                <?= e(article_title($tItem['title'])) ?>
                            </h6>
                            <!-- Views + Time (no icons) -->
                            <div class="d-flex align-items-center gap-1" style="font-size:0.72rem; color:#9ca3af;">
                                <span><?= number_format((int)$tItem['views_count']) ?></span>
                                <span class="cna-feed-dot" style="margin:0 0.25rem;"></span>
                                <span><?= \App\Core\TemplateEngine::timeAgo($tItem['published_at']) ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <!-- Widget 3: News Categories Tag Cloud -->
    <?php if (!empty($categories)) { ?>
    <div class="card border-0 overflow-hidden" style="border:1px solid #e5e7eb !important; border-radius:2px;">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2 px-3"
             style="border-left:3px solid #c8102e;">
            <h6 class="mb-0 fw-bold" style="font-size:0.9rem; color:#0f172a;">
                <?= __('explore_topics') ?>
            </h6>
        </div>
        <div class="card-body p-3">
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($categories as $catTag) { ?>
                    <a href="<?= url('index.php?category=' . $catTag['id']) ?>"
                       class="btn btn-outline-secondary btn-sm text-xs px-3 py-1"
                       style="border-radius:2px; font-size:0.78rem;">
                        <?= e(cat_name($catTag['name'])) ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php } ?>

</aside>
