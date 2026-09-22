<?php
/**
 * Compact Font Size Control Component (A- / 100% / A+)
 * news-platform / templates / components / reading-toolbar.php
 */
?>

<!-- Scroll Progress Bar fixed at top of viewport -->
<div id="readingProgressBar" class="position-fixed top-0 start-0 bg-danger" style="height: 3px; width: 0%; z-index: 1060; transition: width 0.1s linear;"></div>

<!-- Font Size Control Pill (A- / 100% / A+) -->
<div class="d-flex justify-content-end mb-3">
    <div class="btn-group btn-group-sm border rounded-3 overflow-hidden bg-white shadow-2xs" role="group" aria-label="Font size controls">
        <button type="button" class="btn btn-light btn-sm text-dark px-3 py-1.5 fw-bold" id="btnFontDecrease" title="Smaller text (A-)" style="font-size: 0.88rem;">
            A &minus;
        </button>
        <button type="button" class="btn btn-white btn-sm text-dark px-3 py-1.5 font-monospace text-xs fw-bold" id="btnFontReset" title="Reset font size">
            <span id="fontSizeDisplay">100%</span>
        </button>
        <button type="button" class="btn btn-light btn-sm text-dark px-3 py-1.5 fw-bold" id="btnFontIncrease" title="Larger text (A+)" style="font-size: 0.98rem;">
            A &#43;
        </button>
    </div>
</div>

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

    // 2. Font Size Scaling (A- / A+ / Reset)
    const articleBody = document.querySelector('.article-body');
    const display = document.getElementById('fontSizeDisplay');
    const btnDecrease = document.getElementById('btnFontDecrease');
    const btnIncrease = document.getElementById('btnFontIncrease');
    const btnReset = document.getElementById('btnFontReset');

    if (articleBody) {
        const baseSize = 18; // base px
        let currentSize = parseInt(localStorage.getItem('reader_font_size') || baseSize, 10);
        
        function updateFontSize(size) {
            currentSize = Math.min(26, Math.max(14, size));
            articleBody.style.fontSize = currentSize + 'px';
            const percent = Math.round((currentSize / baseSize) * 100);
            if (display) display.textContent = percent + '%';
            localStorage.setItem('reader_font_size', currentSize);
        }

        // Initialize from storage or default
        updateFontSize(currentSize);

        if (btnDecrease) btnDecrease.addEventListener('click', () => updateFontSize(currentSize - 2));
        if (btnIncrease) btnIncrease.addEventListener('click', () => updateFontSize(currentSize + 2));
        if (btnReset) btnReset.addEventListener('click', () => updateFontSize(baseSize));
    }
});
</script>
<?php ?>
