</main><!-- /main -->

<!-- Subscribe, Quick View & Saved Reading List Modals -->
<?php
$subModal = __DIR__ . '/../components/subscribe-modal.php';
$qvModal = __DIR__ . '/../components/quick-view-modal.php';
$savedModal = __DIR__ . '/../components/saved-articles-modal.php';

if (file_exists($subModal)) { include $subModal; }
if (file_exists($qvModal)) { include $qvModal; }
if (file_exists($savedModal)) { include $savedModal; }
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

            <!-- Newsletter CTA -->
            <div class="col-lg-4 col-md-12">
                <div style="border-left:3px solid #c8102e; padding-left:0.75rem; margin-bottom:1.25rem;">
                    <h6 style="color:#fff; font-size:0.78rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; margin:0;">
                        <?= __('feed_sub_title') ?>
                    </h6>
                </div>
                <p style="font-size:0.875rem; color:rgba(255,255,255,0.55); line-height:1.75; margin-bottom:1.25rem;">
                    <?= __('sub_desc_footer') ?>
                </p>
                <button type="button"
                        class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2 fw-bold"
                        style="font-size:0.85rem; border-radius:2px; text-transform:uppercase; letter-spacing:0.05em; padding:0.65rem 1rem;"
                        data-bs-toggle="modal" data-bs-target="#subscribeModal">
                    <i class="bi bi-envelope-check-fill"></i>
                    <?= __('register_sub_shortcut') ?>
                </button>
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
    const CURRENT_LANG = '<?= $_SESSION['lang'] ?? 'en' ?>';
    const IS_KHMER = (CURRENT_LANG === 'kh' || CURRENT_LANG === 'km');
    const TXT_READ_STORY = '<?= __('read_story') ?>';

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
            return JSON.parse(localStorage.getItem(BOOKMARK_KEY)) || [];
        } catch (e) {
            return [];
        }
    }

    function saveArticlesList(list) {
        localStorage.setItem(BOOKMARK_KEY, JSON.stringify(list));
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
        const clearBtn = document.getElementById('clearSavedArticlesBtn');
        const list = getSavedArticles();
        if (badge) {
            badge.textContent = list.length;
            badge.style.display = list.length > 0 ? 'inline-block' : 'none';
        }
        if (clearBtn) {
            clearBtn.style.display = list.length > 0 ? 'block' : 'none';
        }
    }

    function syncBookmarkButtonsState() {
        const buttons = document.querySelectorAll('.bookmark-toggle-btn');
        buttons.forEach(btn => {
            const id = btn.getAttribute('data-id');
            const icon = btn.querySelector('i');
            if (id && isSaved(id)) {
                btn.classList.add('bookmarked');
                if (icon) {
                    icon.className = 'bi bi-bookmark-fill text-danger';
                }
            } else {
                btn.classList.remove('bookmarked');
                if (icon) {
                    icon.className = 'bi bi-bookmark';
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
                <div class="list-group-item p-3 border-bottom d-flex gap-3 align-items-start position-relative">
                    ${item.image ? `<img src="${item.image}" style="width:68px; height:48px; object-fit:cover; border-radius:2px; flex-shrink:0;">` : ''}
                    <div class="flex-grow-1 min-w-0">
                        <div class="text-xs font-monospace text-uppercase fw-bold text-danger mb-1">${displayCategory}</div>
                        <h6 class="fw-bold mb-1" style="font-size:0.86rem; line-height:1.35; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                            <a href="${item.url}" class="text-dark text-decoration-none">${displayTitle}</a>
                        </h6>
                        <div class="text-xs text-muted d-flex align-items-center gap-2">
                            <span>${displayReadingTime}</span>
                            <span>&bull;</span>
                            <a href="${item.url}" class="text-danger fw-bold text-decoration-none">${TXT_READ_STORY} &rarr;</a>
                        </div>
                    </div>
                    <button type="button" class="btn btn-link text-muted p-0 remove-saved-btn" data-id="${item.id}" title="Remove from list" style="font-size:0.9rem;">
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
            const dataRaw = btn.getAttribute('data-article');
            if (dataRaw) {
                try {
                    const articleData = JSON.parse(dataRaw);
                    toggleSaveArticle(articleData);
                } catch (err) {
                    console.error('Error parsing article JSON for bookmark:', err);
                }
            }
        }
    });

    // Attach clear all saved articles handler
    const clearBtn = document.getElementById('clearSavedArticlesBtn');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (confirm('<?= __('confirm_clear_saved') ?? 'Are you sure you want to clear all saved articles?' ?>')) {
                saveArticlesList([]);
            }
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
                            qvBmBtn.className = 'btn btn-danger btn-sm fw-bold d-inline-flex align-items-center gap-1.5';
                            qvBmBtn.querySelector('span').textContent = '<?= __('saved') ?? 'Saved' ?>';
                            qvBmBtn.querySelector('i').className = 'bi bi-bookmark-fill';
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
            const dataRaw = this.getAttribute('data-article');
            if (dataRaw) {
                try {
                    const art = JSON.parse(dataRaw);
                    toggleSaveArticle(art);
                    if (isSaved(art.id)) {
                        this.className = 'btn btn-danger btn-sm fw-bold d-inline-flex align-items-center gap-1.5';
                        this.querySelector('span').textContent = '<?= __('saved') ?? 'Saved' ?>';
                        this.querySelector('i').className = 'bi bi-bookmark-fill';
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
});
</script>
</body>
</html>

