<?php
/**
 * Component: Primary Verified Source Citation Bar (Compact Single Line)
 * news-platform / templates / components / citation-box.php
 */

if (empty($referenceUrl) && empty($referenceSourceName)) {
    return;
}
?>

<div class="citation-box-compact bg-light rounded-3 p-2 px-3 my-3 border d-flex flex-wrap align-items-center justify-content-between gap-2 text-xs">
    <div class="d-flex align-items-center gap-2 min-w-0 text-truncate">
        <span class="badge bg-primary text-white font-monospace text-2xs fw-bold px-2 py-1 flex-shrink-0"><?= __('verified_citation') ?></span>
        <span class="fw-bold text-dark text-truncate"><?= e($referenceSourceName ?? 'Primary Reference Source') ?></span>
    </div>
    <?php if (!empty($referenceUrl)) { ?>
        <a href="<?= e($referenceUrl) ?>" target="_blank" rel="noopener noreferrer" class="text-primary fw-semibold text-decoration-none d-inline-flex align-items-center gap-1 flex-shrink-0 hover-underline ms-auto">
            <span><?= __('inspect_reference') ?></span>
            <i class="bi bi-box-arrow-up-right text-xs"></i>
        </a>
    <?php } ?>
</div>
<?php ?>
