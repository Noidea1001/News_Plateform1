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
            <button type="button" id="btnLivePreviewArticle"
                class="btn btn-outline-danger px-3.5 py-2 fw-semibold rounded-3 text-nowrap shadow-sm d-inline-flex align-items-center gap-1.5">
                <i class="bi bi-eye-fill"></i>
                <span><?= __('live_preview') ?? 'Live Preview' ?></span>
            </button>
            <button type="submit" form="articleForm" id="btnSaveArticleSubmit"
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

                    <!-- Article Title: 2 Separate Fields (KH & EN) -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="form-label fw-bold text-dark mb-0"><?= __('article_title') ?> <span class="text-danger">*</span></span>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-2xs fw-bold px-2 py-0.5">
                                <?= __('dual_language_support_badge') ?>
                            </span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="title_kh" class="form-label small fw-bold text-dark mb-1 d-flex align-items-center gap-1.5">
                                    <span class="badge bg-danger text-white text-2xs px-1.5 py-0.5 rounded">KH</span>
                                    <span><?= __('article_title_kh') ?> <span class="text-danger">*</span></span>
                                </label>
                                <input type="text" class="form-control form-control-lg fs-6" id="title_kh" name="title_kh"
                                    value="<?= e($article['title_kh'] ?? $article['title'] ?? '') ?>" placeholder="<?= e(__('title_kh_placeholder')) ?>"
                                    required>
                                <div class="text-muted text-2xs mt-1">
                                    ឧទាហរណ៍៖ កម្ពុជាសម្រេចបានសមិទ្ធផលថ្មីក្នុងវិស័យបច្ចេកវិទ្យា
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="title_en" class="form-label small fw-bold text-dark mb-1 d-flex align-items-center gap-1.5">
                                    <span class="badge bg-primary text-white text-2xs px-1.5 py-0.5 rounded">EN</span>
                                    <span><?= __('article_title_en') ?> <span class="text-danger">*</span></span>
                                </label>
                                <input type="text" class="form-control form-control-lg fs-6" id="title_en" name="title_en"
                                    value="<?= e($article['title_en'] ?? $article['title'] ?? '') ?>" placeholder="<?= e(__('title_en_placeholder')) ?>">
                                <div class="text-muted text-2xs mt-1">
                                    e.g. Cambodia achieves historic milestones in national technology sector
                                </div>
                            </div>
                        </div>
                        <!-- Hidden composite title for backwards compatibility -->
                        <input type="hidden" id="title" name="title" value="<?= e($article['title'] ?? '') ?>">
                    </div>

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

                    <!-- Article Summary: 2 Separate Fields (KH & EN) -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="form-label fw-bold text-dark mb-0"><?= __('summary_label') ?></span>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-2xs fw-bold px-2 py-0.5">
                                <?= __('dual_language_support_badge') ?>
                            </span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="summary_kh" class="form-label small fw-bold text-dark mb-1 d-flex align-items-center gap-1.5">
                                    <span class="badge bg-danger text-white text-2xs px-1.5 py-0.5 rounded">KH</span>
                                    <span><?= __('summary_kh_label') ?></span>
                                </label>
                                <textarea class="form-control" id="summary_kh" name="summary_kh" rows="3"
                                    placeholder="<?= e(__('summary_kh_placeholder')) ?>"><?= e($article['summary_kh'] ?? $article['summary'] ?? '') ?></textarea>
                                <div class="text-muted text-2xs mt-1">សេចក្ដីសង្ខេបខ្លីសម្រាប់ទំព័រដើម និងការស្វែងរក</div>
                            </div>
                            <div class="col-md-6">
                                <label for="summary_en" class="form-label small fw-bold text-dark mb-1 d-flex align-items-center gap-1.5">
                                    <span class="badge bg-primary text-white text-2xs px-1.5 py-0.5 rounded">EN</span>
                                    <span><?= __('summary_en_label') ?></span>
                                </label>
                                <textarea class="form-control" id="summary_en" name="summary_en" rows="3"
                                    placeholder="<?= e(__('summary_en_placeholder')) ?>"><?= e($article['summary_en'] ?? $article['summary'] ?? '') ?></textarea>
                                <div class="text-muted text-2xs mt-1">Short executive teaser for home feeds and metadata</div>
                            </div>
                        </div>
                        <!-- Hidden composite summary for backwards compatibility -->
                        <textarea class="d-none" id="summary" name="summary"><?= e($article['summary'] ?? '') ?></textarea>
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

                    <!-- Main Content Body (Bilingual Dual Quill WYSIWYG Integration) -->
                    <div class="mb-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label fw-bold text-dark mb-0"><?= __('article_body_label') ?> <span class="text-danger">*</span></label>
                                <ul class="nav nav-pills bg-light p-1 rounded-3 border" id="editorLangTabs" role="tablist" style="font-size: 0.85rem;">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active py-1 px-3 fw-bold rounded-2 text-danger" id="tab-kh-btn" data-bs-toggle="pill" data-bs-target="#tab-editor-kh" type="button" role="tab">
                                            🇰🇭 <?= __('content_kh_label') ?>
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link py-1 px-3 fw-bold rounded-2 text-primary" id="tab-en-btn" data-bs-toggle="pill" data-bs-target="#tab-editor-en" type="button" role="tab">
                                            🇬🇧 <?= __('content_en_label') ?>
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <!-- Manual Drop-Cap Toggle Switch -->
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

                        <!-- Tab Panes for KH and EN Editors -->
                        <div class="tab-content" id="editorLangTabsContent">
                            <!-- Khmer Editor Pane -->
                            <div class="tab-pane fade show active" id="tab-editor-kh" role="tabpanel">
                                <div class="bg-light px-3 py-1.5 border border-bottom-0 rounded-top text-xs fw-semibold text-danger d-flex align-items-center justify-content-between">
                                    <span><i class="bi bi-translate me-1"></i> អត្ថបទភាសាខ្មែរ (Khmer Article Content)</span>
                                    <span class="badge bg-danger text-white text-2xs">Primary</span>
                                </div>
                                <div id="quillEditorKh" class="bg-white rounded-bottom" style="min-height: 280px; font-size: 1.05rem;">
                                    <?= $article['content_kh'] ?? $article['content'] ?? '' ?>
                                </div>
                                <textarea class="d-none" id="content_kh" name="content_kh"><?= e($article['content_kh'] ?? $article['content'] ?? '') ?></textarea>
                            </div>

                            <!-- English Editor Pane -->
                            <div class="tab-pane fade" id="tab-editor-en" role="tabpanel">
                                <div class="bg-light px-3 py-1.5 border border-bottom-0 rounded-top text-xs fw-semibold text-primary d-flex align-items-center justify-content-between">
                                    <span><i class="bi bi-translate me-1"></i> English Body Content (English Article Content)</span>
                                    <span class="badge bg-primary text-white text-2xs">English Edition</span>
                                </div>
                                <div id="quillEditorEn" class="bg-white rounded-bottom" style="min-height: 280px; font-size: 1.05rem;">
                                    <?= $article['content_en'] ?? $article['content'] ?? '' ?>
                                </div>
                                <textarea class="d-none" id="content_en" name="content_en"><?= e($article['content_en'] ?? $article['content'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- Hidden Form Textarea Syncing with Quill for backwards compatibility -->
                        <textarea class="d-none" id="content" name="content"><?= e($article['content'] ?? '') ?></textarea>
                        <div class="text-muted text-xs mt-2">
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
                            <span><?= __('template_blueprint') ?> <span
                                     class="text-danger">*</span></span>
                        </label>
                        <input type="hidden" id="template_type" name="template_type"
                            value="<?= e($article['template_type'] ?? 'standard') ?>">

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
                            <img src="<?= e(image_url($article['featured_image'])) ?>" class="img-fluid rounded border shadow-sm"
                                style="max-height: 180px; width: auto;" alt="Cover Preview" id="featuredImagePreview">
                            <div class="text-xs text-muted mt-1"><?= __('current_image') ?></div>
                        </div>
                    <?php } ?>

                    <div class="mb-3">
                        <label for="featured_image_url" class="form-label fw-semibold small text-dark">
                            <i class="bi bi-link-45deg me-1 text-danger"></i><?= __('featured_image_url_label') ?>
                        </label>
                        <input type="url" class="form-control form-control-sm font-monospace" id="featured_image_url"
                            name="featured_image_url" value="<?= e($article['featured_image'] ?? '') ?>"
                            placeholder="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe">
                        <div class="text-muted text-xs mt-1">
                            <?= __('featured_image_url_hint') ?>
                        </div>
                    </div>

                    <div class="text-center text-muted small my-2 fw-semibold">— <?= __('or_upload_file') ?> —</div>

                    <div class="mb-2">
                        <label for="featured_image"
                            class="form-label fw-semibold small text-dark"><i class="bi bi-cloud-arrow-up me-1 text-primary"></i><?= __('upload_cover') ?></label>
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

<!-- =========================================================================
     Professional Article Live Preview Modal
     ========================================================================= -->
<div class="modal fade" id="articlePreviewModal" tabindex="-1" aria-labelledby="articlePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-lg-down">
        <div class="modal-content border-0 shadow-lg">
            
            <!-- Modal Header with Viewport & Language Controls -->
            <div class="modal-header py-2.5 px-3 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger rounded-1 px-2.5 py-1 text-uppercase fw-bold" style="font-size:0.72rem; letter-spacing:0.04em;">
                        <i class="bi bi-eye-fill me-1"></i><?= __('live_preview') ?? 'Live Preview' ?>
                    </span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-0.5 text-3xs fw-bold" id="previewTemplateBadge">
                        Standard Blueprint
                    </span>
                </div>

                <!-- Responsive Device Viewport Switcher -->
                <div class="d-flex align-items-center gap-1 bg-white p-1 rounded-2 border shadow-2xs">
                    <button type="button" class="btn btn-xs btn-light px-2.5 py-1 text-3xs fw-semibold active device-switch-btn" data-device="desktop" title="Desktop View (100%)">
                        <i class="bi bi-display me-1 text-secondary"></i>Desktop
                    </button>
                    <button type="button" class="btn btn-xs btn-light px-2.5 py-1 text-3xs fw-semibold device-switch-btn" data-device="tablet" title="Tablet View (768px)">
                        <i class="bi bi-tablet me-1 text-secondary"></i>Tablet
                    </button>
                    <button type="button" class="btn btn-xs btn-light px-2.5 py-1 text-3xs fw-semibold device-switch-btn" data-device="mobile" title="Mobile View (390px)">
                        <i class="bi bi-phone me-1 text-secondary"></i>Mobile
                    </button>
                </div>

                <!-- Preview Language Switcher & Close Button -->
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-xs btn-outline-danger active preview-lang-btn" data-lang="kh" style="font-size:0.75rem; padding:2px 10px;">
                            ខ្មែរ (KH)
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-danger preview-lang-btn" data-lang="en" style="font-size:0.75rem; padding:2px 10px;">
                            EN
                        </button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <!-- Modal Body with Device Simulation Frame -->
            <div class="modal-body p-0 bg-light-subtle d-flex justify-content-center overflow-auto" style="min-height: 520px; background:#f1f5f9;">
                <div id="previewFrameWrapper" style="width: 100%; max-width: 100%; transition: all 0.25s ease; background: #ffffff; min-height: 520px;">
                    <div id="previewRenderContainer" class="p-3 p-md-4">
                        <!-- Dynamic rendered article inserted via JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer py-2.5 px-3 bg-white border-top d-flex align-items-center justify-content-between">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-2 fw-semibold" data-bs-dismiss="modal">
                    &larr; <?= __('back_to_editor') ?? 'Back to Editing' ?>
                </button>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted text-3xs d-none d-md-inline">
                        <i class="bi bi-info-circle me-1"></i><?= __('preview_disclaimer') ?? 'Simulates live reader viewport and blueprint styling' ?>
                    </span>
                    <button type="button" id="btnPreviewPublishSubmit" class="btn btn-danger btn-sm px-3.5 py-1.5 rounded-2 fw-bold shadow-2xs">
                        <?= $isEdit ? __('update_article') : __('publish_save') ?>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Real-Time JavaScript Slug Generator & Bilingual Quill Setup -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const titleKhInput = document.getElementById('title_kh');
        const titleEnInput = document.getElementById('title_en');
        const compositeTitleInput = document.getElementById('title');
        const summaryKhInput = document.getElementById('summary_kh');
        const summaryEnInput = document.getElementById('summary_en');
        const compositeSummaryInput = document.getElementById('summary');
        const contentKhInput = document.getElementById('content_kh');
        const contentEnInput = document.getElementById('content_en');
        const compositeContentInput = document.getElementById('content');
        const slugInput = document.getElementById('slug');
        const autoBtn = document.getElementById('btnAutoSlug');

        function syncTitles() {
            const kh = titleKhInput ? titleKhInput.value.trim() : '';
            const en = titleEnInput ? titleEnInput.value.trim() : '';
            if (compositeTitleInput) {
                compositeTitleInput.value = kh || en;
            }
        }

        function syncSummaries() {
            const kh = summaryKhInput ? summaryKhInput.value.trim() : '';
            const en = summaryEnInput ? summaryEnInput.value.trim() : '';
            if (compositeSummaryInput) {
                compositeSummaryInput.value = kh || en;
            }
        }

        function autoDetectDualLanguage(inputVal) {
            const str = inputVal.trim();
            const hasKhmer = /[\u1780-\u17FF]/.test(str);
            const hasLatin = /[a-zA-Z]/.test(str);
            if (!hasKhmer || !hasLatin) return null;

            const delims = [' / ', ' | ', ' - ', '---', '///', '|||'];
            for (const d of delims) {
                if (str.includes(d)) {
                    const parts = str.split(d);
                    const p1 = parts[0].trim();
                    const p2 = parts[1].trim();
                    const p1Kh = /[\u1780-\u17FF]/.test(p1);
                    const p2En = /[a-zA-Z]/.test(p2);
                    if (p1Kh && p2En) return { kh: p1, en: p2 };
                    const p2Kh = /[\u1780-\u17FF]/.test(p2);
                    const p1En = /[a-zA-Z]/.test(p1);
                    if (p2Kh && p1En) return { kh: p2, en: p1 };
                }
            }
            const parenMatch = str.match(/^(.+?)\s*[\(\[](.+?)[\)\]]$/);
            if (parenMatch) {
                const p1 = parenMatch[1].trim();
                const p2 = parenMatch[2].trim();
                const p1Kh = /[\u1780-\u17FF]/.test(p1);
                const p2Kh = /[\u1780-\u17FF]/.test(p2);
                if (p1Kh && !p2Kh) return { kh: p1, en: p2 };
                if (!p1Kh && p2Kh) return { kh: p2, en: p1 };
            }
            return null;
        }

        if (titleKhInput) {
            titleKhInput.addEventListener('input', function() {
                const detected = autoDetectDualLanguage(this.value);
                if (detected) {
                    this.value = detected.kh;
                    if (titleEnInput && (!titleEnInput.value || !titleEnInput.value.trim())) {
                        titleEnInput.value = detected.en;
                        titleEnInput.dispatchEvent(new Event('input'));
                    }
                }
                syncTitles();
            });
        }
        if (titleEnInput) titleEnInput.addEventListener('input', syncTitles);
        if (summaryKhInput) {
            summaryKhInput.addEventListener('input', function() {
                const detected = autoDetectDualLanguage(this.value);
                if (detected) {
                    this.value = detected.kh;
                    if (summaryEnInput && (!summaryEnInput.value || !summaryEnInput.value.trim())) {
                        summaryEnInput.value = detected.en;
                        summaryEnInput.dispatchEvent(new Event('input'));
                    }
                }
                syncSummaries();
            });
        }
        if (summaryEnInput) summaryEnInput.addEventListener('input', syncSummaries);

        function slugify(text) {
            return text.toString().toLowerCase().trim()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text
        }

        if (titleEnInput && slugInput) {
            titleEnInput.addEventListener('input', function () {
                if (!slugInput.dataset.userEdited) {
                    const slug = slugify(titleEnInput.value);
                    if (slug) slugInput.value = slug;
                }
            });
        }

        if (slugInput) {
            slugInput.addEventListener('input', function () {
                slugInput.dataset.userEdited = "true";
            });
        }

        if (autoBtn && slugInput) {
            autoBtn.addEventListener('click', function () {
                const en = titleEnInput ? titleEnInput.value.trim() : '';
                const kh = titleKhInput ? titleKhInput.value.trim() : '';
                const source = en || kh;
                const slug = slugify(source);
                slugInput.value = slug || ('article-' + Date.now());
                delete slugInput.dataset.userEdited;
            });
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

        function createQuillInstance(containerId, placeholderText) {
            const el = document.getElementById(containerId);
            if (!el) return null;

            return new Quill('#' + containerId, {
                theme: 'snow',
                placeholder: placeholderText,
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
                                selectLocalImage(window.activeQuill || this.quill);
                            },
                            video: function () {
                                const targetQuill = window.activeQuill || this.quill;
                                const url = prompt('<?= addslashes(__('js_video_prompt')) ?>');
                                if (url && targetQuill) {
                                    const embedUrl = formatVideoUrl(url);
                                    const range = targetQuill.getSelection(true);
                                    const idx = (range && range.index !== undefined) ? range.index : targetQuill.getLength();
                                    targetQuill.insertEmbed(idx, 'video', embedUrl);
                                    targetQuill.setSelection(idx + 1);
                                }
                            }
                        }
                    }
                }
            });
        }

        function selectLocalImage(quillInstance) {
            if (!quillInstance) return;
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

        const quillKh = createQuillInstance('quillEditorKh', 'សរសេរខ្លឹមសារអត្ថបទនៅទីនេះជាភាសាខ្មែរ (Khmer Body)...');
        const quillEn = createQuillInstance('quillEditorEn', 'Write English article content here (English Body)...');

        window.quillKh = quillKh;
        window.quillEn = quillEn;
        window.activeQuill = quillKh || quillEn;

        // Tab Switching activeQuill
        const tabKhBtn = document.getElementById('tab-kh-btn');
        const tabEnBtn = document.getElementById('tab-en-btn');
        if (tabKhBtn && quillKh) {
            tabKhBtn.addEventListener('shown.bs.tab', function () {
                window.activeQuill = quillKh;
            });
        }
        if (tabEnBtn && quillEn) {
            tabEnBtn.addEventListener('shown.bs.tab', function () {
                window.activeQuill = quillEn;
            });
        }

        // Quick Insert Media Tags Handler into activeQuill
        document.querySelectorAll('.insert-tag-btn, .insert-img-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const tag = this.getAttribute('data-tag');
                const q = window.activeQuill || quillKh || quillEn;
                if (q) {
                    const range = q.getSelection(true);
                    const idx = (range && range.index !== undefined) ? range.index : q.getLength();
                    q.insertText(idx, '\n' + tag + '\n');
                    q.setSelection(idx + tag.length + 2);
                }
            });
        });

        function syncQuillContent() {
            if (quillKh && contentKhInput) {
                contentKhInput.value = quillKh.root.innerHTML;
            }
            if (quillEn && contentEnInput) {
                contentEnInput.value = quillEn.root.innerHTML;
            }
            if (compositeContentInput) {
                const khHtml = quillKh ? quillKh.root.innerHTML : '';
                const enHtml = quillEn ? quillEn.root.innerHTML : '';
                const khText = quillKh ? quillKh.getText().trim() : '';
                compositeContentInput.value = khText.length > 0 ? khHtml : enHtml;
            }
        }

        if (quillKh) {
            quillKh.on('text-change', syncQuillContent);
            if (contentKhInput && quillKh.root.innerHTML) {
                contentKhInput.value = quillKh.root.innerHTML;
            }
        }

        if (quillEn) {
            quillEn.on('text-change', syncQuillContent);
            if (contentEnInput && quillEn.root.innerHTML) {
                contentEnInput.value = quillEn.root.innerHTML;
            }
        }
        syncQuillContent();

        const articleForm = document.getElementById('articleForm');
        if (articleForm) {
            articleForm.addEventListener('submit', function (e) {
                syncTitles();
                syncSummaries();
                syncQuillContent();

                const titleKh = titleKhInput ? titleKhInput.value.trim() : '';
                const titleEn = titleEnInput ? titleEnInput.value.trim() : '';
                const catVal = document.getElementById('category_id') ? document.getElementById('category_id').value : '';
                
                const khText = quillKh ? quillKh.getText().trim() : '';
                const enText = quillEn ? quillEn.getText().trim() : '';
                const khMedia = quillKh && quillKh.root.querySelector('img, video, iframe') !== null;
                const enMedia = quillEn && quillEn.root.querySelector('img, video, iframe') !== null;
                
                const hasTitle = titleKh || titleEn;
                const hasContent = khText || enText || khMedia || enMedia;

                if (!hasTitle || !catVal || !hasContent) {
                    e.preventDefault();
                    if (typeof window.showAdminToast === 'function') {
                        window.showAdminToast('Title, category, and article body content are required.', 'error');
                    } else {
                        alert('Title, category, and article body content are required.');
                    }
                    return false;
                }
            });
        }

        const btnSaveSubmit = document.getElementById('btnSaveArticleSubmit');
        if (btnSaveSubmit && articleForm) {
            btnSaveSubmit.addEventListener('click', function() {
                syncTitles();
                syncSummaries();
                syncQuillContent();
            });
        }

        // =========================================================================
        // Professional Article Live Preview System
        // =========================================================================
        let currentPreviewLang = 'kh';
        let currentPreviewDevice = 'desktop';

        const btnLivePreview = document.getElementById('btnLivePreviewArticle');
        const previewModalEl = document.getElementById('articlePreviewModal');
        const previewFrameWrapper = document.getElementById('previewFrameWrapper');
        const previewRenderContainer = document.getElementById('previewRenderContainer');
        const previewTemplateBadge = document.getElementById('previewTemplateBadge');

        const authorMetadata = {
            name: <?= json_encode($currentUser['username'] ?? 'Editorial Author') ?>,
            role: <?= json_encode(ucfirst($currentUser['role'] ?? 'Reporter')) ?>,
            avatar: <?= json_encode(!empty($currentUser['avatar_url']) ? image_url($currentUser['avatar_url']) : '') ?>
        };

        function getPreviewFormData() {
            syncTitles();
            syncSummaries();
            syncQuillContent();

            const titleKh = titleKhInput ? titleKhInput.value.trim() : '';
            const titleEn = titleEnInput ? titleEnInput.value.trim() : '';
            const summaryKh = summaryKhInput ? summaryKhInput.value.trim() : '';
            const summaryEn = summaryEnInput ? summaryEnInput.value.trim() : '';
            const contentKh = quillKh ? quillKh.root.innerHTML : '';
            const contentEn = quillEn ? quillEn.root.innerHTML : '';
            const templateType = (document.getElementById('template_type') ? document.getElementById('template_type').value : 'standard') || 'standard';

            const catSelect = document.getElementById('category_id');
            const catName = catSelect && catSelect.selectedIndex >= 0 ? catSelect.options[catSelect.selectedIndex].text.trim() : 'News';

            const hasDropCap = document.getElementById('has_drop_cap') ? document.getElementById('has_drop_cap').checked : false;
            const audioEmbedUrl = document.getElementById('audio_embed_url') ? document.getElementById('audio_embed_url').value.trim() : '';
            const videoEmbedUrl = document.getElementById('video_embed_url') ? document.getElementById('video_embed_url').value.trim() : '';
            const refUrl = document.getElementById('reference_url') ? document.getElementById('reference_url').value.trim() : '';
            const refSource = document.getElementById('reference_source_name') ? document.getElementById('reference_source_name').value.trim() : '';

            // Featured Image
            let featuredImgSrc = '';
            const featInput = document.getElementById('featured_image');
            if (featInput && featInput.files && featInput.files[0]) {
                featuredImgSrc = URL.createObjectURL(featInput.files[0]);
            } else {
                const existingImg = document.getElementById('featuredImagePreview');
                if (existingImg && existingImg.src && !existingImg.src.includes('data:image/svg+xml')) {
                    featuredImgSrc = existingImg.src;
                }
            }

            // Word count / reading time
            const activeText = (currentPreviewLang === 'kh' ? (quillKh ? quillKh.getText() : '') : (quillEn ? quillEn.getText() : '')) || '';
            const wordCount = activeText.trim().split(/\s+/).filter(Boolean).length;
            const readingTimeMin = Math.max(1, Math.ceil(wordCount / 180));
            const readingTimeStr = currentPreviewLang === 'kh' ? `រយះពេលអាន ${readingTimeMin} នាទី` : `${readingTimeMin} min read`;

            return {
                titleKh, titleEn, summaryKh, summaryEn,
                contentKh, contentEn, templateType, catName,
                hasDropCap, audioEmbedUrl, videoEmbedUrl, refUrl, refSource,
                featuredImgSrc, readingTimeStr
            };
        }

        function renderArticlePreview() {
            if (!previewRenderContainer) return;

            const data = getPreviewFormData();
            const isKh = (currentPreviewLang === 'kh');
            const title = (isKh ? (data.titleKh || data.titleEn) : (data.titleEn || data.titleKh)) || (isKh ? 'ចំណងជើងអត្ថបទ' : 'Article Title');
            const summary = (isKh ? (data.summaryKh || data.summaryEn) : (data.summaryEn || data.summaryKh)) || '';
            let content = (isKh ? (data.contentKh || data.contentEn) : (data.contentEn || data.contentKh)) || (isKh ? '<p>មាតិកាអត្ថបទនឹងបង្ហាញនៅទីនេះ...</p>' : '<p>Article body will appear here...</p>');

            if (data.hasDropCap) {
                content = content.replace(/<p>/i, '<p class="has-drop-cap">');
            }

            if (previewTemplateBadge) {
                previewTemplateBadge.textContent = data.templateType.toUpperCase() + ' BLUEPRINT';
            }

            const todayStr = new Date().toLocaleDateString(isKh ? 'km-KH' : 'en-US', { year: 'numeric', month: 'long', day: 'numeric' });

            const authorAvatarHtml = authorMetadata.avatar 
                ? `<img src="${authorMetadata.avatar}" alt="${authorMetadata.name}" class="rounded-circle object-fit-cover flex-shrink-0" style="width:38px;height:38px;">`
                : `<div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width:38px;height:38px;background:#c8102e;font-size:0.9rem;">${authorMetadata.name.charAt(0).toUpperCase()}</div>`;

            let templateHtml = '';

            if (data.templateType === 'investigative') {
                templateHtml = `
                    <div class="investigative-preview">
                        <div class="bg-dark text-white p-4 p-md-5 rounded-3 mb-4" style="background:#0b1320 !important;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-danger text-uppercase px-2 py-0.5" style="font-size:0.7rem;">${escapeHtml(data.catName)}</span>
                                <span class="badge bg-white bg-opacity-10 text-white-50 text-3xs">INVESTIGATIVE REPORT</span>
                            </div>
                            <h1 class="fw-bold display-6 mb-3 text-white" style="line-height:1.25;">${escapeHtml(title)}</h1>
                            ${summary ? `<p class="fs-6 text-white-50 mb-4" style="line-height:1.6;">${escapeHtml(summary)}</p>` : ''}
                            <div class="d-flex align-items-center gap-2.5 text-white-50 text-xs border-top border-secondary border-opacity-25 pt-3">
                                <span class="text-white fw-semibold">${escapeHtml(authorMetadata.name)}</span>
                                <span>&middot;</span>
                                <span>${todayStr}</span>
                                <span>&middot;</span>
                                <span>${data.readingTimeStr}</span>
                            </div>
                        </div>

                        ${data.featuredImgSrc ? `
                            <div class="mb-4 text-center">
                                <img src="${data.featuredImgSrc}" class="img-fluid rounded-3 shadow-sm w-100 object-fit-cover" style="max-height:460px;" alt="Lead Cover">
                            </div>
                        ` : ''}

                        <div class="article-content" style="font-size:1.05rem; line-height:1.8; color:#1e293b;">
                            ${content}
                        </div>
                    </div>
                `;
            } else if (data.templateType === 'opinion') {
                templateHtml = `
                    <div class="opinion-preview">
                        <div class="p-3.5 p-md-4 mb-4 rounded-3 bg-white border shadow-2xs d-flex align-items-center gap-3.5">
                            <div class="flex-shrink-0">
                                ${authorMetadata.avatar 
                                    ? `<img src="${authorMetadata.avatar}" alt="${authorMetadata.name}" class="rounded-circle object-fit-cover border" style="width:68px;height:68px;">`
                                    : `<div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold fs-4" style="width:68px;height:68px;background:#0f172a;">${authorMetadata.name.charAt(0).toUpperCase()}</div>`
                                }
                            </div>
                            <div class="min-w-0">
                                <div class="text-danger fw-bold text-3xs text-uppercase mb-1 tracking-wider">OPINION & PERSPECTIVE &bull; ${escapeHtml(data.catName)}</div>
                                <h4 class="fw-bold text-dark mb-0.5">${escapeHtml(authorMetadata.name)}</h4>
                                <div class="text-muted text-xs">${escapeHtml(authorMetadata.role)}</div>
                            </div>
                        </div>

                        <h1 class="fw-bold mb-3 text-dark" style="font-size:1.85rem; line-height:1.3;">${escapeHtml(title)}</h1>
                        ${summary ? `<p class="lead text-secondary mb-3 fst-italic" style="font-size:1.05rem; line-height:1.6;">${escapeHtml(summary)}</p>` : ''}
                        
                        <div class="d-flex align-items-center gap-2 text-muted text-2xs py-2 mb-4 border-top border-bottom">
                            <span>${todayStr}</span>
                            <span>&middot;</span>
                            <span>${data.readingTimeStr}</span>
                        </div>

                        ${data.featuredImgSrc ? `
                            <div class="mb-4">
                                <img src="${data.featuredImgSrc}" class="img-fluid rounded-2 shadow-2xs w-100 object-fit-cover" style="max-height:420px;" alt="Cover">
                            </div>
                        ` : ''}

                        <div class="article-content" style="font-size:1.05rem; line-height:1.8; color:#1e293b;">
                            ${content}
                        </div>
                    </div>
                `;
            } else {
                templateHtml = `
                    <div class="standard-preview">
                        <div class="mb-2">
                            <span class="badge bg-danger text-uppercase px-2 py-1 fw-bold" style="font-size:0.68rem; letter-spacing:0.04em;">
                                ${escapeHtml(data.catName)}
                            </span>
                        </div>
                        <h1 class="fw-bold mb-3 text-dark" style="font-size:1.85rem; line-height:1.32; letter-spacing:-0.01em;">
                            ${escapeHtml(title)}
                        </h1>
                        ${summary ? `<p class="lead text-muted mb-3" style="font-size:1.02rem; line-height:1.6;">${escapeHtml(summary)}</p>` : ''}

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 py-2.5 my-3 border-top border-bottom">
                            <div class="d-flex align-items-center gap-2.5">
                                ${authorAvatarHtml}
                                <div>
                                    <div class="fw-bold text-dark text-xs" style="line-height:1.25;">${escapeHtml(authorMetadata.name)}</div>
                                    <div class="text-muted text-3xs">${escapeHtml(authorMetadata.role)}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted text-2xs">
                                <span>${todayStr}</span>
                                <span>&middot;</span>
                                <span>${data.readingTimeStr}</span>
                                <span>&middot;</span>
                                <span>0 views</span>
                            </div>
                        </div>

                        ${data.featuredImgSrc ? `
                            <div class="mb-4">
                                <img src="${data.featuredImgSrc}" class="img-fluid rounded-2 shadow-2xs w-100 object-fit-cover" style="max-height:440px;" alt="Cover">
                            </div>
                        ` : ''}

                        ${data.audioEmbedUrl ? `
                            <div class="p-3 mb-4 bg-light border rounded-2 d-flex align-items-center gap-3">
                                <i class="bi bi-volume-up-fill fs-4 text-danger"></i>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-bold text-xs text-dark mb-1">Audio Narration / Podcast</div>
                                    <audio controls class="w-100" style="height:32px;"><source src="${escapeHtml(data.audioEmbedUrl)}"></audio>
                                </div>
                            </div>
                        ` : ''}

                        <div class="article-content" style="font-size:1.02rem; line-height:1.8; color:#1e293b;">
                            ${content}
                        </div>

                        ${data.refUrl ? `
                            <div class="mt-4 pt-3 border-top text-muted text-xs d-flex align-items-center gap-2">
                                <i class="bi bi-link-45deg fs-6 text-danger"></i>
                                <span>Source: <strong>${escapeHtml(data.refSource || 'External Reference')}</strong> (<a href="${escapeHtml(data.refUrl)}" target="_blank" class="text-danger text-decoration-none">${escapeHtml(data.refUrl)}</a>)</span>
                            </div>
                        ` : ''}
                    </div>
                `;
            }

            previewRenderContainer.innerHTML = templateHtml;
        }

        // Live Preview Modal Trigger
        if (btnLivePreview) {
            btnLivePreview.addEventListener('click', function(e) {
                e.preventDefault();
                renderArticlePreview();
                if (previewModalEl && typeof bootstrap !== 'undefined') {
                    const bsModal = bootstrap.Modal.getOrCreateInstance(previewModalEl);
                    bsModal.show();
                }
            });
        }

        // Viewport Switcher Handlers
        document.querySelectorAll('.device-switch-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.device-switch-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const device = this.getAttribute('data-device');
                currentPreviewDevice = device;

                if (previewFrameWrapper) {
                    if (device === 'mobile') {
                        previewFrameWrapper.style.maxWidth = '390px';
                        previewFrameWrapper.style.boxShadow = '0 12px 40px rgba(0,0,0,0.18)';
                        previewFrameWrapper.style.border = '2px solid #334155';
                        previewFrameWrapper.style.borderRadius = '24px';
                        previewFrameWrapper.style.margin = '20px auto';
                    } else if (device === 'tablet') {
                        previewFrameWrapper.style.maxWidth = '768px';
                        previewFrameWrapper.style.boxShadow = '0 8px 30px rgba(0,0,0,0.12)';
                        previewFrameWrapper.style.border = '1px solid #cbd5e1';
                        previewFrameWrapper.style.borderRadius = '10px';
                        previewFrameWrapper.style.margin = '16px auto';
                    } else {
                        previewFrameWrapper.style.maxWidth = '100%';
                        previewFrameWrapper.style.boxShadow = 'none';
                        previewFrameWrapper.style.border = 'none';
                        previewFrameWrapper.style.borderRadius = '0';
                        previewFrameWrapper.style.margin = '0';
                    }
                }
            });
        });

        // Language Switcher Handlers
        document.querySelectorAll('.preview-lang-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.preview-lang-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentPreviewLang = this.getAttribute('data-lang') || 'kh';
                renderArticlePreview();
            });
        });

        // Publish from preview modal button
        const btnPreviewPublishSubmit = document.getElementById('btnPreviewPublishSubmit');
        if (btnPreviewPublishSubmit && articleForm) {
            btnPreviewPublishSubmit.addEventListener('click', function() {
                if (previewModalEl && typeof bootstrap !== 'undefined') {
                    const bsModal = bootstrap.Modal.getInstance(previewModalEl);
                    if (bsModal) bsModal.hide();
                }
                const btnSaveSubmit = document.getElementById('btnSaveArticleSubmit');
                if (btnSaveSubmit) {
                    btnSaveSubmit.click();
                } else {
                    articleForm.submit();
                }
            });
        }
    });
</script>