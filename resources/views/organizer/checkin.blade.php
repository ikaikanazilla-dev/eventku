@extends('layouts.organizer')

@section('organizer-content')
    @php($checkinResult = session('checkin_result'))

    <section class="section checkin-page" data-checkin-page data-checkin-autostart="true">
        <div class="container" style="max-width:1000px">
            <div class="checkin-heading">
                <div>
                    <span class="eyebrow">GATE EVENTKU</span>
                    <h1>Check-in Peserta</h1>
                    <p class="muted">Pindai QR Code pada tiket peserta atau masukkan kode tiket untuk memeriksa keabsahannya.</p>
                </div>
                <a class="btn btn-soft" href="{{ route('organizer.registrations') }}">Daftar Peserta</a>
            </div>

            <div class="checkin-steps" aria-label="Langkah pemeriksaan tiket">
                <div class="checkin-step"><span>1</span><div><strong>Pilih cara</strong><small>Scan QR atau kode tiket</small></div></div>
                <div class="checkin-step"><span>2</span><div><strong>Periksa tiket</strong><small>Arahkan kamera atau masukkan kode</small></div></div>
                <div class="checkin-step"><span>3</span><div><strong>Lihat hasil</strong><small>Ikuti petunjuk di layar</small></div></div>
            </div>

            <div class="checkin-layout">
                <div class="card checkin-card">
                    <div class="checkin-card-heading">
                        <div>
                            <span class="eyebrow">PEMERIKSAAN TIKET OTOMATIS</span>
                            <h2>Pilih cara pemeriksaan tiket</h2>
                        </div>
                        <span class="badge badge-success">{{ $event->name }}</span>
                    </div>

                    <div class="checkin-tabs" role="tablist" aria-label="Cara pemeriksaan tiket">
                        <button class="checkin-tab is-active" type="button" role="tab" aria-selected="true" aria-controls="qr-panel" data-checkin-tab="qr">
                            <span class="checkin-tab-icon" aria-hidden="true">⌁</span>
                            <span><strong>Scan QR Code</strong><small>Gunakan kamera tiket</small></span>
                        </button>
                        <button class="checkin-tab" type="button" role="tab" aria-selected="false" aria-controls="manual-panel" data-checkin-tab="manual">
                            <span class="checkin-tab-icon" aria-hidden="true">#</span>
                            <span><strong>Masukkan Kode Tiket</strong><small>Periksa secara manual</small></span>
                        </button>
                    </div>

                    <div class="checkin-panel" id="qr-panel" role="tabpanel" data-checkin-panel="qr">
                        <div class="camera-frame" data-camera-frame>
                            <video id="scanner-video" playsinline muted aria-label="Pratinjau kamera untuk scan QR"></video>
                            <div class="camera-placeholder" data-camera-placeholder>
                                <span class="camera-placeholder-icon" aria-hidden="true">⌁</span>
                                <strong>Kamera siap digunakan</strong>
                                <small>Arahkan kamera ke QR Code tiket peserta.</small>
                            </div>
                            <div class="camera-guide" aria-hidden="true"></div>
                            <p class="camera-instruction" data-camera-instruction>Arahkan QR tiket ke dalam kotak</p>
                            <div class="camera-decode-overlay" data-camera-decode-overlay hidden role="status" aria-live="polite">
                                <span class="camera-decode-check" aria-hidden="true">&#10003;</span>
                                <strong>QR BERHASIL TERBACA</strong>
                                <small>Memvalidasi tiket...</small>
                            </div>
                        </div>

                        <div class="scanner-toolbar">
                            <label class="camera-choice" for="camera-select">
                                <span>Kamera</span>
                                <select id="camera-select" class="select" disabled>
                                    <option>Kamera otomatis</option>
                                </select>
                            </label>
                        <span id="scanner-status" class="scanner-status" role="status" aria-live="polite">Kamera siap digunakan</span>
                        </div>

                        <div class="checkin-actions">
                            <button type="button" id="start-scanner" class="btn btn-primary">Mulai Scan QR</button>
                            <button type="button" id="stop-scanner" class="btn btn-soft" hidden>Hentikan Scan</button>
                        </div>
                        <p class="checkin-help">Pastikan QR Code terlihat jelas, cukup besar, dan berada di dalam kotak panduan.</p>
                    </div>

                    <div class="checkin-panel" id="manual-panel" role="tabpanel" data-checkin-panel="manual" hidden>
                        <form id="checkin-form" method="POST" action="{{ route('organizer.checkin.store', $event) }}" class="manual-checkin-form">
                            @csrf
                            <input type="hidden" name="method" id="checkin-method" value="manual">
                            <div class="form-group">
                                <label for="ticket-code">Kode Tiket</label>
                                <input class="input" id="ticket-code" name="code" value="{{ old('code') }}" placeholder="Contoh: EVT-XXXXXXXX" autocomplete="off" required>
                                <small class="form-help">Masukkan kode yang tertera pada tiket peserta.</small>
                                @error('code')<small class="field-error">{{ $message }}</small>@enderror
                            </div>
                            <button id="manual-submit" class="btn btn-primary" type="submit">Periksa Tiket</button>
                        </form>
                    </div>
                </div>

                <aside class="checkin-result-card {{ $checkinResult ? 'has-result result-'.$checkinResult['tone'] : '' }}" id="checkin-result" aria-live="polite">
                    @if($checkinResult)
                        @if($checkinResult['success'])
                            <div class="result-icon" aria-hidden="true">✓</div>
                        @elseif($checkinResult['tone'] === 'warning')
                            <div class="result-icon result-icon-warning" aria-hidden="true">!</div>
                        @else
                            <div class="result-icon result-icon-error" aria-hidden="true">×</div>
                        @endif
                        <span class="eyebrow">HASIL PEMERIKSAAN</span>
                        <h2>{{ $checkinResult['title'] }}</h2>
                        <p>{{ $checkinResult['message'] }}</p>

                        @if($checkinResult['success'])
                            <div class="result-details">
                                @if($checkinResult['participant_name'])<span><small>Peserta</small><strong>{{ $checkinResult['participant_name'] }}</strong></span>@endif
                                @if($checkinResult['event_name'])<span><small>Event</small><strong>{{ $checkinResult['event_name'] }}</strong></span>@endif
                                @if($checkinResult['checked_in_at'])<span><small>Waktu check-in</small><strong>{{ \Carbon\Carbon::parse($checkinResult['checked_in_at'])->timezone(config('app.timezone'))->format('d M Y, H:i') }}</strong></span>@endif
                            </div>
                            <a class="btn btn-primary result-action" href="{{ route('organizer.checkin', $event) }}">Scan Tiket Berikutnya</a>
                            <a class="btn btn-soft result-action" href="{{ route('organizer.registrations') }}">Kembali ke Daftar Peserta</a>
                        @else
                            @if($checkinResult['code'] === 'already_used' && $checkinResult['previous_checkin_at'])
                                <p class="result-note">Check-in sebelumnya: {{ \Carbon\Carbon::parse($checkinResult['previous_checkin_at'])->timezone(config('app.timezone'))->format('d M Y, H:i') }}</p>
                            @endif
                            <button class="btn btn-primary result-action" type="button" data-checkin-reset>Coba Lagi</button>
                            @if($checkinResult['method'] === 'qr')<button class="btn btn-soft result-action" type="button" data-checkin-mode="qr">Scan Ulang</button>@endif
                        @endif
                    @else
                        <div class="result-icon result-icon-neutral" aria-hidden="true">i</div>
                        <span class="eyebrow">HASIL PEMERIKSAAN</span>
                        <h2>Siap memeriksa tiket</h2>
                        <p>Hasil pemeriksaan akan muncul di sini. Pastikan tiket peserta sudah siap.</p>
                        <div class="result-checklist"><span>✓ QR Code atau kode tiket terbaca</span><span>✓ Tiket terdaftar untuk event ini</span><span>✓ Peserta belum melakukan check-in</span></div>
                    @endif
                </aside>
            </div>
        </div>
    </section>

    <script>
        (() => {
            const video = document.getElementById('scanner-video');
            const placeholder = document.querySelector('[data-camera-placeholder]');
            const startButton = document.getElementById('start-scanner');
            const stopButton = document.getElementById('stop-scanner');
            const statusElement = document.getElementById('scanner-status');
            const cameraSelect = document.getElementById('camera-select');
            const form = document.getElementById('checkin-form');
            const codeInput = document.getElementById('ticket-code');
            const methodInput = document.getElementById('checkin-method');
            const submitButton = document.getElementById('manual-submit');
            const resultCard = document.getElementById('checkin-result');
            const decodeOverlay = document.querySelector('[data-camera-decode-overlay]');
            const tabs = [...document.querySelectorAll('[data-checkin-tab]')];
            const panels = [...document.querySelectorAll('[data-checkin-panel]')];
            let stream = null;
            let detector = null;
            let zxingReader = null;
            let zxingControls = null;
            let scanning = false;
            let processing = false;
            let starting = false;
            let startRequest = 0;

            const setStatus = (message, state = '') => {
                statusElement.textContent = message;
                statusElement.dataset.state = state;
            };

            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            })[character]);

            const formatCheckinTime = (value) => {
                if (!value) return '';
                const date = new Date(value);
                return Number.isNaN(date.getTime())
                    ? value
                    : date.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
            };

            const showDecodeSuccess = () => {
                decodeOverlay.hidden = false;
                setStatus('QR BERHASIL TERBACA. Memvalidasi tiket...', 'decoded');
            };

            const hideDecodeSuccess = () => {
                decodeOverlay.hidden = true;
            };

            const renderCheckinResult = (result) => {
                const tone = ['success', 'warning', 'error'].includes(result.tone) ? result.tone : 'error';
                const success = result.success === true;
                const icon = success
                    ? '<div class="result-icon" aria-hidden="true">&#10003;</div>'
                    : tone === 'warning'
                        ? '<div class="result-icon result-icon-warning" aria-hidden="true">!</div>'
                        : '<div class="result-icon result-icon-error" aria-hidden="true">&times;</div>';
                const details = success ? [
                    ['Peserta', result.participant_name],
                    ['Event', result.event_name],
                    ['Waktu check-in', formatCheckinTime(result.checked_in_at)],
                ].filter(([, value]) => value).map(([label, value]) => `<span><small>${escapeHtml(label)}</small><strong>${escapeHtml(value)}</strong></span>`).join('') : '';
                const previousCheckin = result.code === 'already_used' && result.previous_checkin_at
                    ? `<p class="result-note">Check-in sebelumnya: ${escapeHtml(formatCheckinTime(result.previous_checkin_at))}</p>`
                    : '';

                resultCard.className = `checkin-result-card has-result result-${tone}`;
                resultCard.innerHTML = `${icon}
                    <span class="eyebrow">HASIL PEMERIKSAAN</span>
                    <h2>${escapeHtml(result.title)}</h2>
                    <p>${escapeHtml(result.message)}</p>
                    ${success ? `<div class="result-details">${details}</div>` : previousCheckin}
                    <button class="btn btn-primary result-action" type="button" data-checkin-mode="qr">${success ? 'Scan Tiket Berikutnya' : 'Scan Ulang'}</button>
                    ${success ? '' : '<button class="btn btn-soft result-action" type="button" data-checkin-reset>Coba Lagi</button>'}`;
            };

            const submitCheckinForm = async () => {
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                        body: new FormData(form),
                    });
                    const payload = await response.json();

                    if (!payload.result) throw new Error('Respons validasi tiket tidak lengkap.');

                    renderCheckinResult(payload.result);
                    hideDecodeSuccess();
                    processing = false;
                    submitButton.disabled = false;
                    submitButton.textContent = 'Periksa Tiket';
                    setStatus(
                        payload.success ? 'Tiket valid. Siap memindai peserta berikutnya.' : `QR terbaca. ${payload.result.message}`,
                        payload.success ? 'active' : 'error',
                    );

                    window.setTimeout(() => {
                        const qrTabActive = document.querySelector('[data-checkin-tab="qr"]')?.classList.contains('is-active');
                        if (qrTabActive && !processing) resetCheckin('qr');
                    }, 900);
                } catch (error) {
                    processing = false;
                    submitButton.disabled = false;
                    submitButton.textContent = 'Periksa Tiket';
                    hideDecodeSuccess();
                    startButton.hidden = false;
                    startButton.textContent = 'Coba Lagi';
                    setStatus('Validasi tiket gagal. Coba lagi atau gunakan kode manual.', 'error');
                }
            };

            const submitScannedCode = (rawValue) => {
                const decodedValue = rawValue?.trim();
                if (!decodedValue || processing) return;
                processing = true;
                scanning = false;
                codeInput.value = decodedValue;
                methodInput.value = 'qr';
                stopScanner();
                showDecodeSuccess();
                submitCheckinForm();
            };

            const stopScanner = () => {
                startRequest += 1;
                starting = false;
                scanning = false;
                zxingControls?.stop();
                zxingControls = null;
                zxingReader?.reset?.();
                zxingReader = null;
                detector = null;
                if (stream) stream.getTracks().forEach((track) => track.stop());
                stream = null;
                video.srcObject = null;
                video.hidden = true;
                placeholder.hidden = false;
                hideDecodeSuccess();
                stopButton.hidden = true;
                startButton.hidden = false;
                startButton.disabled = false;
                startButton.textContent = 'Mulai Scan QR';
            };

            const loadFallbackReader = async () => {
                if (window.EventkuQrReader) return new window.EventkuQrReader();
                if (window.EventkuQrReaderLoadError) throw new Error('QR fallback gagal dimuat.');

                await new Promise((resolve, reject) => {
                    const ready = () => resolve();
                    document.addEventListener('eventku:qr-reader-ready', ready, { once: true });
                    window.setTimeout(() => reject(new Error('QR fallback timeout.')), 10000);
                });

                return new window.EventkuQrReader();
            };

            const waitForVideoReady = async () => {
                if (video.readyState >= HTMLMediaElement.HAVE_CURRENT_DATA && video.videoWidth > 0 && video.videoHeight > 0) return;

                await new Promise((resolve, reject) => {
                    const checkReady = () => {
                        if (video.videoWidth > 0 && video.videoHeight > 0) {
                            cleanup();
                            resolve();
                        }
                    };
                    const cleanup = () => {
                        video.removeEventListener('loadedmetadata', checkReady);
                        video.removeEventListener('canplay', checkReady);
                        window.clearTimeout(timeout);
                    };
                    const timeout = window.setTimeout(() => {
                        cleanup();
                        reject(new Error('Video kamera belum siap.'));
                    }, 5000);
                    video.addEventListener('loadedmetadata', checkReady);
                    video.addEventListener('canplay', checkReady);
                    checkReady();
                });
            };

            const loadCameras = async () => {
                if (!navigator.mediaDevices?.enumerateDevices) return [];
                const selectedDeviceId = cameraSelect.value;
                const devices = (await navigator.mediaDevices.enumerateDevices()).filter((device) => device.kind === 'videoinput');
                cameraSelect.innerHTML = '';
                if (!devices.length) {
                    cameraSelect.add(new Option('Kamera otomatis', ''));
                    cameraSelect.disabled = true;
                    return devices;
                }
                devices.forEach((device, index) => cameraSelect.add(new Option(device.label || `Kamera ${index + 1}`, device.deviceId)));
                const preferredDevice = devices.find((device) => /back|rear|environment|belakang/i.test(device.label)) ?? devices[0];
                cameraSelect.value = devices.some((device) => device.deviceId === selectedDeviceId)
                    ? selectedDeviceId
                    : preferredDevice.deviceId;
                cameraSelect.disabled = devices.length < 2;
                return devices;
            };

            const scan = async () => {
                if (!scanning || processing) return;
                try {
                    const found = await detector.detect(video);
                    if (found.length && found[0].rawValue) {
                        submitScannedCode(found[0].rawValue);
                        return;
                    }
                } catch (error) {
                    // Kamera tetap aktif; kegagalan satu frame tidak menghentikan scan.
                }
                if (scanning) window.requestAnimationFrame(scan);
            };

            const startScanner = async () => {
                if (processing || scanning || starting) return;
                if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                    setStatus('Kamera membutuhkan koneksi HTTPS. Gunakan kode tiket manual.', 'error');
                    return;
                }
                if (!navigator.mediaDevices?.getUserMedia) {
                    setStatus('Kamera tidak tersedia. Gunakan kode tiket manual.', 'error');
                    return;
                }

                const request = ++startRequest;
                starting = true;
                startButton.disabled = true;
                startButton.textContent = 'Menghubungkan kamera...';
                setStatus('Menghubungkan kamera...', 'loading');

                const isCurrentRequest = () => request === startRequest;

                try {
                    const selectedDeviceId = cameraSelect.value || undefined;
                    let decoder = 'zxing';
                    detector = null;
                    try {
                        setStatus('Memuat decoder QR...', 'loading');
                        zxingReader = await loadFallbackReader();
                    } catch (error) {
                        if (!('BarcodeDetector' in window)) throw new Error('QR fallback gagal dimuat.');
                        try {
                            detector = new BarcodeDetector({ formats: ['qr_code'] });
                            decoder = 'native';
                        } catch (error) {
                            throw new Error('QR fallback gagal dimuat.');
                        }
                    }

                    if (decoder === 'native') {
                        setStatus('Meminta izin kamera...', 'loading');
                        const candidateStream = await navigator.mediaDevices.getUserMedia({
                            video: selectedDeviceId ? { deviceId: { exact: selectedDeviceId } } : { facingMode: { ideal: 'environment' } },
                        });
                        if (!isCurrentRequest()) {
                            candidateStream.getTracks().forEach((track) => track.stop());
                            return;
                        }
                        stream = candidateStream;
                        video.srcObject = stream;
                        await video.play();
                        await waitForVideoReady();
                    } else {
                        if (!isCurrentRequest()) return;
                        setStatus('Meminta izin kamera...', 'loading');
                    }

                    if (!isCurrentRequest()) return;

                    video.hidden = false;
                    placeholder.hidden = true;
                    scanning = true;
                    startButton.hidden = true;
                    stopButton.hidden = false;
                    await loadCameras();
                    if (!isCurrentRequest()) return;
                    if (decoder === 'native') {
                        setStatus('Mencari QR...', 'active');
                        scan();
                    } else {
                        const controls = await zxingReader.decodeFromVideoDevice(selectedDeviceId, video, (result) => {
                            if (result) {
                                const rawValue = typeof result.getText === 'function' ? result.getText() : result.text;
                                submitScannedCode(rawValue);
                            }
                        });
                        if (!isCurrentRequest()) {
                            controls.stop();
                            return;
                        }
                        zxingControls = controls;
                        await waitForVideoReady();
                        setStatus('Mencari QR...', 'active');
                    }
                } catch (error) {
                    stopScanner();
                    const message = error?.message?.includes('QR fallback')
                        ? 'Scanner QR gagal dimuat. Gunakan kode tiket manual.'
                        : 'Kamera ditolak atau tidak tersedia. Gunakan kode tiket manual.';
                    setStatus(message, 'error');
                    startButton.textContent = 'Coba Lagi';
                } finally {
                    if (isCurrentRequest()) {
                        starting = false;
                        startButton.disabled = false;
                    }
                }
            };

            const resetCheckin = (mode = 'manual') => {
                processing = false;
                methodInput.value = mode;
                if (mode === 'manual') {
                    codeInput.focus();
                } else {
                    startScanner();
                }
            };

            resultCard?.addEventListener('click', (event) => {
                const action = event.target.closest('[data-checkin-reset], [data-checkin-mode="qr"]');
                if (!action) return;
                if (action.matches('[data-checkin-reset]')) resetCheckin('manual');
                if (action.matches('[data-checkin-mode="qr"]')) {
                    document.querySelector('[data-checkin-tab="qr"]').click();
                    resetCheckin('qr');
                }
            });

            tabs.forEach((tab) => tab.addEventListener('click', () => {
                const mode = tab.dataset.checkinTab;
                tabs.forEach((item) => {
                    const active = item === tab;
                    item.classList.toggle('is-active', active);
                    item.setAttribute('aria-selected', String(active));
                });
                panels.forEach((panel) => { panel.hidden = panel.dataset.checkinPanel !== mode; });
                if (mode === 'manual') stopScanner();
                if (mode === 'qr') startScanner();
            }));

            startButton.addEventListener('click', startScanner);
            stopButton.addEventListener('click', () => { stopScanner(); setStatus('Kamera siap digunakan'); });
            cameraSelect.addEventListener('change', () => { if (scanning) { stopScanner(); startScanner(); } });
            form?.addEventListener('submit', (event) => {
                if (methodInput.value === 'qr') {
                    event.preventDefault();
                    if (!processing) {
                        processing = true;
                        submitCheckinForm();
                    }
                    return;
                }
                if (processing) { event.preventDefault(); return; }
                processing = true;
                submitButton.disabled = true;
                submitButton.textContent = 'Sedang memeriksa tiket...';
            });
            loadCameras().catch(() => {});
            window.addEventListener('beforeunload', stopScanner);
            window.addEventListener('pagehide', stopScanner);

            if (document.querySelector('[data-checkin-autostart="true"]')) startScanner();
        })();
    </script>
@endsection
