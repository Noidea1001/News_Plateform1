<?php
/**
 * 404 Page Not Found View
 * news-platform / templates / views / 404.php
 */
?>

<div class="container py-5 text-center">
    <div class="py-5">
        <span class="badge bg-danger text-uppercase px-3 py-2 fs-6 mb-3">Error 404</span>
        <h1 class="display-1 fw-bold editorial-title text-dark">Article Not Found</h1>
        <p class="lead text-secondary mx-auto mb-4" style="max-width: 600px;">
            <?= ($message ?? 'The requested article slug could not be located in our publication database.') ?>
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="/index.php" class="btn btn-danger px-4 py-2 fw-semibold">
                <i class="bi bi-house-door-fill me-1"></i> Return to Homepage
            </a>
            <button type="button" class="btn btn-outline-dark px-4 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#subscribeModal">
                <i class="bi bi-envelope me-1"></i> Subscribe to Feed
            </button>
        </div>
    </div>
</div>
