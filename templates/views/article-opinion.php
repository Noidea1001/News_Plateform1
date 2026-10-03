<?php
/**
 * Template Blueprint 3: Clean Prestigious Columnist Opinion Layout
 * news-platform / templates / views / article-opinion.php
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
?>

<div class="template-opinion py-4 py-md-5" style="background: #fafafa;">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                <!-- Columnist Author Spotlight Header Card -->
                <div class="p-3.5 p-md-4 mb-4 rounded-2 bg-white border" style="border-color: #e2e8f0 !important;">
                    <div class="d-flex align-items-center gap-3.5">
                        <div class="position-relative flex-shrink-0">
                            <?php if (!empty($article['author_avatar'])) { ?>
                                <img src="<?= e(image_url($article['author_avatar'])) ?>"
                                    class="rounded-circle object-fit-cover border"
                                    style="width: 72px; height: 72px;" alt="<?= e($article['author_name']) ?>">
                            <?php } else { ?>
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4 text-white"
                                    style="width: 72px; height: 72px; background: #0f172a;">
                                    <?= strtoupper(mb_substr($article['author_name'], 0, 1)) ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="min-w-0">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="text-danger fw-bold text-3xs text-uppercase tracking-wider">
                                    <?= __('opinion_perspective') ?>
                                </span>
                                <span class="text-muted text-3xs">&bull; <?= e(cat_name($article['category_name'])) ?></span>
                            </div>
                            <h4 class="fw-bold editorial-title mb-1 text-dark" style="font-size: 1.25rem;">
                                <?= e($article['author_name']) ?>
                            </h4>
                            <p class="small text-secondary mb-0 text-truncate" style="font-size: 0.82rem;">
                                <?= e($article['author_bio'] ?? 'Senior Columnist & Editorial Contributor for NewsPlatform.') ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Article Title (Prestigious Serif Typography) -->
                <h1 class="fw-bold editorial-title text-dark mb-3 text-center tracking-tight"
                    style="font-family: var(--font-serif), Georgia, serif; font-size: clamp(2rem, 4vw, 2.75rem); line-height: 1.22;">
                    <?= e(article_title($article)) ?>
                </h1>

                <!-- Summary Standfirst Lead -->
                <?php $summaryText = article_summary($article); ?>
                <?php if (!empty($summaryText)) { ?>
                    <p class="lead text-dark fst-italic text-center mb-4 px-2"
                       style="font-family: var(--font-serif), Georgia, serif; font-size: 1.15rem; line-height: 1.6; color: #334155;">
                        &ldquo;<?= e($summaryText) ?>&rdquo;
                    </p>
                <?php } ?>

                <?php
                $artData = htmlspecialchars(json_encode([
                    'id' => $article['id'],
                    'title' => article_title($article),
                    'summary' => article_summary($article),
                    'category' => cat_name($article['category_name']),
                    'author' => $article['author_name'],
                    'date' => \App\Core\TemplateEngine::formatDate($article['published_at']),
                    'time_ago' => \App\Core\TemplateEngine::timeAgo($article['published_at']),
                    'reading_time' => $article['reading_time'] ?? '3 min read',
                    'views' => number_format((int)$article['views_count']),
                    'image' => image_url($article['featured_image'] ?? ''),
                    'url' => url('article.php?slug=' . urlencode($article['slug']))
                ]), ENT_QUOTES, 'UTF-8');
                ?>

                <!-- Clean Centered Byline (Zero Icon Clutter) -->
                <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 text-muted text-2xs mb-3 pb-3 border-bottom">
                    <span><?= \App\Core\TemplateEngine::formatDate($article['published_at']) ?></span>
                    <span>&middot;</span>
                    <span><?= \App\Core\TemplateEngine::timeAgo($article['published_at']) ?></span>
                    <?php if (!empty($article['reading_time'])) { ?>
                        <span>&middot;</span>
                        <span><?= e($article['reading_time']) ?></span>
                    <?php } ?>
                    <span>&middot;</span>
                    <span><?= number_format((int) $article['views_count']) ?> <?= __('views') ?? 'views' ?></span>
                </div>

                <!-- Top Action Bar: Bookmark & Share Dropdown -->
                <div class="mb-4">
                    <?php include __DIR__ . '/../components/share-buttons.php'; ?>
                </div>

                <!-- Featured Cover Image -->
                <?php if (!empty($article['featured_image'])) { ?>
                    <figure class="mb-4 overflow-hidden rounded-2">
                        <img src="<?= e(image_url($article['featured_image'])) ?>"
                             class="w-100 h-auto object-fit-cover d-block"
                             alt="<?= e($article['title']) ?>"
                             style="max-height: 480px;">
                        <?php if (!empty($article['image_caption'])) { ?>
                            <figcaption class="text-muted text-2xs mt-1.5 px-1 text-center"><?= e($article['image_caption']) ?></figcaption>
                        <?php } ?>
                    </figure>
                <?php } ?>

                <!-- Video Player -->
                <?php if (!empty($article['video_embed_url']) && !\App\Core\Sanitizer::hasVideoInContent($article['content'] ?? null, $article['video_embed_url'])) { ?>
                    <div class="my-4">
                        <h6 class="fw-bold text-dark mb-2"><?= __('video_doc_title') ?></h6>
                        <div class="video-container rounded-2 overflow-hidden border bg-black shadow-sm">
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
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-dark mb-0"><?= __('photo_gallery_title') ?></h6>
                            <span class="badge bg-light text-secondary border fw-normal text-3xs"><?= count($galleryUrls) ?></span>
                        </div>
                        <div id="galleryCarouselOp" class="carousel slide rounded-2 overflow-hidden shadow-sm" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <?php foreach ($galleryUrls as $gIdx => $gUrl) { ?>
                                    <button type="button" data-bs-target="#galleryCarouselOp" data-bs-slide-to="<?= $gIdx ?>"
                                        class="<?= $gIdx === 0 ? 'active' : '' ?>" aria-label="Slide <?= $gIdx + 1 ?>"></button>
                                <?php } ?>
                            </div>
                            <div class="carousel-inner">
                                <?php foreach ($galleryUrls as $gIdx => $gUrl) { ?>
                                    <div class="carousel-item <?= $gIdx === 0 ? 'active' : '' ?>">
                                        <img src="<?= e(image_url($gUrl)) ?>" class="d-block w-100 object-fit-cover"
                                            style="max-height: 440px;" alt="Gallery photo <?= $gIdx + 1 ?>" loading="lazy"
                                            onerror="this.parentElement.style.display='none'">
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
                        <h6 class="fw-bold text-dark mb-2">Columnist Audio Commentary</h6>
                        <?php $audioUrl = e($article['audio_embed_url']); ?>
                        <?php if (str_contains($audioUrl, 'spotify.com') || str_contains($audioUrl, 'soundcloud.com') || str_contains($audioUrl, 'anchor.fm')) { ?>
                            <div class="rounded-2 overflow-hidden border shadow-sm">
                                <iframe src="<?= $audioUrl ?>" width="100%" height="152" frameborder="0"
                                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                                    loading="lazy"></iframe>
                            </div>
                        <?php } else { ?>
                            <div class="bg-light rounded-2 p-3 border shadow-sm">
                                <audio controls class="w-100" preload="metadata">
                                    <source src="<?= $audioUrl ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <!-- Reading Toolbar (Scroll Progress Bar) -->
                <?php include __DIR__ . '/../components/reading-toolbar.php'; ?>

                <!-- Article Content (Rich Serif Typography) -->
                <article class="article-body mb-4 <?= !empty($article['has_drop_cap']) ? 'has-drop-cap' : '' ?>"
                         style="font-family: var(--font-serif), Georgia, serif; font-size: 1.15rem; line-height: 1.85; color: #1e293b;">
                    <?= \App\Core\Sanitizer::parseArticleMedia(article_content($article), $article['gallery_images'] ?? null, $article['featured_image'] ?? null, $article['video_embed_url'] ?? null) ?>
                </article>

                <!-- Primary Citation Source Box -->
                <?php
                $referenceUrl = $article['reference_url'] ?? null;
                $referenceSourceName = $article['reference_source_name'] ?? null;
                include __DIR__ . '/../components/citation-box.php';
                ?>

                <!-- Reader Community & Discussion Section -->
                <?php include __DIR__ . '/../components/comments-section.php'; ?>

                <!-- Columnist Footer Profile Callout -->
                <div class="p-4 my-4 rounded-2 text-center bg-white border">
                    <h5 class="editorial-title fw-bold text-dark mb-1.5" style="font-size: 1.1rem;">
                        <?= str_replace(':name', e($article['author_name']), __('about_columnist')) ?>
                    </h5>
                    <p class="small text-secondary mb-3 mx-auto" style="max-width: 560px; font-size: 0.85rem;">
                        <?= e($article['author_bio'] ?? 'Writes regularly on technology policy, ethics, and cultural shifts.') ?>
                    </p>
                    <div>
                        <?php if (!\App\Core\Auth::readerCheck()) { ?>
                            <button type="button" class="btn btn-outline-danger btn-sm px-3.5 rounded-pill fw-semibold text-xs"
                                data-bs-toggle="modal" data-bs-target="#readerAuthModal" data-auth-tab="register">
                                <?= str_replace(':name', e($article['author_name']), __('subscribe_columnist_feed')) ?>
                            </button>
                        <?php } else { ?>
                            <a href="<?= url('settings.php#tab-topics') ?>" class="btn btn-outline-danger btn-sm px-3.5 rounded-pill fw-semibold text-xs">
                                <?= str_replace(':name', e($article['author_name']), __('subscribe_columnist_feed')) ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <!-- Related Opinion Columns -->
                <?php if (!empty($relatedArticles)) { ?>
                    <div class="pt-4 border-top">
                        <h5 class="editorial-title fw-bold mb-3 text-dark text-center"><?= __('more_perspectives') ?></h5>
                        <div class="row g-3">
                            <?php foreach ($relatedArticles as $rItem) { ?>
                                <div class="col-md-6">
                                    <div class="card h-100 border rounded-2 shadow-none hover-lift bg-white">
                                        <div class="card-body p-3">
                                            <span class="text-danger fw-bold text-3xs text-uppercase mb-1 d-block">Opinion</span>
                                            <h6 class="fw-bold text-dark line-clamp-2 mb-0" style="font-size: 0.9rem; line-height: 1.35;">
                                                <a href="<?= url('article.php?slug=' . urlencode($rItem['slug'])) ?>"
                                                    class="text-dark text-decoration-none">
                                                    <?= e(article_title($rItem)) ?>
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