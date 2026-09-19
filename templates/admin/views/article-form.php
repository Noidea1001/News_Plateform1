<?php
/**
 * CMS Article Editor Form View (Create & Edit Modes)
 * news-platform / templates / admin / views / article-form.php
 */
$isEdit = !empty($article['id']);
$formAction = url('admin/actions/save-article.php');
?>

<!-- Quill WYSIWYG Editor Assets -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<div class="container-fluid px-4 py-4">

    <!-- Top Navigation Bar -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="<?= url('admin/dashboard.php') ?>" class="text-secondary text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i> <?= __('back_to_dashboard') ?>
            </a>
            <h2 class="fw-bold editorial-title mb-0 text-dark mt-1">
                <?= $isEdit ? __('edit_article_title') . ': ' . e($article['title']) : __('draft_post_title') ?>
            </h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= url('admin/dashboard.php') ?>" class="btn btn-outline-secondary px-3.5 py-2 fw-semibold rounded-3 text-nowrap shadow-sm d-inline-flex align-items-center gap-1.5">
                <i class="bi bi-x-circle"></i>
                <span><?= __('cancel') ?></span>
            </a>
            <button type="submit" form="articleForm" class="btn btn-danger px-4 py-2 fw-semibold rounded-3 text-nowrap shadow d-inline-flex align-items-center gap-2">
                <i class="bi bi-cloud-arrow-up-fill fs-6"></i>
                <span><?= $isEdit ? __('update_article') : __('publish_save') ?></span>
            </button>
        </div>
    </div>

    <!-- Error Alert Display -->
    <?php if (!empty($_GET['error'])) { ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= e($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>

    <form id="articleForm" action="<?= $formAction ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <?php if ($isEdit) { ?>
            <input type="hidden" name="id" value="<?= (int)$article['id'] ?>">
            <input type="hidden" name="existing_featured_image" value="<?= e($article['featured_image'] ?? '') ?>">
        <?php } ?>

        <div class="row g-4">
            
            <!-- Left 8 Columns: Main Form Fields -->
            <div class="col-lg-8">
                
                <div class="card border-0 shadow-sm p-4 mb-4">
                    
                    <!-- Article Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold text-dark"><?= __('article_title') ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="title" name="title" 
                               value="<?= e($article['title'] ?? '') ?>" 
                               placeholder="e.g. Next-Generation Autonomous AI Systems Reshape Enterprise Architecture" required>
                    </div>

                    <!-- Slug Input (Auto-generated via JS) -->
                    <div class="mb-4">
                        <label for="slug" class="form-label fw-semibold small text-muted"><?= __('url_slug_label') ?></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted">/article.php?slug=</span>
                            <input type="text" class="form-control font-monospace" id="slug" name="slug" 
                                   value="<?= e($article['slug'] ?? '') ?>" 
                                   placeholder="next-generation-autonomous-ai-systems">
                            <button type="button" class="btn btn-outline-secondary" id="btnAutoSlug"><?= __('auto_generate') ?></button>
                        </div>
                    </div>

                    <!-- Summary / Standfirst -->
                    <div class="mb-4">
                        <label for="summary" class="form-label fw-semibold text-dark"><?= __('summary_label') ?></label>
                        <textarea class="form-control" id="summary" name="summary" rows="3" 
                                  placeholder="<?= __('summary_placeholder') ?>"><?= e($article['summary'] ?? '') ?></textarea>
                    </div>

                    <!-- Main Content Body (Quill WYSIWYG Integration) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                            <span><?= __('article_body_label') ?> (WYSIWYG Rich Text) <span class="text-danger">*</span></span>
                            <span class="badge bg-light text-secondary border fw-normal"><i class="bi bi-pencil-square me-1"></i> <?= __('rich_editor_badge') ?></span>
                        </label>
                        <!-- Quill Editor Container -->
                        <div id="quillEditor" class="bg-white rounded-bottom" style="min-height: 280px; font-size: 1.05rem;">
                            <?= $article['content'] ?? '' ?>
                        </div>
                        <!-- Hidden Form Textarea Syncing with Quill -->
                        <textarea class="d-none" id="content" name="content" required><?= e($article['content'] ?? '') ?></textarea>
                    </div>

                </div>

                <!-- Media, Gallery & Verified Source Citations Card -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold editorial-title text-dark mb-3">
                        <i class="bi bi-collection-play-fill text-danger me-2"></i> <?= __('multimedia_embeds_title') ?>
                    </h5>

                    <!-- Video Embed URL -->
                    <div class="mb-3">
                        <label for="video_embed_url" class="form-label fw-semibold small text-dark"><?= __('video_embed_label') ?></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-youtube text-danger"></i></span>
                            <input type="url" class="form-control" id="video_embed_url" name="video_embed_url" 
                                   value="<?= e($article['video_embed_url'] ?? '') ?>" 
                                   placeholder="https://www.youtube.com/embed/dQw4w9WgXcQ">
                        </div>
                    </div>

                    <!-- Audio / Podcast Embed URL -->
                    <div class="mb-3">
                        <label for="audio_embed_url" class="form-label fw-semibold small text-dark">
                            <i class="bi bi-broadcast me-1 text-primary"></i> <?= __('audio_embed_label') ?>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-music-note-beamed text-primary"></i></span>
                            <input type="url" class="form-control" id="audio_embed_url" name="audio_embed_url" 
                                   value="<?= e($article['audio_embed_url'] ?? '') ?>" 
                                   placeholder="https://open.spotify.com/embed/episode/... or MP3 audio file URL">
                        </div>
                        <div class="text-muted text-xs mt-1"><?= __('audio_embed_hint') ?></div>
                    </div>

                    <!-- Interactive Photo Gallery Images (Newline Separated URLs) -->
                    <div class="mb-4">
                        <label for="gallery_images" class="form-label fw-semibold small text-dark">
                            <i class="bi bi-images me-1 text-success"></i> <?= __('gallery_label') ?>
                        </label>
                        <textarea class="form-control font-monospace small" id="gallery_images" name="gallery_images" rows="3" 
                                  placeholder="https://images.unsplash.com/photo-1&#10;https://images.unsplash.com/photo-2&#10;https://images.unsplash.com/photo-3"><?= e($article['gallery_images'] ?? '') ?></textarea>
                        <div class="text-muted text-xs mt-1"><?= __('gallery_hint') ?></div>
                    </div>

                    <h6 class="fw-bold editorial-title text-dark pt-3 border-top mb-3">
                        <i class="bi bi-patch-check text-primary me-2"></i> <?= __('verified_citations_title') ?>
                    </h6>

                    <div class="row g-3">
                        <!-- Citation Source Name -->
                        <div class="col-md-6">
                            <label for="reference_source_name" class="form-label fw-semibold small text-dark"><?= __('ref_name_label') ?></label>
                            <input type="text" class="form-control" id="reference_source_name" name="reference_source_name" 
                                   value="<?= e($article['reference_source_name'] ?? '') ?>" 
                                   placeholder="e.g. arXiv Research Repository">
                        </div>
                        <!-- Citation Source URL -->
                        <div class="col-md-6">
                            <label for="reference_url" class="form-label fw-semibold small text-dark"><?= __('ref_url_label') ?></label>
                            <input type="url" class="form-control" id="reference_url" name="reference_url" 
                                   value="<?= e($article['reference_url'] ?? '') ?>" 
                                   placeholder="https://arxiv.org/abs/2301.00000">
                        </div>
                    </div>

                </div>

            </div>

            <!-- Right 4 Columns: Settings, Template Picker & Cover Image -->
            <div class="col-lg-4">

                <!-- Publish Settings Card -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h6 class="fw-bold editorial-title text-dark mb-3"><?= __('publishing_control_title') ?></h6>

                    <!-- Status Selector -->
                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold small text-dark"><?= __('post_status') ?></label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft" <?= (isset($article['status']) && $article['status'] === 'draft') ? 'selected' : '' ?>><?= __('draft') ?></option>
                            <option value="published" <?= (isset($article['status']) && $article['status'] === 'published') ? 'selected' : '' ?>><?= __('published') ?></option>
                            <option value="archived" <?= (isset($article['status']) && $article['status'] === 'archived') ? 'selected' : '' ?>><?= __('archived') ?></option>
                        </select>
                    </div>

                    <!-- Template Type Picker (CRITICAL REQUIREMENT) -->
                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <label for="template_type" class="form-label fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-layout-three-columns text-danger fs-5"></i>
                            <span><?= __('template_blueprint') ?> <span class="text-danger">*</span></span>
                        </label>
                        <select class="form-select fw-semibold" id="template_type" name="template_type" required>
                            <option value="standard" <?= (isset($article['template_type']) && $article['template_type'] === 'standard') ? 'selected' : '' ?>>
                                Blueprint 1: Standard (<?= __('tmpl_standard_name') ?>)
                            </option>
                            <option value="investigative" <?= (isset($article['template_type']) && $article['template_type'] === 'investigative') ? 'selected' : '' ?>>
                                Blueprint 2: Investigative (<?= __('tmpl_investigative_name') ?>)
                            </option>
                            <option value="opinion" <?= (isset($article['template_type']) && $article['template_type'] === 'opinion') ? 'selected' : '' ?>>
                                Blueprint 3: Opinion (<?= __('tmpl_opinion_name') ?>)
                            </option>
                        </select>
                        
                        <!-- Interactive Blueprint Visual Preview Box -->
                        <div id="blueprintPreviewBox" class="mt-3 p-3 rounded-3 border bg-white shadow-sm">
                            <div id="previewStandard" class="blueprint-preview">
                                <div class="d-flex align-items-center gap-2 text-primary fw-bold mb-1 text-xs text-uppercase">
                                    <i class="bi bi-layout-three-columns"></i> <?= __('tmpl_standard_name') ?>
                                </div>
                                <p class="text-muted text-xs mb-0"><?= __('blueprint_standard_desc') ?></p>
                            </div>
                            <div id="previewInvestigative" class="blueprint-preview" style="display: none;">
                                <div class="d-flex align-items-center gap-2 text-danger fw-bold mb-1 text-xs text-uppercase">
                                    <i class="bi bi-card-text"></i> <?= __('tmpl_investigative_name') ?>
                                </div>
                                <p class="text-muted text-xs mb-0"><?= __('blueprint_investigative_desc') ?></p>
                            </div>
                            <div id="previewOpinion" class="blueprint-preview" style="display: none;">
                                <div class="d-flex align-items-center gap-2 text-warning text-dark fw-bold mb-1 text-xs text-uppercase">
                                    <i class="bi bi-person-badge"></i> <?= __('tmpl_opinion_name') ?>
                                </div>
                                <p class="text-muted text-xs mb-0"><?= __('blueprint_opinion_desc') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Category Selector -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold small text-dark"><?= __('news_category') ?> <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value=""><?= __('select_category_option') ?></option>
                            <?php foreach ($categories as $cat) { ?>
                                <option value="<?= (int)$cat['id'] ?>" <?= (isset($article['category_id']) && (int)$article['category_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                                    <?= e(cat_name($cat['name'])) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Author Selector -->
                    <div class="mb-3">
                        <label for="author_id" class="form-label fw-semibold small text-dark"><?= __('assigned_author') ?></label>
                        <select class="form-select" id="author_id" name="author_id">
                            <?php foreach ($authors as $aut) { ?>
                                <option value="<?= (int)$aut['id'] ?>" <?= (isset($article['author_id']) && (int)$article['author_id'] === (int)$aut['id']) ? 'selected' : ($aut['id'] === $currentUser['id'] ? 'selected' : '') ?>>
                                    <?= e($aut['username']) ?> (<?= e($aut['role']) ?>)
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Is Breaking Toggle -->
                    <div class="form-check form-switch mt-3 pt-2 border-top">
                        <input class="form-check-input" type="checkbox" id="is_breaking" name="is_breaking" value="1" <?= (!empty($article['is_breaking'])) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold text-danger" for="is_breaking">
                            <i class="bi bi-lightning-fill"></i> <?= __('flag_breaking') ?>
                        </label>
                    </div>

                </div>

                <!-- Featured Cover Image Dropzone Card -->
                <div class="card border-0 shadow-sm p-4">
                    <h6 class="fw-bold editorial-title text-dark mb-3"><?= __('featured_image_label') ?></h6>

                    <?php if ($isEdit && !empty($article['featured_image'])) { ?>
                        <div class="mb-3 text-center">
                            <img src="<?= e($article['featured_image']) ?>" class="img-fluid rounded border shadow-sm" style="max-height: 180px;" alt="Cover">
                            <div class="text-xs text-muted mt-1"><?= __('current_image') ?></div>
                        </div>
                    <?php } ?>

                    <div class="mb-2">
                        <label for="featured_image" class="form-label fw-semibold small text-dark"><?= __('upload_cover') ?></label>
                        <input type="file" class="form-control form-control-sm" id="featured_image" name="featured_image" accept="image/jpeg,image/png,image/webp">
                    </div>
                    <div class="text-muted text-xs">
                        <?= __('permitted_formats') ?>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

<!-- Real-Time JavaScript Slug Generator -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const autoBtn = document.getElementById('btnAutoSlug');

    function slugify(text) {
        return text.toString().toLowerCase().trim()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
    }

    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function() {
            if (!slugInput.dataset.userEdited) {
                slugInput.value = slugify(titleInput.value);
            }
        });

        slugInput.addEventListener('input', function() {
            slugInput.dataset.userEdited = "true";
        });

        if (autoBtn) {
            autoBtn.addEventListener('click', function() {
                slugInput.value = slugify(titleInput.value);
                delete slugInput.dataset.userEdited;
            });
        }
    }

    // Dynamic Template Blueprint Preview Switcher
    const templateSelect = document.getElementById('template_type');
    const previewStandard = document.getElementById('previewStandard');
    const previewInvestigative = document.getElementById('previewInvestigative');
    const previewOpinion = document.getElementById('previewOpinion');

    function updateBlueprintPreview() {
        if (!templateSelect) return;
        const val = templateSelect.value;
        if (previewStandard) previewStandard.style.display = (val === 'standard') ? 'block' : 'none';
        if (previewInvestigative) previewInvestigative.style.display = (val === 'investigative') ? 'block' : 'none';
        if (previewOpinion) previewOpinion.style.display = (val === 'opinion') ? 'block' : 'none';
    }

    if (templateSelect) {
        templateSelect.addEventListener('change', updateBlueprintPreview);
        updateBlueprintPreview();
    }

    // Initialize Quill Rich Text Editor
    if (document.getElementById('quillEditor')) {
        const quill = new Quill('#quillEditor', {
            theme: 'snow',
            placeholder: '<?= addslashes(__('quill_placeholder')) ?>',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['blockquote', 'code-block'],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            }
        });

        const articleForm = document.getElementById('articleForm');
        if (articleForm) {
            articleForm.addEventListener('submit', function() {
                const contentInput = document.getElementById('content');
                if (contentInput) {
                    contentInput.value = quill.root.innerHTML;
                }
            });
        }
    }
});
</script>
