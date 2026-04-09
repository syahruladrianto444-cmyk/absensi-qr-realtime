@extends('layouts.app')
@section('title', 'Absensi — ' . $event->nama_event)

@section('styles')
<style>
.absensi-container {
    min-height: 100vh;
    display: flex; flex-direction: column;
    align-items: center; justify-content: flex-start;
    padding: 20px 16px 40px;
    max-width: 480px;
    margin: 0 auto;
}

/* Header */
.absensi-header {
    width: 100%; text-align: center;
    padding: 24px 0 20px;
}
.absensi-logo {
    font-size: 36px; margin-bottom: 8px;
    animation: float 3s ease-in-out infinite;
    display: inline-block;
}
.absensi-event-name {
    font-size: 20px; font-weight: 800;
    letter-spacing: -0.02em; margin-bottom: 4px;
}
.absensi-event-time {
    font-size: 13px; color: var(--text-muted);
}

/* State Cards */
.state-card {
    width: 100%;
    padding: 28px 24px;
    margin-bottom: 16px;
    text-align: center;
    animation: fadeInUp 0.6s cubic-bezier(0.16,1,0.3,1) forwards;
}

/* Loading State */
#stateLoading .loading-icon {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: var(--gradient-primary);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    font-size: 30px;
    box-shadow: 0 0 40px var(--accent-glow);
    animation: pulse-glow 2s ease-in-out infinite;
}
#stateLoading h2 { font-size: 18px; font-weight: 700; margin-bottom: 10px; }
#stateLoading p { font-size: 14px; color: var(--text-muted); }
.loading-steps {
    display: flex; flex-direction: column; gap: 8px;
    margin-top: 20px; text-align: left;
}
.step-item {
    display: flex; align-items: center; gap: 10px;
    font-size: 13px; color: var(--text-muted);
    padding: 8px 12px;
    border-radius: 8px;
    transition: var(--transition);
}
.step-item.active { background: rgba(99,102,241,0.1); color: var(--text-primary); }
.step-item.done { color: var(--success); }
.step-icon { width: 20px; text-align: center; }
.step-spinner { width: 14px; height: 14px; border: 2px solid rgba(99,102,241,0.3); border-top-color: var(--accent-primary); border-radius: 50%; animation: spin 0.7s linear infinite; }

/* Form State */
#stateForm { display: none; }
#stateForm .form-icon {
    width: 64px; height: 64px;
    border-radius: 18px;
    background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(16,185,129,0.1));
    border: 1px solid rgba(16,185,129,0.3);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px; font-size: 26px;
}
.distance-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px;
    background: rgba(16,185,129,0.1);
    border: 1px solid rgba(16,185,129,0.2);
    border-radius: var(--radius-full);
    font-size: 13px; color: var(--success);
    font-weight: 600; margin-bottom: 20px;
}

/* Embed iframe */
.form-embed {
    width: 100%; border: none;
    border-radius: var(--radius);
    background: white;
    min-height: 500px;
}

/* Manual form fallback */
.manual-form { text-align: left; }
.manual-form h3 { font-size: 16px; font-weight: 700; margin-bottom: 16px; text-align: center; }

/* Rejected State */
#stateRejected { display: none; }
.reject-icon {
    width: 80px; height: 80px; border-radius: 50%;
    background: rgba(239,68,68,0.15);
    border: 2px solid rgba(239,68,68,0.3);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px; font-size: 32px;
    animation: shake 0.6s ease-in-out;
}
.distance-meter {
    margin: 16px auto;
    width: 100%; max-width: 280px;
}
.distance-meter-bar {
    height: 8px; border-radius: 4px;
    background: rgba(255,255,255,0.1);
    overflow: hidden; margin-bottom: 6px;
}
.distance-meter-fill {
    height: 100%; border-radius: 4px;
    background: linear-gradient(90deg, var(--success), var(--danger));
    transition: width 1s cubic-bezier(0.34,1.56,0.64,1);
}
.distance-labels {
    display: flex; justify-content: space-between;
    font-size: 11px; color: var(--text-muted);
}
</style>
@endsection

