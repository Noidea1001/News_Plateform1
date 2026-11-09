<?php
/**
 * Component: Reading Toolbar with Font Size Adjuster & Print Functionality
 * news-platform / templates / components / reading-toolbar.php
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
?>

<!-- Scroll Progress Bar fixed at top of viewport -->
<div id="readingProgressBar" class="position-fixed top-0 start-0 bg-danger" style="height: 3px; width: 0%; z-index: 1060; transition: width 0.1s linear;"></div>

<!-- In-Page Reader Utility Strip (Font Size & Print) -->
<div class="reading-controls-bar d-flex align-items-center justify-content-between py-2 px-3 my-3 bg-white border rounded-2" style="border-color: #e5e7eb;">
    
    <!-- Font Size Controls -->
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted fw-semibold text-2xs d-none d-sm-inline" style="font-size: 0.72rem;">
            <i class="bi bi-fonts text-danger me-1"></i><?= __('font_size_label') ?? 'Text Size' ?>:
        </span>
        <div class="btn-group btn-group-sm font-size-btn-group" role="group" aria-label="Font Size">
            <button type="button" class="btn btn-outline-secondary font-size-btn" data-size="small" title="Small Text">
                <span style="font-size: 0.75rem;">A-</span>
            </button>
            <button type="button" class="btn btn-outline-secondary font-size-btn active" data-size="normal" title="Normal Text">
                <span style="font-size: 0.85rem; font-weight: 700;">A</span>
            </button>
            <button type="button" class="btn btn-outline-secondary font-size-btn" data-size="large" title="Large Text">
                <span style="font-size: 1rem; font-weight: 800;">A+</span>
            </button>
        </div>
    </div>

    <!-- Print & Clean Reader Action -->
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1.5 py-1 px-2.5 rounded-2"
                style="font-size: 0.75rem;" onclick="window.print()" title="<?= __('print_article') ?? 'Print Article' ?>">
            <i class="bi bi-printer"></i>
            <span class="d-none d-md-inline"><?= __('print_article') ?? 'Print' ?></span>
        </button>
    </div>

</div>

<style>
.font-size-btn-group .btn {
    padding: 2px 10px;
    font-size: 0.78rem;
    color: #475569;
    border-color: #e2e8f0;
}
.font-size-btn-group .btn:hover {
    background-color: #f8fafc;
    color: #0f172a;
    border-color: #cbd5e1;
}
.font-size-btn-group .btn.active {
    background-color: #c8102e !important;
    border-color: #c8102e !important;
    color: #ffffff !important;
}

/* Dynamic Font Sizes applied to Article Content */
.article-body.font-size-small {
    font-size: 0.95rem !important;
    line-height: 1.7 !important;
}
.article-body.font-size-normal {
    font-size: 1.08rem !important;
    line-height: 1.8 !important;
}
.article-body.font-size-large {
    font-size: 1.25rem !important;
    line-height: 1.95 !important;
}

/* Print Styles */
@media print {
    header, footer, .top-utility-header, .navbar, .category-nav-cna, 
    .editorial-actions-bar, .reading-controls-bar, .np-audio-player-card,
    .comments-section, .related-coverage, aside, .btn, #readingProgressBar {
        display: none !important;
    }
    body {
        background: #ffffff !important;
        color: #000000 !important;
        font-size: 12pt !important;
    }
    .article-body {
        font-size: 12pt !important;
        line-height: 1.6 !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Reading Scroll Progress Bar
    const progressBar = document.getElementById('readingProgressBar');
    if (progressBar) {
        window.addEventListener('scroll', function() {
            const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
            if (totalHeight > 0) {
                const progress = (window.scrollY / totalHeight) * 100;
                progressBar.style.width = Math.min(100, Math.max(0, progress)) + '%';
            }
        });
    }

    // 2. Persistent Font Size Controller
    const FONT_STORAGE_KEY = 'np_reader_font_size_v2';
    const articleBody = document.querySelector('.article-body');
    const fontBtns = document.querySelectorAll('.font-size-btn');

    function applyFontSize(size) {
        if (!articleBody) return;
        articleBody.classList.remove('font-size-small', 'font-size-normal', 'font-size-large');
        articleBody.classList.add('font-size-' + size);

        fontBtns.forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-size') === size);
        });

        try {
            localStorage.setItem(FONT_STORAGE_KEY, size);
        } catch(e) {}
    }

    // Initialize from storage or default to normal
    const savedSize = localStorage.getItem(FONT_STORAGE_KEY) || 'normal';
    applyFontSize(savedSize);

    fontBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const size = this.getAttribute('data-size');
            applyFontSize(size);
        });
    });
});
</script>
