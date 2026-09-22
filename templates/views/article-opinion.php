<?php
/**
 * Template Blueprint 3: Columnist Profile Focus Opinion Layout (Warm Theme)
 * news-platform / templates / views / article-opinion.php
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
?>

<div class="template-opinion py-5">
    <div class="container">
        
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                <!-- Columnist Author Spotlight Header Card -->
                <div class="columnist-card p-4 mb-4 shadow-sm rounded-4">
                    <div class="d-flex align-items-center gap-4">
                        <div class="position-relative">
                            <?php if (!empty($article['author_avatar'])) { ?>
                                <img src="<?= e($article['author_avatar']) ?>" class="rounded-circle object-fit-cover border border-3 border-warning shadow" style="width: 80px; height: 80px;" alt="<?= e($article['author_name']) ?>">
                            <?php } else { ?>
                                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3 border border-3 border-white shadow" style="width: 80px; height: 80px;">
                                    <?= strtoupper(substr($article['author_name'], 0, 1)) ?>
                                </div>
                            <?php } ?>
                            <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-danger text-white border border-white">
                                <i class="bi bi-chat-quote-fill"></i>
                            </span>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-warning text-dark text-uppercase font-monospace text-xs fw-bold"><?= __('opinion_perspective') ?></span>
                                <span class="text-muted text-xs">&bull; <?= __('columnist_commentary') ?></span>
                            </div>
                            <h4 class="fw-bold editorial-title mb-1 text-dark"><?= e($article['author_name']) ?></h4>
                            <p class="small text-secondary mb-0">
                                <?= e($article['author_bio'] ?? 'Senior Contributing Editor and Columnist for NewsPlatform.') ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Article Title -->
                <h1 class="display-5 fw-bold editorial-title text-dark mb-4 text-center tracking-tight">
                    <?= e(article_title($article['title'])) ?>
                </h1>

                <!-- Summary Standfirst -->
                <p class="lead text-dark fst-italic text-center mb-4 px-3" style="font-family: var(--font-serif);">
                    &ldquo;<?= e($article['summary']) ?>&rdquo;
                </p>

                <div class="d-flex align-items-center justify-content-center gap-3 text-muted small mb-4 pb-3 border-bottom">
                    <span><i class="bi bi-calendar-event me-1 text-danger"></i> <?= __('published_date', ['date' => \App\Core\TemplateEngine::formatDate($article['published_at'])]) ?></span>
                    <span>&bull;</span>
                    <span><i class="bi bi-clock me-1 text-warning"></i> <?= $article['reading_time'] ?? __('min_read', ['min' => 5]) ?></span>
                    <span>&bull;</span>
                    <span><i class="bi bi-eye me-1 text-primary"></i> <?= __('total_readers', ['count' => number_format((int)$article['views_count'])]) ?></span>
                </div>

                <!-- Featured Cover Image -->
                <?php if (!empty($article['featured_image'])) { ?>
                    <div class="mb-5 rounded-4 overflow-hidden shadow-sm">
                        <img src="<?= e($article['featured_image']) ?>" class="w-100 h-auto" alt="<?= e($article['title']) ?>">
                    </div>
                <?php } ?>

                <!-- Stylized Opinion Pull Quote -->
                <div class="opinion-quote">
                    &ldquo;Groundbreaking progress demands human courage and serendipity over pure algorithmic aggregation.&rdquo;
                </div>

                <!-- Video Player -->
                <?php if (!empty($article['video_embed_url']) && !\App\Core\Sanitizer::hasVideoInContent($article['content'] ?? null, $article['video_embed_url'])) { ?>
                    <div class="my-4">
                        <h6 class="fw-bold text-dark mb-2.5"><?= __('video_doc_title') ?></h6>
                        <div class="video-container shadow-sm rounded-4 overflow-hidden border bg-black">
                            <iframe src="<?= e($article['video_embed_url']) ?>" allowfullscreen></iframe>
                        </div>
                    </div>
                <?php } ?>

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
                        <div id="galleryCarouselOp" class="carousel slide shadow-sm rounded-4 overflow-hidden" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <?php foreach ($galleryUrls as $gIdx => $gUrl) { ?>
                                    <button type="button" data-bs-target="#galleryCarouselOp" data-bs-slide-to="<?= $gIdx ?>"
                                        class="<?= $gIdx === 0 ? 'active' : '' ?>" aria-label="Slide <?= $gIdx + 1 ?>"></button>
                                <?php } ?>
                            </div>
                            <div class="carousel-inner">
                                <?php foreach ($galleryUrls as $gIdx => $gUrl) { ?>
                                    <div class="carousel-item <?= $gIdx === 0 ? 'active' : '' ?>">
                                        <img src="<?= e($gUrl) ?>" class="d-block w-100 object-fit-cover"
                                            style="max-height: 440px;" alt="Gallery photo <?= $gIdx + 1 ?>"
                                            loading="lazy" onerror="this.parentElement.style.display='none'">
                                    </div>
                                <?php } ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarouselOp" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarouselOp" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                <?php } ?>

                <!-- Audio Podcast Player -->
                <?php if (!empty($article['audio_embed_url'])) { ?>
                    <div class="my-4">
                        <h6 class="fw-bold text-dark mb-2.5 d-flex align-items-center gap-2">
                            <i class="bi bi-headphones text-primary fs-5"></i> Columnist Audio Commentary
                        </h6>
                        <?php $audioUrl = e($article['audio_embed_url']); ?>
                        <?php if (str_contains($audioUrl, 'spotify.com') || str_contains($audioUrl, 'soundcloud.com') || str_contains($audioUrl, 'anchor.fm')) { ?>
                            <div class="shadow-sm rounded-4 overflow-hidden border">
                                <iframe src="<?= $audioUrl ?>" width="100%" height="152" frameborder="0"
                                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                                    loading="lazy"></iframe>
                            </div>
                        <?php } else { ?>
                            <div class="bg-light rounded-4 p-3.5 border shadow-sm">
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

                <!-- Article Content -->
                <div class="article-body mb-4 <?= !empty($article['has_drop_cap']) ? 'has-drop-cap' : '' ?>">
                    <?= \App\Core\Sanitizer::parseArticleMedia($article['content'], $article['gallery_images'] ?? null, $article['featured_image'] ?? null, $article['video_embed_url'] ?? null) ?>
                </div>

                <!-- Social Share Buttons -->
                <div class="mb-4">
                    <?php include __DIR__ . '/../components/share-buttons.php'; ?>
                </div>

                <!-- Primary Citation Source Box -->
                <?php 
                $referenceUrl = $article['reference_url'] ?? null;
                $referenceSourceName = $article['reference_source_name'] ?? null;
                include __DIR__ . '/../components/citation-box.php'; 
                ?>


                <!-- Columnist Footer Profile Callout -->
                <div class="columnist-card p-4 my-5 shadow-sm rounded-4 text-center">
                    <h5 class="editorial-title fw-bold text-dark mb-2"><?= str_replace(':name', e($article['author_name']), __('about_columnist')) ?></h5>
                    <p class="small text-secondary mb-3 max-width-600 mx-auto">
                        <?= e($article['author_bio'] ?? 'Writes regularly on technology policy, ethics, and cultural shifts.') ?>
                    </p>
                    <button type="button" class="btn btn-outline-danger btn-sm px-4 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#subscribeModal">
                        <i class="bi bi-bell-fill me-1"></i> <?= str_replace(':name', e($article['author_name']), __('subscribe_columnist_feed')) ?>
                    </button>
                </div>

                <!-- Related Opinion Columns -->
                <?php if (!empty($relatedArticles)) { ?>
                <div class="pt-4 border-top">
                    <h4 class="editorial-title fw-bold mb-4 text-dark text-center"><?= __('more_perspectives') ?></h4>
                    <div class="row g-3">
                        <?php foreach ($relatedArticles as $rItem) { ?>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift bg-white">
                                    <div class="card-body p-3.5">
                                        <span class="badge bg-warning text-dark mb-2 text-xs fw-bold">Opinion</span>
                                        <h6 class="fw-bold text-dark line-clamp-2">
                                            <a href="<?= url('article.php?slug=' . urlencode($rItem['slug'])) ?>" class="text-dark text-decoration-none">
                                                <?= e(article_title($rItem['title'])) ?>
                                            </a>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>

    </div>
</div>
