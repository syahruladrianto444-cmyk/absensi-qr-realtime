@extends('layouts.app')
@section('title', 'Absensi Berhasil!')

@section('styles')
<style>
.success-container {
    min-height: 100vh; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 24px 16px; max-width: 440px; margin: 0 auto;
}
.success-icon-wrap {
    position: relative; margin-bottom: 28px;
}
.success-circle {
    width: 100px; height: 100px; border-radius: 50%;
    background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(16,185,129,0.05));
    border: 2px solid rgba(16,185,129,0.4);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto;
    animation: pulse-glow 2s ease-in-out infinite;
    --accent-glow: rgba(16,185,129,0.3);
}
.checkmark-svg { width: 50px; height: 50px; }
.checkmark-path {
    fill: none; stroke: #10b981; stroke-width: 3;
    stroke-linecap: round; stroke-linejoin: round;
    stroke-dasharray: 100; stroke-dashoffset: 100;
    animation: checkmark 0.8s ease-out 0.3s forwards;
}
.confetti-ring {
    position: absolute; top: -10px; left: 50%; transform: translateX(-50%);
    width: 120px; height: 120px; border-radius: 50%;
    border: 2px dashed rgba(16,185,129,0.3);
    animation: spin 8s linear infinite;
}
.success-card {
    width: 100%; padding: 28px 24px; text-align: center; margin-bottom: 16px;
    animation: fadeInUp 0.8s cubic-bezier(0.16,1,0.3,1) forwards;
}
.success-title { font-size: 28px; font-weight: 800; color: var(--success); margin-bottom: 6px; letter-spacing: -0.02em; }
.success-sub { font-size: 15px; color: var(--text-secondary); margin-bottom: 24px; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; text-align: left; margin-bottom: 20px; }
.info-item {
    background: rgba(0,0,0,0.2); border: 1px solid var(--border-glass);
    border-radius: var(--radius-sm); padding: 14px;
}
.info-item-label { font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
.info-item-value { font-size: 14px; color: var(--text-primary); font-weight: 600; }
.seal {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 18px;
    background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2);
    border-radius: var(--radius-full); font-size: 13px; color: var(--success); font-weight: 600;
    margin-bottom: 20px;
}
</style>
@endsection

@section('content')
<div class="success-container">
    <div class="success-icon-wrap animate-in">
        <div class="confetti-ring"></div>
        <div class="success-circle">
            <svg class="checkmark-svg" viewBox="0 0 50 50">
                <path class="checkmark-path" d="M12 25 L22 35 L38 17"/>
            </svg>
        </div>
    </div>

    <div class="success-card glass animate-in animate-delay-1">
        <h1 class="success-title">✅ Absensi Tercatat!</h1>
        <p class="success-sub">Kehadiran Anda telah berhasil diverifikasi dan disimpan.</p>

        <div class="seal animate-in animate-delay-2">
            <i class="fas fa-shield-check"></i> Terverifikasi oleh Sistem UPGRIS
        </div>

        <div class="info-grid animate-in animate-delay-3">
            <div class="info-item">
                <div class="info-item-label">Nama</div>
                <div class="info-item-value">{{ request('nama', 'Mahasiswa') }}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">NPM</div>
                <div class="info-item-value" style="font-family:monospace;">{{ request('npm', '-') }}</div>
            </div>
            <div class="info-item" style="grid-column:1/-1;">
                <div class="info-item-label">Event</div>
                <div class="info-item-value">{{ request('event', '-') }}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Waktu</div>
                <div class="info-item-value" style="font-size:13px;">{{ request('time', now()->format('d M Y H:i:s')) }}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Jarak</div>
                <div class="info-item-value" style="color:var(--success);">{{ request('distance', '-') }}</div>
            </div>
        </div>

        <div style="padding:16px;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.2);border-radius:var(--radius-sm);font-size:13px;color:var(--text-secondary);text-align:left;">
            <p style="margin-bottom:4px;font-weight:600;color:var(--text-primary);">📋 Catatan</p>
            <p>Data absensi Anda sudah masuk ke sistem. Tidak perlu screenshot — data tersimpan secara real-time.</p>
        </div>
    </div>
</div>
@endsection
