<?php
/**
 * Public Reader Homepage View — CNA-Style Layout
 * news-platform / templates / views / home.php
 *
 * Layout:
 *  • Hero search section (navy top bar)
 *  • Full-width Lead Article (hero card, top of page)
 *  • 2-column layout: Main feed (col-lg-8) + Sidebar (col-lg-4)
 *  • Main feed = vertical list of 10 articles per page with Previous/1/2/3/Next pagination
 *  • "Top Headlines" sidebar panel REMOVED — replaced by sidebar.php
 */
?>

<div class="homepage-wrapper">

    <!-- ======================================================
         MAIN BODY
    ====================================================== -->
    <div class="container homepage-body py-3 py-md-4">

        <?php if (!empty($searchQuery)) { ?>
            <?php if (!empty($noSearchResults)) { ?>
                <!-- Search Result Mismatch Fallback Banner (Real-World Standard) -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 mb-4 bg-white border border-danger-subtle rounded-2 shadow-2xs">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-danger-subtle text-danger p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-search" style="font-size: 0.95rem;"></i>
                        </span>
                        <div>
                            <div class="fw-bold text-dark fs-6">
                                <?= __('no_articles_found_for') ?> <span class="text-danger">"<?= e($searchQuery) ?>"</span>
                            </div>
                            <div class="text-muted text-xs mt-0.5">
                                <i class="bi bi-compass me-1 text-danger"></i><?= __('showing_latest_recommendations') ?>
                            </div>
                        </div>
                    </div>
                    <a href="<?= url('index.php') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 text-xs d-flex align-items-center gap-1.5 shadow-2xs">
                        <i class="bi bi-x-circle text-danger"></i> <?= __('clear_search') ?? 'Clear' ?>
                    </a>
                </div>
            <?php } else { ?>
                <!-- Active Global Search Status Banner -->
                <div class="d-flex align-items-center justify-content-between p-3 mb-4 bg-white border rounded-2 shadow-2xs">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-search text-white" style="font-size: 0.85rem;"></i>
                        </span>
                        <div>
                            <div class="text-muted text-2xs text-uppercase fw-bold"><?= __('search_results_for') ?? 'Search Results' ?></div>
                            <div class="fw-bold text-dark fs-6">"<?= e($searchQuery) ?>"</div>
                        </div>
                    </div>
                    <a href="<?= url('index.php') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 text-xs d-flex align-items-center gap-1.5 shadow-2xs">
                        <i class="bi bi-x-circle text-danger"></i> <?= __('clear_search') ?? 'Clear' ?>
                    </a>
                </div>
            <?php } ?>
        <?php } ?>

        <?php if (empty($articles)) { ?>
            <!-- Empty / No Results State -->
            <div class="text-center py-5 bg-white border" style="border-radius:2px; margin-top:1.5rem;">
                <div class="mb-3" style="font-size:3rem; color:#d1d5db;"><i class="bi bi-newspaper"></i></div>
                <h3 class="fw-bold mb-2" style="color:#0f172a;"><?= __('no_articles_found') ?></h3>
                <p class="text-muted mb-4"><?= __('no_articles_desc') ?></p>
                <a href="<?= url('index.php') ?>" class="btn btn-danger px-4" style="border-radius:2px;">
                    <i class="bi bi-arrow-left me-2"></i><?= __('return_home') ?>
                </a>
            </div>

        <?php } else { ?>

            <?php
            /* ── Lead Hero & Secondary Grid logic (Only on default page 1 without search) ── */
            $lead = null;
            $secondaryArticles = [];
            $feedArticles = $articles;

            if (empty($searchQuery) && empty($activeCategoryId) && $currentPage === 1 && count($articles) > 0) {
                $lead = $articles[0];
                if (count($articles) >= 3) {
                    $secondaryArticles = array_slice($articles, 1, 2);
                    $feedArticles = array_slice($articles, 3);
                } else {
                    $feedArticles = array_slice($articles, 1);
                }
            }

            if (!empty($lead)) {
                $leadData = htmlspecialchars(json_encode([
                    'id' => $lead['id'],
                    'title' => article_title($lead),
                    'title_kh' => article_title($lead, 'kh'),
                    'title_en' => article_title($lead, 'en'),
                    'summary' => article_summary($lead),
                    'summary_kh' => article_summary($lead, 'kh'),
                    'summary_en' => article_summary($lead, 'en'),
                    'category' => cat_name($lead['category_name']),
                    'category_kh' => cat_name($lead['category_name'], 'kh'),
                    'category_en' => cat_name($lead['category_name'], 'en'),
                    'author' => $lead['author_name'],
                    'date' => \App\Core\TemplateEngine::formatDate($lead['published_at']),
                    'time_ago' => \App\Core\TemplateEngine::timeAgo($lead['published_at']),
                    'reading_time' => $lead['reading_time'] ?? '3 min read',
                    'views' => number_format((int)$lead['views_count']),
                    'image' => image_url($lead['featured_image'] ?? ''),
                    'url' => url('article.php?slug=' . urlencode($lead['slug']))
                ]), ENT_QUOTES, 'UTF-8');
            }
            ?>

            <?php if (!empty($lead)) { ?>
                <!-- ==================================================
                     LEAD ARTICLE — Full-width hero card (CNA style)
                ================================================== -->
                <div class="lead-article-card mb-4">
                    <!-- Image -->
                    <div class="lead-article-image position-relative overflow-hidden" style="background:#0f172a;">
                        <?php if (!empty($lead['featured_image'])) { ?>
                            <img src="<?= e(image_url($lead['featured_image'])) ?>" alt="<?= e(article_title($lead)) ?>" loading="eager"
                                 style="width:100%; height:100%; object-fit:cover;"
                                 onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.classList.remove('d-none');">
                            <div class="d-none align-items-center justify-content-center h-100 w-100 fs-1 text-muted"
                                style="background:#1e293b;">
                                <i class="bi bi-newspaper"></i>
                            </div>
                        <?php } else { ?>
                            <div class="d-flex align-items-center justify-content-center h-100 w-100 fs-1 text-muted"
                                style="background:#1e293b;">
                                <i class="bi bi-newspaper"></i>
                            </div>
                        <?php } ?>
                        <!-- Floating badges -->
                        <div class="lead-badge-left">
                            <?php if (!empty($lead['is_breaking'])) { ?>
                                <span class="lead-badge-pill" style="background:#c8102e; color:#fff;">
                                    <i class="bi bi-lightning-fill me-1"></i><?= __('breaking') ?>
                                </span>
                            <?php } ?>
                            <span class="lead-badge-pill bg-dark-pill"><?= e(cat_name($lead['category_name'])) ?></span>
                        </div>
                        <div class="lead-badge-right">
                            <span class="lead-badge-pill tmpl-badge-<?= e($lead['template_type']) ?>">
                                <?= mb_strtoupper(e(tmpl_name($lead['template_type']))) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="lead-article-body">
                        <div class="lead-meta-top d-flex align-items-center gap-2 mb-2 text-xs text-muted">
                            <span><?= e($lead['author_name']) ?></span>
                            <span>&bull;</span>
                            <span><?= \App\Core\TemplateEngine::timeAgo($lead['published_at']) ?></span>
                        </div>

                        <h2 class="lead-article-title">
                            <a href="<?= url('article.php?slug=' . urlencode($lead['slug'])) ?>">
                                <?= e(article_title($lead)) ?>
                            </a>
                        </h2>

                        <p class="lead-article-summary"><?= e(article_summary($lead)) ?></p>

                        <div class="lead-article-footer d-flex align-items-center justify-content-between w-100">
                            <a href="<?= url('article.php?slug=' . urlencode($lead['slug'])) ?>" class="lead-read-link">
                                <?= __('read_full_article') ?> <i class="bi bi-chevron-right"></i>
                            </a>
                            <div class="d-flex align-items-center gap-2 ms-auto">
                                <button type="button" class="btn btn-quick-view btn-sm qv-trigger-btn" data-article='<?= $leadData ?>'>
                                    <i class="bi bi-eye me-1"></i><?= __('quick_view') ?? 'Quick View' ?>
                                </button>
                                <?php if (\App\Core\Auth::readerCheck() || \App\Core\Auth::check()) { ?>
                                    <button type="button" class="btn btn-bookmark btn-sm bookmark-toggle-btn" data-id="<?= $lead['id'] ?>" data-article='<?= $leadData ?>'>
                                        <i class="bi bi-bookmark"></i>
                                    </button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <!-- ==================================================
                 SECONDARY FEATURED GRID (Top 2 Highlight Stories)
            ================================================== -->
            <?php if (!empty($secondaryArticles)) { ?>
                <div class="secondary-featured-grid">
                    <?php foreach ($secondaryArticles as $sItem) { ?>
                        <?php
                        $sData = htmlspecialchars(json_encode([
                            'id' => $sItem['id'],
                            'title' => article_title($sItem),
                            'title_kh' => article_title($sItem, 'kh'),
                            'title_en' => article_title($sItem, 'en'),
                            'summary' => article_summary($sItem),
                            'summary_kh' => article_summary($sItem, 'kh'),
                            'summary_en' => article_summary($sItem, 'en'),
                            'category' => cat_name($sItem['category_name']),
                            'category_kh' => cat_name($sItem['category_name'], 'kh'),
                            'category_en' => cat_name($sItem['category_name'], 'en'),
                            'author' => $sItem['author_name'],
                            'date' => \App\Core\TemplateEngine::formatDate($sItem['published_at']),
                            'time_ago' => \App\Core\TemplateEngine::timeAgo($sItem['published_at']),
                            'reading_time' => $sItem['reading_time'] ?? '3 min read',
                            'views' => number_format((int)$sItem['views_count']),
                            'image' => image_url($sItem['featured_image'] ?? ''),
                            'url' => url('article.php?slug=' . urlencode($sItem['slug']))
                        ]), ENT_QUOTES, 'UTF-8');
                        ?>
                        <div class="secondary-grid-card">
                            <div class="secondary-grid-thumb position-relative overflow-hidden" style="background:#f1f5f9;">
                                <?php if (!empty($sItem['featured_image'])) { ?>
                                    <img src="<?= e(image_url($sItem['featured_image'])) ?>" 
                                         alt="<?= e(article_title($sItem)) ?>" 
                                         loading="lazy" 
                                         style="width:100%; height:100%; object-fit:cover;"
                                         onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                    <div class="cna-feed-thumb-empty" style="display:none; width:100%; height:100%; align-items:center; justify-content:center; background:#e2e8f0; color:#64748b;">
                                        <i class="bi bi-newspaper fs-2"></i>
                                    </div>
                                <?php } else { ?>
                                    <div class="cna-feed-thumb-empty d-flex align-items-center justify-content-center w-100 h-100" style="background:#e2e8f0; color:#64748b;">
                                        <i class="bi bi-newspaper fs-2"></i>
                                    </div>
                                <?php } ?>
                                <span class="lead-badge-pill bg-dark-pill position-absolute" style="top:0.6rem; left:0.6rem; z-index:3;">
                                    <?= e(cat_name($sItem['category_name'])) ?>
                                </span>
                            </div>
                            <div class="secondary-grid-body">
                                <div class="d-flex align-items-center gap-2 mb-2 text-xs text-muted">
                                    <span><?= e($sItem['author_name']) ?></span>
                                    <span>&bull;</span>
                                    <span><?= \App\Core\TemplateEngine::timeAgo($sItem['published_at']) ?></span>
                                </div>
                                <h3 class="secondary-grid-title">
                                    <a href="<?= url('article.php?slug=' . urlencode($sItem['slug'])) ?>">
                                        <?= e(article_title($sItem)) ?>
                                    </a>
                                </h3>
                                <p class="secondary-grid-summary"><?= e(article_summary($sItem)) ?></p>
                                <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top">
                                    <span class="text-xs text-muted">
                                        <i class="bi bi-clock me-1"></i><?= e($sItem['reading_time'] ?? '3 min read') ?>
                                    </span>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <button type="button" class="btn btn-quick-view btn-sm qv-trigger-btn" data-article='<?= $sData ?>'>
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <?php if (\App\Core\Auth::readerCheck() || \App\Core\Auth::check()) { ?>
                                            <button type="button" class="btn btn-bookmark btn-sm bookmark-toggle-btn" data-id="<?= $sItem['id'] ?>" data-article='<?= $sData ?>'>
                                                <i class="bi bi-bookmark"></i>
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <!-- ==================================================
                 2-COLUMN: FEED LIST (left 8) + SIDEBAR (right 4)
            ================================================== -->
            <div class="row g-4">

                <!-- ── Main Feed Stream ─────────────────────────── -->
                <div class="col-lg-8">

                    <!-- CNA Section Header -->
                    <div class="feed-section-header">
                        <div class="feed-section-title">
                            <h3><?= __('latest_stream') ?></h3>
                        </div>
                        <span class="feed-count-badge">
                            <?= __('showing_reports', ['count' => !empty($noSearchResults) ? count($feedArticles) : $totalCount]) ?>
                        </span>
                    </div>

                    <!-- ── CNA-Style Vertical News List ── -->
                    <div class="cna-news-list">
                        <?php if (empty($feedArticles)) { ?>
                            <div class="py-5 text-center" style="color:#9ca3af;">
                                <?= __('no_articles_found') ?>
                            </div>
                        <?php } else { ?>
                            <?php foreach ($feedArticles as $idx => $item) { ?>
                                <?php
                                $absNum = $idx + 1 + (($currentPage - 1) * $perPage);
                                $numLabel = km_num(str_pad((string) $absNum, 2, '0', STR_PAD_LEFT));

                                $itemData = htmlspecialchars(json_encode([
                                    'id' => $item['id'],
                                    'title' => article_title($item),
                                    'title_kh' => article_title($item, 'kh'),
                                    'title_en' => article_title($item, 'en'),
                                    'summary' => article_summary($item),
                                    'summary_kh' => article_summary($item, 'kh'),
                                    'summary_en' => article_summary($item, 'en'),
                                    'category' => cat_name($item['category_name']),
                                    'category_kh' => cat_name($item['category_name'], 'kh'),
                                    'category_en' => cat_name($item['category_name'], 'en'),
                                    'author' => $item['author_name'],
                                    'date' => \App\Core\TemplateEngine::formatDate($item['published_at']),
                                    'time_ago' => \App\Core\TemplateEngine::timeAgo($item['published_at']),
                                    'reading_time' => $item['reading_time'] ?? '3 min read',
                                    'views' => number_format((int)$item['views_count']),
                                    'image' => image_url($item['featured_image'] ?? ''),
                                    'url' => url('article.php?slug=' . urlencode($item['slug']))
                                ]), ENT_QUOTES, 'UTF-8');
                                ?>

                                <div class="cna-feed-row position-relative">

                                    <!-- Red number badge — consistent for ALL items -->
                                    <div class="cna-feed-num"><?= $numLabel ?></div>

                                    <!-- Thumbnail -->
                                    <div class="cna-feed-thumb">
                                        <a href="<?= url('article.php?slug=' . urlencode($item['slug'])) ?>">
                                            <?php if (!empty($item['featured_image'])) { ?>
                                                <img src="<?= e(image_url($item['featured_image'])) ?>" alt="<?= e(article_title($item)) ?>" loading="lazy">
                                            <?php } else { ?>
                                                <div class="cna-feed-thumb-empty">NP</div>
                                            <?php } ?>
                                        </a>
                                    </div>

                                    <!-- Text content -->
                                    <div class="cna-feed-body">

                                        <!-- Category pill + time -->
                                        <div class="cna-feed-meta">
                                            <span class="cna-feed-cat"><?= e(cat_name($item['category_name'])) ?></span>
                                            <span class="cna-feed-dot"></span>
                                            <span class="cna-feed-time"><?= \App\Core\TemplateEngine::timeAgo($item['published_at']) ?></span>
                                        </div>

                                        <!-- Title -->
                                        <h4 class="cna-feed-title">
                                            <a href="<?= url('article.php?slug=' . urlencode($item['slug'])) ?>" class="text-decoration-none color-inherit">
                                                <?= e(article_title($item)) ?>
                                            </a>
                                        </h4>

                                        <!-- Summary -->
                                        <p class="cna-feed-summary"><?= e(article_summary($item)) ?></p>

                                        <!-- Bottom row: author · views · actions -->
                                        <div class="cna-feed-footer">
                                            <span class="cna-feed-author"><?= e($item['author_name']) ?></span>
                                            <span class="cna-feed-dot"></span>
                                            <span class="cna-feed-views"><?= __('total_readers', ['count' => number_format((int) $item['views_count'])]) ?></span>
                                            
                                            <div class="d-inline-flex align-items-center gap-1.5 ms-auto">
                                                <button type="button" class="btn btn-quick-view btn-sm qv-trigger-btn py-0.5 px-2" data-article='<?= $itemData ?>' title="Quick Preview">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <?php if (\App\Core\Auth::readerCheck() || \App\Core\Auth::check()) { ?>
                                                    <button type="button" class="btn btn-bookmark btn-sm bookmark-toggle-btn py-0.5 px-2" data-id="<?= $item['id'] ?>" data-article='<?= $itemData ?>' title="Save for later">
                                                        <i class="bi bi-bookmark"></i>
                                                    </button>
                                                <?php } ?>
                                                <a href="<?= url('article.php?slug=' . urlencode($item['slug'])) ?>" class="cna-feed-read ms-1"><?= __('read_story') ?> &rarr;</a>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            <?php } ?>
                        <?php } ?>
                    </div><!-- /.cna-news-list -->

                    <!-- ── CNA-Style Pagination ── -->
                    <?php if (empty($noSearchResults) && isset($totalPages) && $totalPages > 1) { ?>
                        <nav aria-label="<?= __('page_navigation') ?? 'Page navigation' ?>" class="mt-4 mb-2">
                            <ul class="pagination justify-content-center" style="gap:0.25rem;">

                                <!-- Previous -->
                                <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link"
                                        style="border-radius:2px; font-weight:700; font-size:0.85rem; padding:0.5rem 1rem;"
                                        href="<?= url('index.php?' . http_build_query(array_merge($_GET, ['page' => $currentPage - 1]))) ?>">
                                        <?= __('pagination_prev') ?>
                                    </a>
                                </li>

                                <!-- Page Numbers (smart window of 5 pages max) -->
                                <?php
                                $windowSize = 5;
                                $halfWindow = (int) floor($windowSize / 2);
                                $startPage = max(1, $currentPage - $halfWindow);
                                $endPage = min($totalPages, $startPage + $windowSize - 1);
                                if ($endPage - $startPage < $windowSize - 1) {
                                    $startPage = max(1, $endPage - $windowSize + 1);
                                }

                                if ($startPage > 1) { ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            style="border-radius:2px; font-size:0.85rem; min-width:36px; text-align:center;"
                                            href="<?= url('index.php?' . http_build_query(array_merge($_GET, ['page' => 1]))) ?>"><?= km_num(1) ?></a>
                                    </li>
                                    <?php if ($startPage > 2) { ?>
                                        <li class="page-item disabled">
                                            <span class="page-link"
                                                style="border-radius:2px; font-size:0.85rem; min-width:36px; text-align:center;">…</span>
                                        </li>
                                    <?php }
                                }

                                for ($p = $startPage; $p <= $endPage; $p++) { ?>
                                    <li class="page-item <?= ($p === $currentPage) ? 'active' : '' ?>">
                                        <a class="page-link"
                                            style="border-radius:2px; font-size:0.85rem; min-width:36px; text-align:center; font-weight:700;"
                                            href="<?= url('index.php?' . http_build_query(array_merge($_GET, ['page' => $p]))) ?>">
                                            <?= km_num($p) ?>
                                        </a>
                                    </li>
                                <?php }

                                if ($endPage < $totalPages) {
                                    if ($endPage < $totalPages - 1) { ?>
                                        <li class="page-item disabled">
                                            <span class="page-link"
                                                style="border-radius:2px; font-size:0.85rem; min-width:36px; text-align:center;">…</span>
                                        </li>
                                    <?php } ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            style="border-radius:2px; font-size:0.85rem; min-width:36px; text-align:center;"
                                            href="<?= url('index.php?' . http_build_query(array_merge($_GET, ['page' => $totalPages]))) ?>"><?= km_num($totalPages) ?></a>
                                    </li>
                                <?php } ?>

                                <!-- Next -->
                                <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                    <a class="page-link"
                                        style="border-radius:2px; font-weight:700; font-size:0.85rem; padding:0.5rem 1rem;"
                                        href="<?= url('index.php?' . http_build_query(array_merge($_GET, ['page' => $currentPage + 1]))) ?>">
                                        <?= __('pagination_next') ?>
                                    </a>
                                </li>

                            </ul>

                            <!-- Page info text -->
                            <p class="text-center mt-2" style="font-size:0.78rem; color:#9ca3af;">
                                <?= __('page_x_of_y', ['current' => $currentPage, 'total' => $totalPages]) ?>
                                &nbsp;&mdash;&nbsp;
                                <?= __('total_articles_count', ['count' => $totalCount]) ?>
                            </p>
                        </nav>
                    <?php } ?>

                </div><!-- /.col-lg-8 -->

                <!-- ── Sidebar ──────────────────────────────────── -->
                <div class="col-lg-4">
                    <?php include __DIR__ . '/../components/sidebar.php'; ?>
                </div>

            </div><!-- /.row -->

        <?php } ?>

    </div><!-- /.homepage-body -->
</div><!-- /.homepage-wrapper -->