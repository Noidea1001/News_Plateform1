<?php
/**
 * Component: Public Reader Sidebar — CNA-Style Clean Design
 * news-platform / templates / components / sidebar.php
 */
require_once __DIR__ . '/../../src/Core/helpers.php';

$sidebarDb = \App\Core\Database::getInstance();
$sidebarReader = \App\Core\Auth::reader();
$sidebarSubscribedIds = [];
if ($sidebarReader) {
    $sSubs = $sidebarDb->fetchAll(
        "SELECT category_id FROM reader_subscriptions WHERE reader_id = :rid",
        ['rid' => (int)$sidebarReader['id']]
    );
    $sidebarSubscribedIds = array_map(fn($s) => (int)$s['category_id'], $sSubs);
}

$sidebarCats = $sidebarDb->fetchAll("SELECT id, name, slug FROM categories ORDER BY name ASC LIMIT 6");
?>

<aside class="d-flex flex-column gap-3.5">

    <!-- Widget 1: Reader Topic Subscriptions & Alert Preferences (Rebuilt from editorial dispatch) -->
    <div class="card border-0 shadow-2xs overflow-hidden" style="border:1px solid #e5e7eb !important; border-radius:4px; background:#fff;">
        <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between"
             style="border-left:3px solid #c8102e;">
            <h6 class="mb-0 fw-bold d-flex align-items-center gap-1.5" style="font-size:0.88rem; color:#0f172a;">
                <i class="bi bi-bell-fill text-danger"></i>
                <span><?= __('topic_subscriptions_title') ?></span>
            </h6>
            <span class="badge bg-danger bg-opacity-10 text-danger text-3xs px-2 py-0.5 rounded-pill fw-bold" id="sidebarSubCountBadge">
                <?= count($sidebarSubscribedIds) ?> <?= __('subscribed_topic') ?>
            </span>
        </div>
        <div class="card-body p-3">
            <p class="text-muted text-2xs mb-2.5" style="line-height:1.4;">
                <?= __('topic_subscriptions_desc') ?>
            </p>
            <div class="d-flex flex-column gap-1.5" id="sidebarTopicList">
                <?php foreach ($sidebarCats as $sCat) { 
                    $isSub = in_array((int)$sCat['id'], $sidebarSubscribedIds, true);
                ?>
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-2 border bg-light bg-opacity-50 topic-item-row" style="transition:background 0.15s ease;">
                        <span class="fw-semibold text-xs text-dark d-flex align-items-center gap-1.5 text-truncate pe-1">
                            <i class="bi bi-bookmark-star text-danger opacity-75"></i>
                            <span class="text-truncate"><?= e(cat_name($sCat['name'])) ?></span>
                        </span>
                        <button type="button" 
                            class="btn btn-sm <?= $isSub ? 'btn-danger text-white' : 'btn-outline-secondary' ?> rounded-pill px-2.5 py-0.5 text-2xs fw-bold flex-shrink-0 topic-sub-btn"
                            data-cat-id="<?= (int)$sCat['id'] ?>"
                            data-is-sub="<?= $isSub ? '1' : '0' ?>"
                            data-logged-in="<?= $sidebarReader ? '1' : '0' ?>"
                            style="font-size: 0.72rem;">
                            <?= $isSub ? '✓ ' . __('subscribed_topic') : '+ ' . __('subscribe_topic') ?>
                        </button>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Widget 2: Most Read Stories -->
    <?php if (!empty($trendingArticles)) { ?>
    <div class="card border-0 overflow-hidden most-read-card">
        <!-- Header — CNA red left-border style -->
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between px-3"
             style="border-left:3px solid #c8102e; border-top-left-radius: inherit !important;">
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
             style="border-left:3px solid #c8102e; border-top-left-radius: inherit !important;">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const topicButtons = document.querySelectorAll('.topic-sub-btn');
    topicButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const isLoggedIn = this.getAttribute('data-logged-in') === '1';
            if (!isLoggedIn) {
                const authModal = document.getElementById('readerAuthModal');
                if (authModal && typeof bootstrap !== 'undefined') {
                    const bsModal = bootstrap.Modal.getOrCreateInstance(authModal);
                    bsModal.show();
                } else {
                    window.location.href = '<?= url("login.php") ?>';
                }
                return;
            }

            const catId = this.getAttribute('data-cat-id');
            const wasSub = this.getAttribute('data-is-sub') === '1';
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:0.6rem;height:0.6rem;"></span>';

            fetch('<?= url("api/v1/subscription.php") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ category_id: parseInt(catId, 10) })
            })
            .then(res => res.json())
            .then(data => {
                this.disabled = false;
                if (data.success) {
                    if (data.subscribed) {
                        this.setAttribute('data-is-sub', '1');
                        this.className = 'btn btn-sm btn-danger text-white rounded-pill px-2.5 py-0.5 text-2xs fw-bold flex-shrink-0 topic-sub-btn';
                        this.innerHTML = '✓ <?= addslashes(__('subscribed_topic')) ?>';
                    } else {
                        this.setAttribute('data-is-sub', '0');
                        this.className = 'btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-0.5 text-2xs fw-bold flex-shrink-0 topic-sub-btn';
                        this.innerHTML = '+ <?= addslashes(__('subscribe_topic')) ?>';
                    }
                    const countBadge = document.getElementById('sidebarSubCountBadge');
                    if (countBadge && typeof data.total_subscriptions !== 'undefined') {
                        countBadge.textContent = data.total_subscriptions + ' <?= addslashes(__('subscribed_topic')) ?>';
                    }
                    if (typeof fetchNotifications === 'function') {
                        fetchNotifications();
                    }
                } else if (data.require_login) {
                    const authModal = document.getElementById('readerAuthModal');
                    if (authModal && typeof bootstrap !== 'undefined') {
                        bootstrap.Modal.getOrCreateInstance(authModal).show();
                    }
                }
            })
            .catch(() => {
                this.disabled = false;
                this.innerHTML = wasSub ? '✓ <?= addslashes(__('subscribed_topic')) ?>' : '+ <?= addslashes(__('subscribe_topic')) ?>';
            });
        });
    });
});
</script>
