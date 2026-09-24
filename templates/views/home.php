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
         HERO SEARCH SECTION
    ====================================================== -->
    <div class="hero-search-section">
        <div class="container">
            <div class="hero-search-inner">

                <?php if (!empty($searchQuery)) { ?>
                    <!-- Active Search State -->
                    <div class="d-inline-flex align-items-center gap-2 mb-3 px-3 py-1"
                        style="background:rgba(255,255,255,0.10); border:1px solid rgba(255,255,255,0.2); font-size:0.82rem; color:rgba(255,255,255,0.85); border-radius:2px;">
                        <i class="bi bi-search" style="color:#c8102e;"></i>
                        <span><?= __('search_results_for') ?? 'Results for' ?>:
                            <strong>"<?= e($searchQuery) ?>"</strong></span>
                        <a href="<?= url('index.php') ?>" class="ms-2 text-decoration-none fw-bold" style="color:#fca5a5;">
                            <i class="bi bi-x-circle"></i> <?= __('clear_search') ?? 'Clear' ?>
                        </a>
                    </div>
                <?php } else { ?>
                    <!-- Default hero headline -->
                    <div class="hero-eyebrow">
                        <span class="live-dot me-1"></span>
                        <?= __('latest_stream') ?>
                    </div>
                    <h2 class="hero-title"><?= __('site_tagline') ?></h2>
                <?php } ?>

                <!-- Search Bar with Real-Time Live Search & Dropdown -->
                <form class="hero-search-form position-relative" action="<?= url('index.php') ?>" method="GET"
                    role="search" id="heroSearchForm">
                    <div class="hero-search-bar">
                        <span class="search-icon-left">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="q" id="heroSearchInput" class="hero-search-input"
                            placeholder="<?= __('search_placeholder') ?>" value="<?= e($_GET['q'] ?? '') ?>"
                            autocomplete="off" aria-label="<?= __('search_placeholder') ?>">
                        <span id="heroSearchClear"
                            class="bootstrap-search-clear text-muted p-2 me-1 <?= empty($_GET['q']) ? 'd-none' : '' ?>"
                            title="<?= __('clear_search') ?>">
                            <i class="bi bi-x-circle-fill" style="font-size:1rem; color:#9ca3af;"></i>
                        </span>
                        <button type="submit" class="hero-search-btn">
                            <i class="bi bi-search"></i>
                            <span class="d-none d-sm-inline"><?= __('search_btn') ?? 'Search' ?></span>
                        </button>
                    </div>

                    <!-- Live Search Preview Dropdown -->
                    <div id="heroLiveSearchDropdown"
                        class="card border-0 shadow-lg position-absolute w-100 mt-1 text-start"
                        style="display:none; top:100%; left:0; z-index:1060; max-height:420px; overflow-y:auto; border-radius:2px;">
                        <div class="list-group list-group-flush" id="heroLiveSearchList"></div>
                    </div>

                    <!-- Quick Topic Chips -->
                    <?php if (empty($searchQuery)) { ?>
                        <div class="search-topic-chips">
                            <?php
                            $chipDb = \App\Core\Database::getInstance();
                            $chipCats = $chipDb->fetchAll("SELECT * FROM categories LIMIT 5");
                            foreach ($chipCats as $chip) { ?>
                                <a href="<?= url('index.php?category=' . $chip['id']) ?>" class="topic-chip">
                                    <?= e(cat_name($chip['name'])) ?>
                                </a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </form>

            </div>
        </div>
    </div>

    <!-- ======================================================
         MAIN BODY
    ====================================================== -->
    <div class="container homepage-body py-4">

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
                    'title' => article_title($lead['title']),
                    'title_kh' => article_title($lead['title'], 'kh'),
                    'title_en' => article_title($lead['title'], 'en'),
                    'summary' => article_summary($lead['summary']),
                    'summary_kh' => article_summary($lead['summary'], 'kh'),
                    'summary_en' => article_summary($lead['summary'], 'en'),
                    'category' => cat_name($lead['category_name']),
                    'category_kh' => cat_name($lead['category_name'], 'kh'),
                    'category_en' => cat_name($lead['category_name'], 'en'),
                    'author' => $lead['author_name'],
                    'date' => \App\Core\TemplateEngine::formatDate($lead['published_at']),
                    'time_ago' => \App\Core\TemplateEngine::timeAgo($lead['published_at']),
                    'reading_time' => $lead['reading_time'] ?? '3 min read',
                    'views' => number_format((int)$lead['views_count']),
                    'image' => $lead['featured_image'] ?? '',
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
                    <div class="lead-article-image">
                        <?php if (!empty($lead['featured_image'])) { ?>
                            <img src="<?= e($lead['featured_image']) ?>" alt="<?= e(article_title($lead['title'])) ?>" loading="eager">
                        <?php } else { ?>
                            <div class="d-flex align-items-center justify-content-center h-100 fs-1 text-muted"
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
                        <div class="lead-meta-top">
                            <span><?= e($lead['author_name']) ?></span>
                            <span>&bull;</span>
                            <span><?= \App\Core\TemplateEngine::timeAgo($lead['published_at']) ?></span>
                        </div>

                        <h2 class="lead-article-title">
                            <a href="<?= url('article.php?slug=' . urlencode($lead['slug'])) ?>">
                                <?= e(article_title($lead['title'])) ?>
                            </a>
                        </h2>

                        <p class="lead-article-summary"><?= e(article_summary($lead['summary'])) ?></p>

                        <div class="lead-article-footer d-flex align-items-center justify-content-between w-100">
                            <a href="<?= url('article.php?slug=' . urlencode($lead['slug'])) ?>" class="lead-read-link">
                                <?= __('read_full_article') ?> <i class="bi bi-chevron-right"></i>
                            </a>
                            <div class="d-flex align-items-center gap-2 ms-auto">
                                <button type="button" class="btn btn-quick-view btn-sm qv-trigger-btn" data-article='<?= $leadData ?>'>
                                    <i class="bi bi-eye me-1"></i><?= __('quick_view') ?? 'Quick View' ?>
                                </button>
                                <button type="button" class="btn btn-bookmark btn-sm bookmark-toggle-btn" data-id="<?= $lead['id'] ?>" data-article='<?= $leadData ?>'>
                                    <i class="bi bi-bookmark"></i>
                                </button>
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
                            'title' => article_title($sItem['title']),
                            'title_kh' => article_title($sItem['title'], 'kh'),
                            'title_en' => article_title($sItem['title'], 'en'),
                            'summary' => article_summary($sItem['summary']),
                            'summary_kh' => article_summary($sItem['summary'], 'kh'),
                            'summary_en' => article_summary($sItem['summary'], 'en'),
                            'category' => cat_name($sItem['category_name']),
                            'category_kh' => cat_name($sItem['category_name'], 'kh'),
                            'category_en' => cat_name($sItem['category_name'], 'en'),
                            'author' => $sItem['author_name'],
                            'date' => \App\Core\TemplateEngine::formatDate($sItem['published_at']),
                            'time_ago' => \App\Core\TemplateEngine::timeAgo($sItem['published_at']),
                            'reading_time' => $sItem['reading_time'] ?? '3 min read',
                            'views' => number_format((int)$sItem['views_count']),
                            'image' => $sItem['featured_image'] ?? '',
                            'url' => url('article.php?slug=' . urlencode($sItem['slug']))
                        ]), ENT_QUOTES, 'UTF-8');
                        ?>
                        <div class="secondary-grid-card">
                            <div class="secondary-grid-thumb">
                                <?php if (!empty($sItem['featured_image'])) { ?>
                                    <img src="<?= e($sItem['featured_image']) ?>" alt="<?= e(article_title($sItem['title'])) ?>" loading="lazy">
                                <?php } else { ?>
                                    <div class="cna-feed-thumb-empty">NP</div>
                                <?php } ?>
                                <span class="lead-badge-pill bg-dark-pill position-absolute" style="top:0.6rem; left:0.6rem;">
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
                                        <?= e(article_title($sItem['title'])) ?>
                                    </a>
                                </h3>
                                <p class="secondary-grid-summary"><?= e(article_summary($sItem['summary'])) ?></p>
                                <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top">
                                    <span class="text-xs text-muted">
                                        <i class="bi bi-clock me-1"></i><?= e($sItem['reading_time'] ?? '3 min read') ?>
                                    </span>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <button type="button" class="btn btn-quick-view btn-sm qv-trigger-btn" data-article='<?= $sData ?>'>
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-bookmark btn-sm bookmark-toggle-btn" data-id="<?= $sItem['id'] ?>" data-article='<?= $sData ?>'>
                                            <i class="bi bi-bookmark"></i>
                                        </button>
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
                            <?= __('showing_reports', ['count' => $totalCount]) ?>
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
                                    'title' => article_title($item['title']),
                                    'title_kh' => article_title($item['title'], 'kh'),
                                    'title_en' => article_title($item['title'], 'en'),
                                    'summary' => article_summary($item['summary']),
                                    'summary_kh' => article_summary($item['summary'], 'kh'),
                                    'summary_en' => article_summary($item['summary'], 'en'),
                                    'category' => cat_name($item['category_name']),
                                    'category_kh' => cat_name($item['category_name'], 'kh'),
                                    'category_en' => cat_name($item['category_name'], 'en'),
                                    'author' => $item['author_name'],
                                    'date' => \App\Core\TemplateEngine::formatDate($item['published_at']),
                                    'time_ago' => \App\Core\TemplateEngine::timeAgo($item['published_at']),
                                    'reading_time' => $item['reading_time'] ?? '3 min read',
                                    'views' => number_format((int)$item['views_count']),
                                    'image' => $item['featured_image'] ?? '',
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
                                                <img src="<?= e($item['featured_image']) ?>" alt="<?= e(article_title($item['title'])) ?>" loading="lazy">
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
                                                <?= e(article_title($item['title'])) ?>
                                            </a>
                                        </h4>

                                        <!-- Summary -->
                                        <p class="cna-feed-summary"><?= e(article_summary($item['summary'])) ?></p>

                                        <!-- Bottom row: author · views · actions -->
                                        <div class="cna-feed-footer">
                                            <span class="cna-feed-author"><?= e($item['author_name']) ?></span>
                                            <span class="cna-feed-dot"></span>
                                            <span class="cna-feed-views"><?= __('total_readers', ['count' => number_format((int) $item['views_count'])]) ?></span>
                                            
                                            <div class="d-inline-flex align-items-center gap-1.5 ms-auto">
                                                <button type="button" class="btn btn-quick-view btn-sm qv-trigger-btn py-0.5 px-2" data-article='<?= $itemData ?>' title="Quick Preview">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-bookmark btn-sm bookmark-toggle-btn py-0.5 px-2" data-id="<?= $item['id'] ?>" data-article='<?= $itemData ?>' title="Save for later">
                                                    <i class="bi bi-bookmark"></i>
                                                </button>
                                                <a href="<?= url('article.php?slug=' . urlencode($item['slug'])) ?>" class="cna-feed-read ms-1"><?= __('read_story') ?> &rarr;</a>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            <?php } ?>
                        <?php } ?>
                    </div><!-- /.cna-news-list -->

                    <!-- ── CNA-Style Pagination ── -->
                    <?php if (isset($totalPages) && $totalPages > 1) { ?>
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