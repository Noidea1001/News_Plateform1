<?php
/**
 * Component: Saved Reading List Offcanvas / Modal
 * news-platform / templates / components / saved-articles-modal.php
 */
$isReaderLoggedIn = \App\Core\Auth::readerCheck() || \App\Core\Auth::check();
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
        <?php if ($isReaderLoggedIn) { ?>
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
        <?php } else { ?>
            <div class="p-4 text-center my-auto">
                <div class="mb-3 mx-auto rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width:68px; height:68px;">
                    <i class="bi bi-bookmark-lock-fill text-danger" style="font-size: 2rem;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1.5"><?= __('bookmark_login_prompt_title') ?></h6>
                <p class="text-muted text-xs mb-3 px-2"><?= __('bookmark_login_prompt_desc') ?></p>
                <button type="button" class="btn btn-danger btn-sm px-3.5 py-2 fw-semibold rounded-2 shadow-2xs"
                    data-bs-dismiss="offcanvas" data-bs-toggle="modal" data-bs-target="#readerAuthModal" data-auth-tab="register">
                    <i class="bi bi-person-plus-fill me-1"></i><?= __('create_account') ?>
                </button>
            </div>
        <?php } ?>
    </div>
</div>
