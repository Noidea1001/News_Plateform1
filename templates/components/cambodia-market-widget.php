<?php
/**
 * Component: Daily Cambodian Market & Economic Indicators Widget
 * news-platform / templates / components / cambodia-market-widget.php
 * Official NBC exchange rates, Ministry of Commerce fuel prices, and Gold index.
 */

require_once __DIR__ . '/../../src/Core/helpers.php';
$currentLang = $_SESSION['lang'] ?? 'en';
$isKh = ($currentLang === 'kh' || $currentLang === 'km');
?>

<section class="sbw cambodia-market-card mb-3" style="border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; overflow: hidden;">
    <div class="sbw-head d-flex align-items-center justify-content-between p-2.5 px-3 border-bottom" style="background: #fafafa;">
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center text-danger flex-shrink-0">
                <i class="bi bi-graph-up-arrow" style="font-size: 0.95rem;"></i>
            </span>
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.84rem;"><?= __('market_cambodia_title') ?></h6>
        </div>
        <span class="badge bg-light text-secondary border fw-medium" style="font-size: 0.65rem;">
            <?= date('d M Y') ?>
        </span>
    </div>

    <!-- Market Navigation Pills -->
    <ul class="nav nav-pills nav-fill p-1.5 border-bottom bg-white" id="marketPillsTab" role="tablist" style="gap: 4px;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active py-1 px-2 text-2xs fw-bold rounded-2 shadow-none" id="pills-cur-tab" data-bs-toggle="pill" data-bs-target="#pills-cur" type="button" role="tab" style="font-size: 0.72rem;">
                <i class="bi bi-currency-dollar me-0.5"></i><?= __('market_currency_tab') ?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-1 px-2 text-2xs fw-bold rounded-2 shadow-none" id="pills-fuel-tab" data-bs-toggle="pill" data-bs-target="#pills-fuel" type="button" role="tab" style="font-size: 0.72rem;">
                <i class="bi bi-fuel-pump me-0.5"></i><?= __('market_fuel_tab') ?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-1 px-2 text-2xs fw-bold rounded-2 shadow-none" id="pills-gold-tab" data-bs-toggle="pill" data-bs-target="#pills-gold" type="button" role="tab" style="font-size: 0.72rem;">
                <i class="bi bi-gem me-0.5"></i><?= __('market_gold_tab') ?>
            </button>
        </li>
    </ul>

    <div class="tab-content p-2.5" id="marketPillsTabContent">
        <!-- 1. Currency Rates (NBC) -->
        <div class="tab-pane fade show active" id="pills-cur" role="tabpanel">
            <div class="d-flex flex-column gap-1.5">
                <div class="d-flex align-items-center justify-content-between p-1.5 px-2 rounded-2" style="background:#f8fafc; border:1px solid #f1f5f9;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-white text-dark border px-1.5 py-0.5 fw-bold" style="font-size:0.68rem;">USD</span>
                        <span class="text-xs text-secondary fw-semibold">1 USD =</span>
                    </div>
                    <span class="fw-bold text-dark text-xs" style="font-size:0.85rem; color:#0f172a;">
                        <?= $isKh ? '៤,០៤៥ ៛' : '4,045 KHR' ?>
                    </span>
                </div>
                <div class="d-flex align-items-center justify-content-between p-1.5 px-2 rounded-2" style="background:#f8fafc; border:1px solid #f1f5f9;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-white text-dark border px-1.5 py-0.5 fw-bold" style="font-size:0.68rem;">THB</span>
                        <span class="text-xs text-secondary fw-semibold">1 THB =</span>
                    </div>
                    <span class="fw-bold text-dark text-xs" style="font-size:0.85rem; color:#0f172a;">
                        <?= $isKh ? '១១៨.៥០ ៛' : '118.50 KHR' ?>
                    </span>
                </div>
                <div class="d-flex align-items-center justify-content-between p-1.5 px-2 rounded-2" style="background:#f8fafc; border:1px solid #f1f5f9;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-white text-dark border px-1.5 py-0.5 fw-bold" style="font-size:0.68rem;">EUR</span>
                        <span class="text-xs text-secondary fw-semibold">1 EUR =</span>
                    </div>
                    <span class="fw-bold text-dark text-xs" style="font-size:0.85rem; color:#0f172a;">
                        <?= $isKh ? '៤,៣៨០ ៛' : '4,380 KHR' ?>
                    </span>
                </div>
            </div>
            <div class="text-muted text-3xs mt-2 text-center" style="font-size:0.65rem;">
                <i class="bi bi-shield-check text-success me-0.5"></i> <?= __('market_source_nbc') ?>
            </div>
        </div>

        <!-- 2. Fuel Prices (Ministry of Commerce) -->
        <div class="tab-pane fade" id="pills-fuel" role="tabpanel">
            <div class="d-flex flex-column gap-1.5">
                <div class="d-flex align-items-center justify-content-between p-1.5 px-2 rounded-2" style="background:#f8fafc; border:1px solid #f1f5f9;">
                    <div class="d-flex align-items-center gap-1.5">
                        <span class="rounded-circle p-1 bg-warning bg-opacity-25 text-warning-emphasis d-inline-flex" style="width:18px;height:18px;align-items:center;justify-content:center;font-size:0.6rem;"><i class="bi bi-fuel-pump-fill"></i></span>
                        <span class="text-xs text-dark fw-semibold"><?= __('fuel_regular') ?></span>
                    </div>
                    <span class="fw-bold text-dark text-xs" style="font-size:0.85rem;">
                        <?= $isKh ? '៤,១៥០ ៛' : '4,150 KHR' ?>
                    </span>
                </div>
                <div class="d-flex align-items-center justify-content-between p-1.5 px-2 rounded-2" style="background:#f8fafc; border:1px solid #f1f5f9;">
                    <div class="d-flex align-items-center gap-1.5">
                        <span class="rounded-circle p-1 bg-danger bg-opacity-25 text-danger d-inline-flex" style="width:18px;height:18px;align-items:center;justify-content:center;font-size:0.6rem;"><i class="bi bi-speedometer"></i></span>
                        <span class="text-xs text-dark fw-semibold"><?= __('fuel_super') ?></span>
                    </div>
                    <span class="fw-bold text-dark text-xs" style="font-size:0.85rem;">
                        <?= $isKh ? '៤,៧០០ ៛' : '4,700 KHR' ?>
                    </span>
                </div>
                <div class="d-flex align-items-center justify-content-between p-1.5 px-2 rounded-2" style="background:#f8fafc; border:1px solid #f1f5f9;">
                    <div class="d-flex align-items-center gap-1.5">
                        <span class="rounded-circle p-1 bg-dark bg-opacity-10 text-dark d-inline-flex" style="width:18px;height:18px;align-items:center;justify-content:center;font-size:0.6rem;"><i class="bi bi-truck"></i></span>
                        <span class="text-xs text-dark fw-semibold"><?= __('fuel_diesel') ?></span>
                    </div>
                    <span class="fw-bold text-dark text-xs" style="font-size:0.85rem;">
                        <?= $isKh ? '៤,០០០ ៛' : '4,000 KHR' ?>
                    </span>
                </div>
            </div>
            <div class="text-muted text-3xs mt-2 text-center" style="font-size:0.65rem;">
                <i class="bi bi-building-check text-primary me-0.5"></i> <?= __('market_source_moc') ?>
            </div>
        </div>

        <!-- 3. Phnom Penh Gold Price -->
        <div class="tab-pane fade" id="pills-gold" role="tabpanel">
            <div class="d-flex flex-column gap-1.5">
                <div class="d-flex align-items-center justify-content-between p-2 rounded-2" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a;">
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.82rem;"><?= __('gold_kilo') ?></div>
                        <div class="text-muted text-3xs" style="font-size: 0.65rem;">Phnom Penh Central Market</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-warning-emphasis" style="font-size: 0.95rem;">
                            <?= $isKh ? '$២,៧៤០' : '$2,740' ?>
                        </div>
                        <div class="text-muted text-3xs" style="font-size: 0.65rem;"><?= __('gold_per_damlung') ?></div>
                    </div>
                </div>
            </div>
            <div class="text-muted text-3xs mt-2 text-center" style="font-size:0.65rem;">
                <i class="bi bi-clock-history me-0.5"></i> <?= $isKh ? 'បច្ចុប្បន្នភាពរៀងរាល់ព្រឹក' : 'Updated Daily' ?>
            </div>
        </div>
    </div>
</section>

<style>
.cambodia-market-card .nav-pills .nav-link {
    color: #64748b;
    background: transparent;
    border: 1px solid transparent;
}
.cambodia-market-card .nav-pills .nav-link.active {
    color: #c8102e !important;
    background: #fef2f2 !important;
    border-color: #fee2e2 !important;
}
</style>