@section('content')
<div class="absensi-container">
    {{-- Header --}}
    <div class="absensi-header animate-in">
        <div class="absensi-logo">📡</div>
        <h1 class="absensi-event-name">{{ $event->nama_event }}</h1>
        <p class="absensi-event-time">
            <i class="fas fa-clock"></i>
            {{ $event->start_time->format('d M Y H:i') }} — {{ $event->end_time->format('H:i') }} WIB
        </p>
    </div>

    {{-- STATE: Loading / Detecting Location --}}
    <div class="state-card glass" id="stateLoading">
        <div class="loading-icon">📍</div>
        <h2>Mendeteksi Lokasi</h2>
        <p>Pastikan GPS Anda aktif dan izinkan akses lokasi</p>
        <div class="loading-steps">
            <div class="step-item active" id="step1">
                <span class="step-icon"><div class="step-spinner"></div></span>
                <span>Meminta izin lokasi GPS...</span>
            </div>
            <div class="step-item" id="step2">
                <span class="step-icon">📡</span>
                <span>Menghitung jarak ke kampus...</span>
            </div>
            <div class="step-item" id="step3">
                <span class="step-icon">🛡️</span>
                <span>Memvalidasi keamanan data...</span>
            </div>
        </div>
    </div>

    {{-- STATE: Form (valid location) --}}
    <div id="stateForm" style="width:100%;">
        <div class="state-card glass" style="margin-bottom:16px;padding:20px 24px;">
            <div class="form-icon">✅</div>
            <h2 style="font-size:18px;font-weight:800;margin-bottom:6px;">Lokasi Tervalidasi!</h2>
            <div class="distance-badge">
                <i class="fas fa-map-marker-alt"></i>
                <span id="distanceInfo">0m dari lokasi event</span>
            </div>

            @if($event->google_form_url)
                {{-- Google Form Embed --}}
                <p style="font-size:14px;color:var(--text-muted);margin-bottom:16px;">Isi data absensi di bawah ini:</p>
                <iframe
                    src="{{ $event->google_form_url }}?embedded=true"
                    class="form-embed"
                    id="googleFormEmbed"
                    loading="lazy">
                    Memuat form...
                </iframe>
                <p style="font-size:12px;color:var(--text-muted);margin-top:10px;">
                    <i class="fas fa-info-circle"></i>
                    Data absensi tercatat otomatis di sistem setelah Anda submit form.
                </p>
            @else
                {{-- Built-in Form --}}
                <div class="manual-form">
                    <h3>Isi Data Absensi</h3>
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" id="inputNama" class="form-control" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">NPM *</label>
                        <input type="text" id="inputNpm" class="form-control" placeholder="cth: 22070140001" required>
                    </div>
                    <div id="submitError" class="alert alert-error hidden"></div>
                    <button onclick="submitAttendance()" id="submitBtn" class="btn btn-success btn-lg w-full">
                        <i class="fas fa-check-circle"></i> Kirim Absensi
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- STATE: Rejected --}}
    <div class="state-card glass" id="stateRejected">
        <div class="reject-icon">🚫</div>
        <h2 style="font-size:20px;font-weight:800;margin-bottom:8px;color:var(--danger);">Di Luar Radius!</h2>
        <p style="font-size:14px;color:var(--text-muted);margin-bottom:20px;">
            Anda tidak berada di area kampus yang ditentukan.
        </p>
        <div class="distance-meter">
            <div class="distance-meter-bar">
                <div class="distance-meter-fill" id="distanceFill" style="width:0%"></div>
            </div>
            <div class="distance-labels">
                <span>0m</span>
                <span id="radiusLabel">{{ $event->radius }}m (batas)</span>
            </div>
        </div>
        <div style="margin:16px 0;padding:16px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);border-radius:var(--radius-sm);">
            <p style="font-size:16px;font-weight:700;color:var(--danger);" id="rejectDistance">— meter</p>
            <p style="font-size:13px;color:var(--text-muted);">dari lokasi event (batas: {{ $event->radius }}m)</p>
        </div>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">
            Pastikan Anda berada di dalam gedung kampus dan coba lagi.
        </p>
        <button onclick="retryLocation()" class="btn btn-primary w-full">
            <i class="fas fa-redo"></i> Coba Lagi
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script>
const TOKEN = '{{ $token }}';
const EVENT_RADIUS = {{ $event->radius }};

let userLat = null, userLon = null, userAccuracy = null;

