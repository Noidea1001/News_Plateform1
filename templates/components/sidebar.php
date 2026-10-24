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
/* ===== Compact sidebar widgets (Most Read · Explore Topics · Topic Subscriptions) ===== */
.public-sidebar { gap: 0.85rem !important; }

.sbw {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
}
.sbw-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    padding: 0.55rem 0.8rem;
    border-bottom: 1px solid #f1f5f9;
}
.sbw-title {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    margin: 0;
    font-size: 0.84rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
}
.sbw-title::before {
    content: "";
    width: 3px;
    height: 0.95rem;
    border-radius: 2px;
    background: #c8102e;
    flex-shrink: 0;
}
.sbw-body { padding: 0.6rem 0.8rem 0.7rem; }

/* --- 1. Most read: numbered list, no badge row --- */
.sbw-rank-list { list-style: none; margin: 0; padding: 0; }
.sbw-rank-item + .sbw-rank-item { border-top: 1px solid #f1f5f9; }
.sbw-rank-link {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    padding: 0.5rem 0.8rem;
    text-decoration: none;
    transition: background 0.12s ease;
}
.sbw-rank-link:hover { background: #f8fafc; }
.sbw-rank-num {
    flex-shrink: 0;
    width: 1.35rem;
    font-size: 1.05rem;
    font-weight: 800;
    line-height: 1.25;
    color: #cbd5e1;
    text-align: center;
    font-variant-numeric: tabular-nums;
}
.sbw-rank-item:nth-child(-n+3) .sbw-rank-num { color: #c8102e; }
.sbw-rank-text { min-width: 0; flex: 1; }
.sbw-rank-title {
    margin: 0 0 0.15rem;
    font-size: 0.8rem;
    font-weight: 600;
    line-height: 1.4;
    color: #0f172a;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.12s ease;
}
.sbw-rank-link:hover .sbw-rank-title { color: #c8102e; }
.sbw-rank-meta {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.66rem;
    color: #94a3b8;
    white-space: nowrap;
    overflow: hidden;
}
.sbw-rank-meta .sbw-cat {
    color: #c8102e;
    font-weight: 600;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* --- 2 & 3. Chips (topics + subscription toggles) --- */
.sbw-chips { display: flex; flex-wrap: wrap; gap: 0.35rem; }
.sbw-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.2rem 0.6rem;
    font-size: 0.72rem;
    font-weight: 500;
    line-height: 1.35;
    color: #334155;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    text-decoration: none;
    cursor: pointer;
    transition: color 0.12s ease, border-color 0.12s ease, background 0.12s ease;
}
.sbw-chip:hover { color: #c8102e; border-color: #fca5a5; background: #fff; }
.sbw-chip i { font-size: 0.78rem; line-height: 1; }

.sbw-sub.is-sub {
    color: #fff;
    background: #c8102e;
    border-color: #c8102e;
}
.sbw-sub.is-sub:hover { color: #fff; background: #a50d26; border-color: #a50d26; }
.sbw-sub:disabled { opacity: 0.65; cursor: wait; }

.sbw-count {
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    padding: 0.1rem 0.45rem;
    font-size: 0.66rem;
    font-weight: 700;
    color: #c8102e;
    background: rgba(200, 16, 46, 0.08);
    border-radius: 999px;
    white-space: nowrap;
}
.sbw-desc {
    margin: 0 0 0.5rem;
    font-size: 0.7rem;
    line-height: 1.4;
    color: #64748b;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@media (max-width: 991.98px) {
    .public-sidebar { margin-top: 0.5rem; }
}
</style>

<aside class="d-flex flex-column public-sidebar mb-4">

    <!-- 1. Most Read Stories (អត្ថបទដែលមានអ្នកអានច្រើន) — compact numbered list -->
    <?php if (!empty($trendingArticles)) { ?>
    <section class="sbw">
        <div class="sbw-head">
            <h6 class="sbw-title"><?= __('most_read') ?></h6>
            <i class="bi bi-graph-up-arrow text-danger" style="font-size:0.8rem;" aria-hidden="true"></i>
        </div>
        <ol class="sbw-rank-list">
            <?php foreach ($trendingArticles as $tIndex => $tItem) { ?>
                <li class="sbw-rank-item">
                    <a href="<?= url('article.php?slug=' . urlencode($tItem['slug'])) ?>" class="sbw-rank-link">
                        <span class="sbw-rank-num"><?= km_num((string)($tIndex + 1)) ?></span>
                        <div class="sbw-rank-text">
                            <h6 class="sbw-rank-title"><?= e(article_title($tItem)) ?></h6>
                            <div class="sbw-rank-meta">
                                <span class="sbw-cat"><?= e(cat_name($tItem['category_name'])) ?></span>
                                <span>&middot;</span>
                                <span><i class="bi bi-eye"></i> <?= km_num(number_format((int)$tItem['views_count'])) ?></span>
                                <span>&middot;</span>
                                <span><?= \App\Core\TemplateEngine::timeAgo($tItem['published_at']) ?></span>
                            </div>
                        </div>
                    </a>
                </li>
            <?php } ?>
        </ol>
    </section>
    <?php } ?>

    <!-- 2. Explore Topics (រករកប្រធានបទ) — compact chips -->
    <?php if (!empty($categories)) { ?>
    <section class="sbw">
        <div class="sbw-head">
            <h6 class="sbw-title"><?= __('explore_topics') ?></h6>
        </div>
        <div class="sbw-body">
            <div class="sbw-chips">
                <?php foreach ($categories as $catTag) { ?>
                    <a href="<?= url('index.php?category=' . (int)$catTag['id']) ?>" class="sbw-chip">
                        <?= e(cat_name($catTag['name'])) ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </section>
    <?php } ?>

    <!-- 3. Reader Topic Subscriptions (ការជាវព័ត៌មានតាមផ្នែក) — toggle chips instead of rows -->
    <section class="sbw">
        <div class="sbw-head">
            <h6 class="sbw-title"><?= __('topic_subscriptions_title') ?></h6>
            <span class="sbw-count" id="sidebarSubCountBadge" title="<?= e(__('subscribed_topic')) ?>">
                <i class="bi bi-bell-fill"></i>
                <span id="sidebarSubCountNum"><?= km_num((string)count($sidebarSubscribedIds)) ?></span>
            </span>
        </div>
        <div class="sbw-body">
            <p class="sbw-desc"><?= __('topic_subscriptions_desc') ?></p>
            <div class="sbw-chips" id="sidebarTopicList">
                <?php foreach ($sidebarCats as $sCat) {
                    $isSub = in_array((int)$sCat['id'], $sidebarSubscribedIds, true);
                ?>
                    <button type="button"
                        class="sbw-chip sbw-sub topic-sub-btn<?= $isSub ? ' is-sub' : '' ?>"
                        data-cat-id="<?= (int)$sCat['id'] ?>"
                        data-is-sub="<?= $isSub ? '1' : '0' ?>"
                        data-logged-in="<?= $sidebarReader ? '1' : '0' ?>"
                        aria-pressed="<?= $isSub ? 'true' : 'false' ?>"
                        title="<?= e($isSub ? __('subscribed_topic') : __('subscribe_topic')) ?>">
                        <i class="bi <?= $isSub ? 'bi-check2' : 'bi-plus' ?>"></i>
                        <span><?= e(cat_name($sCat['name'])) ?></span>
                    </button>
                <?php } ?>
            </div>
        </div>
    </section>

</aside>

<script>
(function() {
    // Delegated click handler for topic subscription chips (persists across dynamic language switches)
    if (window._sidebarSubAttached) return;
    window._sidebarSubAttached = true;

    const LABEL_SUB = <?= json_encode(__('subscribed_topic'), JSON_UNESCAPED_UNICODE) ?>;
    const LABEL_UNSUB = <?= json_encode(__('subscribe_topic'), JSON_UNESCAPED_UNICODE) ?>;

    function setChipState(btn, subscribed) {
        btn.setAttribute('data-is-sub', subscribed ? '1' : '0');
        btn.setAttribute('aria-pressed', subscribed ? 'true' : 'false');
        btn.setAttribute('title', subscribed ? LABEL_SUB : LABEL_UNSUB);
        btn.classList.toggle('is-sub', subscribed);
        const icon = btn.querySelector('i');
        if (icon) icon.className = 'bi ' + (subscribed ? 'bi-check2' : 'bi-plus');
    }

    function showAuthModal() {
        const authModal = document.getElementById('readerAuthModal');
        if (authModal && typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getOrCreateInstance(authModal).show();
        } else {
            window.location.href = <?= json_encode(url('login.php')) ?>;
        }
    }

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.topic-sub-btn');
        if (!btn) return;
        e.preventDefault();

        if (btn.getAttribute('data-logged-in') !== '1') {
            showAuthModal();
            return;
        }

        const catId = parseInt(btn.getAttribute('data-cat-id'), 10);
        const wasSub = btn.getAttribute('data-is-sub') === '1';
        const icon = btn.querySelector('i');
        btn.disabled = true;
        if (icon) icon.className = 'spinner-border spinner-border-sm';
        if (icon) { icon.style.width = '0.6rem'; icon.style.height = '0.6rem'; icon.style.borderWidth = '0.12em'; }

        fetch(<?= json_encode(url('api/v1/subscription.php')) ?>, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ category_id: catId })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if (icon) { icon.style.width = ''; icon.style.height = ''; icon.style.borderWidth = ''; }
            if (data.success) {
                setChipState(btn, !!data.subscribed);
                const countNum = document.getElementById('sidebarSubCountNum');
                if (countNum && typeof data.total_subscriptions !== 'undefined') {
                    countNum.textContent = data.total_subscriptions;
                }
                if (typeof fetchNotifications === 'function') {
                    fetchNotifications();
                }
            } else {
                setChipState(btn, wasSub);
                if (data.require_login) showAuthModal();
            }
        })
        .catch(() => {
            btn.disabled = false;
            if (icon) { icon.style.width = ''; icon.style.height = ''; icon.style.borderWidth = ''; }
            setChipState(btn, wasSub);
        });
    });
})();
</script>
