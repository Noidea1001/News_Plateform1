<?php
/**
 * Template Blueprint 2: Clean Deep-Read Investigative Format
 * news-platform / templates / views / article-investigative.php
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
?>

<div class="template-investigative">

    <!-- Cinematic Editorial Hero Banner Section -->
    <header class="hero-banner py-4 py-md-5" style="background: #0f172a; border-bottom: 3px solid #c8102e;">
        <div class="container text-center py-2 py-md-3" style="max-width: 860px;">

            <!-- Category & Special Report Pill -->
            <div class="d-flex align-items-center justify-content-center gap-2 mb-3 flex-wrap">
                <span class="badge bg-danger text-uppercase px-2.5 py-1 text-3xs fw-bold rounded-1 tracking-wider">
                    <?= __('special_report') ?>
                </span>
                <span class="text-white-50 text-xs fw-semibold text-uppercase tracking-wide">
                    &bull; <?= e(cat_name($article['category_name'])) ?>
                </span>
            </div>

            <!-- Monumental Article Title -->
            <h1 class="fw-bold editorial-title text-white mb-3 tracking-tight"
                style="font-size: clamp(2rem, 4.2vw, 2.85rem); line-height: 1.22;">
                <?= e(article_title($article)) ?>
            </h1>

            <!-- Standfirst Lead Summary -->
            <?php $summaryText = article_summary($article); ?>
            <?php if (!empty($summaryText)) { ?>
                <p class="lead text-light opacity-90 mx-auto mb-4"
                   style="max-width: 720px; font-size: 1.15rem; line-height: 1.6; color: #cbd5e1 !important; font-weight: 400;">
                    <?= e($summaryText) ?>
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

            <!-- Clean Author & Date Byline (Zero icon clutter) -->
            <div class="d-flex flex-wrap align-items-center justify-content-center gap-2.5 text-white-50 text-2xs pt-3 border-top border-secondary border-opacity-25">
                <div class="d-flex align-items-center gap-2">
                    <?php if (!empty($article['author_avatar'])): ?>
                        <img src="<?= e(image_url($article['author_avatar'])) ?>" alt="<?= e($article['author_name']) ?>"
                            class="rounded-circle object-fit-cover border border-secondary" style="width: 24px; height: 24px;">
                    <?php endif; ?>
                    <span class="text-white fw-semibold"><?= e($article['author_name']) ?></span>
                </div>
                <span>&middot;</span>
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

        </div>
    </header>

    <!-- Centered Deep-Read Article Body -->
    <div class="container py-4 py-md-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                <!-- Top Action Bar: Bookmark & Share Dropdown -->
                <div class="mb-4">
                    <?php include __DIR__ . '/../components/share-buttons.php'; ?>
                </div>

                <!-- Featured High-Res Cover Image -->
                <?php if (!empty($article['featured_image'])) { ?>
                    <figure class="mb-4 overflow-hidden rounded-2">
                        <img src="<?= e(image_url($article['featured_image'])) ?>"
                             class="w-100 h-auto object-fit-cover d-block"
                             alt="<?= e($article['title']) ?>"
                             style="max-height: 520px;">
                        <?php if (!empty($article['image_caption'])) { ?>
                            <figcaption class="text-muted text-2xs mt-1.5 px-1"><?= e($article['image_caption']) ?></figcaption>
                        <?php } ?>
                    </figure>
                <?php } ?>

                <!-- Video Embed Player -->
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
                        <div id="galleryCarouselInv" class="carousel slide rounded-2 overflow-hidden shadow-sm" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <?php foreach ($galleryUrls as $gIdx => $gUrl) { ?>
                                    <button type="button" data-bs-target="#galleryCarouselInv" data-bs-slide-to="<?= $gIdx ?>"
                                        class="<?= $gIdx === 0 ? 'active' : '' ?>" aria-label="Slide <?= $gIdx + 1 ?>"></button>
                                <?php } ?>
                            </div>
                            <div class="carousel-inner">
                                <?php foreach ($galleryUrls as $gIdx => $gUrl) { ?>
                                    <div class="carousel-item <?= $gIdx === 0 ? 'active' : '' ?>">
                                        <img src="<?= e(image_url($gUrl)) ?>" class="d-block w-100 object-fit-cover"
                                            style="max-height: 460px;" alt="Evidence photo <?= $gIdx + 1 ?>" loading="lazy"
                                            onerror="this.parentElement.style.display='none'">
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
                        <h6 class="fw-bold text-dark mb-2">Audio Report / Podcast</h6>
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

                <!-- Deep Read Body Content with Drop Cap -->
                <article class="article-body mb-4 <?= !empty($article['has_drop_cap']) ? 'has-drop-cap' : '' ?>"
                         style="font-size: 1.12rem; line-height: 1.85; color: #1e293b;">
                    <?= \App\Core\Sanitizer::parseArticleMedia(article_content($article), $article['gallery_images'] ?? null, $article['featured_image'] ?? null, $article['video_embed_url'] ?? null) ?>
                </article>

                <!-- Verified Primary Source Citation Component -->
                <?php
                $referenceUrl = $article['reference_url'] ?? null;
                $referenceSourceName = $article['reference_source_name'] ?? null;
                include __DIR__ . '/../components/citation-box.php';
                ?>

                <!-- Reader Community & Discussion Section -->
                <?php include __DIR__ . '/../components/comments-section.php'; ?>

                <!-- Reader Callout Banner -->
                <div class="card text-white my-5 border-0 p-4 rounded-2 text-center" style="background:#0f172a;">
                    <h5 class="editorial-title text-white mb-2"><?= __('support_investigative') ?></h5>
                    <p class="small text-light opacity-75 mb-3 mx-auto" style="max-width: 580px;">
                        <?= __('support_desc') ?>
                    </p>
                    <div class="d-flex justify-content-center">
                        <?php if (!\App\Core\Auth::readerCheck()) { ?>
                            <button type="button" class="btn btn-danger btn-sm px-3.5 py-1.5 rounded-pill fw-semibold text-xs"
                                data-bs-toggle="modal" data-bs-target="#readerAuthModal" data-auth-tab="register">
                                <?= __('create_account') ?>
                            </button>
                        <?php } else { ?>
                            <a href="<?= url('settings.php#tab-topics') ?>" class="btn btn-danger btn-sm px-3.5 py-1.5 rounded-pill fw-semibold text-xs">
                                <?= __('settings_tab_topics') ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <!-- Related Investigative Content -->
                <?php if (!empty($relatedArticles)) { ?>
                    <div class="pt-4 border-top">
                        <h5 class="editorial-title fw-bold mb-3 text-dark"><?= __('further_investigations') ?></h5>
                        <div class="list-group list-group-flush rounded-2 border overflow-hidden">
                            <?php foreach ($relatedArticles as $rItem) { ?>
                                <a href="<?= url('article.php?slug=' . urlencode($rItem['slug'])) ?>"
                                    class="list-group-item list-group-item-action py-3 px-3.5 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-danger fw-bold text-3xs text-uppercase mb-1 d-block"><?= e(cat_name($rItem['category_name'])) ?></span>
                                        <h6 class="mb-0 fw-bold text-dark text-xs" style="line-height:1.35;"><?= e(article_title($rItem)) ?></h6>
                                    </div>
                                    <span class="text-muted text-xs ms-2">&rarr;</span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>

</div>