<?php
/**
 * Component: Quick Article Preview Modal
 * news-platform / templates / components / quick-view-modal.php
 */
?>
<!-- Quick Article Preview Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius: 4px !important; border: 1px solid #e5e7eb !important; overflow: hidden !important;">
            <div class="modal-header border-bottom py-3 px-4 bg-white" style="border-left: 4px solid #c8102e; border-top-left-radius: inherit;">
                <div class="d-flex align-items-center gap-2">
                    <span id="qvCategory" class="badge text-uppercase px-2 py-1 fw-bold" style="background:#c8102e; color:#ffffff; font-size:0.68rem; letter-spacing:0.05em;">CATEGORY</span>
                    <span id="qvReadingTime" class="text-muted text-xs"><i class="bi bi-clock me-1"></i>3 min read</span>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <h3 id="qvTitle" class="fw-bold mb-3" style="color:#0f172a; font-size:1.35rem; line-height:1.35; letter-spacing:-0.015em;">Article Title</h3>
                
                <div id="qvImageWrapper" class="mb-3 overflow-hidden" style="max-height: 260px; background:#f3f4f6; display:none;">
                    <img id="qvImage" src="" alt="Article Preview" class="w-100 h-100 object-fit-cover">
                </div>

                <div class="d-flex align-items-center gap-3 text-muted text-xs mb-3 pb-2 border-bottom">
                    <span><i class="bi bi-person me-1 text-danger"></i><span id="qvAuthor">Author</span></span>
                    <span>&bull;</span>
                    <span><i class="bi bi-calendar3 me-1"></i><span id="qvDate">Date</span></span>
                    <span>&bull;</span>
                    <span><i class="bi bi-eye me-1" style="color:#c8102e;"></i><span id="qvViews">0 views</span></span>
                </div>

                <p id="qvSummary" class="text-secondary mb-4" style="font-size:0.95rem; line-height:1.7;">
                    Summary text preview...
                </p>
            </div>
            <div class="modal-footer border-top py-3 px-4 bg-light d-flex justify-content-between align-items-center">
                <button type="button" id="qvBookmarkBtn" class="btn btn-outline-secondary btn-sm fw-bold d-inline-flex align-items-center gap-1.5" style="border-radius:2px;">
                    <i class="bi bi-bookmark"></i> <span><?= __('save_for_later') ?? 'Save Article' ?></span>
                </button>
                <a id="qvFullArticleLink" href="#" class="btn btn-danger btn-sm px-4 fw-bold" style="border-radius:2px; text-transform:uppercase; letter-spacing:0.04em;">
                    <?= __('read_full_article') ?? 'Read Full Story' ?> <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
