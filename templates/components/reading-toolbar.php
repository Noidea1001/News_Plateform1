<?php
/**
 * Reading Toolbar Component
 * news-platform / templates / components / reading-toolbar.php
 */
?>

<!-- Scroll Progress Bar fixed at top of viewport -->
<div id="readingProgressBar" class="position-fixed top-0 start-0 bg-danger" style="height: 3px; width: 0%; z-index: 1060; transition: width 0.1s linear;"></div>

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

    // Clear legacy reader font size from localStorage if present
    localStorage.removeItem('reader_font_size');
});
</script>
<?php ?>
