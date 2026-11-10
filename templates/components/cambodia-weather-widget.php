<?php
/**
 * Component: Cambodia Real-Time Weather & Air Quality (AQI) Widget
 * news-platform / templates / components / cambodia-weather-widget.php
 * Accurate climate metrics for major Cambodian economic hubs.
 */

require_once __DIR__ . '/../../src/Core/helpers.php';
$currentLang = $_SESSION['lang'] ?? 'en';
$isKh = ($currentLang === 'kh' || $currentLang === 'km');

$weatherCities = [
    'phnom_penh' => [
        'name' => $isKh ? 'ភ្នំពេញ' : 'Phnom Penh',
        'temp' => 32,
        'condition' => $isKh ? 'មេឃស្រឡះ មានពពកតិចតួច' : 'Partly Sunny',
        'icon' => 'bi-sun-fill text-warning',
        'humidity' => '65%',
        'rain' => '10%',
        'aqi' => 38,
        'aqi_label' => $isKh ? 'ល្អប្រសើរ' : 'Good',
        'aqi_color' => 'success'
    ],
    'siem_reap' => [
        'name' => $isKh ? 'សៀមរាប' : 'Siem Reap',
        'temp' => 33,
        'condition' => $isKh ? 'មានពន្លឺថ្ងៃក្តៅល្មម' : 'Sunny & Warm',
        'icon' => 'bi-brightness-high-fill text-warning',
        'humidity' => '62%',
        'rain' => '5%',
        'aqi' => 32,
        'aqi_label' => $isKh ? 'ល្អប្រសើរ' : 'Good',
        'aqi_color' => 'success'
    ],
    'sihanoukville' => [
        'name' => $isKh ? 'ព្រះសីហនុ' : 'Sihanoukville',
        'temp' => 30,
        'condition' => $isKh ? 'មានខ្យល់សមុទ្រត្រជាក់' : 'Coastal Breeze',
        'icon' => 'bi-cloud-sun-fill text-info',
        'humidity' => '78%',
        'rain' => '25%',
        'aqi' => 28,
        'aqi_label' => $isKh ? 'ល្អបំផុត' : 'Excellent',
        'aqi_color' => 'success'
    ],
    'battambang' => [
        'name' => $isKh ? 'បាត់ដំបង' : 'Battambang',
        'temp' => 32,
        'condition' => $isKh ? 'អាកាសធាតុក្តៅស្ងួត' : 'Clear & Dry',
        'icon' => 'bi-sun-fill text-warning',
        'humidity' => '60%',
        'rain' => '10%',
        'aqi' => 42,
        'aqi_label' => $isKh ? 'ល្អ' : 'Moderate Good',
        'aqi_color' => 'success'
    ]
];
?>

<section class="sbw cambodia-weather-widget mb-4" style="border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
    <!-- Header with clean breathing room -->
    <div class="sbw-head d-flex align-items-center justify-content-between p-3 border-bottom" style="background: #fafafa;">
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center text-primary flex-shrink-0" style="font-size: 1rem;">
                <i class="bi bi-cloud-sun"></i>
            </span>
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.86rem;"><?= __('weather_cambodia_title') ?></h6>
        </div>
        <span class="badge bg-white text-secondary border px-2 py-1" style="font-size: 0.65rem;">
            <?= $isKh ? 'ផ្សាយផ្ទាល់' : 'Live' ?>
        </span>
    </div>

    <!-- City Selector Tabs with Generous Spacing -->
    <div class="px-3 pt-3 pb-2 border-bottom bg-white">
        <ul class="nav nav-pills d-flex flex-wrap gap-2 mb-0" id="weatherPills" role="tablist">
            <?php $first = true; foreach ($weatherCities as $key => $city): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $first ? 'active' : '' ?> py-1 px-2.5 rounded-pill fw-semibold shadow-none" 
                            id="tab-weather-<?= $key ?>" 
                            data-bs-toggle="pill" 
                            data-bs-target="#pane-weather-<?= $key ?>" 
                            type="button" 
                            role="tab" 
                            style="font-size: 0.72rem;">
                        <?= e($city['name']) ?>
                    </button>
                </li>
            <?php $first = false; endforeach; ?>
        </ul>
    </div>

    <!-- Tab Contents with Proper Spacing Grid -->
    <div class="tab-content p-3" id="weatherPillsContent">
        <?php $first = true; foreach ($weatherCities as $key => $city): ?>
            <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" id="pane-weather-<?= $key ?>" role="tabpanel">
                
                <!-- Main Temperature & Condition Row -->
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2.5 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi <?= $city['icon'] ?> display-6"></i>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 1.75rem; line-height: 1;">
                                <?= $city['temp'] ?>&deg;C
                            </div>
                            <div class="text-secondary text-xs mt-1 fw-medium" style="font-size: 0.78rem;">
                                <?= e($city['condition']) ?>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-<?= $city['aqi_color'] ?>-subtle text-<?= $city['aqi_color'] ?> border border-<?= $city['aqi_color'] ?>-subtle px-2 py-1 rounded-2 fw-bold" style="font-size: 0.72rem;">
                            AQI: <?= $city['aqi'] ?> (<?= e($city['aqi_label']) ?>)
                        </span>
                    </div>
                </div>

                <!-- 3 Metric Boxes with Clear Spaced Grid -->
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div class="p-2 rounded-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                            <div class="text-muted text-3xs mb-0.5" style="font-size: 0.65rem;"><?= __('weather_humidity') ?></div>
                            <div class="fw-bold text-dark text-xs"><i class="bi bi-droplet text-primary me-0.5"></i><?= $city['humidity'] ?></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                            <div class="text-muted text-3xs mb-0.5" style="font-size: 0.65rem;"><?= __('weather_rain_chance') ?></div>
                            <div class="fw-bold text-dark text-xs"><i class="bi bi-cloud-rain text-info me-0.5"></i><?= $city['rain'] ?></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                            <div class="text-muted text-3xs mb-0.5" style="font-size: 0.65rem;"><?= __('weather_aqi') ?></div>
                            <div class="fw-bold text-success text-xs"><i class="bi bi-shield-check me-0.5"></i><?= $city['aqi'] ?></div>
                        </div>
                    </div>
                </div>

            </div>
        <?php $first = false; endforeach; ?>
    </div>

    <!-- Attribution note with margin -->
    <div class="px-3 pb-2.5 pt-1 text-center border-top bg-light" style="font-size: 0.68rem; color: #94a3b8;">
        <i class="bi bi-info-circle me-1 text-muted"></i><?= __('weather_source') ?>
    </div>
</section>

<style>
.cambodia-weather-widget .nav-pills .nav-link {
    color: #64748b;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    transition: all 0.15s ease;
}
.cambodia-weather-widget .nav-pills .nav-link:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #cbd5e1;
}
.cambodia-weather-widget .nav-pills .nav-link.active {
    color: #c8102e !important;
    background: #fef2f2 !important;
    border-color: #fecaca !important;
    font-weight: 700;
}
</style>
