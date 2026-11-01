<?php
/**
 * Component: Smart Audio News Player (Khmer & English Text-to-Speech)
 * news-platform / templates / components / audio-player.php
 */

if (!isset($article) || empty($article)) {
    return;
}

$audioTitle = article_title($article);
$audioSummary = article_summary($article);
$audioBodyRaw = strip_tags($article['content'] ?? '');
$audioFullText = trim($audioTitle . '. ' . $audioSummary . '. ' . $audioBodyRaw);
$audioFullTextJson = json_encode($audioFullText, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
$currentLang = $_SESSION['lang'] ?? 'en';
$isKhmer = ($currentLang === 'kh' || $currentLang === 'km');
?>

<div class="np-audio-player-card my-3 p-2.5 p-sm-3 bg-white border rounded-3 shadow-2xs" style="border-color: #e5e7eb;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
        
        <!-- Left: Audio Title & Wave Indicator -->
        <div class="d-flex align-items-center gap-2.5 min-w-0">
            <button type="button" id="npAudioPlayBtn" class="btn btn-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                    style="width: 42px; height: 42px; background: #c8102e; border: none;" title="<?= __('audio_news_listen') ?>">
                <i class="bi bi-play-fill fs-4" id="npAudioPlayIcon" style="margin-left: 2px;"></i>
            </button>
            <div class="min-w-0">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark text-xs" style="font-size: 0.88rem; line-height: 1.2;">
                        <?= __('audio_news_title') ?>
                    </span>
                    <!-- Animated Wave Bars (Active during playback) -->
                    <span id="npAudioWave" class="np-audio-wave d-none">
                        <span class="bar bar-1"></span>
                        <span class="bar bar-2"></span>
                        <span class="bar bar-3"></span>
                        <span class="bar bar-4"></span>
                    </span>
                </div>
                <div class="text-muted text-3xs mt-0.5 text-truncate" id="npAudioStatusText" style="font-size: 0.72rem;">
                    <?= __('audio_news_listen') ?> &bull; AI Voice (<?= $isKhmer ? 'ភាសាខ្មែរ' : 'English' ?>)
                </div>
            </div>
        </div>

        <!-- Right: Audio Controls (Stop, Speed, Mute) -->
        <div class="d-flex align-items-center gap-1.5 ms-auto">
            <!-- Speed Button -->
            <button type="button" id="npAudioSpeedBtn" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5 py-1 text-2xs fw-bold" style="font-size: 0.72rem;" title="<?= __('audio_news_speed') ?>">
                1.0x
            </button>

            <!-- Stop Button -->
            <button type="button" id="npAudioStopBtn" class="btn btn-outline-secondary btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center"
                    style="width: 32px; height: 32px;" title="<?= __('audio_news_stop') ?>" disabled>
                <i class="bi bi-stop-fill" style="font-size: 0.9rem;"></i>
            </button>
        </div>

    </div>

    <!-- Live Audio Progress Bar -->
    <div class="progress mt-2.5" style="height: 4px; background: #f1f5f9; border-radius: 2px;">
        <div id="npAudioProgressBar" class="progress-bar bg-danger" role="progressbar" style="width: 0%; transition: width 0.2s linear;"></div>
    </div>
</div>

<style>
.np-audio-wave {
    display: inline-flex;
    align-items: flex-end;
    gap: 2px;
    height: 14px;
}
.np-audio-wave .bar {
    width: 2.5px;
    background: #c8102e;
    border-radius: 1px;
    animation: npSoundWave 0.8s ease-in-out infinite alternate;
}
.np-audio-wave .bar-1 { height: 40%; animation-delay: 0.1s; }
.np-audio-wave .bar-2 { height: 90%; animation-delay: 0.3s; }
.np-audio-wave .bar-3 { height: 60%; animation-delay: 0.2s; }
.np-audio-wave .bar-4 { height: 100%; animation-delay: 0.4s; }

@keyframes npSoundWave {
    0% { height: 20%; }
    100% { height: 100%; }
}
</style>

<script>
(function() {
    const fullText = <?= $audioFullTextJson ?>;
    const isKhmerLang = <?= $isKhmer ? 'true' : 'false' ?>;
    const playBtn = document.getElementById('npAudioPlayBtn');
    const playIcon = document.getElementById('npAudioPlayIcon');
    const stopBtn = document.getElementById('npAudioStopBtn');
    const speedBtn = document.getElementById('npAudioSpeedBtn');
    const waveIndicator = document.getElementById('npAudioWave');
    const statusText = document.getElementById('npAudioStatusText');
    const progressBar = document.getElementById('npAudioProgressBar');

    if (!('speechSynthesis' in window)) {
        if (playBtn) playBtn.disabled = true;
        if (statusText) statusText.textContent = 'Speech synthesis not supported in this browser.';
        return;
    }

    const synth = window.speechSynthesis;
    let chunks = [];
    let currentChunkIndex = 0;
    let isPlaying = false;
    let currentRate = 1.0;
    const rates = [1.0, 1.25, 1.5];

    // Split text into readable sentences/clauses
    function prepareChunks(text) {
        // Split on punctuation or Khmer spaces/khan
        const raw = text.split(/([.?!។\n]+)/g);
        const result = [];
        let buffer = '';
        for (let i = 0; i < raw.length; i++) {
            buffer += raw[i];
            if (buffer.length > 80 || i === raw.length - 1) {
                const trimmed = buffer.trim();
                if (trimmed.length > 0) result.push(trimmed);
                buffer = '';
            }
        }
        return result.length > 0 ? result : [text];
    }

    chunks = prepareChunks(fullText);

    function getBestVoice() {
        const voices = synth.getVoices();
        if (isKhmerLang) {
            // Find Khmer voice
            const khVoice = voices.find(v => v.lang && (v.lang.toLowerCase().includes('km') || v.lang.toLowerCase().includes('kh')));
            if (khVoice) return khVoice;
        }
        // English or default voice
        return voices.find(v => v.lang && v.lang.toLowerCase().startsWith('en')) || voices[0] || null;
    }

    function speakCurrentChunk() {
        if (currentChunkIndex >= chunks.length) {
            stopAudio();
            return;
        }

        const utterance = new SpeechSynthesisUtterance(chunks[currentChunkIndex]);
        utterance.rate = currentRate;
        const voice = getBestVoice();
        if (voice) {
            utterance.voice = voice;
            utterance.lang = voice.lang;
        } else if (isKhmerLang) {
            utterance.lang = 'km-KH';
        }

        utterance.onend = function() {
            if (isPlaying) {
                currentChunkIndex++;
                updateProgress();
                speakCurrentChunk();
            }
        };

        utterance.onerror = function() {
            if (isPlaying) {
                currentChunkIndex++;
                updateProgress();
                speakCurrentChunk();
            }
        };

        synth.speak(utterance);
    }

    function updateProgress() {
        if (chunks.length === 0) return;
        const pct = Math.min(100, Math.round(((currentChunkIndex + 1) / chunks.length) * 100));
        if (progressBar) progressBar.style.width = pct + '%';
    }

    function startAudio() {
        if (synth.paused) {
            synth.resume();
            setPlayingUi(true);
            return;
        }
        synth.cancel();
        isPlaying = true;
        setPlayingUi(true);
        speakCurrentChunk();
    }

    function pauseAudio() {
        if (synth.speaking && !synth.paused) {
            synth.pause();
            isPlaying = false;
            setPlayingUi(false, true);
        }
    }

    function stopAudio() {
        synth.cancel();
        isPlaying = false;
        currentChunkIndex = 0;
        setPlayingUi(false, false);
        if (progressBar) progressBar.style.width = '0%';
    }

    function setPlayingUi(active, paused = false) {
        if (playIcon) {
            playIcon.className = active ? 'bi bi-pause-fill fs-4' : 'bi bi-play-fill fs-4';
            if (!active) playIcon.style.marginLeft = '2px';
            else playIcon.style.marginLeft = '0';
        }
        if (waveIndicator) {
            if (active) waveIndicator.classList.remove('d-none');
            else waveIndicator.classList.add('d-none');
        }
        if (stopBtn) stopBtn.disabled = (!active && !paused);
        if (statusText) {
            if (active) statusText.textContent = <?= json_encode(__('audio_news_playing'), JSON_UNESCAPED_UNICODE) ?>;
            else if (paused) statusText.textContent = <?= json_encode(__('audio_news_paused'), JSON_UNESCAPED_UNICODE) ?>;
            else statusText.textContent = <?= json_encode(__('audio_news_listen'), JSON_UNESCAPED_UNICODE) ?> + ' • AI Voice (' + (isKhmerLang ? 'ភាសាខ្មែរ' : 'English') + ')';
        }
    }

    if (playBtn) {
        playBtn.addEventListener('click', function() {
            if (isPlaying) {
                pauseAudio();
            } else {
                startAudio();
            }
        });
    }

    if (stopBtn) {
        stopBtn.addEventListener('click', stopAudio);
    }

    if (speedBtn) {
        speedBtn.addEventListener('click', function() {
            const currentIdx = rates.indexOf(currentRate);
            const nextIdx = (currentIdx + 1) % rates.length;
            currentRate = rates[nextIdx];
            speedBtn.textContent = currentRate.toFixed(1) + 'x';
            if (isPlaying) {
                synth.cancel();
                speakCurrentChunk();
            }
        });
    }

    // Stop speaking when user navigates away
    window.addEventListener('beforeunload', function() {
        if (synth) synth.cancel();
    });
})();
</script>
