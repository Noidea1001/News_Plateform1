<?php
/**
 * Component: Recently Read Stories / Reading History Widget
 * news-platform / templates / components / reading-history-widget.php
 * Displays the reader's local browsing history with smooth thumb layouts.
 */

require_once __DIR__ . '/../../src/Core/helpers.php';
$currentLang = $_SESSION['lang'] ?? 'en';
$isKh = ($currentLang === 'kh' || $currentLang === 'km');
?>

<section class="sbw reading-history-widget mb-4 d-none" id="readingHistoryCard" style="border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
    <!-- Header with proper breathing room -->
    <div class="sbw-head d-flex align-items-center justify-content-between p-3 border-bottom" style="background: #fafafa;">
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center text-danger flex-shrink-0" style="font-size: 1rem;">
                <i class="bi bi-clock-history"></i>
            </span>
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.86rem;"><?= __('reading_history_title') ?></h6>
        </div>
        <button type="button" id="clearHistoryBtn" class="btn btn-link p-0 text-decoration-none text-muted" style="font-size: 0.7rem;" title="<?= __('reading_history_clear') ?>">
            <i class="bi bi-trash3 me-0.5"></i><?= __('reading_history_clear') ?>
        </button>
    </div>

    <!-- History List with clean spaced rows -->
    <div class="p-2.5" id="readingHistoryList">
        <!-- Rendered dynamically by client-side JS -->
    </div>
</section>

<script>
(function() {
    const HISTORY_KEY = 'np_reading_history_v1';
    const card = document.getElementById('readingHistoryCard');
    const list = document.getElementById('readingHistoryList');
    const clearBtn = document.getElementById('clearHistoryBtn');

    function renderHistory() {
        if (!card || !list) return;
        let items = [];
        try {
            items = JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]');
        } catch(e) { items = []; }

        if (!Array.isArray(items) || items.length === 0) {
            card.classList.add('d-none');
            return;
        }

        // Show card and render up to 4 recent items
        card.classList.remove('d-none');
        list.innerHTML = '';

        items.slice(0, 4).forEach(function(item, idx) {
            const row = document.createElement('a');
            row.href = item.url || ('article.php?slug=' + encodeURIComponent(item.slug));
            row.className = 'd-flex align-items-start gap-2.5 p-2 rounded-2 text-decoration-none transition-bg text-dark' + (idx > 0 ? ' border-top' : '');
            row.style.borderColor = '#f1f5f9';

            const imgBox = document.createElement('div');
            imgBox.style.width = '64px';
            imgBox.style.height = '48px';
            imgBox.style.borderRadius = '4px';
            imgBox.style.overflow = 'hidden';
            imgBox.style.flexShrink = '0';
            imgBox.style.background = '#f8fafc';

            if (item.image) {
                imgBox.innerHTML = '<img src="' + item.image + '" alt="" style="width:100%; height:100%; object-fit:cover;">';
            } else {
                imgBox.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted" style="font-size:0.75rem;"><i class="bi bi-newspaper"></i></div>';
            }

            const bodyBox = document.createElement('div');
            bodyBox.className = 'flex-grow-1 min-w-0';
            bodyBox.innerHTML = '<span class="text-danger fw-bold text-3xs text-uppercase d-block mb-0.5" style="font-size:0.65rem;">' + (item.category || '') + '</span>' +
                                '<h6 class="fw-bold mb-1 text-truncate-2" style="font-size:0.78rem; line-height:1.35; color:#0f172a;">' + (item.title || '') + '</h6>';

            row.appendChild(imgBox);
            row.appendChild(bodyBox);

            row.addEventListener('mouseenter', function() { this.style.backgroundColor = '#f8fafc'; });
            row.addEventListener('mouseleave', function() { this.style.backgroundColor = 'transparent'; });

            list.appendChild(row);
        });
    }

    renderHistory();

    if (clearBtn) {
        clearBtn.addEventListener('click', function(e) {
            e.preventDefault();
            try {
                localStorage.removeItem(HISTORY_KEY);
            } catch(e) {}
            if (card) card.classList.add('d-none');
        });
    }

    // Record current article if on article page
    const artElem = document.querySelector('[data-article-history-id]');
    if (artElem) {
        try {
            const artId = artElem.getAttribute('data-article-history-id');
            const artTitle = artElem.getAttribute('data-article-history-title');
            const artSlug = artElem.getAttribute('data-article-history-slug');
            const artCat = artElem.getAttribute('data-article-history-category');
            const artImg = artElem.getAttribute('data-article-history-image');
            const artUrl = window.location.href;

            if (artId && artTitle) {
                let items = JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]');
                items = items.filter(i => String(i.id) !== String(artId));
                items.unshift({
                    id: artId,
                    title: artTitle,
                    slug: artSlug,
                    category: artCat,
                    image: artImg,
                    url: artUrl,
                    time: Date.now()
                });
                if (items.length > 10) items = items.slice(0, 10);
                localStorage.setItem(HISTORY_KEY, JSON.stringify(items));
            }
        } catch(e) {}
    }
})();
</script>