function setState(state) {
    ['stateLoading','stateForm','stateRejected'].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.style.display = 'none';
    });
    const target = document.getElementById(state);
    if(target) { target.style.display = 'block'; target.style.animation = 'fadeInUp 0.6s cubic-bezier(0.16,1,0.3,1) forwards'; }
}

function activateStep(n) {
    for(let i=1; i<=3; i++) {
        const el = document.getElementById('step'+i);
        if(!el) continue;
        if(i < n) { el.classList.remove('active'); el.classList.add('done'); el.querySelector('.step-icon').textContent = '✅'; }
        else if(i === n) { el.classList.add('active'); el.classList.remove('done'); }
    }
}

function startLocationDetection() {
    if (!navigator.geolocation) {
        alert('Browser Anda tidak mendukung geolocation. Gunakan Chrome atau Firefox terbaru.');
        return;
    }
    activateStep(1);
    navigator.geolocation.getCurrentPosition(
        onLocationSuccess,
        onLocationError,
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
}

async function onLocationSuccess(pos) {
    userLat = pos.coords.latitude;
    userLon = pos.coords.longitude;
    userAccuracy = pos.coords.accuracy;
    activateStep(2);

    await new Promise(r => setTimeout(r, 600));
    activateStep(3);

    try {
        const res = await fetch('/validate-location', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                token: TOKEN,
                latitude: userLat,
                longitude: userLon,
                accuracy: userAccuracy,
                altitude: pos.coords.altitude,
                speed: pos.coords.speed
            })
        });
        const data = await res.json();

        await new Promise(r => setTimeout(r, 400));

        if (data.valid) {
            document.getElementById('distanceInfo').textContent = data.distance + 'm dari lokasi event';
            setState('stateForm');
        } else if (data.type === 'expired') {
            window.location.replace('/absensi-expired');
        } else {
            document.getElementById('rejectDistance').textContent = data.distance + ' meter';
            const pct = Math.min((data.distance / (EVENT_RADIUS * 3)) * 100, 100);
            document.getElementById('distanceFill').style.width = pct + '%';
            setState('stateRejected');
        }
    } catch(e) {
        console.error(e);
        alert('Gagal menghubungi server. Periksa koneksi internet Anda.');
    }
}

function onLocationError(err) {
    let msg = 'Gagal mendapatkan lokasi.';
    if(err.code === 1) msg = 'Izin lokasi ditolak. Aktifkan izin GPS di browser Anda.';
    else if(err.code === 2) msg = 'Lokasi tidak tersedia. Pastikan GPS aktif.';
    else if(err.code === 3) msg = 'Timeout. Coba lagi di tempat dengan sinyal GPS lebih baik.';
    alert(msg);
}

function retryLocation() {
    setState('stateLoading');
    activateStep(1);
    startLocationDetection();
}

async function submitAttendance() {
    const nama = document.getElementById('inputNama')?.value.trim();
    const npm = document.getElementById('inputNpm')?.value.trim();
    const errEl = document.getElementById('submitError');
    const btn = document.getElementById('submitBtn');

    if(!nama || !npm) {
        errEl.textContent = 'Nama dan NPM wajib diisi!';
        errEl.classList.remove('hidden');
        return;
    }
    errEl.classList.add('hidden');
    btn.disabled = true;
    btn.innerHTML = '<div class="spinner" style="width:20px;height:20px;display:inline-block;vertical-align:middle;margin-right:8px;"></div> Mengirim...';

    try {
        const res = await fetch('/submit-attendance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                token: TOKEN,
                nama: nama,
                npm: npm,
                latitude: userLat,
                longitude: userLon
            })
        });
        const data = await res.json();
        if(data.success) {
            window.location.href = '/absensi-success?nama=' + encodeURIComponent(data.data.nama)
                + '&npm=' + encodeURIComponent(data.data.npm)
                + '&event=' + encodeURIComponent(data.data.event)
                + '&time=' + encodeURIComponent(data.data.time)
                + '&distance=' + encodeURIComponent(data.data.distance);
        } else {
            errEl.textContent = data.message;
            errEl.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Kirim Absensi';
        }
    } catch(e) {
        errEl.textContent = 'Koneksi gagal. Coba lagi.';
        errEl.classList.remove('hidden');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Kirim Absensi';
    }
}

// Auto start on page load
window.addEventListener('load', () => { startLocationDetection(); });
</script>
@endsection
