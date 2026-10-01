<?php
/**
 * CMS Admin News Archive ("បណ្ណសារព័ត៌មាន") Management View
 * news-platform / templates / admin / views / archive.php
 */
$categoryFilter = $filters['category'] ?? 0;
$yearFilter = $filters['year'] ?? 0;
$monthFilter = $filters['month'] ?? 0;
$blueprintFilter = $filters['blueprint'] ?? '';
$statusFilter = $filters['status'] ?? '';
$breakingFilter = $filters['breaking'] ?? null;
$searchFilter = $filters['q'] ?? '';
$sortFilter = $filters['sort'] ?? 'newest';

$hasActiveFilters = ($categoryFilter > 0 || $yearFilter > 0 || $monthFilter > 0 || !empty($blueprintFilter) || !empty($statusFilter) || $breakingFilter === 1 || !empty($searchFilter) || $sortFilter !== 'newest');

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

<div class="container-fluid px-4 py-4">

    <!-- Top Navigation & Actions -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="small text-muted mb-1">
                <a href="<?= url('admin/dashboard.php') ?>" class="text-secondary text-decoration-none">
                    &larr; <?= __('editorial_overview') ?>
                </a>
            </div>
            <h2 class="editorial-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-archive-fill text-danger fs-3"></i>
                <span><?= __('archive_page_title') ?> (CMS Archive Control)</span>
            </h2>
            <div class="text-muted small mt-1">
                គ្រប់គ្រង ស្វែងរក និងច្រោះព័ត៌មានចាស់ៗទាំងអស់ក្នុងប្រព័ន្ធ
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 fs-6 rounded-pill">
                <?= number_format($totalCount) ?> <?= __('archive_articles_count') ?>
            </span>
            <a href="<?= url('admin/article-create.php') ?>" class="btn btn-danger px-3 py-2 fw-semibold rounded-2 shadow-sm text-nowrap">
                <i class="bi bi-plus-lg me-1"></i> <?= __('draft_new_article') ?>
            </a>
            <a href="<?= url('archive.php') ?>" target="_blank" class="btn btn-outline-secondary px-3 py-2 fw-semibold rounded-2 text-nowrap" title="<?= __('live_public_site') ?>">
                <i class="bi bi-box-arrow-up-right me-1"></i> Public Archive
            </a>
        </div>
    </div>

    <!-- Filter Control Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3.5">
            <form action="<?= url('admin/archive.php') ?>" method="GET" id="adminArchiveFilterForm">
                <div class="row g-2.5">
                    <!-- Search Keyword -->
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label text-2xs fw-bold text-muted mb-1"><?= __('search_placeholder') ?></label>
                        <input type="text" name="q" class="form-control form-control-sm"
                            placeholder="ស្វែងរកតាមចំណងជើង ឬខ្លឹមសារ..."
                            value="<?= e($searchFilter) ?>">
                    </div>

                    <!-- Category -->
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label text-2xs fw-bold text-muted mb-1"><?= __('categories') ?></label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="0"><?= __('filter_category_all') ?></option>
                            <?php foreach ($categories as $cat) { ?>
                                <option value="<?= (int)$cat['id'] ?>" <?= $categoryFilter === (int)$cat['id'] ? 'selected' : '' ?>>
                                    <?= e(cat_name($cat['name'])) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Year -->
                    <div class="col-md-2 col-lg-1">
                        <label class="form-label text-2xs fw-bold text-muted mb-1"><?= __('filter_year') ?></label>
                        <select name="year" class="form-select form-select-sm">
                            <option value="0"><?= __('filter_year_all') ?></option>
                            <?php foreach ($years as $yr) { ?>
                                <option value="<?= (int)$yr ?>" <?= $yearFilter === (int)$yr ? 'selected' : '' ?>>
                                    <?= (int)$yr ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Month -->
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label text-2xs fw-bold text-muted mb-1"><?= __('filter_month') ?></label>
                        <select name="month" class="form-select form-select-sm">
                            <option value="0"><?= __('filter_month_all') ?></option>
                            <?php foreach ($khMonths as $mNum => $mLabel) { ?>
                                <option value="<?= $mNum ?>" <?= $monthFilter === $mNum ? 'selected' : '' ?>>
                                    <?= $mLabel ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="col-md-2 col-lg-1">
                        <label class="form-label text-2xs fw-bold text-muted mb-1"><?= __('status') ?></label>
                        <select name="status" class="form-select form-select-sm">
                            <option value=""><?= __('all') ?? 'All' ?></option>
                            <option value="published" <?= $statusFilter === 'published' ? 'selected' : '' ?>><?= __('published') ?></option>
                            <option value="draft" <?= $statusFilter === 'draft' ? 'selected' : '' ?>><?= __('draft') ?></option>
                            <option value="archived" <?= $statusFilter === 'archived' ? 'selected' : '' ?>><?= __('archived') ?></option>
                        </select>
                    </div>

                    <!-- Blueprint -->
                    <div class="col-md-2 col-lg-1">
                        <label class="form-label text-2xs fw-bold text-muted mb-1">Blueprint</label>
                        <select name="blueprint" class="form-select form-select-sm">
                            <option value=""><?= __('all') ?? 'All' ?></option>
                            <option value="standard" <?= $blueprintFilter === 'standard' ? 'selected' : '' ?>>Standard</option>
                            <option value="investigative" <?= $blueprintFilter === 'investigative' ? 'selected' : '' ?>>Investigative</option>
                            <option value="opinion" <?= $blueprintFilter === 'opinion' ? 'selected' : '' ?>>Opinion</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label text-2xs fw-bold text-muted mb-1"><?= __('filter_sort_label') ?></label>
                        <select name="sort" class="form-select form-select-sm">
                            <option value="newest" <?= $sortFilter === 'newest' ? 'selected' : '' ?>><?= __('filter_sort_newest') ?></option>
                            <option value="oldest" <?= $sortFilter === 'oldest' ? 'selected' : '' ?>><?= __('filter_sort_oldest') ?></option>
                            <option value="views" <?= $sortFilter === 'views' ? 'selected' : '' ?>><?= __('filter_sort_views') ?></option>
                            <option value="alpha" <?= $sortFilter === 'alpha' ? 'selected' : '' ?>><?= __('filter_sort_alpha') ?></option>
                        </select>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-2 border-top">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input cursor-pointer" type="checkbox" name="breaking" value="1" id="adminFilterBreaking" <?= $breakingFilter === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label text-xs fw-bold text-dark cursor-pointer" for="adminFilterBreaking">
                            <span class="badge bg-danger text-white text-3xs me-1">HOT</span> <?= __('filter_breaking_only') ?>
                        </label>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <?php if ($hasActiveFilters) { ?>
                            <a href="<?= url('admin/archive.php') ?>" class="btn btn-outline-secondary btn-sm px-3 rounded-2 text-xs">
                                <i class="bi bi-arrow-counterclockwise me-1"></i><?= __('filter_reset_btn') ?>
                            </a>
                        <?php } ?>
                        <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold rounded-2 text-xs shadow-sm">
                            <i class="bi bi-funnel-fill me-1"></i><?= __('filter_apply_btn') ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Articles Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-2xs text-uppercase text-secondary fw-bold">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th><?= __('article_title') ?></th>
                        <th><?= __('categories') ?></th>
                        <th><?= __('author') ?></th>
                        <th>Blueprint</th>
                        <th><?= __('status') ?></th>
                        <th><?= __('views') ?? 'Views' ?></th>
                        <th><?= __('published_at') ?? 'Published' ?></th>
                        <th class="text-end" style="min-width: 140px;"><?= __('actions') ?></th>
                    </tr>
                </thead>
                <tbody class="small">
                    <?php if (!empty($articles)) { ?>
                        <?php foreach ($articles as $art) { ?>
                            <?php 
                            $artTitle = article_title($art);
                            $editUrl = url('admin/article-edit.php?id=' . $art['id']);
                            $publicUrl = url('article.php?slug=' . urlencode($art['slug']));
                            $deleteUrl = url('admin/actions/delete-article.php?id=' . $art['id'] . '&csrf_token=' . e($csrfToken));
                            ?>
                            <tr>
                                <td class="text-muted text-xs"><?= (int)$art['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <?php if (!empty($art['featured_image'])) { ?>
                                            <img src="<?= e(str_starts_with($art['featured_image'], 'http') ? $art['featured_image'] : url($art['featured_image'])) ?>"
                                                alt="" class="rounded object-fit-cover flex-shrink-0" style="width: 44px; height: 44px;">
                                        <?php } ?>
                                        <div class="overflow-hidden">
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 380px;">
                                                <a href="<?= $editUrl ?>" class="text-dark text-decoration-none hover-danger">
                                                    <?= e($artTitle) ?>
                                                </a>
                                            </div>
                                            <div class="text-2xs text-muted text-truncate" style="max-width: 380px;">
                                                /article.php?slug=<?= e($art['slug']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-2xs">
                                        <?= e(cat_name($art['category_name'])) ?>
                                    </span>
                                </td>
                                <td class="text-muted text-xs"><?= e($art['author_name'] ?? 'Admin') ?></td>
                                <td>
                                    <span class="badge bg-light text-secondary border text-3xs text-uppercase">
                                        <?= e($art['template_type'] ?? 'standard') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($art['status'] === 'published') { ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle text-3xs">
                                            <?= __('published') ?>
                                        </span>
                                    <?php } elseif ($art['status'] === 'draft') { ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle text-3xs">
                                            <?= __('draft') ?>
                                        </span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle text-3xs">
                                            <?= __('archived') ?>
                                        </span>
                                    <?php } ?>
                                    <?php if (!empty($art['is_breaking'])) { ?>
                                        <span class="badge bg-danger text-white text-3xs">HOT</span>
                                    <?php } ?>
                                </td>
                                <td class="text-muted text-xs">
                                    <i class="bi bi-eye me-1"></i><?= number_format((int)$art['views_count']) ?>
                                </td>
                                <td class="text-muted text-2xs">
                                    <?= !empty($art['published_at']) ? \App\Core\TemplateEngine::formatDate($art['published_at'], 'M j, Y H:i') : '<span class="text-muted fst-italic">Unpublished</span>' ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= $editUrl ?>" class="btn btn-outline-primary py-0.5 px-2 text-xs" title="<?= __('edit') ?>">
                                            <i class="bi bi-pencil-square me-1"></i><?= __('edit') ?>
                                        </a>
                                        <a href="<?= $publicUrl ?>" target="_blank" class="btn btn-outline-secondary py-0.5 px-2 text-xs" title="View Article">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if (\App\Core\Auth::hasRole('admin')) { ?>
                                            <a href="#" class="btn btn-outline-danger py-0.5 px-2 text-xs"
                                                onclick="confirmDeleteCard('<?= $deleteUrl ?>', <?= htmlspecialchars(json_encode($artTitle), ENT_QUOTES, 'UTF-8') ?>); return false;"
                                                title="<?= __('delete') ?>">
                                                <i class="bi bi-trash3"></i>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2 text-muted"></i>
                                <div><?= __('no_articles_found') ?></div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1) { ?>
            <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
                <span class="small text-muted">
                    Page <?= $currentPage ?> of <?= $totalPages ?> (<?= number_format($totalCount) ?> articles)
                </span>
                <nav>
                    <ul class="pagination pagination-sm mb-0 gap-1">
                        <?php
                        $queryParams = $_GET;
                        unset($queryParams['page']);
                        $queryString = http_build_query($queryParams);
                        $linkBase = url('admin/archive.php') . ($queryString ? '?' . $queryString . '&page=' : '?page=');
                        ?>
                        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link rounded-2" href="<?= $linkBase . ($currentPage - 1) ?>">&laquo;</a>
                        </li>
                        <?php for ($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++) { ?>
                            <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
                                <a class="page-link rounded-2 <?= $p === $currentPage ? 'bg-danger border-danger' : '' ?>" href="<?= $linkBase . $p ?>"><?= $p ?></a>
                            </li>
                        <?php } ?>
                        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link rounded-2" href="<?= $linkBase . ($currentPage + 1) ?>">&raquo;</a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php } ?>
    </div>

</div>
