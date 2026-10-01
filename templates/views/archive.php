<?php
/**
 * News Archive View ("បណ្ណសារព័ត៌មាន")
 * news-platform / templates / views / archive.php
 */
$categoryFilter = $filters['category'] ?? 0;
$yearFilter = $filters['year'] ?? 0;
$monthFilter = $filters['month'] ?? 0;
$blueprintFilter = $filters['blueprint'] ?? '';
$breakingFilter = $filters['breaking'] ?? null;
$searchFilter = $filters['q'] ?? '';
$sortFilter = $filters['sort'] ?? 'newest';

$hasActiveFilters = ($categoryFilter > 0 || $yearFilter > 0 || $monthFilter > 0 || !empty($blueprintFilter) || $breakingFilter === 1 || !empty($searchFilter) || $sortFilter !== 'newest');

$khMonths = [
    1 => 'មករា (Jan)',
    2 => 'កុម្ភៈ (Feb)',
    3 => 'មីនា (Mar)',
    4 => 'មេសា (Apr)',
    5 => 'ឧសភា (May)',
    6 => 'មិថុនា (Jun)',
    7 => 'កក្កដា (Jul)',
    8 => 'សីហា (Aug)',
    9 => 'កញ្ញា (Sep)',
    10 => 'តុលា (Oct)',
    11 => 'វិច្ឆិកា (Nov)',
    12 => 'ធ្នូ (Dec)',
];
?>

