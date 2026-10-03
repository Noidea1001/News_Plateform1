<?php
/**
 * Template Blueprint 1: Clean Professional Standard Editorial Layout
 * news-platform / templates / views / article-standard.php
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
?>

<div class="template-standard py-3 py-md-4">
    <div class="container">

        <!-- Breadcrumb Navigation (Clean & Minimalist) -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb text-2xs text-muted mb-0">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>" class="text-secondary text-decoration-none"><?= __('home') ?? 'Home' ?></a></li>
                <li class="breadcrumb-item"><a href="<?= url('index.php?category=' . $article['category_id']) ?>" class="text-secondary text-decoration-none"><?= e(cat_name($article['category_name'])) ?></a></li>
                <li class="breadcrumb-item active text-dark text-truncate" style="max-width: 320px;" aria-current="page"><?= e(article_title($article)) ?></li>
            </ol>
        </nav>

        <!-- Category & Breaking Indicator -->
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <a href="<?= url('index.php?category=' . $article['category_id']) ?>"
               class="text-danger fw-bold text-uppercase text-decoration-none"
               style="font-size: 0.78rem; letter-spacing: 0.06em;">
                <?= e(cat_name($article['category_name'])) ?>
            </a>
            <?php if (!empty($article['is_breaking'])) { ?>
                <span class="badge bg-danger text-white text-3xs px-2 py-0.5 rounded-1 fw-bold text-uppercase tracking-wider">
                    <?= __('breaking') ?>
                </span>
            <?php } ?>
        </div>

        <!-- Article Title -->
        <h1 class="fw-bold editorial-title text-dark mb-3" style="font-size: clamp(1.85rem, 3.8vw, 2.5rem); line-height: 1.25; letter-spacing: -0.02em;">
            <?= e(article_title($article)) ?>
        </h1>

        <!-- Standfirst / Summary Paragraph -->
        <?php $summaryText = article_summary($article); ?>
        <?php if (!empty($summaryText)) { ?>
            <p class="article-lead text-secondary mb-3" style="font-size: 1.12rem; line-height: 1.55; color: #475569; font-weight: 400;">
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

        <!-- Clean Editorial Byline Bar (NYT / Reuters Style) -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 py-2.5 my-3 border-top border-bottom">
            <!-- Author Info -->
            <div class="d-flex align-items-center gap-2.5">
                <?php if (!empty($article['author_avatar'])): ?>
                    <img src="<?= e(image_url($article['author_avatar'])) ?>" alt="<?= e($article['author_name']) ?>"
                        class="rounded-circle object-fit-cover flex-shrink-0 border" style="width: 38px; height: 38px;">
                <?php else: ?>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                        style="width: 38px; height: 38px; font-size: 0.85rem; background-color: #c8102e;">
                        <?= strtoupper(mb_substr($article['author_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <div class="fw-bold text-dark text-xs" style="line-height: 1.25;"><?= e($article['author_name']) ?></div>
                    <div class="text-muted text-3xs"><?= e($article['author_role'] ?? 'Reporter') ?></div>
                </div>
            </div>

            <!-- Date, Reading Time & Views Metadata (Clean middot separators, no icon clutter) -->
            <div class="d-flex align-items-center gap-2 text-muted text-2xs ms-auto flex-wrap">
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

        <!-- Top Action Bar: Bookmark & Share Dropdown -->
        <div class="mb-3">
            <?php include __DIR__ . '/../components/share-buttons.php'; ?>
        </div>

        <!-- Main 2-Column Grid Layout -->
        <div class="row g-4">

            <!-- Primary Article Column -->
            <div class="col-lg-8">

                <!-- Featured Cover Image -->
                <?php if (!empty($article['featured_image'])) { ?>
                    <figure class="mb-4 overflow-hidden rounded-2">
                        <img src="<?= e(image_url($article['featured_image'])) ?>"
                             class="w-100 h-auto object-fit-cover d-block"
                             alt="<?= e($article['title']) ?>"
                             style="max-height: 480px;">
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
                        <div id="galleryCarouselStd" class="carousel slide rounded-2 overflow-hidden shadow-sm" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <?php foreach ($galleryUrls as $gIdx => $gUrl) { ?>
                                    <button type="button" data-bs-target="#galleryCarouselStd" data-bs-slide-to="<?= $gIdx ?>"
                                        class="<?= $gIdx === 0 ? 'active' : '' ?>" aria-label="Slide <?= $gIdx + 1 ?>"></button>
                                <?php } ?>
                            </div>
                            <div class="carousel-inner">
                                <?php foreach ($galleryUrls as $gIdx => $gUrl) { ?>
                                    <div class="carousel-item <?= $gIdx === 0 ? 'active' : '' ?>">
                                        <img src="<?= e(image_url($gUrl)) ?>" class="d-block w-100 object-fit-cover" style="max-height: 440px;"
                                            alt="Gallery photo <?= $gIdx + 1 ?>" loading="lazy"
                                            onerror="this.parentElement.style.display='none'">
                                    </div>
                                <?php } ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarouselStd" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarouselStd" data-bs-slide="next">
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

                <!-- Reading Toolbar -->
                <?php include __DIR__ . '/../components/reading-toolbar.php'; ?>

                <!-- Article Body Content -->
                <article class="article-body mb-4 <?= !empty($article['has_drop_cap']) ? 'has-drop-cap' : '' ?>"
                         style="font-size: 1.08rem; line-height: 1.8; color: #1e293b;">
                    <?= \App\Core\Sanitizer::parseArticleMedia(article_content($article), $article['gallery_images'] ?? null, $article['featured_image'] ?? null, $article['video_embed_url'] ?? null) ?>
                </article>

                <!-- Verified Primary Citation Card Component -->
                <?php
                $referenceUrl = $article['reference_url'] ?? null;
                $referenceSourceName = $article['reference_source_name'] ?? null;
                include __DIR__ . '/../components/citation-box.php';
                ?>

                <!-- Reader Community & Discussion Section -->
                <?php include __DIR__ . '/../components/comments-section.php'; ?>

                <!-- Related Articles Grid -->
                <?php if (!empty($relatedArticles)) { ?>
                    <div class="mt-5 pt-4 border-top">
                        <h5 class="editorial-title fw-bold mb-3 text-dark"><?= __('related_coverage') ?></h5>
                        <div class="row g-3">
                            <?php foreach ($relatedArticles as $rItem) { ?>
                                <div class="col-md-4">
                                    <div class="card h-100 border border-light-subtle rounded-2 shadow-none hover-lift">
                                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                                            <div>
                                                <span class="text-danger fw-bold text-3xs text-uppercase mb-1 d-block"><?= e(cat_name($rItem['category_name'])) ?></span>
                                                <h6 class="fw-bold text-dark line-clamp-2 mb-2" style="font-size: 0.9rem; line-height: 1.35;">
                                                    <a href="<?= url('article.php?slug=' . urlencode($rItem['slug'])) ?>" class="text-dark text-decoration-none">
                                                        <?= e(article_title($rItem)) ?>
                                                    </a>
                                                </h6>
                                            </div>
                                            <div class="text-3xs text-muted mt-2">
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

            <!-- Right Sidebar Column -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 2rem;">
                    <?php include __DIR__ . '/../components/sidebar.php'; ?>
                </div>
            </div>

        </div>

    </div>
</div>