<?php
/**
 * Component: Public Reader Sidebar — Clean, Compact, Responsive & Space-Saving Design
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

if (!isset($trendingArticles) || empty($trendingArticles)) {
    $trendingArticles = $sidebarDb->fetchAll(
        "SELECT a.id, a.title, a.title_en, a.title_kh, a.slug, a.views_count, a.published_at, c.name as category_name 
         FROM articles a 
         JOIN categories c ON a.category_id = c.id 
         WHERE a.status = 'published' 
         ORDER BY a.views_count DESC, a.published_at DESC 
         LIMIT 5"
    );
}

if (!isset($categories) || empty($categories)) {
    $categories = $sidebarDb->fetchAll("SELECT id, name, slug FROM categories ORDER BY name ASC LIMIT 12");
}

$sidebarCats = $sidebarDb->fetchAll("SELECT id, name, slug FROM categories ORDER BY name ASC LIMIT 6");
?>

<style>
/* Specific responsive styles for the 3 sidebar widgets */
.sidebar-most-read-rank {
    width: 28px !important;
    min-width: 28px !important;
    height: 28px !important;
    background: #c8102e !important;
    color: #ffffff !important;
    font-size: 0.74rem !important;
    font-weight: 800 !important;
    border-radius: 4px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    margin-right: 12px !important;
    margin-top: 2px !important;
    line-height: 1 !important;
    letter-spacing: 0.02em;
}

.sidebar-topic-chip {
    display: inline-flex;
    align-items: center;
    padding: 5px 12px;
    font-size: 0.76rem;
    font-weight: 500;
    color: #334155;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    text-decoration: none;
    transition: all 0.15s ease;
    line-height: 1.25;
}

.sidebar-topic-chip:hover {
    color: #c8102e;
    border-color: #fca5a5;
    background: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(200, 16, 46, 0.08);
}

.sidebar-widget-card {
    border: 1px solid #e5e7eb !important;
    border-radius: 6px !important;
    background: #ffffff !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    overflow: hidden;
}

.sidebar-widget-header {
    background: #ffffff;
    padding: 0.65rem 0.85rem;
    border-bottom: 1px solid #f1f5f9;
    border-left: 3px solid #c8102e;
}
</style>

