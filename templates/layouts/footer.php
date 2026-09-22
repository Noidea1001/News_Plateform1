</main><!-- /main -->

<!-- Subscribe Modal -->
<?php
$modalPath = __DIR__ . '/../components/subscribe-modal.php';
if (file_exists($modalPath)) { include $modalPath; }
?>

<!-- ── CNA-Style Footer ────────────────────────────────────────────────── -->
<footer class="bg-brand-navy text-white mt-auto" style="border-top: 3px solid #c8102e; padding-top: 3rem; padding-bottom: 2rem;">
    <div class="container">
        <div class="row g-5 mb-4">

            <!-- Brand & Mission -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="brand-logo-badge" style="width:44px;height:44px;font-size:1.05rem;border-radius:2px;">NP</span>
                    <span class="fw-bold" style="color:#fff; font-size:1.05rem; letter-spacing:-0.02em;">
                        <?= __('site_title') ?>
                    </span>
                </div>
                <p style="font-size:0.875rem; color:rgba(255,255,255,0.55); line-height:1.75; margin-bottom:1.5rem;">
                    <?= __('footer_desc') ?>
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="footer-social-btn" title="X / Twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="footer-social-btn" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="footer-social-btn" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="footer-social-btn" style="color:#fbbf24;" title="RSS"><i class="bi bi-rss-fill"></i></a>
                </div>
            </div>

            <!-- Content Blueprints -->
            <div class="col-lg-4 col-md-6">
                <!-- CNA-style section title: red left border -->
                <div style="border-left:3px solid #c8102e; padding-left:0.75rem; margin-bottom:1.25rem;">
                    <h6 style="color:#fff; font-size:0.78rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; margin:0;">
                        <?= __('layout_blueprints_title') ?>
                    </h6>
                </div>
                <ul class="list-unstyled d-flex flex-column gap-3" style="font-size:0.875rem;">
                    <li class="d-flex align-items-center gap-3">
                        <span style="width:28px;height:28px;background:rgba(255,255,255,0.07);display:inline-flex;align-items:center;justify-content:center;border-radius:2px;color:rgba(255,255,255,0.60);">
                            <i class="bi bi-layout-three-columns" style="font-size:0.85rem;"></i>
                        </span>
                        <span style="color:rgba(255,255,255,0.60);"><?= __('footer_tmpl_1') ?></span>
                    </li>
                    <li class="d-flex align-items-center gap-3">
                        <span style="width:28px;height:28px;background:rgba(200,16,46,0.18);display:inline-flex;align-items:center;justify-content:center;border-radius:2px;color:#fca5a5;">
                            <i class="bi bi-card-text" style="font-size:0.85rem;"></i>
                        </span>
                        <span style="color:rgba(255,255,255,0.60);"><?= __('footer_tmpl_2') ?></span>
                    </li>
                    <li class="d-flex align-items-center gap-3">
                        <span style="width:28px;height:28px;background:rgba(255,255,255,0.07);display:inline-flex;align-items:center;justify-content:center;border-radius:2px;color:rgba(255,255,255,0.60);">
                            <i class="bi bi-person-lines-fill" style="font-size:0.85rem;"></i>
                        </span>
                        <span style="color:rgba(255,255,255,0.60);"><?= __('footer_tmpl_3') ?></span>
                    </li>
                </ul>
            </div>

            <!-- Newsletter CTA -->
            <div class="col-lg-4 col-md-12">
                <div style="border-left:3px solid #c8102e; padding-left:0.75rem; margin-bottom:1.25rem;">
                    <h6 style="color:#fff; font-size:0.78rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; margin:0;">
                        <?= __('feed_sub_title') ?>
                    </h6>
                </div>
                <p style="font-size:0.875rem; color:rgba(255,255,255,0.55); line-height:1.75; margin-bottom:1.25rem;">
                    <?= __('sub_desc_footer') ?>
                </p>
                <button type="button"
                        class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2 fw-bold"
                        style="font-size:0.85rem; border-radius:2px; text-transform:uppercase; letter-spacing:0.05em; padding:0.65rem 1rem;"
                        data-bs-toggle="modal" data-bs-target="#subscribeModal">
                    <i class="bi bi-envelope-check-fill"></i>
                    <?= __('register_sub_shortcut') ?>
                </button>
            </div>
        </div>

        <!-- Bottom bar -->
        <div style="border-top:1px solid rgba(255,255,255,0.08); padding-top:1.25rem; margin-top:1rem;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2"
                 style="font-size:0.78rem; color:rgba(255,255,255,0.40);">
                <p class="mb-0">&copy; <?= date('Y') ?> <?= __('rights_reserved') ?></p>
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= url('index.php') ?>"
                       class="text-decoration-none"
                       style="color:rgba(255,255,255,0.40);"
                       onmouseover="this.style.color='rgba(255,255,255,0.80)'"
                       onmouseout="this.style.color='rgba(255,255,255,0.40)'">
                        <?= __('cda_badge') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
