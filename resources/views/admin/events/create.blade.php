@extends('layouts.admin')
@section('page-title', 'Buat Event Baru')

@section('actions')
<a href="{{ route('admin.events.index') }}" class="btn btn-outline btn-sm">
    <i class="fas fa-arrow-left"></i> Kembali
</a>
@endsection

@section('styles')
@parent
<style>
.create-grid { display: grid; grid-template-columns: 1fr 380px; gap: 20px; }
.form-card { padding: 28px; }
.section-label {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.08em; color: var(--accent-primary);
    margin: 24px 0 16px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(99,102,241,0.2);
}
.section-label:first-child { margin-top: 0; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.map-preview {
    border-radius: var(--radius-sm);
    overflow: hidden;
    height: 260px;
    border: 1px solid var(--border-glass);
    background: rgba(0,0,0,0.3);
    position: relative;
}
.map-placeholder {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    height: 100%; color: var(--text-muted); gap: 8px;
    font-size: 13px;
}
.campus-btn {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 14px;
    background: rgba(99,102,241,0.1);
    border: 1px solid rgba(99,102,241,0.2);
    border-radius: var(--radius-sm);
    color: var(--accent-primary);
    font-size: 13px; font-weight: 600; cursor: pointer;
    transition: var(--transition); width: 100%;
    margin-bottom: 10px;
}
.campus-btn:hover { background: rgba(99,102,241,0.2); }
.coord-display {
    background: rgba(0,0,0,0.3);
    border: 1px solid var(--border-glass);
    border-radius: var(--radius-sm);
    padding: 12px;
    font-family: monospace; font-size: 12px;
    color: var(--text-secondary);
    word-break: break-all;
}
.radius-slider { width: 100%; accent-color: var(--accent-primary); cursor: pointer; }
.sticky-help { position: sticky; top: 24px; }
.help-card { padding: 20px; margin-bottom: 16px; }
.help-item {
    display: flex; gap: 12px; padding: 12px 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    font-size: 13px;
}
.help-item:last-child { border-bottom: none; padding-bottom: 0; }
.help-icon { font-size: 18px; flex-shrink: 0; margin-top: 2px; }
@media (max-width: 900px) {
    .create-grid { grid-template-columns: 1fr; }
    .form-row { grid-template-columns: 1fr; }
    .sticky-help { position: static; }
}
</style>
@endsection

@section('page-content')
<form method="POST" action="{{ route('admin.events.store') }}" id="createEventForm">
@csrf
<div class="create-grid">
    {{-- Main Form --}}
    <div class="glass form-card animate-in">
        <p class="section-label">Informasi Event</p>
        <div class="form-group">
            <label class="form-label">Nama Event *</label>
            <input type="text" name="nama_event" class="form-control" value="{{ old('nama_event') }}"
                   placeholder="cth: Kuliah Pemrograman Web - Sesi 1" required>
            @error('nama_event') <small style="color:var(--danger);margin-top:4px;display:block;">{{ $message }}</small> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Waktu Mulai *</label>
                <input type="datetime-local" name="start_time" class="form-control"
                       value="{{ old('start_time', now()->format('Y-m-d\TH:i')) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Waktu Selesai *</label>
                <input type="datetime-local" name="end_time" class="form-control"
                       value="{{ old('end_time', now()->addHours(2)->format('Y-m-d\TH:i')) }}" required>
            </div>
        </div>

        <p class="section-label">Lokasi & Radius</p>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Latitude *</label>
                <input type="number" name="latitude" id="lat" class="form-control"
                       step="any" value="{{ old('latitude', -6.9883196665620675) }}" required placeholder="-6.988...">
            </div>
            <div class="form-group">
                <label class="form-label">Longitude *</label>
                <input type="number" name="longitude" id="lng" class="form-control"
                       step="any" value="{{ old('longitude', 110.43569087874343) }}" required placeholder="110.435...">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Radius Validasi: <strong id="radiusVal">50</strong> meter</label>
            <input type="range" name="radius" id="radiusSlider" class="radius-slider"
                   min="10" max="500" value="{{ old('radius', 50) }}" style="margin-top:8px;">
            <input type="hidden" name="radius" id="radiusHidden" value="{{ old('radius', 50) }}">
            <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--text-muted);margin-top:4px;">
                <span>10m</span><span>500m</span>
            </div>
        </div>



        @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <ul style="margin:0;padding-left:16px;">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
        @endif

        <div style="display:flex;gap:12px;margin-top:8px;">
            <button type="submit" class="btn btn-primary btn-lg" style="flex:1;">
                <i class="fas fa-rocket"></i> Buat Event & Generate QR
            </button>
        </div>
    </div>

    {{-- Help Sidebar --}}
    <div class="sticky-help animate-in animate-delay-2">
        <div class="glass help-card">
            <h4 style="font-size:14px;font-weight:700;margin-bottom:12px;">
                <i class="fas fa-map-marker-alt" style="color:var(--accent-primary);"></i> Koordinat Kampus
            </h4>
            <button type="button" class="campus-btn" onclick="setUpgrisCoords()">
                <i class="fas fa-university"></i> Gunakan Koordinat UPGRIS
            </button>
            <button type="button" class="campus-btn" onclick="getMyLocation()">
                <i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya
            </button>
            <div class="coord-display" id="coordDisplay">
                Lat: {{ config('absensi.default_latitude') }}<br>
                Lng: {{ config('absensi.default_longitude') }}
            </div>
        </div>

        <div class="glass help-card">
            <h4 style="font-size:14px;font-weight:700;margin-bottom:12px;">
                <i class="fas fa-lightbulb" style="color:var(--warning);"></i> Tips
            </h4>
            <div class="help-item">
                <span class="help-icon">⏱️</span>
                <span>QR Code auto-refresh setiap <strong>{{ config('absensi.qr_refresh_seconds') }} detik</strong> untuk mencegah titip absen.</span>
            </div>
            <div class="help-item">
                <span class="help-icon">📍</span>
                <span>Radius <strong>50m</strong> cocok untuk 1 ruangan. Gunakan <strong>100m</strong> untuk gedung besar.</span>
            </div>

        </div>
    </div>
</div>
</form>
@endsection

@section('page-scripts')
<script>
const radiusSlider = document.getElementById('radiusSlider');
const radiusVal = document.getElementById('radiusVal');
const radiusHidden = document.getElementById('radiusHidden');
radiusSlider.addEventListener('input', function() {
    radiusVal.textContent = this.value;
    radiusHidden.value = this.value;
});

function updateCoordDisplay() {
    const lat = document.getElementById('lat').value;
    const lng = document.getElementById('lng').value;
    document.getElementById('coordDisplay').innerHTML =
        `Lat: ${parseFloat(lat).toFixed(8)}<br>Lng: ${parseFloat(lng).toFixed(8)}`;
}
document.getElementById('lat').addEventListener('input', updateCoordDisplay);
document.getElementById('lng').addEventListener('input', updateCoordDisplay);

function setUpgrisCoords() {
    document.getElementById('lat').value = -6.9883196665620675;
    document.getElementById('lng').value = 110.43569087874343;
    updateCoordDisplay();
}

function getMyLocation() {
    if (!navigator.geolocation) return alert('Geolocation tidak didukung browser ini.');
    navigator.geolocation.getCurrentPosition(function(pos) {
        document.getElementById('lat').value = pos.coords.latitude;
        document.getElementById('lng').value = pos.coords.longitude;
        updateCoordDisplay();
    }, function() {
        alert('Gagal mendapatkan lokasi. Pastikan GPS aktif.');
    });
}
</script>
@endsection
