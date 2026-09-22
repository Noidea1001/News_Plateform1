<?php
/**
 * Template Blueprint 2: Single-Column Deep-Read Investigative Format
 * news-platform / templates / views / article-investigative.php
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
?>

<div class="template-investigative">

    <!-- Dark Hero Banner Section -->
    <div class="hero-banner border-bottom border-danger border-4">
        <div class="container max-width-900 text-center py-4">
            
            <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                <span class="badge bg-danger text-uppercase px-3 py-2 fs-6 tracking-wider">
                    <i class="bi bi-shield-check me-1"></i> <?= __('special_report') ?>
                </span>
                <span class="badge bg-secondary text-uppercase px-2 py-2">
                    <?= e(cat_name($article['category_name'])) ?>
                </span>
            </div>

            <h1 class="display-4 fw-bold editorial-title text-white mb-4 lh-tight tracking-tight">
                <?= e(article_title($article['title'])) ?>
            </h1>

            <p class="lead text-light opacity-90 fs-4 fw-normal mx-auto mb-4" style="max-width: 760px;">
                <?= e($article['summary']) ?>
            </p>

            <div class="d-flex align-items-center justify-content-center gap-3 text-secondary small pt-3 border-top border-secondary border-opacity-50">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 36px; height: 36px;">
                        <?= strtoupper(substr($article['author_name'], 0, 1)) ?>
                    </div>
                    <span class="text-white fw-semibold"><?= e($article['author_name']) ?></span>
                </div>
                <span>&bull;</span>
                <span><i class="bi bi-calendar3 me-1 text-danger"></i> <?= \App\Core\TemplateEngine::formatDate($article['published_at']) ?></span>
                <span>&bull;</span>
                <span><i class="bi bi-eye me-1 text-warning"></i> <?= __('total_readers', ['count' => number_format((int)$article['views_count'])]) ?></span>
            </div>

        </div>
    </div>

    <!-- Centered Deep-Read Article Body -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                <!-- Featured High-Res Cover Image -->
                <?php if (!empty($article['featured_image'])) { ?>
                    <div class="mb-5 rounded-4 overflow-hidden shadow-lg">
                        <img src="<?= e($article['featured_image']) ?>" class="w-100 h-auto" alt="<?= e($article['title']) ?>">
                        <div class="bg-light p-2 text-center text-muted small border-top">
                            <i class="bi bi-camera me-1"></i> <?= __('featured_evidence') ?>
                        </div>
                    </div>
                <?php } ?>

                <!-- Video Embed Player -->
                <?php if (!empty($article['video_embed_url']) && !\App\Core\Sanitizer::hasVideoInContent($article['content'] ?? null, $article['video_embed_url'])) { ?>
                    <div class="my-4">
                        <h6 class="fw-bold text-dark mb-2.5"><?= __('video_doc_title') ?></h6>
                        <div class="video-container shadow-md rounded-4 overflow-hidden border bg-black">
                            <iframe src="<?= e($article['video_embed_url']) ?>" allowfullscreen></iframe>
                        </div>
                    </div>
                <?php } ?>

                <!-- Pull Quote Callout Box -->
                <div class="investigative-callout shadow-sm rounded-end">
                    "This investigation relies on verified primary source documents and rigorous data cross-referencing."
                </div>

                <!-- Photo Gallery Carousel -->
                <?php
                $galleryUrls = \App\Core\Sanitizer::getUnusedGalleryImages($article['gallery_images'] ?? null, $article['content'] ?? null);
                ?>
                <?php if (!empty($galleryUrls)) { ?>
                    <div class="my-4">
                        <h6 class="fw-bold text-dark mb-3">
                            <span><?= __('photo_gallery_title') ?></span>
                            <span class="badge bg-light border text-muted fw-normal ms-2"><?= count($galleryUrls) ?></span>
                        </h6>
                        <div id="galleryCarouselInv" class="carousel slide shadow-lg rounded-4 overflow-hidden" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <?php foreach ($galleryUrls as $gIdx => $gUrl) { ?>
                                    <button type="button" data-bs-target="#galleryCarouselInv" data-bs-slide-to="<?= $gIdx ?>"
                                        class="<?= $gIdx === 0 ? 'active' : '' ?>" aria-label="Slide <?= $gIdx + 1 ?>"></button>
                                <?php } ?>
                            </div>
                            <div class="carousel-inner">
                                <?php foreach ($galleryUrls as $gIdx => $gUrl) { ?>
                                    <div class="carousel-item <?= $gIdx === 0 ? 'active' : '' ?>">
                                        <img src="<?= e($gUrl) ?>" class="d-block w-100 object-fit-cover"
                                            style="max-height: 460px;" alt="Evidence photo <?= $gIdx + 1 ?>"
                                            loading="lazy" onerror="this.parentElement.style.display='none'">
                                    </div>
                                <?php } ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarouselInv" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarouselInv" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                <?php } ?>

                <!-- Audio Podcast Player -->
                <?php if (!empty($article['audio_embed_url'])) { ?>
                    <div class="my-4">
                        <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                            <i class="bi bi-headphones text-primary fs-5"></i> <?= __('audio_report_podcast') ?>
                        </h6>
                        <?php $audioUrl = e($article['audio_embed_url']); ?>
                        <?php if (str_contains($audioUrl, 'spotify.com') || str_contains($audioUrl, 'soundcloud.com') || str_contains($audioUrl, 'anchor.fm')) { ?>
                            <div class="shadow-sm rounded-4 overflow-hidden border">
                                <iframe src="<?= $audioUrl ?>" width="100%" height="152" frameborder="0"
                                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                                    loading="lazy"></iframe>
                            </div>
                        <?php } else { ?>
                            <div class="bg-light rounded-4 p-3 border shadow-sm">
                                <audio controls class="w-100" preload="metadata">
                                    <source src="<?= $audioUrl ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <!-- Reading Toolbar (Font Size Controls A-/A+/Reset, Read Time, Copy Link, Progress Bar) -->
                <?php include __DIR__ . '/../components/reading-toolbar.php'; ?>

                <!-- Deep Read Body Content with Drop Cap -->
                <div class="article-body mb-4 <?= !empty($article['has_drop_cap']) ? 'has-drop-cap' : '' ?>">
                    <?= \App\Core\Sanitizer::parseArticleMedia($article['content'], $article['gallery_images'] ?? null, $article['featured_image'] ?? null, $article['video_embed_url'] ?? null) ?>
                </div>

                <!-- Social Share Buttons -->
                <div class="mb-4">
                    <?php include __DIR__ . '/../components/share-buttons.php'; ?>
                </div>

                <!-- Verified Primary Source Citation Component -->
                <?php 
                $referenceUrl = $article['reference_url'] ?? null;
                $referenceSourceName = $article['reference_source_name'] ?? null;
                include __DIR__ . '/../components/citation-box.php'; 
                ?>


                <!-- Reader Feed Callout Footer -->
                <div class="card bg-brand-navy text-white my-5 border-0 shadow-lg p-4 rounded-4 text-center">
                    <h4 class="editorial-title text-white mb-2"><?= __('support_investigative') ?></h4>
                    <p class="small text-secondary mb-3 max-width-600 mx-auto">
                        <?= __('support_desc') ?>
                    </p>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-danger px-4 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#subscribeModal">
                            <i class="bi bi-bell-fill"></i> <?= __('get_feed_cta') ?>
                        </button>
                    </div>
                </div>

                <!-- Related Investigative Content -->
                <?php if (!empty($relatedArticles)) { ?>
                <div class="pt-4 border-top">
                    <h4 class="editorial-title fw-bold mb-4 text-dark"><?= __('further_investigations') ?></h4>
                    <div class="list-group list-group-flush shadow-sm rounded-4 border overflow-hidden">
                        <?php foreach ($relatedArticles as $rItem) { ?>
                            <a href="<?= url('article.php?slug=' . urlencode($rItem['slug'])) ?>" class="list-group-item list-group-item-action py-3 px-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-danger text-uppercase font-monospace text-xs mb-1"><?= e(cat_name($rItem['category_name'])) ?></span>
                                    <h6 class="mb-0 fw-bold text-dark"><?= e(article_title($rItem['title'])) ?></h6>
                                </div>
                                <i class="bi bi-arrow-right text-danger fs-5"></i>
                            </a>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>
    </div>

</div>