<aside class="d-flex flex-column gap-3.5 public-sidebar mb-4">

    <!-- 1. Most Read Stories (អត្ថបទដែលមានអ្នកអានច្រើន) — Clean, Properly Spaced & Responsive -->
    <?php if (!empty($trendingArticles)) { ?>
    <div class="card border-0 sidebar-widget-card most-read-card">
        <div class="card-header sidebar-widget-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem; color:#0f172a; letter-spacing:-0.01em;">
                <?= __('most_read') ?>
            </h6>
            <span style="font-size:0.68rem; color:#9ca3af; font-weight:600; text-transform:uppercase; letter-spacing:0.04em;">
                <?= __('live_traffic') ?>
            </span>
        </div>
        <div class="list-group list-group-flush">
            <?php foreach ($trendingArticles as $tIndex => $tItem) { ?>
                <a href="<?= url('article.php?slug=' . urlencode($tItem['slug'])) ?>"
                   class="list-group-item list-group-item-action py-2.5 px-3 border-bottom text-decoration-none trending-list-item"
                   style="transition: background 0.12s ease;">
                    <div class="d-flex align-items-start">
                        <!-- Uniform Red Ranking Number with 12px Right Spacing -->
                        <div class="sidebar-most-read-rank flex-shrink-0">
                            <?= km_num(sprintf('%02d', $tIndex + 1)) ?>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <!-- Category label -->
                            <div class="mb-1">
                                <span class="badge bg-danger-subtle text-danger px-1.5 py-0.5 rounded-pill fw-bold"
                                      style="font-size:0.64rem; letter-spacing:0.02em;">
                                    <?= e(cat_name($tItem['category_name'])) ?>
                                </span>
                            </div>
                            <!-- Title (max 2 lines with proper line height) -->
                            <h6 class="mb-1 fw-bold trending-title"
                                style="font-size:0.83rem; line-height:1.42; color:#0f172a; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                <?= e(article_title($tItem)) ?>
                            </h6>
                            <!-- Views + Time metadata -->
                            <div class="d-flex align-items-center gap-1.5" style="font-size:0.68rem; color:#9ca3af;">
                                <span><?= number_format((int)$tItem['views_count']) ?></span>
                                <span>&middot;</span>
                                <span><?= \App\Core\TemplateEngine::timeAgo($tItem['published_at']) ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <!-- 2. Explore Topics (រករកប្រធានបទ) — Clean & Responsive Topic Chips -->
    <?php if (!empty($categories)) { ?>
    <div class="card border-0 sidebar-widget-card">
        <div class="card-header sidebar-widget-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem; color:#0f172a;">
                <?= __('explore_topics') ?>
            </h6>
        </div>
        <div class="card-body p-3 pb-3.5">
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($categories as $catTag) { ?>
                    <a href="<?= url('index.php?category=' . $catTag['id']) ?>"
                       class="sidebar-topic-chip">
                        <?= e(cat_name($catTag['name'])) ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php } ?>

    <!-- 3. Reader Topic Subscriptions (ការជាវព័ត៌មានតាមផ្នែក) — Correct Spacing, Responsive & Clear Buttons -->
    <div class="card border-0 sidebar-widget-card">
        <div class="card-header sidebar-widget-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem; color:#0f172a;">
                <?= __('topic_subscriptions_title') ?>
            </h6>
            <span class="badge bg-danger bg-opacity-10 text-danger text-3xs px-2.5 py-1 rounded-pill fw-bold" id="sidebarSubCountBadge">
                <?= count($sidebarSubscribedIds) ?> <?= __('subscribed_topic') ?>
            </span>
        </div>
        <div class="card-body p-3 pb-3.5">
            <p class="text-muted text-xs mb-3" style="line-height:1.4; font-size:0.75rem;">
                <?= __('topic_subscriptions_desc') ?>
            </p>
            <div class="d-flex flex-column gap-2" id="sidebarTopicList">
                <?php foreach ($sidebarCats as $sCat) { 
                    $isSub = in_array((int)$sCat['id'], $sidebarSubscribedIds, true);
                ?>
                    <div class="d-flex align-items-center justify-content-between px-3 rounded-2 topic-item-row"
                         style="background:#f8fafc; border:1px solid #f1f5f9; height:36px; transition:all 0.12s ease;">
                        <span class="fw-semibold text-dark text-truncate pe-2" style="font-size:0.8rem;">
                            <?= e(cat_name($sCat['name'])) ?>
                        </span>
                        <button type="button" 
                            class="btn btn-sm <?= $isSub ? 'btn-danger text-white' : 'btn-outline-danger' ?> rounded-pill px-2.5 py-0 fw-bold flex-shrink-0 topic-sub-btn"
                            data-cat-id="<?= (int)$sCat['id'] ?>"
                            data-is-sub="<?= $isSub ? '1' : '0' ?>"
                            data-logged-in="<?= $sidebarReader ? '1' : '0' ?>"
                            style="font-size: 0.72rem; height: 24px; line-height: 22px; border-width: 1.5px;">
                            <?= $isSub ? '✓ ' . __('subscribed_topic') : '+ ' . __('subscribe_topic') ?>
                        </button>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

</aside>

<script>
(function() {
    // Delegated click handler for topic subscription buttons (persists across dynamic language switches)
    if (!window._sidebarSubAttached) {
        window._sidebarSubAttached = true;
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.topic-sub-btn');
            if (!btn) return;
            e.preventDefault();

            const isLoggedIn = btn.getAttribute('data-logged-in') === '1';
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

            const catId = btn.getAttribute('data-cat-id');
            const wasSub = btn.getAttribute('data-is-sub') === '1';
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:0.6rem;height:0.6rem;"></span>';

            fetch('<?= url("api/v1/subscription.php") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ category_id: parseInt(catId, 10) })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                if (data.success) {
                    if (data.subscribed) {
                        btn.setAttribute('data-is-sub', '1');
                        btn.className = 'btn btn-sm btn-danger text-white rounded-pill px-2.5 py-0 fw-bold flex-shrink-0 topic-sub-btn';
                        btn.innerHTML = '✓ <?= addslashes(__('subscribed_topic')) ?>';
                    } else {
                        btn.setAttribute('data-is-sub', '0');
                        btn.className = 'btn btn-sm btn-outline-danger rounded-pill px-2.5 py-0 fw-bold flex-shrink-0 topic-sub-btn';
                        btn.innerHTML = '+ <?= addslashes(__('subscribe_topic')) ?>';
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
                btn.disabled = false;
                btn.className = wasSub 
                    ? 'btn btn-sm btn-danger text-white rounded-pill px-2.5 py-0 fw-bold flex-shrink-0 topic-sub-btn' 
                    : 'btn btn-sm btn-outline-danger rounded-pill px-2.5 py-0 fw-bold flex-shrink-0 topic-sub-btn';
                btn.innerHTML = wasSub ? '✓ <?= addslashes(__('subscribed_topic')) ?>' : '+ <?= addslashes(__('subscribe_topic')) ?>';
            });
        });
    }
})();
</script>
