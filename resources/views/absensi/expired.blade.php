@extends('layouts.app')
@section('title', 'QR Code Expired')

@section('styles')
<style>
.expired-container {
    min-height: 100vh; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 24px 16px; max-width: 420px; margin: 0 auto;
    text-align: center;
}
.expired-icon {
    font-size: 72px; margin-bottom: 20px;
    animation: float 3s ease-in-out infinite;
    display: inline-block;
}
.expired-card {
    width: 100%; padding: 32px 24px;
    animation: fadeInUp 0.7s cubic-bezier(0.16,1,0.3,1) forwards;
}
.expired-title { font-size: 24px; font-weight: 800; margin-bottom: 8px; letter-spacing:-0.02em; }
.expired-sub { font-size: 15px; color: var(--text-muted); margin-bottom: 24px; }
.step-list { text-align: left; margin: 20px 0; }
.step-list li {
    padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.04);
    font-size: 14px; color: var(--text-secondary);
    display: flex; align-items: center; gap: 10px;
}
.step-list li:last-child { border-bottom: none; }
</style>
@endsection

@section('content')
<div class="expired-container">
    <div class="expired-icon animate-in">⏰</div>
    <div class="expired-card glass animate-in animate-delay-1">
        <h1 class="expired-title">QR Code Kadaluarsa</h1>
        <p class="expired-sub">{{ $message ?? 'QR Code yang Anda scan sudah tidak berlaku atau telah habis masa aktifnya.' }}</p>

        <div style="padding:16px;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:var(--radius-sm);margin-bottom:20px;">
            <p style="font-size:13px;color:var(--warning);font-weight:600;margin-bottom:4px;">
                <i class="fas fa-info-circle"></i> Mengapa ini terjadi?
            </p>
            <p style="font-size:13px;color:var(--text-muted);">
                QR Code diperbarui setiap 30 detik untuk keamanan sistem absensi.
            </p>
        </div>

        <ul class="step-list">
            <li><span>1️⃣</span> Minta admin untuk menampilkan QR terbaru</li>
            <li><span>2️⃣</span> Scan ulang QR Code yang baru</li>
            <li><span>3️⃣</span> Proses absensi akan dimulai ulang otomatis</li>
        </ul>

        <div style="padding:14px;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.2);border-radius:var(--radius-sm);font-size:13px;color:var(--text-secondary);">
            <i class="fas fa-shield-alt" style="color:var(--accent-primary);"></i>
            Sistem QR dinamis ini melindungi dari titip absen dan pemalsuan lokasi.
        </div>
    </div>
</div>
@endsection
