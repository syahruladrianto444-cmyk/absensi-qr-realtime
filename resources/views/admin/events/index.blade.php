@extends('layouts.admin')
@section('page-title', 'Semua Event')

@section('actions')
<a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-sm">
    <i class="fas fa-plus"></i> Buat Event
</a>
@endsection

@section('styles')
@parent
<style>
    .events-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 16px;
    }
    .event-card {
        padding: 24px;
        transition: var(--transition);
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .event-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-glow);
        border-color: rgba(99,102,241,0.3);
    }
    .event-header {
        display: flex; justify-content: space-between; align-items: flex-start;
        margin-bottom: 16px;
    }
    .event-title {
        font-size: 16px; font-weight: 700;
        letter-spacing: -0.01em;
        line-height: 1.3;
    }
    .event-meta {
        display: flex; flex-direction: column; gap: 8px;
        font-size: 13px; color: var(--text-muted);
    }
    .event-meta-item {
        display: flex; align-items: center; gap: 8px;
    }
    .event-meta-item i { width: 16px; text-align: center; color: var(--text-muted); }
    .event-footer {
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid var(--border-glass);
        display: flex; justify-content: space-between; align-items: center;
    }
    .hadir-count {
        font-size: 22px; font-weight: 800;
        letter-spacing: -0.02em;
    }
    .pagination-wrapper {
        display: flex; justify-content: center; margin-top: 28px; gap: 6px;
    }
    .pagination-wrapper a, .pagination-wrapper span {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 13px;
        text-decoration: none;
        color: var(--text-secondary);
        border: 1px solid var(--border-glass);
        transition: var(--transition);
    }
    .pagination-wrapper a:hover { background: var(--bg-glass); }
    .pagination-wrapper .active span {
        background: var(--gradient-primary); color: white;
        border-color: transparent;
    }
    @media (max-width: 768px) {
        .events-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('page-content')
<div class="events-grid">
    @forelse($events as $event)
        <a href="{{ route('admin.events.show', $event) }}" class="event-card glass animate-in" style="animation-delay:{{ $loop->index * 0.05 }}s">
            <div class="event-header">
                <h3 class="event-title">{{ $event->nama_event }}</h3>
                @if($event->is_active)
                    <span class="badge badge-success"><i class="fas fa-circle" style="font-size:6px;"></i> Aktif</span>
                @else
                    <span class="badge badge-danger">Nonaktif</span>
                @endif
            </div>
            <div class="event-meta">
                <div class="event-meta-item">
                    <i class="fas fa-clock"></i>
                    {{ $event->start_time->format('d M Y H:i') }} - {{ $event->end_time->format('H:i') }}
                </div>
                <div class="event-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    Radius {{ $event->radius }}m
                </div>
                @if($event->google_form_url)
                <div class="event-meta-item">
                    <i class="fab fa-google"></i> Google Form terhubung
                </div>
                @endif
            </div>
            <div class="event-footer">
                <div>
                    <div class="hadir-count text-gradient">{{ $event->hadir_count ?? 0 }}</div>
                    <div style="font-size:12px; color:var(--text-muted);">mahasiswa hadir</div>
                </div>
                <span class="btn btn-outline btn-sm">
                    <i class="fas fa-qrcode"></i> Lihat QR
                </span>
            </div>
        </a>
    @empty
        <div class="glass p-6 text-center" style="grid-column: 1/-1;">
            <i class="fas fa-calendar-plus" style="font-size:48px; color:var(--text-muted); margin-bottom:16px;"></i>
            <p style="font-size:16px; font-weight:600; margin-bottom:8px;">Belum ada event</p>
            <p style="color:var(--text-muted); margin-bottom:20px;">Buat event pertama untuk mulai absensi</p>
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Event Baru
            </a>
        </div>
    @endforelse
</div>

@if($events->hasPages())
<div class="pagination-wrapper">
    {{ $events->links('pagination::simple-bootstrap-5') }}
</div>
@endif
@endsection
