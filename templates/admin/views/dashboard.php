<?php
/**
 * Admin Dashboard View (CMA Overview)
 * news-platform / templates / admin / views / dashboard.php
 */

// Extract article view totals for analytics chart
$chartTitles = [];
$chartViews = [];
foreach (array_slice($articles, 0, 7) as $art) {
    $chartTitles[] = mb_strimwidth($art['title'], 0, 22, '...');
    $chartViews[] = (int) $art['views_count'];
}
?>

<div class="container-fluid px-3 px-md-6 py-4">

    <!-- Dashboard Top Header Bar -->
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <h2 class="fw-bold editorial-title mb-0 text-dark fs-3"><?= __('editorial_overview') ?></h2>
                <span
                    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2.5 py-1 text-xs text-nowrap">
                    <span class="pulsing-dot bg-danger me-1"></span> <?= __('live_feed') ?>
                </span>
            </div>
            <p class="text-muted small mb-0">
                <?= __('welcome_back') ?>, <strong><?= e($currentUser['username']) ?></strong>
                <span class="mx-1">&bull;</span> <span
                    class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill text-xs text-uppercase"><?= e($currentUser['role']) ?></span>
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="<?= url('public/index.php') ?>" target="_blank"
                class="btn btn-outline-dark px-3 py-2 fw-semibold rounded-3 text-nowrap d-inline-flex align-items-center shadow-sm text-xs">
                <span><?= __('live_public_site') ?> &rarr;</span>
            </a>
            <a href="<?= url('admin/article-create.php') ?>"
                class="btn btn-danger text-white px-4 py-2 fw-semibold rounded-3 text-nowrap d-inline-flex align-items-center shadow-sm text-xs">
                <span>+ <?= __('draft_new_article') ?></span>
            </a>
        </div>
    </div>

    <!-- 4 High Level Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card stat-card-modern border-0 shadow-sm bg-white p-3 p-md-3.5 h-100">
                <div>
                    <span
                        class="text-muted text-xs text-uppercase fw-bold tracking-wider d-block mb-1"><?= __('total_articles') ?></span>
                    <h3 class="fw-extrabold text-dark mb-0 fs-3"><?= number_format($totalArticles) ?></h3>
                    <div class="mt-2 text-xs text-muted d-none d-sm-block">
                        <span class="text-success fw-bold">+12%</span> vs last month
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card-modern border-0 shadow-sm bg-white p-3 p-md-3.5 h-100">
                <div>
                    <span
                        class="text-muted text-xs text-uppercase fw-bold tracking-wider d-block mb-1"><?= __('published_live') ?></span>
                    <h3 class="fw-extrabold text-success mb-0 fs-3"><?= number_format($publishedCount) ?></h3>
                    <div class="mt-2 text-xs text-muted d-none d-sm-block">
                        <span class="pulsing-dot bg-success me-1"></span> CDA Active
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card-modern border-0 shadow-sm bg-white p-3 p-md-3.5 h-100">
                <div>
                    <span
                        class="text-muted text-xs text-uppercase fw-bold tracking-wider d-block mb-1"><?= __('drafts_pending') ?></span>
                    <h3 class="fw-extrabold text-warning mb-0 fs-3"><?= number_format($draftCount) ?></h3>
                    <div class="mt-2 text-xs text-muted d-none d-sm-block">
                        <span class="text-warning fw-semibold">In Queue</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card-modern border-0 shadow-sm bg-white p-3 p-md-3.5 h-100">
                <div>
                    <span
                        class="text-muted text-xs text-uppercase fw-bold tracking-wider d-block mb-1"><?= __('active_subscribers') ?></span>
                    <h3 class="fw-extrabold text-danger mb-0 fs-3"><?= number_format($totalSubscribers) ?></h3>
                    <div class="mt-2 text-xs text-muted d-none d-sm-block">
                        <span class="text-danger fw-bold">AJAX Feed</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Section -->
    <div class="row g-4 mb-4">

        <!-- Article Engagement & Views Trend Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div
                    class="chart-card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 rounded-top-4">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark editorial-title fs-5"><?= __('traffic_impressions') ?></h5>
                        <p class="text-muted text-xs mb-0"><?= __('traffic_impressions_sub') ?></p>
                    </div>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button"
                            class="btn btn-outline-secondary active fw-semibold text-xs"><?= __('top_stories') ?></button>
                        <button type="button"
                            class="btn btn-outline-secondary fw-semibold text-xs"><?= __('30_days') ?></button>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div style="height: 260px; position: relative;">
                        <canvas id="trafficAnalyticsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Blueprint Ratio Doughnut Chart -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="chart-card-header rounded-top-4">
                    <h5 class="fw-bold mb-0 text-dark editorial-title fs-5"><?= __('template_ratio') ?></h5>
                    <p class="text-muted text-xs mb-0"><?= __('template_ratio_sub') ?></p>
                </div>
                <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-center align-items-center">
                    <div style="height: 180px; width: 180px; position: relative;" class="mb-3">
                        <canvas id="templateDoughnutChart"></canvas>
                    </div>
                    <div class="w-100 text-xs">
                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom">
                            <span class="fw-semibold text-dark"><?= __('tmpl_standard_name') ?></span>
                            <span class="badge bg-primary rounded-pill"><?= $standardCount ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom">
                            <span class="fw-semibold text-dark"><?= __('tmpl_investigative_name') ?></span>
                            <span class="badge bg-danger rounded-pill"><?= $investigativeCount ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1.5">
                            <span class="fw-semibold text-dark"><?= __('tmpl_opinion_name') ?></span>
                            <span class="badge bg-warning text-dark rounded-pill"><?= $opinionCount ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Main Content Repository Table Section -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card admin-card-clean overflow-hidden">

                <!-- Table Header Bar with Live Filter Controls -->
                <div class="p-4 border-bottom bg-white">
                    <!-- Row 1: Title & Subtitle (Left) + Status Filter Tabs (Right) -->
                    <div
                        class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark editorial-title fs-5"><?= __('publication_repository') ?>
                            </h5>
                            <p class="text-muted text-xs mb-0"><?= __('publication_repository_sub') ?></p>
                        </div>

                        <!-- Quick Filter Tabs -->
                        <div class="btn-group btn-group-sm flex-shrink-0" id="statusFilterTabs">
                            <button class="btn btn-dark btn-sm rounded-pill px-4 filter-tab-btn active"
                                data-filter="all"><?= __('filter_all') ?> (<?= count($articles) ?>)</button>
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-4 filter-tab-btn"
                                data-filter="published"><?= __('published') ?></button>
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-4 filter-tab-btn"
                                data-filter="draft"><?= __('draft') ?></button>
                        </div>
                    </div>

                    <!-- Row 2: Live Search Input Bar -->
                    <div class="input-group input-group-sm mb-2 w-100 w-md-50">
                        <span
                            class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-muted fw-semibold text-xs">
                            <i class="bi bi-search me-1"></i> <?= __('search') ?>
                        </span>
                        <input type="text" id="tableSearchInput"
                            class="form-control bg-light border-start-0 rounded-end-pill pe-3 text-xs"
                            placeholder="<?= __('search_repo_placeholder') ?>">
                    </div>
                </div>

                <!-- Responsive Table Component with Max Height Scrolling -->
                <div class="table-responsive admin-scroll-table">
                    <table class="table table-hover align-middle mb-0 table-custom" id="articlesTable">
                        <thead class="table-light text-xs text-uppercase text-muted border-bottom">
                            <tr>
                                <th class="ps-4" style="min-width: 220px;"><?= __('article_title') ?></th>
                                <th style="min-width: 130px;"><?= __('category') ?></th>
                                <th style="min-width: 140px;"><?= __('template_blueprint') ?></th>
                                <th style="min-width: 110px;"><?= __('status') ?></th>
                                <th style="min-width: 80px;"><?= __('views') ?></th>
                                <th class="pe-4 text-end" style="min-width: 130px;"><?= __('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($articles)) { ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <?= __('no_articles_found_dash') ?>
                                    </td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($articles as $art) { ?>
                                    <tr class="article-row" data-status="<?= e($art['status']) ?>"
                                        data-template="<?= e($art['template_type']) ?>"
                                        data-search="<?= e(mb_strtolower($art['title'] . ' ' . $art['category_name'] . ' ' . $art['author_name'])) ?>">
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark text-truncate mb-1" style="max-width: 260px;"
                                                title="<?= e(article_title($art['title'])) ?>">
                                                <?php if ($art['is_breaking']) { ?>
                                                    <span
                                                        class="badge bg-danger text-white text-xs me-1"><?= __('breaking') ?></span>
                                                <?php } ?>
                                                <?= e(article_title($art['title'])) ?>
                                            </div>
                                            <div class="text-muted text-xs d-flex align-items-center gap-1">
                                                <span><?= e($art['author_name']) ?></span>
                                                <span>&bull;</span>
                                                <span><?= \App\Core\TemplateEngine::timeAgo($art['created_at']) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-category font-sans">
                                                <?= e(cat_name($art['category_name'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $tmplBadges = [
                                                'standard' => 'badge-tmpl-standard',
                                                'investigative' => 'badge-tmpl-investigative',
                                                'opinion' => 'badge-tmpl-opinion'
                                            ];
                                            $badgeClass = $tmplBadges[$art['template_type']] ?? 'badge-secondary';
                                            ?>
                                            <span class="badge <?= $badgeClass ?> px-2.5 py-1 text-uppercase text-xs fw-bold">
                                                <?= e(tmpl_name($art['template_type'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($art['status'] === 'published') { ?>
                                                <span class="badge badge-status-published px-2.5 py-1 fw-semibold">
                                                    <span class="pulsing-dot bg-success me-1"></span>
                                                    <?= __('published') ?>
                                                </span>
                                            <?php } elseif ($art['status'] === 'draft') { ?>
                                                <span class="badge badge-status-draft px-2.5 py-1 fw-semibold">
                                                    <?= __('draft') ?>
                                                </span>
                                            <?php } else { ?>
                                                <span class="badge bg-dark text-white px-2.5 py-1 fw-semibold">
                                                    <?= __('archived') ?>
                                                </span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-muted small fw-semibold">
                                            <?= number_format((int) $art['views_count']) ?>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= url('public/article.php?slug=' . urlencode($art['slug'])) ?>"
                                                    target="_blank" class="btn btn-sm btn-outline-secondary px-2 py-0.5"
                                                    style="font-size:0.75rem;" title="Preview Public Render">
                                                    <?= __('view') ?>
                                                </a>
                                                <a href="<?= url('admin/article-edit.php?id=' . $art['id']) ?>"
                                                    class="btn btn-sm btn-outline-primary px-2 py-0.5"
                                                    style="font-size:0.75rem;" title="Edit Post">
                                                    <?= __('edit') ?>
                                                </a>
                                                <?php if ($currentUser['role'] === 'admin') { ?>
                                                    <?php $delUrl = url('admin/actions/delete-article.php?id=' . $art['id'] . '&csrf_token=' . e($csrfToken)); ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger px-2 py-0.5"
                                                        style="font-size:0.75rem;"
                                                        onclick="confirmDeleteCard('<?= e($delUrl) ?>', '<?= e(addslashes(article_title($art['title']))) ?>')"
                                                        title="Delete Article">
                                                        <?= __('delete') ?>
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- Readers Feed & Blueprint Widget Sidebar -->
        <div class="col-lg-4">

            <!-- Live Subscriber Registrations Feed Widget -->
            <div class="card admin-card-clean mb-4 overflow-hidden">
                <div class="chart-card-header d-flex align-items-center justify-content-between rounded-top-4">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark editorial-title"><?= __('recent_subscribers') ?></h6>
                        <span class="text-muted text-xs"><?= __('ajax_feed_sub') ?></span>
                    </div>
                    <span
                        class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-1 text-xs">
                        <span class="pulsing-dot bg-danger me-1"></span> <?= __('live_feed') ?>
                    </span>
                </div>
                <div class="list-group list-group-flush admin-scroll-list">
                    <?php if (empty($recentSubscribers)) { ?>
                        <div class="p-4 text-center text-muted small">
                            <i class="bi bi-inbox fs-2 text-muted d-block mb-1"></i>
                            <?= __('no_subscribers_yet') ?>
                        </div>
                    <?php } else { ?>
                        <?php foreach ($recentSubscribers as $sub) { ?>
                            <div class="list-group-item py-3 px-3.5 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 fs-6 d-flex align-items-center justify-content-center"
                                        style="width:36px; height:36px;">
                                        <i class="bi bi-envelope-check-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark small text-truncate" style="max-width: 170px;"
                                            title="<?= e($sub['email']) ?>">
                                            <?= e($sub['email']) ?>
                                        </div>
                                        <div class="text-muted text-xs">
                                            Category: <span
                                                class="fw-medium text-dark"><?= e(cat_name($sub['category_name'] ?? 'All Categories')) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <span class="badge bg-light text-muted border text-xs rounded-pill">
                                    <?= \App\Core\TemplateEngine::timeAgo($sub['subscribed_at']) ?>
                                </span>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>

            <!-- Blueprint Architecture Info Box -->
            <div class="card border-0 shadow-sm rounded-4 bg-brand-navy text-white p-4">
                <div class="mb-2">
                    <h6 class="fw-bold mb-0 text-white editorial-title"><?= __('blueprint_engine_title') ?></h6>
                </div>
                <p class="small text-white opacity-75 mb-3" style="font-size: 0.85rem;">
                    <?= __('blueprint_engine_desc') ?>
                </p>
                <ul class="list-unstyled text-xs text-white opacity-75 mb-0 d-flex flex-column gap-2">
                    <li class="d-flex align-items-center gap-2">
                        <span
                            style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#38bdf8;"></span>
                        <span><strong class="text-white"><?= __('tmpl_standard_name') ?>:</strong>
                            <?= __('blueprint_standard_desc') ?></span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span
                            style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#f87171;"></span>
                        <span><strong class="text-white"><?= __('tmpl_investigative_name') ?>:</strong>
                            <?= __('blueprint_investigative_desc') ?></span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span
                            style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#fbbf24;"></span>
                        <span><strong class="text-white"><?= __('tmpl_opinion_name') ?>:</strong>
                            <?= __('blueprint_opinion_desc') ?></span>
                    </li>
                </ul>
            </div>

        </div>
    </div>

</div>

<!-- Chart.js Data Visualizer & Live Search Filter Engine -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // 1. Traffic Analytics Line Chart
        const trafficCtx = document.getElementById('trafficAnalyticsChart').getContext('2d');

        const gradient = trafficCtx.createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(217, 4, 41, 0.30)');
        gradient.addColorStop(1, 'rgba(217, 4, 41, 0.0)');

        new Chart(trafficCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chartTitles) ?>,
                datasets: [{
                    label: 'Reader Impressions',
                    data: <?= json_encode($chartViews) ?>,
                    borderColor: '#d90429',
                    borderWidth: 2.5,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#d90429',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4.5,
                    pointHoverRadius: 6.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 500 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 11 }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' }
                    }
                }
            }
        });

        // 2. Template Blueprint Doughnut Chart
        const doughnutCtx = document.getElementById('templateDoughnutChart').getContext('2d');
        new Chart(doughnutCtx, {
            type: 'doughnut',
            data: {
                labels: ['<?= __('tmpl_standard_name') ?>', '<?= __('tmpl_investigative_name') ?>', '<?= __('tmpl_opinion_name') ?>'],
                datasets: [{
                    data: [<?= $standardCount ?>, <?= $investigativeCount ?>, <?= $opinionCount ?>],
                    backgroundColor: ['#0d6efd', '#d90429', '#ffc107'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                animation: { duration: 500 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8
                    }
                }
            }
        });

        // 3. Live Client-Side Table Search Engine & Tab Filters
        const searchInput = document.getElementById('tableSearchInput');
        const rows = document.querySelectorAll('.article-row');
        const filterTabs = document.querySelectorAll('#statusFilterTabs .filter-tab-btn');

        let currentFilter = 'all';

        function filterTable() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                const status = row.getAttribute('data-status') || '';

                const matchesSearch = searchData.includes(query);
                const matchesStatus = (currentFilter === 'all') || (status === currentFilter);

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterTable);
        }

        filterTabs.forEach(tab => {
            tab.addEventListener('click', function () {
                filterTabs.forEach(t => {
                    t.classList.remove('active', 'btn-dark');
                    t.classList.add('btn-outline-secondary');
                });
                this.classList.add('active', 'btn-dark');
                this.classList.remove('btn-outline-secondary');

                currentFilter = this.getAttribute('data-filter');
                filterTable();
            });
        });

    });
</script>