<div class="archive-page py-4">
    <div class="container">

        <!-- Header Breadcrumb & Title -->
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-2 text-muted">
                    <li class="breadcrumb-item"><a href="<?= url('index.php') ?>" class="text-decoration-none text-muted"><i class="bi bi-house-door me-1"></i><?= __('nav_home') ?></a></li>
                    <li class="breadcrumb-item active text-danger fw-bold" aria-current="page"><?= __('nav_archive') ?></li>
                </ol>
            </nav>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 border-bottom pb-3">
                <div>
                    <h1 class="editorial-title display-6 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-archive-fill text-danger fs-3"></i>
                        <span><?= __('archive_page_title') ?></span>
                    </h1>
                    <p class="text-muted mb-0 small"><?= __('archive_subtitle') ?></p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 fs-6 rounded-pill">
                        <i class="bi bi-newspaper me-1"></i> <?= number_format($totalCount) ?> <?= __('archive_articles_count') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Filter Control Box -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
            <div class="card-body p-4">
                <form action="<?= url('archive.php') ?>" method="GET" id="archiveFilterForm">
                    <div class="row g-3">
                        <!-- Search Keyword -->
                        <div class="col-md-4 col-lg-3">
                            <label class="form-label small fw-bold text-muted mb-1">
                                <i class="bi bi-search text-danger me-1"></i><?= __('search_placeholder') ?>
                            </label>
                            <input type="text" name="q" class="form-control form-control-sm"
                                placeholder="<?= __('search_placeholder') ?>..."
                                value="<?= e($searchFilter) ?>">
                        </div>

                        <!-- Category Filter -->
                        <div class="col-md-4 col-lg-3">
                            <label class="form-label small fw-bold text-muted mb-1">
                                <i class="bi bi-tag text-danger me-1"></i><?= __('filter_category_all') ?>
                            </label>
                            <select name="category" class="form-select form-select-sm">
                                <option value="0"><?= __('filter_category_all') ?></option>
                                <?php foreach ($categories as $cat) { ?>
                                    <option value="<?= (int)$cat['id'] ?>" <?= $categoryFilter === (int)$cat['id'] ? 'selected' : '' ?>>
                                        <?= e(category_name($cat)) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div class="col-md-2 col-lg-2">
                            <label class="form-label small fw-bold text-muted mb-1">
                                <i class="bi bi-calendar text-danger me-1"></i><?= __('filter_year') ?>
                            </label>
                            <select name="year" class="form-select form-select-sm">
                                <option value="0"><?= __('filter_year_all') ?></option>
                                <?php foreach ($years as $yr) { ?>
                                    <option value="<?= (int)$yr ?>" <?= $yearFilter === (int)$yr ? 'selected' : '' ?>>
                                        <?= (int)$yr ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Month Filter -->
                        <div class="col-md-2 col-lg-2">
                            <label class="form-label small fw-bold text-muted mb-1">
                                <i class="bi bi-calendar-month text-danger me-1"></i><?= __('filter_month') ?>
                            </label>
                            <select name="month" class="form-select form-select-sm">
                                <option value="0"><?= __('filter_month_all') ?></option>
                                <?php foreach ($khMonths as $mNum => $mLabel) { ?>
                                    <option value="<?= $mNum ?>" <?= $monthFilter === $mNum ? 'selected' : '' ?>>
                                        <?= $mLabel ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Blueprint Format -->
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label small fw-bold text-muted mb-1">
                                <i class="bi bi-layout-text-window text-danger me-1"></i><?= __('filter_blueprint') ?>
                            </label>
                            <select name="blueprint" class="form-select form-select-sm">
                                <option value=""><?= __('filter_blueprint_all') ?></option>
                                <option value="standard" <?= $blueprintFilter === 'standard' ? 'selected' : '' ?>><?= __('blueprint_standard') ?></option>
                                <option value="investigative" <?= $blueprintFilter === 'investigative' ? 'selected' : '' ?>><?= __('blueprint_investigative') ?></option>
                                <option value="opinion" <?= $blueprintFilter === 'opinion' ? 'selected' : '' ?>><?= __('blueprint_opinion') ?></option>
                            </select>
                        </div>

                        <!-- Sort Order -->
                        <div class="col-md-3 col-lg-3">
                            <label class="form-label small fw-bold text-muted mb-1">
                                <i class="bi bi-sort-down text-danger me-1"></i><?= __('filter_sort_label') ?>
                            </label>
                            <select name="sort" class="form-select form-select-sm">
                                <option value="newest" <?= $sortFilter === 'newest' ? 'selected' : '' ?>><?= __('filter_sort_newest') ?></option>
                                <option value="oldest" <?= $sortFilter === 'oldest' ? 'selected' : '' ?>><?= __('filter_sort_oldest') ?></option>
                                <option value="views" <?= $sortFilter === 'views' ? 'selected' : '' ?>><?= __('filter_sort_views') ?></option>
                                <option value="alpha" <?= $sortFilter === 'alpha' ? 'selected' : '' ?>><?= __('filter_sort_alpha') ?></option>
                            </select>
                        </div>

                        <!-- Breaking Only Toggle & Action Buttons -->
                        <div class="col-md-9 col-lg-9 d-flex flex-wrap align-items-end justify-content-between gap-2">
                            <div class="form-check form-switch mb-1">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="breaking" value="1" id="filterBreaking" <?= $breakingFilter === 1 ? 'checked' : '' ?>>
                                <label class="form-check-label small fw-bold text-dark cursor-pointer" for="filterBreaking">
                                    <span class="badge bg-danger text-white me-1 text-2xs">HOT</span> <?= __('filter_breaking_only') ?>
                                </label>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <?php if ($hasActiveFilters) { ?>
                                    <a href="<?= url('archive.php') ?>" class="btn btn-outline-secondary btn-sm px-3 rounded-2">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i><?= __('filter_reset_btn') ?>
                                    </a>
                                <?php } ?>
                                <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold rounded-2 shadow-sm">
                                    <i class="bi bi-funnel-fill me-1"></i><?= __('filter_apply_btn') ?>
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

                <!-- Active Filter Tags Ribbon -->
                <?php if ($hasActiveFilters) { ?>
                    <div class="d-flex flex-wrap align-items-center gap-2 pt-3 mt-3 border-top">
                        <span class="text-xs fw-bold text-muted"><?= __('filter_active_label') ?>:</span>
                        <?php if (!empty($searchFilter)) { ?>
                            <span class="badge bg-light text-dark border text-2xs px-2 py-1">
                                <?= __('search_placeholder') ?>: "<?= e($searchFilter) ?>"
                            </span>
                        <?php } ?>
                        <?php if ($categoryFilter > 0) { ?>
                            <?php 
                            $matchedCat = null;
                            foreach ($categories as $c) { if ((int)$c['id'] === $categoryFilter) { $matchedCat = $c; break; } }
                            ?>
                            <span class="badge bg-light text-dark border text-2xs px-2 py-1">
                                <?= __('filter_category_all') ?>: <?= e(category_name($matchedCat)) ?>
                            </span>
                        <?php } ?>
                        <?php if ($yearFilter > 0) { ?>
                            <span class="badge bg-light text-dark border text-2xs px-2 py-1">
                                <?= __('filter_year') ?>: <?= $yearFilter ?>
                            </span>
                        <?php } ?>
                        <?php if ($monthFilter > 0) { ?>
                            <span class="badge bg-light text-dark border text-2xs px-2 py-1">
                                <?= __('filter_month') ?>: <?= $khMonths[$monthFilter] ?? $monthFilter ?>
                            </span>
                        <?php } ?>
                        <?php if (!empty($blueprintFilter)) { ?>
                            <span class="badge bg-light text-dark border text-2xs px-2 py-1">
                                <?= __('filter_blueprint') ?>: <?= ucfirst($blueprintFilter) ?>
                            </span>
                        <?php } ?>
                        <?php if ($breakingFilter === 1) { ?>
                            <span class="badge bg-danger text-white text-2xs px-2 py-1">
                                <?= __('filter_breaking_only') ?>
                            </span>
                        <?php } ?>
                        <a href="<?= url('archive.php') ?>" class="text-xs text-danger text-decoration-none fw-bold ms-1">
                            <?= __('clear_search') ?> &times;
                        </a>
                    </div>
                <?php } ?>

            </div>
        </div>

        <!-- Articles Feed Grid -->
        <?php if (!empty($articles)) { ?>
            <div class="row g-4">
                <?php foreach ($articles as $art) { ?>
                    <?php 
                    $artTitle = article_title($art);
                    $artSummary = article_summary($art);
                    $artUrl = url('article.php?slug=' . urlencode($art['slug']));
                    $imgUrl = !empty($art['featured_image']) ? (str_starts_with($art['featured_image'], 'http') ? $art['featured_image'] : url($art['featured_image'])) : url('assets/images/placeholder.jpg');
                    $tplType = $art['template_type'] ?? 'standard';
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden article-card-hover bg-white transition-all d-flex flex-column" style="max-height: 430px;">
                            <!-- Thumbnail with max-height -->
                            <div class="position-relative overflow-hidden flex-shrink-0" style="height: 175px; max-height: 175px; background: #f1f5f9;">
                                <a href="<?= $artUrl ?>" class="d-block w-100 h-100">
                                    <img src="<?= e($imgUrl) ?>" alt="<?= e($artTitle) ?>" class="w-100 h-100 object-fit-cover"
                                         onerror="this.src='<?= url('assets/images/placeholder.jpg') ?>';">
                                </a>
                                <div class="position-absolute top-0 start-0 m-2 d-flex flex-column gap-1" style="z-index: 2;">
                                    <span class="badge bg-danger text-white shadow-sm text-3xs px-2 py-0.5 rounded-1 fw-bold">
                                        <?= e(category_name($art)) ?>
                                    </span>
                                    <?php if (!empty($art['is_breaking'])) { ?>
                                        <span class="badge bg-dark text-white shadow-sm text-3xs px-2 py-0.5 rounded-1 fw-bold">
                                            <span class="live-dot me-1"></span>BREAKING
                                        </span>
                                    <?php } ?>
                                </div>
                                <?php if ($tplType !== 'standard') { ?>
                                    <div class="position-absolute bottom-0 end-0 m-2" style="z-index: 2;">
                                        <span class="badge bg-white bg-opacity-90 text-dark shadow-2xs text-3xs border">
                                            <?= ucfirst($tplType) ?>
                                        </span>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- Content -->
                            <div class="card-body p-3 d-flex flex-column justify-content-between flex-grow-1 overflow-hidden">
                                <div>
                                    <div class="d-flex align-items-center gap-2 text-muted text-3xs mb-1.5">
                                        <span><i class="bi bi-clock me-1"></i><?= e($art['time_ago']) ?></span>
                                        <span>&bull;</span>
                                        <span><i class="bi bi-book me-1"></i><?= $art['reading_time'] ?> <?= __('reading_time_min') ?></span>
                                        <?php if (!empty($art['views_count'])) { ?>
                                            <span>&bull;</span>
                                            <span><i class="bi bi-eye me-1"></i><?= number_format($art['views_count']) ?></span>
                                        <?php } ?>
                                    </div>

                                    <h5 class="fw-bold editorial-title mb-1.5 text-dark" style="font-size: 0.95rem; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        <a href="<?= $artUrl ?>" class="text-dark text-decoration-none hover-danger">
                                            <?= e($artTitle) ?>
                                        </a>
                                    </h5>

                                    <p class="text-muted text-2xs mb-2" style="line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?= e($artSummary) ?>
                                    </p>
                                </div>

                                <div class="pt-2 border-top d-flex align-items-center justify-content-between mt-auto">
                                    <div class="d-flex align-items-center gap-1.5 text-3xs text-muted">
                                        <i class="bi bi-person-circle"></i>
                                        <span><?= e($art['author_name'] ?? 'Editorial') ?></span>
                                    </div>
                                    <a href="<?= $artUrl ?>" class="text-danger small fw-bold text-decoration-none" style="font-size: 0.78rem;">
                                        <?= __('read_more') ?> &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Pagination Bar -->
            <?php if ($totalPages > 1) { ?>
                <div class="d-flex justify-content-center mt-5">
                    <nav aria-label="Archive pagination">
                        <ul class="pagination pagination-sm shadow-sm gap-1">
                            <?php
                            $queryParams = $_GET;
                            unset($queryParams['page']);
                            $queryString = http_build_query($queryParams);
                            $linkBase = url('archive.php') . ($queryString ? '?' . $queryString . '&page=' : '?page=');
                            ?>

                            <!-- Previous Page -->
                            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link rounded-2 px-3" href="<?= $linkBase . ($currentPage - 1) ?>">
                                    &laquo; <?= __('pagination_prev') ?>
                                </a>
                            </li>

                            <?php 
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);
                            if ($startPage > 1) {
                                echo '<li class="page-item"><a class="page-link rounded-2" href="' . $linkBase . '1">1</a></li>';
                                if ($startPage > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }
                            for ($p = $startPage; $p <= $endPage; $p++) { ?>
                                <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link rounded-2 <?= $p === $currentPage ? 'bg-danger border-danger' : '' ?>" href="<?= $linkBase . $p ?>">
                                        <?= $p ?>
                                    </a>
                                </li>
                            <?php } 
                            if ($endPage < $totalPages) {
                                if ($endPage < $totalPages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link rounded-2" href="' . $linkBase . $totalPages . '">' . $totalPages . '</a></li>';
                            }
                            ?>

                            <!-- Next Page -->
                            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link rounded-2 px-3" href="<?= $linkBase . ($currentPage + 1) ?>">
                                    <?= __('pagination_next') ?> &raquo;
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php } ?>

        <?php } else { ?>
            <!-- Empty Results State -->
            <div class="card border-0 shadow-sm rounded-3 p-5 text-center bg-white my-5">
                <div class="mb-3">
                    <i class="bi bi-inbox text-muted display-4"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2"><?= __('archive_no_results') ?></h4>
                <p class="text-muted small mx-auto" style="max-width: 500px;">
                    <?= __('archive_no_results_hint') ?>
                </p>
                <div class="mt-3">
                    <a href="<?= url('archive.php') ?>" class="btn btn-outline-danger px-4 py-2 rounded-2 fw-semibold">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> <?= __('filter_reset_btn') ?>
                    </a>
                </div>
            </div>
        <?php } ?>

    </div>
</div>
