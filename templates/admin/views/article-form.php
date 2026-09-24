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
                &larr; <?= __('back_to_dashboard') ?>
            </a>
            <h2 class="fw-bold editorial-title mb-0 text-dark mt-1">
                <?= $isEdit ? __('edit_article_title') . ': ' . e($article['title']) : __('draft_post_title') ?>
            </h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= url('admin/dashboard.php') ?>"
                class="btn btn-outline-secondary px-3.5 py-2 fw-semibold rounded-3 text-nowrap shadow-sm">
                <span><?= __('cancel') ?></span>
            </a>
            <button type="submit" form="articleForm"
                class="btn btn-danger px-4 py-2 fw-semibold rounded-3 text-nowrap shadow">
                <span><?= $isEdit ? __('update_article') : __('publish_save') ?></span>
            </button>
        </div>
    </div>

    <!-- Top Navigation Bar -->

    <form id="articleForm" action="<?= $formAction ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <?php if ($isEdit) { ?>
            <input type="hidden" name="id" value="<?= (int) $article['id'] ?>">
            <input type="hidden" name="existing_featured_image" value="<?= e($article['featured_image'] ?? '') ?>">
        <?php } ?>

        <div class="row g-4">

            <!-- Left 8 Columns: Main Form Fields -->
            <div class="col-lg-8">

                <div class="card border-0 shadow-sm p-4 mb-4">

                    <!-- Article Title -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label for="title" class="form-label fw-bold text-dark mb-0"><?= __('article_title') ?> <span
                                    class="text-danger">*</span></label>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-2xs fw-bold px-2 py-0.5">
                                Dual-Language Support
                            </span>
                        </div>
                        <input type="text" class="form-control form-control-lg" id="title" name="title"
                            value="<?= e($article['title'] ?? '') ?>" placeholder="<?= e(__('title_placeholder')) ?>"
                            required>
                        <div class="text-muted text-xs mt-1">
                            <?= __('manual_translation_hint_title') ?>
                        </div>
                    </div>

                    <!-- Slug Input (Auto-generated via JS) -->
                    <div class="mb-4">
                        <label for="slug"
                            class="form-label fw-semibold small text-muted"><?= __('url_slug_label') ?></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted">/article.php?slug=</span>
                            <input type="text" class="form-control font-monospace" id="slug" name="slug"
                                value="<?= e($article['slug'] ?? '') ?>"
                                placeholder="next-generation-autonomous-ai-systems">
                            <button type="button" class="btn btn-outline-secondary"
                                id="btnAutoSlug"><?= __('auto_generate') ?></button>
                        </div>
                    </div>

                    <!-- Summary / Standfirst -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label for="summary" class="form-label fw-semibold text-dark mb-0"><?= __('summary_label') ?></label>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-2xs fw-bold px-2 py-0.5">
                                Dual-Language Support
                            </span>
                        </div>
                        <textarea class="form-control" id="summary" name="summary" rows="3"
                            placeholder="<?= __('summary_placeholder') ?>"><?= e($article['summary'] ?? '') ?></textarea>
                        <div class="text-muted text-xs mt-1">
                            <?= __('manual_translation_hint_summary') ?>
                        </div>
                    </div>

                    <!-- Content Media Reference Helper Panel (Images & Videos) -->
                    <?php
                    $galleryList = [];
                    if (!empty($article['gallery_images'])) {
                        $galleryList = array_values(array_filter(array_map('trim', explode("\n", $article['gallery_images']))));
                    }

                    $videoList = [];
                    if (!empty($article['video_embed_url'])) {
                        $videoList = array_values(array_filter(array_map('trim', explode("\n", $article['video_embed_url']))));
                    }
                    ?>
                    <div class="card bg-light border-0 p-3 mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-dark small">
                                <i class="bi bi-collection-play text-danger me-1"></i> <?= __('media_ref_title') ?>
                            </span>
                            <div class="d-flex gap-1">
                                <span class="badge bg-danger text-white"><?= count($galleryList) ?>
                                    <?= __('photos') ?></span>
                                <span class="badge bg-primary text-white"><?= count($videoList) ?>
                                    <?= __('videos') ?></span>
                            </div>
                        </div>
                        <p class="text-muted text-xs mb-2">
                            <?= __('media_ref_hint') ?>
                        </p>

                        <!-- Image Insert Buttons -->
                        <div class="mb-3">
                            <div class="text-xs fw-semibold text-muted mb-1.5"><i
                                    class="bi bi-image me-1 text-danger"></i> <?= __('images_label') ?></div>
                            <?php if (!empty($galleryList)) { ?>
                                <div class="d-flex flex-wrap align-items-center">
                                    <?php foreach ($galleryList as $idx => $gUrl) { ?>
                                        <?php $imgNum = $idx + 1; ?>
                                        <div class="btn-group btn-group-sm me-2 mb-2 shadow-2xs" role="group">
                                            <button type="button" class="btn btn-outline-danger insert-tag-btn"
                                                data-tag="[image:<?= $imgNum ?>]" title="Insert Center">
                                                <i class="bi bi-plus-circle-fill me-1"></i>[image:<?= $imgNum ?>]
                                            </button>
                                            <button type="button" class="btn btn-outline-danger px-2 insert-tag-btn"
                                                data-tag="[image:<?= $imgNum ?>:left]" title="Float Left with text wrap">
                                                <i class="bi bi-align-start"></i> Left
                                            </button>
                                            <button type="button" class="btn btn-outline-danger px-2 insert-tag-btn"
                                                data-tag="[image:<?= $imgNum ?>:right]" title="Float Right with text wrap">
                                                <i class="bi bi-align-end"></i> Right
                                            </button>
                                            <button type="button" class="btn btn-outline-danger px-2 insert-tag-btn"
                                                data-tag="[image:<?= $imgNum ?>:full]" title="Full Width">
                                                <i class="bi bi-arrows-expand"></i> Full
                                            </button>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <div class="text-xs text-muted fst-italic"><?= __('no_gallery_yet') ?></div>
                            <?php } ?>
                        </div>

                        <!-- Video Insert Buttons -->
                        <div>
                            <div class="text-xs fw-semibold text-muted mb-1.5"><i
                                    class="bi bi-play-btn me-1 text-primary"></i> <?= __('videos_label') ?></div>
                            <?php if (!empty($videoList)) { ?>
                                <div class="d-flex flex-wrap align-items-center">
                                    <?php foreach ($videoList as $vIdx => $vUrl) { ?>
                                        <?php $vNum = $vIdx + 1; ?>
                                        <div class="btn-group btn-group-sm me-2 mb-2 shadow-2xs" role="group">
                                            <button type="button" class="btn btn-outline-primary insert-tag-btn"
                                                data-tag="[video:<?= $vNum ?>]" title="Insert Center">
                                                <i class="bi bi-play-circle-fill me-1"></i>[video:<?= $vNum ?>]
                                            </button>
                                            <button type="button" class="btn btn-outline-primary px-2 insert-tag-btn"
                                                data-tag="[video:<?= $vNum ?>:left]" title="Float Left with text wrap">
                                                <i class="bi bi-align-start"></i> Left
                                            </button>
                                            <button type="button" class="btn btn-outline-primary px-2 insert-tag-btn"
                                                data-tag="[video:<?= $vNum ?>:right]" title="Float Right with text wrap">
                                                <i class="bi bi-align-end"></i> Right
                                            </button>
                                            <button type="button" class="btn btn-outline-primary px-2 insert-tag-btn"
                                                data-tag="[video:<?= $vNum ?>:full]" title="Full Width">
                                                <i class="bi bi-arrows-expand"></i> Full
                                            </button>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <div class="text-xs text-muted fst-italic"><?= __('no_video_yet') ?></div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Main Content Body (Quill WYSIWYG Integration) -->
                    <div class="mb-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                            <label class="form-label fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <span><?= __('article_body_label') ?> (WYSIWYG Rich Text) <span
                                        class="text-danger">*</span></span>
                                <span
                                    class="badge bg-light text-secondary border fw-normal"><?= __('rich_editor_badge') ?></span>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-2xs fw-bold px-2 py-0.5">
                                    Dual-Language Support
                                </span>
                            </label>

                            <!-- Manual Drop-Cap Toggle Switch (No bg, no border) -->
                            <div class="form-check form-switch m-0 p-0 d-inline-flex align-items-center">
                                <input class="form-check-input ms-0 me-2 cursor-pointer" type="checkbox"
                                    id="has_drop_cap" name="has_drop_cap" value="1"
                                    <?= (!empty($article['has_drop_cap'])) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold text-dark text-xs mb-0 cursor-pointer"
                                    for="has_drop_cap" title="<?= e(__('enable_drop_cap_hint')) ?>">
                                    <i class="bi bi-type-h1 me-1 text-danger"></i> <?= __('enable_drop_cap_label') ?>
                                </label>
                            </div>
                        </div>

                        <!-- Quill Editor Container -->
                        <div id="quillEditor" class="bg-white rounded-bottom"
                            style="min-height: 280px; font-size: 1.05rem;">
                            <?= $article['content'] ?? '' ?>
                        </div>
                        <!-- Hidden Form Textarea Syncing with Quill -->
                        <textarea class="d-none" id="content" name="content"
                            required><?= e($article['content'] ?? '') ?></textarea>
                        <div class="text-muted text-xs mt-1">
                            <?= __('manual_translation_hint_content') ?>
                        </div>
                    </div>

                </div>

                <!-- Media, Gallery & Verified Source Citations Card -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold editorial-title text-dark mb-3">
                        <?= __('multimedia_embeds_title') ?>
                    </h5>

                    <!-- Video Embed URLs (Newline Separated or Single) -->
                    <div class="mb-3">
                        <label for="video_embed_url"
                            class="form-label fw-semibold small text-dark"><?= __('video_embed_label') ?>
                            <?= __('one_per_line') ?></label>
                        <textarea class="form-control font-monospace small" id="video_embed_url" name="video_embed_url"
                            rows="2"
                            placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ&#10;https://youtu.be/video2"><?= e($article['video_embed_url'] ?? '') ?></textarea>
                        <div class="text-muted text-xs mt-1"><?= __('video_embed_hint') ?></div>
                    </div>


                    <!-- Audio / Podcast Embed URL -->
                    <div class="mb-3">
                        <label for="audio_embed_url" class="form-label fw-semibold small text-dark">
                            <?= __('audio_embed_label') ?>
                        </label>
                        <input type="url" class="form-control" id="audio_embed_url" name="audio_embed_url"
                            value="<?= e($article['audio_embed_url'] ?? '') ?>"
                            placeholder="https://open.spotify.com/embed/episode/... or MP3 audio file URL">
                        <div class="text-muted text-xs mt-1"><?= __('audio_embed_hint') ?></div>
                    </div>

                    <!-- Upload Multiple Gallery / Content Images -->
                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <label for="gallery_files"
                            class="form-label fw-bold small text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-cloud-arrow-up-fill text-danger fs-5"></i>
                            <span><?= __('upload_multi_images') ?></span>
                        </label>
                        <input type="file" class="form-control" id="gallery_files" name="gallery_files[]" multiple
                            accept="image/jpeg,image/png,image/webp">
                        <div class="text-muted text-xs mt-1"><?= __('upload_multi_images_hint') ?></div>
                    </div>

                    <!-- Interactive Photo Gallery Images (Newline Separated URLs) -->
                    <div class="mb-4">
                        <label for="gallery_images" class="form-label fw-semibold small text-dark">
                            <?= __('gallery_label') ?>
                        </label>
                        <textarea class="form-control font-monospace small" id="gallery_images" name="gallery_images"
                            rows="3"
                            placeholder="https://images.unsplash.com/photo-1&#10;https://images.unsplash.com/photo-2&#10;https://images.unsplash.com/photo-3"><?= e($article['gallery_images'] ?? '') ?></textarea>
                        <div class="text-muted text-xs mt-1"><?= __('gallery_hint') ?></div>
                    </div>

                    <h6 class="fw-bold editorial-title text-dark pt-3 border-top mb-3">
                        <?= __('verified_citations_title') ?>
                    </h6>

                    <div class="row g-3">
                        <!-- Citation Source Name -->
                        <div class="col-md-6">
                            <label for="reference_source_name"
                                class="form-label fw-semibold small text-dark"><?= __('ref_name_label') ?></label>
                            <input type="text" class="form-control" id="reference_source_name"
                                name="reference_source_name" value="<?= e($article['reference_source_name'] ?? '') ?>"
                                placeholder="<?= e(__('ref_name_placeholder')) ?>">
                        </div>

                        <!-- Citation Source URL -->
                        <div class="col-md-6">
                            <label for="reference_url"
                                class="form-label fw-semibold small text-dark"><?= __('ref_url_label') ?></label>
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
                        <label for="status"
                            class="form-label fw-semibold small text-dark"><?= __('post_status') ?></label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft" <?= (isset($article['status']) && $article['status'] === 'draft') ? 'selected' : '' ?>><?= __('draft') ?></option>
                            <option value="published" <?= (isset($article['status']) && $article['status'] === 'published') ? 'selected' : '' ?>><?= __('published') ?></option>
                            <option value="archived" <?= (isset($article['status']) && $article['status'] === 'archived') ? 'selected' : '' ?>><?= __('archived') ?></option>
                        </select>
                    </div>

                    <!-- Template Type Picker — Visual Interactive Cards -->
                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <label class="form-label fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                            <span><?= __('template_blueprint') ?> (ពុម្ពគំរូប្លង់) <span
                                    class="text-danger">*</span></span>
                        </label>
                        <input type="hidden" id="template_type" name="template_type"
                            value="<?= e($article['template_type'] ?? 'standard') ?>" required>

                        <div class="blueprint-picker-grid">
                            <!-- Card 1: Standard -->
                            <div class="blueprint-picker-card <?= (!isset($article['template_type']) || $article['template_type'] === 'standard') ? 'active' : '' ?>"
                                data-val="standard">
                                <div class="blueprint-check">&check;</div>
                                <div class="blueprint-mini-wireframe wf-standard">
                                    <div class="wf-bar wf-main"></div>
                                    <div class="wf-bar wf-side"></div>
                                </div>
                                <div class="fw-bold text-dark text-xs mb-1">
                                    <?= e(tmpl_name('standard')) ?>
                                </div>
                                <div class="text-muted" style="font-size: 0.68rem; line-height: 1.3;">
                                    <?= __('blueprint_standard_desc') ?>
                                </div>
                            </div>

                            <!-- Card 2: Investigative -->
                            <div class="blueprint-picker-card <?= (isset($article['template_type']) && $article['template_type'] === 'investigative') ? 'active' : '' ?>"
                                data-val="investigative">
                                <div class="blueprint-check">&check;</div>
                                <div class="blueprint-mini-wireframe wf-investigative">
                                    <div class="wf-bar wf-hero"></div>
                                    <div class="wf-bar wf-body"></div>
                                </div>
                                <div class="fw-bold text-dark text-xs mb-1">
                                    <?= e(tmpl_name('investigative')) ?>
                                </div>
                                <div class="text-muted" style="font-size: 0.68rem; line-height: 1.3;">
                                    <?= __('blueprint_investigative_desc') ?>
                                </div>
                            </div>

                            <!-- Card 3: Opinion -->
                            <div class="blueprint-picker-card <?= (isset($article['template_type']) && $article['template_type'] === 'opinion') ? 'active' : '' ?>"
                                data-val="opinion">
                                <div class="blueprint-check">&check;</div>
                                <div class="blueprint-mini-wireframe wf-opinion">
                                    <div class="wf-avatar me-1"></div>
                                    <div class="wf-bar wf-text"></div>
                                </div>
                                <div class="fw-bold text-dark text-xs mb-1">
                                    <?= e(tmpl_name('opinion')) ?>
                                </div>
                                <div class="text-muted" style="font-size: 0.68rem; line-height: 1.3;">
                                    <?= __('blueprint_opinion_desc') ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Selector -->
                    <div class="mb-3">
                        <label for="category_id"
                            class="form-label fw-semibold small text-dark"><?= __('news_category') ?> <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value=""><?= __('select_category_option') ?></option>
                            <?php foreach ($categories as $cat) { ?>
                                <option value="<?= (int) $cat['id'] ?>" <?= (isset($article['category_id']) && (int) $article['category_id'] === (int) $cat['id']) ? 'selected' : '' ?>>
                                    <?= e(cat_name($cat['name'])) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Author Selector -->
                    <div class="mb-3">
                        <label for="author_id"
                            class="form-label fw-semibold small text-dark"><?= __('assigned_author') ?></label>
                        <select class="form-select" id="author_id" name="author_id">
                            <?php foreach ($authors as $aut) { ?>
                                <option value="<?= (int) $aut['id'] ?>" <?= (isset($article['author_id']) && (int) $article['author_id'] === (int) $aut['id']) ? 'selected' : ($aut['id'] === $currentUser['id'] ? 'selected' : '') ?>>
                                    <?= e($aut['username']) ?> (<?= e($aut['role']) ?>)
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Is Breaking Toggle -->
                    <div class="form-check form-switch mt-3 pt-2 border-top">
                        <input class="form-check-input" type="checkbox" id="is_breaking" name="is_breaking" value="1"
                            <?= (!empty($article['is_breaking'])) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold text-danger" for="is_breaking">
                            <?= __('flag_breaking') ?>
                        </label>
                    </div>

                </div>

                <!-- Featured Cover Image Dropzone Card -->
                <div class="card border-0 shadow-sm p-4">
                    <h6 class="fw-bold editorial-title text-dark mb-3"><?= __('featured_image_label') ?></h6>

                    <?php if ($isEdit && !empty($article['featured_image'])) { ?>
                        <div class="mb-3 text-center">
                            <img src="<?= e($article['featured_image']) ?>" class="img-fluid rounded border shadow-sm"
                                style="max-height: 180px;" alt="Cover">
                            <div class="text-xs text-muted mt-1"><?= __('current_image') ?></div>
                        </div>
                    <?php } ?>

                    <div class="mb-2">
                        <label for="featured_image"
                            class="form-label fw-semibold small text-dark"><?= __('upload_cover') ?></label>
                        <input type="file" class="form-control form-control-sm" id="featured_image"
                            name="featured_image" accept="image/jpeg,image/png,image/webp">
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
    document.addEventListener('DOMContentLoaded', function () {
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
            titleInput.addEventListener('input', function () {
                if (!slugInput.dataset.userEdited) {
                    slugInput.value = slugify(titleInput.value);
                }
            });

            slugInput.addEventListener('input', function () {
                slugInput.dataset.userEdited = "true";
            });

            if (autoBtn) {
                autoBtn.addEventListener('click', function () {
                    slugInput.value = slugify(titleInput.value);
                    delete slugInput.dataset.userEdited;
                });
            }
        }

        // Dynamic Visual Blueprint Card Picker
        const hiddenTemplateInput = document.getElementById('template_type');
        const pickerCards = document.querySelectorAll('.blueprint-picker-card');

        pickerCards.forEach(card => {
            card.addEventListener('click', function () {
                pickerCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                if (hiddenTemplateInput) {
                    hiddenTemplateInput.value = this.getAttribute('data-val');
                }
            });
        });

        // Initialize Quill Rich Text Editor
        if (document.getElementById('quillEditor')) {
            const quill = new Quill('#quillEditor', {
                theme: 'snow',
                placeholder: '<?= addslashes(__('quill_placeholder')) ?>',
                modules: {
                    toolbar: {
                        container: [
                            [{ 'header': [1, 2, 3, 4, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            ['blockquote', 'code-block'],
                            ['link', 'image', 'video'],
                            ['clean']
                        ],
                        handlers: {
                            image: function () {
                                selectLocalImage(quill);
                            },
                            video: function () {
                                const url = prompt('<?= addslashes(__('js_video_prompt')) ?>');

                                if (url) {
                                    const embedUrl = formatVideoUrl(url);
                                    const range = quill.getSelection(true);
                                    const idx = (range && range.index !== undefined) ? range.index : quill.getLength();
                                    quill.insertEmbed(idx, 'video', embedUrl);
                                    quill.setSelection(idx + 1);
                                }
                            }
                        }
                    }
                }
            });

            window.quill = quill;

            function formatVideoUrl(url) {
                url = url.trim();
                const ytMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_\-]+)/i);
                if (ytMatch) {
                    return 'https://www.youtube.com/embed/' + ytMatch[1];
                }
                const vmMatch = url.match(/vimeo\.com\/(?:video\/)?([0-9]+)/i);
                if (vmMatch) {
                    return 'https://player.vimeo.com/video/' + vmMatch[1];
                }
                return url;
            }

            function selectLocalImage(quillInstance) {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/jpeg,image/png,image/webp');
                input.click();

                input.onchange = function () {
                    const file = input.files[0];
                    if (file) {
                        const formData = new FormData();
                        formData.append('image', file);

                        const range = quillInstance.getSelection(true);

                        fetch('<?= url("admin/actions/upload-image.php") ?>', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.text())
                            .then(text => {
                                let data;
                                try {
                                    data = JSON.parse(text);
                                } catch (e) {
                                    throw new Error('Server error response: ' + text.replace(/<[^>]*>?/gm, '').trim().substring(0, 200));
                                }
                                if (data.url) {
                                    const idx = (range && range.index !== undefined) ? range.index : quillInstance.getLength();
                                    quillInstance.insertEmbed(idx, 'image', data.url);
                                    quillInstance.setSelection(idx + 1);
                                } else {
                                    alert(data.error || 'Failed to upload image.');
                                }
                            })
                            .catch(err => {
                                alert('Upload failed: ' + err.message);
                            });
                    }
                };
            }

            // Media Tag Quick Insert Handler (Images & Videos)
            document.querySelectorAll('.insert-tag-btn, .insert-img-btn').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const tag = this.getAttribute('data-tag');
                    if (window.quill) {
                        const range = window.quill.getSelection(true);
                        const idx = (range && range.index !== undefined) ? range.index : window.quill.getLength();
                        window.quill.insertText(idx, '\n' + tag + '\n');
                        window.quill.setSelection(idx + tag.length + 2);
                    }
                });
            });


            const articleForm = document.getElementById('articleForm');
            if (articleForm) {
                articleForm.addEventListener('submit', function () {
                    const contentInput = document.getElementById('content');
                    if (contentInput) {
                        contentInput.value = quill.root.innerHTML;
                    }
                });
            }
        }
    });
</script>