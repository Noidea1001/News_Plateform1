<?php
/**
 * Component: Saved Reading List Offcanvas / Modal
 * news-platform / templates / components / saved-articles-modal.php
 */
?>
<!-- Saved Articles Offcanvas / Modal -->
<div class="offcanvas offcanvas-end border-start" tabindex="-1" id="savedArticlesModal" aria-labelledby="savedArticlesModalLabel" style="width: 380px; max-width: 90vw; overflow: hidden !important;">
    <div class="offcanvas-header border-bottom py-3 px-3 bg-white" style="border-left: 4px solid #c8102e; border-top-left-radius: inherit;">
        <h5 class="offcanvas-title fw-bold text-dark d-flex align-items-center gap-2" id="savedArticlesModalLabel" style="font-size: 1rem;">
            <i class="bi bi-bookmark-fill text-danger"></i> <?= __('saved_reading_list') ?? 'Saved Reading List' ?>
        </h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0 bg-white d-flex flex-column">
        <div id="savedArticlesList" class="list-group list-group-flush flex-grow-1 overflow-y-auto">
            <!-- Dynamically populated via JS -->
            <div class="p-4 text-center text-muted my-auto" id="savedEmptyState">
                <i class="bi bi-bookmark-dash fs-1 d-block mb-2 text-secondary"></i>
                <p class="mb-0 fw-semibold" style="font-size: 0.9rem;"><?= __('no_saved_articles') ?? 'No saved articles yet.' ?></p>
                <small class="text-xs"><?= __('click_bookmark_hint') ?? 'Click the bookmark icon on any article to save it for later.' ?></small>
            </div>
        </div>

        <div class="p-3 border-top bg-light mt-auto text-end">
            <button type="button" id="clearSavedArticlesBtn" class="btn btn-outline-danger btn-sm fw-bold w-100" style="border-radius: 2px; display: none;">
                <i class="bi bi-trash3 me-1"></i> <?= __('clear_all_saved') ?? 'Clear Saved Reading List' ?>
            </button>
        </div>
    </div>
</div>
