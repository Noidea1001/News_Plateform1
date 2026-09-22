<?php
/**
 * Template Blueprint 1: Classic 2-Column Standard Article Layout
 * news-platform / templates / views / article-standard.php
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
?>

<div class="template-standard py-4">
    <div class="container">

        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>"
                        class="text-secondary text-decoration-none"><i class="bi bi-house-door me-1"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('index.php?category=' . $article['category_id']) ?>"
                        class="text-secondary text-decoration-none"><?= e(cat_name($article['category_name'])) ?></a>
                </li>
                <li class="breadcrumb-item active text-truncate" style="max-width: 320px;">
                    <?= e(article_title($article['title'])) ?></li>
            </ol>
        </nav>

        <!-- Category & Template Badge -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <span
                class="badge bg-danger text-uppercase px-2.5 py-1 fw-bold"><?= e(cat_name($article['category_name'])) ?></span>
            <span class="badge bg-secondary text-uppercase px-2.5 py-1 fw-bold"><?= __('standard_layout') ?></span>
            <?php if (!empty($article['is_breaking'])) { ?>
                <span class="badge bg-warning text-dark text-uppercase px-2.5 py-1 breaking-badge fw-bold">
                    <i class="bi bi-lightning-fill me-1"></i> <?= __('breaking') ?>
                </span>
            <?php } ?>
        </div>

        <!-- Article Title -->
        <h1 class="display-5 fw-bold editorial-title text-dark mb-3 tracking-tight">
            <?= e(article_title($article['title'])) ?>
        </h1>

        <!-- Summary Paragraph -->
        <p
            class="lead text-secondary fw-normal fs-5 mb-4 border-start border-4 border-danger ps-3 py-2 bg-light rounded-end">
            <?= e($article['summary']) ?>
        </p>

        <!-- Author & Published Metadata Bar -->
        <div
            class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 bg-white rounded-4 border mb-4 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 shadow-sm"
                    style="width: 44px; height: 44px;">
                    <?= strtoupper(substr($article['author_name'], 0, 1)) ?>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark"><?= e($article['author_name']) ?></h6>
                    <span class="text-muted text-xs"><?= e($article['author_role'] ?? 'Reporter') ?></span>
                </div>
            </div>
            <div class="text-md-end text-muted small">
                <div><i class="bi bi-clock me-1 text-danger"></i>
                    <?= str_replace(':date', \App\Core\TemplateEngine::formatDate($article['published_at']), __('published_date')) ?>
                </div>
                <div class="text-xs text-secondary mt-1"><i class="bi bi-eye me-1"></i>
                    <?= str_replace(':count', number_format((int) $article['views_count']), __('total_readers')) ?>
                </div>
            </div>
        </div>

        <!-- Main 2-Column Grid Layout -->
        <div class="row g-4">

            <!-- Primary Article Column -->
            <div class="col-lg-8">

                <!-- Featured Cover Image -->
                <?php if (!empty($article['featured_image'])) { ?>
                    <div class="mb-4 rounded-4 overflow-hidden shadow-sm">
                        <img src="<?= e($article['featured_image']) ?>" class="w-100 h-auto object-fit-cover"
                            alt="<?= e($article['title']) ?>" style="max-height: 480px;">
                    </div>
                <?php } ?>

                <!-- Video Embed Player -->
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
                        <div id="galleryCarouselStd" class="carousel slide shadow-sm rounded-4 overflow-hidden"
                            data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <?php foreach ($galleryUrls as $gIdx => $gUrl) { ?>
                                    <button type="button" data-bs-target="#galleryCarouselStd" data-bs-slide-to="<?= $gIdx ?>"
                                        class="<?= $gIdx === 0 ? 'active' : '' ?>" aria-label="Slide <?= $gIdx + 1 ?>"></button>
                                <?php } ?>
                            </div>
                            <div class="carousel-inner">
                                <?php foreach ($galleryUrls as $gIdx => $gUrl) { ?>
                                    <div class="carousel-item <?= $gIdx === 0 ? 'active' : '' ?>">
                                        <img src="<?= e($gUrl) ?>" class="d-block w-100 object-fit-cover" style="max-height: 440px;"
                                            alt="Gallery photo <?= $gIdx + 1 ?>" loading="lazy"
                                            onerror="this.parentElement.style.display='none'">
                                    </div>
                                <?php } ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarouselStd"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarouselStd"
                                data-bs-slide="next">
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
                            <i class="bi bi-headphones text-primary fs-5"></i> Audio Report / Podcast
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

                <!-- Reading Toolbar -->
                <?php include __DIR__ . '/../components/reading-toolbar.php'; ?>

                <!-- Article Body Content -->
                <div class="article-body mb-4 <?= !empty($article['has_drop_cap']) ? 'has-drop-cap' : '' ?>">
                    <?= \App\Core\Sanitizer::parseArticleMedia($article['content'], $article['gallery_images'] ?? null, $article['featured_image'] ?? null, $article['video_embed_url'] ?? null) ?>
                </div>

                <!-- Social Share Buttons -->
                <div class="mb-4">
                    <?php include __DIR__ . '/../components/share-buttons.php'; ?>
                </div>

                <!-- Verified Primary Citation Card Component -->
                <?php
                $referenceUrl = $article['reference_url'] ?? null;
                $referenceSourceName = $article['reference_source_name'] ?? null;
                include __DIR__ . '/../components/citation-box.php';
                ?>

                <!-- Related Articles Grid -->
                <?php if (!empty($relatedArticles)) { ?>
                    <div class="mt-5 pt-4 border-top">
                        <h4 class="editorial-title fw-bold mb-4 text-dark">Related Coverage</h4>
                        <div class="row g-3">
                            <?php foreach ($relatedArticles as $rItem) { ?>
                                <div class="col-md-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
                                        <div class="card-body p-3.5 d-flex flex-column justify-content-between">
                                            <div>
                                                <span
                                                    class="badge bg-light text-dark border mb-2 text-xs"><?= e(cat_name($rItem['category_name'])) ?></span>
                                                <h6 class="fw-bold text-dark line-clamp-2 mb-2" style="font-size: 0.925rem;">
                                                    <a href="<?= url('article.php?slug=' . urlencode($rItem['slug'])) ?>"
                                                        class="text-dark text-decoration-none">
                                                        <?= e(article_title($rItem['title'])) ?>
                                                    </a>
                                                </h6>
                                            </div>
                                            <div class="text-xs text-muted mt-2">
                                                <?= \App\Core\TemplateEngine::timeAgo($rItem['published_at']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

            </div>

            <!-- Right Sidebar Column (Stacks under Citation Box on Mobile) -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 2rem;">
                    <?php include __DIR__ . '/../components/sidebar.php'; ?>
                </div>
            </div>

        </div>

    </div>
</div>