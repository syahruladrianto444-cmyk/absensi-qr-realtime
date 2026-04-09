@extends('layouts.admin')
@section('page-title', $event->nama_event)

@section('actions')
<div style="display:flex;gap:8px;">
    <button id="toggleBtn" onclick="toggleEvent({{ $event->id }})" class="btn btn-sm {{ $event->is_active ? 'btn-danger' : 'btn-success' }}">
        <i class="fas fa-{{ $event->is_active ? 'pause' : 'play' }}"></i>
        {{ $event->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
    </button>
    <a href="{{ route('admin.attendance.export', $event) }}" class="btn btn-outline btn-sm">
        <i class="fas fa-download"></i> Export CSV
    </a>
    <a href="{{ route('admin.events.index') }}" class="btn btn-outline btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>
@endsection

@section('styles')
@parent
<style>
.show-grid { display: grid; grid-template-columns: 1fr 420px; gap: 20px; align-items: start; }

/* QR Section */
.qr-card { padding: 28px; position: sticky; top: 24px; }
.qr-title { font-size: 15px; font-weight: 700; margin-bottom: 20px; text-align: center; }
.qr-wrapper {
    position: relative; text-align: center;
    background: white; border-radius: var(--radius);
    padding: 16px; margin-bottom: 16px;
}
.qr-wrapper svg { display: block; margin: 0 auto; width: 100%; height: auto; max-width: 100%; }
.qr-overlay {
    position: absolute; inset: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    border-radius: var(--radius);
    display: flex; align-items: center; justify-content: center;
    flex-direction: column; gap: 8px;
    font-size: 14px; font-weight: 600; color: white;
}
.qr-overlay.hidden { display: none; }

/* Countdown Ring */
.countdown-ring-wrapper {
    display: flex; justify-content: center; align-items: center;
    gap: 12px; margin-bottom: 16px;
}
.ring-container { position: relative; width: 60px; height: 60px; flex-shrink: 0; }
.ring-container svg { transform: rotate(-90deg); }
.ring-bg { fill: none; stroke: rgba(255,255,255,0.1); stroke-width: 4; }
.ring-progress { fill: none; stroke: #6366f1; stroke-width: 4; stroke-linecap: round;
    stroke-dasharray: 157; transition: stroke-dashoffset 1s linear; }
.ring-number {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; font-weight: 800; color: var(--text-primary);
}
.countdown-label { font-size: 13px; color: var(--text-muted); }
.countdown-label strong { display: block; font-size: 14px; color: var(--text-primary); }

/* QR URL */
.qr-url {
    background: rgba(0,0,0,0.3); border: 1px solid var(--border-glass);
    border-radius: var(--radius-sm); padding: 10px 12px;
    font-size: 11px; color: var(--text-muted);
    word-break: break-all; margin-bottom: 12px;
}

/* Stats Row */
.stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
.mini-stat { padding: 16px; text-align: center; }
.mini-stat-val { font-size: 24px; font-weight: 800; letter-spacing: -0.02em; }
.mini-stat-lbl { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

/* Logs Table */
.logs-card { padding: 24px; }

@media (max-width: 1024px) {
    .show-grid { grid-template-columns: 1fr; }
    .qr-card { position: static; }
}
</style>
@endsection

@section('page-content')
<div class="show-grid">
    {{-- Left: Stats + Logs --}}
    <div>
        <div class="stats-row animate-in">
            <div class="mini-stat glass">
                <div class="mini-stat-val text-gradient">{{ $hadir_count ?? 0 }}</div>
                <div class="mini-stat-lbl">Hadir</div>
            </div>
            <div class="mini-stat glass">
                <div class="mini-stat-val" style="color:var(--warning);">{{ $event->radius }}m</div>
                <div class="mini-stat-lbl">Radius</div>
            </div>
            <div class="mini-stat glass">
                <div class="mini-stat-val" style="color:var(--info);">
                    {{ $event->is_active ? '🟢' : '🔴' }}
                </div>
                <div class="mini-stat-lbl">{{ $event->is_active ? 'Aktif' : 'Nonaktif' }}</div>
            </div>
        </div>

        {{-- Event Info --}}
        <div class="glass logs-card animate-in animate-delay-1" style="margin-bottom:20px;">
            <h3 class="section-title" style="font-size:14px;font-weight:700;margin-bottom:14px;">
                <i class="fas fa-info-circle" style="color:var(--accent-primary);"></i> Informasi Event
            </h3>
            <div style="display:grid;gap:10px;font-size:14px;color:var(--text-secondary);">
                <div style="display:flex;gap:10px;">
                    <i class="fas fa-clock" style="width:16px;margin-top:2px;color:var(--text-muted);"></i>
                    {{ $event->start_time->format('d M Y H:i') }} — {{ $event->end_time->format('H:i') }} WIB
                </div>
                <div style="display:flex;gap:10px;">
                    <i class="fas fa-map-marker-alt" style="width:16px;margin-top:2px;color:var(--text-muted);"></i>
                    {{ $event->latitude }}, {{ $event->longitude }}
                </div>

            </div>
        </div>

        {{-- Attendance Table --}}
        <div class="glass logs-card animate-in animate-delay-2">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="font-size:14px;font-weight:700;">
                    <i class="fas fa-list" style="color:var(--info);"></i> Log Absensi
                </h3>
                <a href="{{ route('admin.attendance.index', $event) }}" class="btn btn-outline btn-sm">
                    Lihat Semua
                </a>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>NPM</th>
                            <th>Jarak</th>
                            <th>Status</th>
                            <th>Fraud</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td style="color:var(--text-muted);">{{ $loop->iteration }}</td>
                            <td style="color:var(--text-primary);font-weight:500;">{{ $log->nama }}</td>
                            <td>{{ $log->npm }}</td>
                            <td>{{ $log->distance }}m</td>
                            <td>
                                @if($log->status === 'hadir')
                                    <span class="badge badge-success">Hadir</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @php $fl = $log->fraud_level; @endphp
                                <span class="badge badge-{{ $fl === 'high' ? 'danger' : ($fl === 'medium' ? 'warning' : 'success') }}">
                                    {{ number_format($log->fraud_score, 2) }}
                                </span>
                            </td>
                            <td style="font-size:12px;">{{ $log->created_at->format('H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">
                                <i class="fas fa-inbox" style="font-size:24px;display:block;margin-bottom:8px;"></i>
                                Belum ada absensi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $logs->links() }}
        </div>
    </div>

    {{-- Right: QR Code --}}
    <div class="qr-card glass animate-in animate-delay-3" id="qrSection">
        <h3 class="qr-title">📡 QR Code Absensi</h3>
        <div class="qr-wrapper" id="qrWrapper">
            <div id="qrSvg">{!! $qrImage !!}</div>
            <div class="qr-overlay hidden" id="qrOverlay">
                <div class="spinner"></div>
                <span>Memperbarui...</span>
            </div>
        </div>

        <div class="countdown-ring-wrapper">
            <div class="ring-container">
                <svg viewBox="0 0 56 56" width="56" height="56">
                    <circle class="ring-bg" cx="28" cy="28" r="25"/>
                    <circle class="ring-progress" id="ringProgress" cx="28" cy="28" r="25"/>
                </svg>
                <div class="ring-number" id="countdownNum">{{ config('absensi.qr_refresh_seconds') }}</div>
            </div>
            <div class="countdown-label">
                <strong>Auto Refresh</strong>
                QR berubah setiap {{ config('absensi.qr_refresh_seconds') }}s
            </div>
        </div>

        <div class="qr-url">
            🔗 <span id="qrUrlDisplay">{{ $qrUrl }}</span>
        </div>

        <button onclick="manualRefresh()" class="btn btn-primary w-full mb-4">
            <i class="fas fa-sync-alt" id="refreshIcon"></i> Refresh QR Sekarang
        </button>

        <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:var(--radius-sm);padding:14px;font-size:13px;color:var(--text-secondary);">
            <p style="margin-bottom:6px;"><strong style="color:#34d399;"><i class="fas fa-shield-alt"></i> Anti Fraud Aktif</strong></p>
            <p>✓ QR kedaluwarsa setiap {{ config('absensi.qr_token_expiry_seconds', 60) }} detik</p>
            <p>✓ Validasi lokasi GPS (±{{ $event->radius }}m)</p>
            <p>✓ Deteksi fake GPS</p>
            <p>✓ 1 NPM = 1 absensi per event</p>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script>
const refreshSeconds = {{ config('absensi.qr_refresh_seconds') }};
const eventId = {{ $event->id }};
let countdown = refreshSeconds;
let isRefreshing = false;
const circumference = 2 * Math.PI * 25; // 157

const ringProgress = document.getElementById('ringProgress');
const countdownNum = document.getElementById('countdownNum');
ringProgress.style.strokeDashoffset = 0;

function updateRing(seconds) {
    const offset = circumference * (1 - seconds / refreshSeconds);
    ringProgress.style.strokeDashoffset = offset;
    countdownNum.textContent = seconds;
}

async function doRefresh() {
    if (isRefreshing) return;
    isRefreshing = true;
    document.getElementById('qrOverlay').classList.remove('hidden');
    document.getElementById('refreshIcon').classList.add('fa-spin');

    try {
        const res = await fetch(`/admin/events/${eventId}/qr/refresh`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        document.getElementById('qrSvg').innerHTML = data.qr_svg;
        document.getElementById('qrUrlDisplay').textContent = data.url;
        countdown = refreshSeconds;
        updateRing(countdown);
    } catch(e) {
        console.error('QR refresh failed', e);
    } finally {
        isRefreshing = false;
        document.getElementById('qrOverlay').classList.add('hidden');
        document.getElementById('refreshIcon').classList.remove('fa-spin');
    }
}

function manualRefresh() { doRefresh(); }

// Countdown timer
setInterval(() => {
    countdown--;
    if (countdown <= 0) {
        doRefresh();
        countdown = refreshSeconds;
    }
    updateRing(countdown);
}, 1000);

// Toggle event active/inactive
async function toggleEvent(id) {
    const btn = document.getElementById('toggleBtn');
    const res = await fetch(`/admin/events/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    });
    const data = await res.json();
    location.reload();
}
</script>
@endsection
