</main><!-- /main -->

<!-- Quick View, Saved Reading List & Reader Auth Modals -->
<?php
$qvModal    = __DIR__ . '/../components/quick-view-modal.php';
$savedModal = __DIR__ . '/../components/saved-articles-modal.php';
$authModal  = __DIR__ . '/../components/auth-modal.php';
$pwaModal   = __DIR__ . '/../components/pwa-install-modal.php';
$pushPromptModal = __DIR__ . '/../components/push-prompt-banner.php';

if (file_exists($qvModal))    { include $qvModal; }
if (file_exists($savedModal)) { include $savedModal; }
if (file_exists($authModal))  { include $authModal; }
if (file_exists($pwaModal))   { include $pwaModal; }
if (file_exists($pushPromptModal)) { include $pushPromptModal; }
?>

<!-- ── CNA-Style Footer ────────────────────────────────────────────────── -->
<footer class="bg-brand-navy text-white mt-auto" style="border-top: 3px solid #c8102e; padding-top: 3rem; padding-bottom: 2rem;">
    <div class="container">
        <div class="row g-5 mb-4">

            <!-- Brand & Mission -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="brand-logo-badge" style="width:44px;height:44px;font-size:1.05rem;border-radius:2px;">NP</span>
                    <span class="fw-bold" style="color:#fff; font-size:1.05rem; letter-spacing:-0.02em;">
                        <?= __('site_title') ?>
                    </span>
                </div>
                <p style="font-size:0.875rem; color:rgba(255,255,255,0.55); line-height:1.75; margin-bottom:1.5rem;">
                    <?= __('footer_desc') ?>
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="footer-social-btn" title="X / Twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="footer-social-btn" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="footer-social-btn" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="footer-social-btn" style="color:#fbbf24;" title="RSS"><i class="bi bi-rss-fill"></i></a>
                </div>
            </div>

            <!-- Content Blueprints -->
            <div class="col-lg-4 col-md-6">
                <!-- CNA-style section title: red left border -->
                <div style="border-left:3px solid #c8102e; padding-left:0.75rem; margin-bottom:1.25rem;">
                    <h6 style="color:#fff; font-size:0.78rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; margin:0;">
                        <?= __('layout_blueprints_title') ?>
                    </h6>
                </div>
                <ul class="list-unstyled d-flex flex-column gap-3" style="font-size:0.875rem;">
                    <li class="d-flex align-items-center gap-3">
                        <span style="width:28px;height:28px;background:rgba(255,255,255,0.07);display:inline-flex;align-items:center;justify-content:center;border-radius:2px;color:rgba(255,255,255,0.60);">
                            <i class="bi bi-layout-three-columns" style="font-size:0.85rem;"></i>
                        </span>
                        <span style="color:rgba(255,255,255,0.60);"><?= __('footer_tmpl_1') ?></span>
                    </li>
                    <li class="d-flex align-items-center gap-3">
                        <span style="width:28px;height:28px;background:rgba(200,16,46,0.18);display:inline-flex;align-items:center;justify-content:center;border-radius:2px;color:#fca5a5;">
                            <i class="bi bi-card-text" style="font-size:0.85rem;"></i>
                        </span>
                        <span style="color:rgba(255,255,255,0.60);"><?= __('footer_tmpl_2') ?></span>
                    </li>
                    <li class="d-flex align-items-center gap-3">
                        <span style="width:28px;height:28px;background:rgba(255,255,255,0.07);display:inline-flex;align-items:center;justify-content:center;border-radius:2px;color:rgba(255,255,255,0.60);">
                            <i class="bi bi-person-lines-fill" style="font-size:0.85rem;"></i>
                        </span>
                        <span style="color:rgba(255,255,255,0.60);"><?= __('footer_tmpl_3') ?></span>
                    </li>
                </ul>
            </div>

            <!-- Create Account CTA (replaces old email newsletter) -->
            <div class="col-lg-4 col-md-12">
                <div style="border-left:3px solid #c8102e; padding-left:0.75rem; margin-bottom:1.25rem;">
                    <h6 style="color:#fff; font-size:0.78rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; margin:0;">
                        <?= __('notifications_account_required') ?>
                    </h6>
                </div>
                <p style="font-size:0.875rem; color:rgba(255,255,255,0.55); line-height:1.75; margin-bottom:1.25rem;">
                    <?= __('notifications_login_prompt_desc') ?>
                </p>
                <?php if (!\App\Core\Auth::readerCheck()) { ?>
                <button type="button"
                        class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2 fw-bold"
                        style="font-size:0.85rem; border-radius:2px; text-transform:uppercase; letter-spacing:0.05em; padding:0.65rem 1rem;"
                        data-bs-toggle="modal" data-bs-target="#readerAuthModal" data-auth-tab="register">
                    <i class="bi bi-person-plus-fill"></i>
                    <?= __('create_account') ?>
                </button>
                <?php } else { ?>
                <a href="<?= url('settings.php') ?>"
                   class="btn btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2 fw-bold"
                   style="font-size:0.85rem; border-radius:2px; padding:0.65rem 1rem;">
                    <i class="bi bi-gear-fill"></i>
                    <?= __('settings_page_title') ?>
                </a>
                <?php } ?>
            </div>
        </div>

        <!-- Bottom bar -->
        <div style="border-top:1px solid rgba(255,255,255,0.08); padding-top:1.25rem; margin-top:1rem;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2"
                 style="font-size:0.78rem; color:rgba(255,255,255,0.40);">
                <p class="mb-0">&copy; <?= date('Y') ?> <?= __('rights_reserved') ?></p>
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= url('index.php') ?>"
                       class="text-decoration-none"
                       style="color:rgba(255,255,255,0.40);"
                       onmouseover="this.style.color='rgba(255,255,255,0.80)'"
                       onmouseout="this.style.color='rgba(255,255,255,0.40)'">
                        <?= __('cda_badge') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Public Reader Interactivity JS Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Reading Progress Bar Handler
    const progressBar = document.getElementById('readingProgressBar');
    if (progressBar) {
        window.addEventListener('scroll', function () {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (height > 0) ? (winScroll / height) * 100 : 0;
            progressBar.style.width = scrolled + '%';
        });
    }

    // 2. Bookmark LocalStorage Storage Engine
    const BOOKMARK_KEY = 'np_saved_articles_v1';
    window.CURRENT_LANG = '<?= $_SESSION['lang'] ?? 'en' ?>';
    let CURRENT_LANG = window.CURRENT_LANG;
    let IS_KHMER = (CURRENT_LANG === 'kh' || CURRENT_LANG === 'km');
    const TXT_READ_STORY = '<?= __('read_story') ?>';
    const IS_LOGGED_IN = <?= (\App\Core\Auth::readerCheck() || \App\Core\Auth::check()) ? 'true' : 'false' ?>;

    // Inherit central translation dictionary from PHP src/Core/helpers.php
    const translationMaps = <?= json_encode(get_translation_maps(), JSON_UNESCAPED_UNICODE) ?>;
    const categoryTranslations = translationMaps.categories || {};
    const titleTranslations = translationMaps.titles || {};

    const kmToEnCategory = {};
    for (let [en, km] of Object.entries(categoryTranslations)) {
        kmToEnCategory[km] = en;
    }

    const enToKmTitle = {};
    for (let [km, en] of Object.entries(titleTranslations)) {
        enToKmTitle[en] = km;
    }

    function toKmNum(str) {
        const digits = {'0':'០','1':'១','2':'២','3':'៣','4':'៤','5':'៥','6':'៦','7':'៧','8':'៨','9':'៩'};
        return String(str).replace(/[0-9]/g, d => digits[d]);
    }

    function translateCategoryJs(catStr) {
        if (!catStr) return 'NEWS';
        let str = catStr.replace(/^[\d\.\-\s]+/u, '').trim();
        const hasKhmer = /[\u1780-\u17FF]/.test(str);

        if (IS_KHMER) {
            if (hasKhmer) return str;
            return categoryTranslations[str] || str;
        } else {
            if (!hasKhmer) return str;
            return kmToEnCategory[str] || str;
        }
    }

    function translateTitleJs(titleStr) {
        if (!titleStr) return '';
        let str = titleStr.trim();
        
        // Handle dual language format: "Part1 (Part2)"
        const match = str.match(/^([^()]+)\s*\(([^()]+)\)$/);
        if (match) {
            const p1 = match[1].trim();
            const p2 = match[2].trim();
            const isP1Km = /[\u1780-\u17FF]/.test(p1);
            const isP2Km = /[\u1780-\u17FF]/.test(p2);
            if (IS_KHMER) {
                if (isP1Km) return toKmNum(p1);
                if (isP2Km) return toKmNum(p2);
            } else {
                if (!isP1Km && p1) return p1;
                if (!isP2Km && p2) return p2;
            }
        }

        const hasKhmer = /[\u1780-\u17FF]/.test(str);

        if (IS_KHMER) {
            if (hasKhmer) return toKmNum(str);
            return enToKmTitle[str] ? toKmNum(enToKmTitle[str]) : toKmNum(str);
        } else {
            if (!hasKhmer) return str;
            return titleTranslations[str] || str;
        }
    }

    function formatReadingTimeJs(timeStr) {
        if (!timeStr) return IS_KHMER ? 'រយះពេលអាន ៣ នាទី' : '3 min read';
        const numMatch = String(timeStr).match(/\d+/);
        const kmNumMatch = String(timeStr).match(/[០-៩]+/);
        
        let num = 3;
        if (numMatch) {
            num = parseInt(numMatch[0], 10);
        } else if (kmNumMatch) {
            const kmDigits = {'០':0,'១':1,'២':2,'៣':3,'៤':4,'៥':5,'៦':6,'៧':7,'៨':8,'៩':9};
            num = parseInt(kmNumMatch[0].split('').map(c => kmDigits[c]).join(''), 10);
        }

        if (IS_KHMER) {
            return `រយះពេលអាន ${toKmNum(num)} នាទី`;
        } else {
            return `${num} min read`;
        }
    }

    function getSavedArticles() {
        try {
            const raw = localStorage.getItem(BOOKMARK_KEY) || localStorage.getItem('np_saved_articles') || '[]';
            const parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    }

    function saveArticlesList(list) {
        const jsonStr = JSON.stringify(list);
        localStorage.setItem(BOOKMARK_KEY, jsonStr);
        localStorage.setItem('np_saved_articles', jsonStr);
        updateSavedCountBadge();
        renderSavedArticlesList();
        syncBookmarkButtonsState();
    }

    function isSaved(id) {
        const list = getSavedArticles();
        return list.some(item => String(item.id) === String(id));
    }

    function toggleSaveArticle(articleData) {
        let list = getSavedArticles();
        const index = list.findIndex(item => String(item.id) === String(articleData.id));
        if (index > -1) {
            list.splice(index, 1);
        } else {
            list.unshift(articleData);
        }
        saveArticlesList(list);
    }

    function updateSavedCountBadge() {
        const badge = document.getElementById('savedCountBadge');
        const mobileBadge = document.getElementById('mobileSavedCountBadge');
        const clearBtn = document.getElementById('clearSavedArticlesBtn');
        const list = getSavedArticles();
        const count = list.length;
        const countText = count > 99 ? '99+' : count;

        [badge, mobileBadge].forEach(b => {
            if (b) {
                b.textContent = count > 0 ? countText : '';
                if (count > 0) {
                    b.classList.remove('d-none');
                    b.classList.add('d-flex');
                } else {
                    b.classList.remove('d-flex');
                    b.classList.add('d-none');
                }
            }
        });
        if (clearBtn) {
            clearBtn.style.display = count > 0 ? 'block' : 'none';
        }
        const confirmCard = document.getElementById('clearSavedConfirmCard');
        const clearBar = document.getElementById('clearSavedButtonBar');
        if (count === 0) {
            if (confirmCard) confirmCard.classList.add('d-none');
            if (clearBar) clearBar.classList.remove('d-none');
        }
    }

    function syncBookmarkButtonsState() {
        const buttons = document.querySelectorAll('.bookmark-toggle-btn');
        buttons.forEach(btn => {
            const id = btn.getAttribute('data-id');
            const icon = btn.querySelector('i');
            const label = btn.querySelector('.bookmark-label');
            if (id && isSaved(id)) {
                btn.classList.add('bookmarked');
                if (icon) {
                    icon.className = 'bi bi-bookmark-fill text-danger';
                }
                if (label) {
                    label.textContent = '<?= __('saved') ?? 'Saved' ?>';
                }
            } else {
                btn.classList.remove('bookmarked');
                if (icon) {
                    icon.className = 'bi bi-bookmark text-danger';
                }
                if (label) {
                    label.textContent = '<?= __('save_for_later') ?? 'Save' ?>';
                }
            }
        });
    }

    function renderSavedArticlesList() {
        const container = document.getElementById('savedArticlesList');
        const emptyState = document.getElementById('savedEmptyState');
        if (!container) return;

        const list = getSavedArticles();
        if (list.length === 0) {
            container.innerHTML = `
                <div class="p-4 text-center text-muted my-auto" id="savedEmptyState">
                    <i class="bi bi-bookmark-dash fs-1 d-block mb-2 text-secondary"></i>
                    <p class="mb-0 fw-semibold" style="font-size: 0.9rem;"><?= __('no_saved_articles') ?? 'No saved articles yet.' ?></p>
                    <small class="text-xs"><?= __('click_bookmark_hint') ?? 'Click the bookmark icon on any article to save it for later.' ?></small>
                </div>
            `;
            return;
        }

        let html = '';
        list.forEach(item => {
            let displayTitle = item.title || '';
            if (IS_KHMER) {
                displayTitle = item.title_kh || translateTitleJs(item.title);
            } else {
                displayTitle = item.title_en || translateTitleJs(item.title);
            }

            let displayCategory = item.category || 'NEWS';
            if (IS_KHMER) {
                displayCategory = item.category_kh || translateCategoryJs(item.category);
            } else {
                displayCategory = item.category_en || translateCategoryJs(item.category);
            }

            const displayReadingTime = formatReadingTimeJs(item.reading_time);

            html += `
                <div class="list-group-item p-3 border-bottom d-flex gap-2.5 align-items-start position-relative">
                    ${item.image ? `<img src="${item.image}" style="width:72px; height:52px; object-fit:cover; border-radius:4px; flex-shrink:0;">` : ''}
                    <div class="flex-grow-1 min-w-0 pe-1">
                        <div class="text-3xs font-monospace text-uppercase fw-bold text-danger mb-1">${displayCategory}</div>
                        <h6 class="fw-bold mb-1" style="font-size:0.84rem; line-height:1.35; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                            <a href="${item.url}" class="text-dark text-decoration-none">${displayTitle}</a>
                        </h6>
                        <div class="text-2xs text-muted d-flex align-items-center gap-1.5 flex-wrap mt-1">
                            <span>${displayReadingTime}</span>
                            <span>&bull;</span>
                            <a href="${item.url}" class="text-danger fw-bold text-decoration-none">${TXT_READ_STORY} &rarr;</a>
                        </div>
                    </div>
                    <button type="button" class="btn btn-link text-muted p-1 remove-saved-btn ms-auto flex-shrink-0" data-id="${item.id}" title="Remove from list" style="font-size:1.05rem; line-height:1;">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                </div>
            `;
        });
        container.innerHTML = html;

        // Attach delete events inside list
        container.querySelectorAll('.remove-saved-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                let list = getSavedArticles();
                list = list.filter(item => String(item.id) !== String(id));
                saveArticlesList(list);
            });
        });
    }

    // Attach click events on bookmark toggle buttons
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.bookmark-toggle-btn');
        if (btn) {
            e.preventDefault();
            e.stopPropagation();

            // Require account to bookmark articles
            if (!IS_LOGGED_IN) {
                const authModalEl = document.getElementById('readerAuthModal');
                if (authModalEl && typeof bootstrap !== 'undefined') {
                    const modal = bootstrap.Modal.getOrCreateInstance(authModalEl);
                    modal.show();
                } else {
                    window.location.href = '<?= url("register.php") ?>';
                }
                return;
            }

            let articleData = null;
            const dataRaw = btn.getAttribute('data-article');
            if (dataRaw) {
                try {
                    articleData = JSON.parse(dataRaw);
                } catch (err) {
                    console.warn('Could not parse data-article JSON, attempting fallback metadata:', err);
                }
            }

            if (!articleData) {
                const id = btn.getAttribute('data-id');
                if (id) {
                    const card = btn.closest('.cna-feed-row, .lead-article-card, .secondary-grid-card, .editorial-actions-bar, .article-container') || document;
                    const titleEl = card.querySelector('.lead-article-title a, .secondary-grid-title a, .cna-feed-title a, h1, h2 a, h3 a, h4 a');
                    const imgEl = card.querySelector('img');
                    articleData = {
                        id: id,
                        title: titleEl ? titleEl.textContent.trim() : 'Saved Article',
                        url: titleEl ? titleEl.href : window.location.href,
                        image: imgEl ? imgEl.src : ''
                    };
                }
            }

            if (articleData && articleData.id) {
                toggleSaveArticle(articleData);
            }
        }
    });

    // Delegated click listener to safely open Saved Articles Offcanvas drawer & ensure fresh rendering
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('[data-bs-target="#savedArticlesModal"], [data-bs-toggle="offcanvas"][href="#savedArticlesModal"]');
        if (trigger) {
            e.preventDefault();
            const modalEl = document.getElementById('savedArticlesModal');
            if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Offcanvas) {
                renderSavedArticlesList();
                const offcanvasInstance = bootstrap.Offcanvas.getOrCreateInstance(modalEl);
                offcanvasInstance.show();
            }
        }
    });

    // Native show.bs.offcanvas hook to guarantee fresh render
    document.addEventListener('show.bs.offcanvas', function (e) {
        if (e.target && e.target.id === 'savedArticlesModal') {
            renderSavedArticlesList();
        }
    });

    // Attach clear all saved articles handler (Alert Card UI)
    const clearBtn = document.getElementById('clearSavedArticlesBtn');
    const clearBar = document.getElementById('clearSavedButtonBar');
    const confirmCard = document.getElementById('clearSavedConfirmCard');
    const cancelBtn = document.getElementById('cancelClearSavedBtn');
    const execBtn = document.getElementById('executeClearSavedBtn');

    if (clearBtn && confirmCard) {
        clearBtn.addEventListener('click', function () {
            if (clearBar) clearBar.classList.add('d-none');
            confirmCard.classList.remove('d-none');
        });
    }

    if (cancelBtn && confirmCard) {
        cancelBtn.addEventListener('click', function () {
            confirmCard.classList.add('d-none');
            if (clearBar) clearBar.classList.remove('d-none');
        });
    }

    if (execBtn && confirmCard) {
        execBtn.addEventListener('click', function () {
            saveArticlesList([]);
            confirmCard.classList.add('d-none');
            if (clearBar) clearBar.classList.remove('d-none');
        });
    }

    // 3. Quick View Modal Handler
    const qvModalEl = document.getElementById('quickViewModal');
    let qvModalInstance = null;
    if (qvModalEl && typeof bootstrap !== 'undefined') {
        qvModalInstance = new bootstrap.Modal(qvModalEl);
    }

    document.addEventListener('click', function (e) {
        const qvBtn = e.target.closest('.qv-trigger-btn');
        if (qvBtn) {
            e.preventDefault();
            e.stopPropagation();
            const dataRaw = qvBtn.getAttribute('data-article');
            if (dataRaw && qvModalInstance) {
                try {
                    const art = JSON.parse(dataRaw);
                    document.getElementById('qvTitle').textContent = art.title || '';
                    document.getElementById('qvSummary').textContent = art.summary || '';
                    document.getElementById('qvCategory').textContent = art.category || 'NEWS';
                    document.getElementById('qvAuthor').textContent = art.author || 'Editorial Staff';
                    document.getElementById('qvDate').textContent = art.date || '';
                    document.getElementById('qvReadingTime').textContent = art.reading_time || '3 min read';
                    document.getElementById('qvViews').textContent = (art.views || '0') + ' views';
                    document.getElementById('qvFullArticleLink').href = art.url || '#';

                    const imgWrapper = document.getElementById('qvImageWrapper');
                    const imgEl = document.getElementById('qvImage');
                    if (art.image && imgWrapper && imgEl) {
                        imgEl.src = art.image;
                        imgWrapper.style.display = 'block';
                    } else if (imgWrapper) {
                        imgWrapper.style.display = 'none';
                    }

                    const qvBmBtn = document.getElementById('qvBookmarkBtn');
                    if (qvBmBtn) {
                        qvBmBtn.setAttribute('data-id', art.id);
                        qvBmBtn.setAttribute('data-article', dataRaw);
                        if (isSaved(art.id)) {
                            qvBmBtn.className = 'btn btn-outline-danger btn-sm fw-bold d-inline-flex align-items-center gap-1.5';
                            qvBmBtn.querySelector('span').textContent = '<?= __('saved') ?? 'Saved' ?>';
                            qvBmBtn.querySelector('i').className = 'bi bi-bookmark-fill text-danger';
                        } else {
                            qvBmBtn.className = 'btn btn-outline-secondary btn-sm fw-bold d-inline-flex align-items-center gap-1.5';
                            qvBmBtn.querySelector('span').textContent = '<?= __('save_for_later') ?? 'Save Article' ?>';
                            qvBmBtn.querySelector('i').className = 'bi bi-bookmark';
                        }
                    }

                    qvModalInstance.show();
                } catch (err) {
                    console.error('Error populating Quick View modal:', err);
                }
            }
        }
    });

    const qvBmBtn = document.getElementById('qvBookmarkBtn');
    if (qvBmBtn) {
        qvBmBtn.addEventListener('click', function () {
            // Require account to bookmark
            if (!IS_LOGGED_IN) {
                if (qvModalInstance) qvModalInstance.hide();
                const authModalEl = document.getElementById('readerAuthModal');
                if (authModalEl && typeof bootstrap !== 'undefined') {
                    const modal = bootstrap.Modal.getOrCreateInstance(authModalEl);
                    modal.show();
                } else {
                    window.location.href = '<?= url("register.php") ?>';
                }
                return;
            }

            const dataRaw = this.getAttribute('data-article');
            if (dataRaw) {
                try {
                    const art = JSON.parse(dataRaw);
                    toggleSaveArticle(art);
                    if (isSaved(art.id)) {
                        this.className = 'btn btn-outline-danger btn-sm fw-bold d-inline-flex align-items-center gap-1.5';
                        this.querySelector('span').textContent = '<?= __('saved') ?? 'Saved' ?>';
                        this.querySelector('i').className = 'bi bi-bookmark-fill text-danger';
                    } else {
                        this.className = 'btn btn-outline-secondary btn-sm fw-bold d-inline-flex align-items-center gap-1.5';
                        this.querySelector('span').textContent = '<?= __('save_for_later') ?? 'Save Article' ?>';
                        this.querySelector('i').className = 'bi bi-bookmark';
                    }
                } catch (err) {
                    console.error(err);
                }
            }
        });
    }

    // Initialize state
    updateSavedCountBadge();
    renderSavedArticlesList();
    syncBookmarkButtonsState();

    // 4. Progressive Web App (PWA) & Service Worker Registration
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    const pwaBtn = document.getElementById('pwaInstallBtn');
    const mobilePwaBtn = document.getElementById('mobilePwaInstallBtn');
    const modalInstallBtn = document.getElementById('pwaModalInstallBtn');
    let deferredPrompt = null;

    if ('serviceWorker' in navigator) {
        const registerSw = function () {
            const swUrl = '<?= url("sw.js") ?>';
            const swScope = '<?= rtrim(url(""), "/") ?>/';
            navigator.serviceWorker.register(swUrl, { scope: swScope })
                .then(function (reg) {
                    console.log('[PWA] Service Worker registered with scope:', reg.scope);
                    reg.update();
                })
                .catch(function (err) {
                    console.warn('[PWA] Service Worker registration failed:', err);
                });
        };
        if (document.readyState === 'complete') {
            registerSw();
        } else {
            window.addEventListener('load', registerSw);
        }
    }

    function showInstallUi() {
        if (isStandalone) return;
        if (pwaBtn) {
            pwaBtn.classList.remove('d-none');
            pwaBtn.classList.add('d-inline-flex');
        }
        if (mobilePwaBtn) {
            mobilePwaBtn.classList.remove('d-none');
            mobilePwaBtn.classList.add('d-flex');
        }
    }

    function hideInstallUi() {
        if (pwaBtn) {
            pwaBtn.classList.remove('d-inline-flex');
            pwaBtn.classList.add('d-none');
        }
        if (mobilePwaBtn) {
            mobilePwaBtn.classList.remove('d-flex');
            mobilePwaBtn.classList.add('d-none');
        }
    }

    // Capture beforeinstallprompt event (Chrome, Edge, Android)
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
        showInstallUi();
    });

    async function triggerInstallFlow() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const choiceResult = await deferredPrompt.userChoice;
            if (choiceResult && choiceResult.outcome === 'accepted') {
                hideInstallUi();
            }
            deferredPrompt = null;
        } else {
            // Show guide modal for browsers without prompt or manual install
            const modalEl = document.getElementById('pwaInstallModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        }
    }

    if (pwaBtn) {
        pwaBtn.addEventListener('click', function (e) {
            e.preventDefault();
            triggerInstallFlow();
        });
    }

    if (mobilePwaBtn) {
        mobilePwaBtn.addEventListener('click', function (e) {
            e.preventDefault();
            triggerInstallFlow();
        });
    }

    if (modalInstallBtn) {
        modalInstallBtn.addEventListener('click', async function (e) {
            e.preventDefault();
            if (deferredPrompt) {
                const modalEl = document.getElementById('pwaInstallModal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
                deferredPrompt.prompt();
                const choiceResult = await deferredPrompt.userChoice;
                if (choiceResult && choiceResult.outcome === 'accepted') {
                    hideInstallUi();
                }
                deferredPrompt = null;
            } else {
                alert('<?= addslashes(__('pwa_desktop_instructions')) ?>');
            }
        });
    }

    // Listen for successful app installation
    window.addEventListener('appinstalled', function () {
        console.log('[PWA] App successfully installed!');
        hideInstallUi();
        deferredPrompt = null;
    });

    // 5. Web Push API: Browser Push Alerts for Breaking News Ticker Items
    const PUSH_I18N = {
        en: {
            subscribe: 'Get Breaking Alerts',
            subscribed: 'Breaking Alerts Active',
            denied: 'Alerts Blocked in Browser',
            title: 'Real-Time Browser Push Alerts',
            successMsg: 'Push alerts enabled! You will now receive breaking news notifications anytime.',
            on: 'ON',
            off: 'OFF',
            tap: 'TAP'
        },
        kh: {
            subscribe: 'ទទួលដំណឹងបន្ទាន់',
            subscribed: 'បានបើកការជូនដំណឹង',
            denied: 'បានបដិសេធក្នុង Browser',
            title: 'ការជូនដំណឹងរហ័សតាម Browser',
            successMsg: 'បានបើកការជូនដំណឹងជោគជ័យ! អ្នកនឹងទទួលបានព័ត៌មានទាន់ហេតុការណ៍គ្រប់ពេលវេលា។',
            on: 'ON',
            off: 'OFF',
            tap: 'TAP'
        }
    };

    function getPushText(key) {
        const lang = (window.CURRENT_LANG === 'kh' || window.CURRENT_LANG === 'km') ? 'kh' : 'en';
        const dict = PUSH_I18N[lang] || PUSH_I18N.en;
        return dict[key] || '';
    }

    function urlB64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    window.updatePushBtnState = function(isSubscribed, isDenied = false) {
        window._lastKnownPushState = { isSubscribed: !!isSubscribed, isDenied: !!isDenied };
        const toggleBtns = document.querySelectorAll('.push-toggle-action-btn, #pushSubscribeBtn');
        toggleBtns.forEach(btn => {
            const icon = btn.querySelector('.push-toggle-icon, #pushSubscribeIcon');
            const text = btn.querySelector('.push-toggle-text, #pushSubscribeText');
            const badge = btn.querySelector('.mobile-push-status-badge, #mobilePushStatusBadge');
            const isMenuItem = btn.classList.contains('mobile-menu-item');

            // Ensure ticker button is always visible and properly styled
            if (btn.id === 'pushSubscribeBtn') {
                btn.classList.remove('d-none');
                btn.classList.add('d-inline-flex');
                btn.setAttribute('title', getPushText('title'));
            }

            if (isDenied) {
                if (icon) {
                    icon.className = 'bi bi-bell-slash-fill text-secondary';
                    icon.style.display = isMenuItem ? 'inline-block' : 'none';
                }
                if (text) text.textContent = getPushText('denied');
                btn.disabled = true;
                if (!isMenuItem) {
                    btn.classList.remove('btn-outline-danger', 'btn-success');
                    btn.classList.add('btn-secondary');
                } else {
                    btn.style.opacity = '0.5';
                    if (badge) {
                        badge.className = 'mobile-push-status-badge badge rounded-pill ms-auto bg-secondary text-white';
                        badge.textContent = getPushText('off');
                        badge.style.display = 'inline-flex';
                    }
                }
            } else if (isSubscribed) {
                if (icon) {
                    icon.className = 'bi bi-bell-fill text-success';
                    icon.style.display = isMenuItem ? 'inline-block' : 'none';
                }
                if (text) text.textContent = getPushText('subscribed');
                btn.disabled = false;
                if (!isMenuItem) {
                    btn.classList.remove('btn-outline-danger', 'btn-secondary');
                    btn.classList.add('btn-success');
                } else {
                    btn.style.opacity = '1';
                    if (badge) {
                        badge.className = 'mobile-push-status-badge badge rounded-pill ms-auto bg-success text-white';
                        badge.textContent = getPushText('on');
                        badge.style.display = 'inline-flex';
                    }
                }
            } else {
                if (icon) {
                    icon.className = 'bi bi-bell text-warning';
                    icon.style.display = 'inline-block';
                }
                if (text) text.textContent = getPushText('subscribe');
                btn.disabled = false;
                if (!isMenuItem) {
                    btn.classList.remove('btn-success', 'btn-secondary');
                    btn.classList.add('btn-outline-danger');
                } else {
                    btn.style.opacity = '1';
                    if (badge) {
                        badge.className = 'mobile-push-status-badge badge rounded-pill ms-auto bg-warning text-dark';
                        badge.textContent = getPushText('tap');
                        badge.style.display = 'inline-flex';
                    }
                }
            }
        });

        const promptEl = document.getElementById('npPushNotificationPrompt');
        if (isSubscribed && promptEl) {
            promptEl.classList.add('d-none');
        }
    };

    window.initPushSubscriptionUi = async function() {
        if (!('PushManager' in window) || !('serviceWorker' in navigator) || !('Notification' in window)) {
            return;
        }

        // Reveal any push buttons immediately
        const tickerBtns = document.querySelectorAll('#pushSubscribeBtn');
        tickerBtns.forEach(btn => {
            btn.classList.remove('d-none');
            btn.classList.add('d-inline-flex');
        });

        // Fast path: if state already known in this session, render immediately without waiting for SW
        if (window._lastKnownPushState) {
            window.updatePushBtnState(window._lastKnownPushState.isSubscribed, window._lastKnownPushState.isDenied);
        }

        try {
            const reg = await navigator.serviceWorker.ready;
            const sub = await reg.pushManager.getSubscription();

            const promptEl = document.getElementById('npPushNotificationPrompt');
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            const isStandalonePwa = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

            if (sub) {
                window.updatePushBtnState(true, false);
                if (promptEl) promptEl.classList.add('d-none');
            } else if (Notification.permission === 'denied') {
                window.updatePushBtnState(false, true);
                if (promptEl) promptEl.classList.add('d-none');
            } else {
                window.updatePushBtnState(false, false);

                // Show floating opt-in card if not dismissed recently
                const dismissedUntil = localStorage.getItem('np_push_prompt_dismissed_until');
                const now = Date.now();
                if (!dismissedUntil || now > parseInt(dismissedUntil, 10)) {
                    setTimeout(() => {
                        const curPrompt = document.getElementById('npPushNotificationPrompt');
                        if (curPrompt && (!Notification.permission || Notification.permission === 'default')) {
                            if (isIOS && !isStandalonePwa) {
                                const iosHint = document.getElementById('iosSafariPushHint');
                                if (iosHint) iosHint.classList.remove('d-none');
                            }
                            curPrompt.classList.remove('d-none');
                        }
                    }, 2000);
                }
            }
        } catch (err) {
            console.warn('[WebPush] Error checking subscription state:', err);
        }
    };
        });

        const promptEl = document.getElementById('npPushNotificationPrompt');
        if (isSubscribed && promptEl) {
            promptEl.classList.add('d-none');
        }
    }

    async function handlePushToggleAction(e) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();

        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        const isStandalonePwa = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

        if (isIOS && !isStandalonePwa) {
            const iosModalEl = document.getElementById('iosPushGuideModal');
            if (iosModalEl && typeof bootstrap !== 'undefined') {
                const modal = bootstrap.Modal.getOrCreateInstance(iosModalEl);
                modal.show();
                return;
            }
        }

        if (!('Notification' in window)) {
            alert('This browser does not support Web Push notifications.');
            return;
        }

        try {
            const reg = await navigator.serviceWorker.ready;
            const existingSub = await reg.pushManager.getSubscription();

            if (existingSub) {
                // Toggle Off: Unsubscribe
                const endpoint = existingSub.endpoint;
                await existingSub.unsubscribe();
                await fetch('<?= url("api/v1/push-subscription.php") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'unsubscribe', endpoint: endpoint })
                });
                updatePushBtnState(false);
            } else {
                // Toggle On: Request permission & subscribe
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    updatePushBtnState(false, true);
                    return;
                }

                // Fetch VAPID Public Key
                const keyRes = await fetch('<?= url("api/v1/push-subscription.php") ?>');
                const keyData = await keyRes.json();

                if (!keyData.success || !keyData.publicKey) {
                    throw new Error(keyData.error || 'Failed to retrieve VAPID key');
                }

                const sub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlB64ToUint8Array(keyData.publicKey)
                });

                const subJson = sub.toJSON();

                // Send subscription to backend
                const saveRes = await fetch('<?= url("api/v1/push-subscription.php") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'subscribe',
                        endpoint: subJson.endpoint,
                        keys: subJson.keys
                    })
                });

                const saveResult = await saveRes.json();
                if (saveResult.success) {
                    window.updatePushBtnState(true);
                    if (typeof window.showAdminToast === 'function') {
                        window.showAdminToast(getPushText('successMsg'), 'success');
                    }
                } else {
                    throw new Error(saveResult.error || 'Server rejected subscription');
                }
            }
        } catch (err) {
            console.error('[WebPush] Subscription error:', err);
            alert('Could not enable push alerts: ' + err.message);
        }
    }

    // Push Prompt Banner event listeners (document delegation for seamless language switches)
    function dismissPushPrompt() {
        const promptEl = document.getElementById('npPushNotificationPrompt');
        if (promptEl) promptEl.classList.add('d-none');
        localStorage.setItem('np_push_prompt_dismissed_until', (Date.now() + 86400000).toString());
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('#dismissPushPromptBtn, #laterPushPromptBtn')) {
            e.preventDefault();
            dismissPushPrompt();
        } else if (e.target.closest('#acceptPushPromptBtn')) {
            e.preventDefault();
            handlePushToggleAction(e);
            dismissPushPrompt();
        }
    });

    document.addEventListener('click', function(e) {
        const toggleBtn = e.target.closest('.push-toggle-action-btn, #pushSubscribeBtn');
        if (toggleBtn) {
            e.preventDefault();
            handlePushToggleAction(e);
        }
    });

    // =========================================================================
    // Seamless In-Place Language Switcher (Zero Reload / Zero Refresh)
    // =========================================================================
    window.switchLanguageSeamlessly = async function(targetLang, targetUrl) {
        if (!targetUrl) return;
        const normLang = (targetLang === 'km' || targetLang === 'kh') ? 'kh' : 'en';

        // Check if already in the target language
        if (window.CURRENT_LANG === normLang) return;

        const savedScrollY = window.scrollY;
        const progressBar = document.getElementById('readingProgressBar');
        if (progressBar) {
            progressBar.style.transition = 'width 0.15s ease';
            progressBar.style.width = '60%';
            progressBar.style.opacity = '1';
        }

        try {
            const response = await fetch(targetUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                window.location.href = targetUrl;
                return;
            }

            const html = await response.text();
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');

            // 1. Update Title & HTML Language
            document.title = newDoc.title;
            document.documentElement.lang = normLang;
            window.CURRENT_LANG = normLang;
            CURRENT_LANG = normLang;
            IS_KHMER = (normLang === 'kh');

            // 2. Update address bar URL cleanly without page reload
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, newDoc.title, targetUrl);
            }

            // 3. Swap page regions smoothly in place
            const swapSelectors = [
                '.top-utility-header',
                '.navbar-main',
                '#mobileSearchCollapse',
                '.nav-category-toolbar',
                '.breaking-ticker-bar',
                'main',
                'footer',
                '#savedArticlesModal',
                '#readerAuthModal',
                '#quickViewModal'
            ];

            swapSelectors.forEach(selector => {
                const currentEl = document.querySelector(selector);
                const newEl = newDoc.querySelector(selector);
                if (currentEl && newEl) {
                    currentEl.innerHTML = newEl.innerHTML;
                    if (newEl.className !== currentEl.className) {
                        currentEl.className = newEl.className;
                    }
                }
            });

            // 4. Maintain exact reading position
            window.scrollTo(0, savedScrollY);

            // 5. Hide micro progress bar
            if (progressBar) {
                progressBar.style.width = '100%';
                setTimeout(() => {
                    progressBar.style.opacity = '0';
                    progressBar.style.width = '0%';
                }, 200);
            }

            // 6. Dismiss any open dropdowns or mobile menus
            document.querySelectorAll('.dropdown-menu.show').forEach(m => m.classList.remove('show'));

            // 7. Sync badges, bookmarks, notification counter & push notification UI in new language
            if (typeof window.restoreBadgesImmediately === 'function') {
                window.restoreBadgesImmediately();
            }
            if (typeof syncBookmarkButtonsState === 'function') {
                syncBookmarkButtonsState();
            }
            if (typeof renderSavedArticlesList === 'function') {
                renderSavedArticlesList();
            }
            if (typeof updateSavedCountBadge === 'function') {
                updateSavedCountBadge();
            }
            if (typeof fetchNotifications === 'function') {
                fetchNotifications();
            }
            if (typeof window.initPushSubscriptionUi === 'function') {
                window.initPushSubscriptionUi();
            }

        } catch (err) {
            console.error('Seamless language transition failed:', err);
            window.location.href = targetUrl;
        }
    };

    // Delegated click listener for all language switch triggers
    document.addEventListener('click', function(e) {
        const langTrigger = e.target.closest('.lang-switch-link');
        if (!langTrigger) return;
        e.preventDefault();
        const lang = langTrigger.getAttribute('data-lang') || 'en';
        const href = langTrigger.getAttribute('href') || langTrigger.getAttribute('data-href');
        if (href) {
            window.switchLanguageSeamlessly(lang, href);
        }
    });

    if (typeof window.initPushSubscriptionUi === 'function') {
        window.initPushSubscriptionUi();
    }
});
</script>
</body>
</html>

