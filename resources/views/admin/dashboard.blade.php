@extends('layouts.admin')
@section('page-title', 'Dashboard')

@section('styles')
@parent
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }
    .stat-card {
        padding: 24px;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; right: 0;
        width: 80px; height: 80px;
        border-radius: 50%;
        opacity: 0.1;
        transform: translate(20px, -20px);
    }
    .stat-card:nth-child(1)::before { background: var(--accent-primary); }
    .stat-card:nth-child(2)::before { background: var(--success); }
    .stat-card:nth-child(3)::before { background: var(--info); }
    .stat-card:nth-child(4)::before { background: var(--warning); }
    .stat-card:nth-child(5)::before { background: var(--danger); }
    .stat-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        margin-bottom: 16px;
    }
    .stat-icon.purple { background: rgba(99,102,241,0.15); color: #818cf8; }
    .stat-icon.green { background: rgba(16,185,129,0.15); color: #34d399; }
    .stat-icon.blue { background: rgba(6,182,212,0.15); color: #22d3ee; }
    .stat-icon.yellow { background: rgba(245,158,11,0.15); color: #fbbf24; }
    .stat-icon.red { background: rgba(239,68,68,0.15); color: #f87171; }
    .stat-value {
        font-size: 32px; font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1;
    }
    .stat-label {
        font-size: 13px; color: var(--text-muted);
        margin-top: 6px; font-weight: 500;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .section-title {
        font-size: 16px; font-weight: 700;
        margin-bottom: 16px; letter-spacing: -0.01em;
    }
    .event-mini-card {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 16px;
        border-bottom: 1px solid rgba(255,255,255,0.04);
        transition: var(--transition);
    }
    .event-mini-card:last-child { border-bottom: none; }
    .event-mini-card:hover { background: rgba(99,102,241,0.05); }
    .event-name { font-size: 14px; font-weight: 600; }
    .event-count { font-size: 13px; color: var(--text-muted); }

    @media (max-width: 768px) {
        .dashboard-grid { grid-template-columns: 1fr; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('page-content')
<div class="stats-grid">
    <div class="stat-card glass animate-in animate-delay-1">
        <div class="stat-icon purple"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-value">{{ $stats['total_events'] }}</div>
        <div class="stat-label">Total Event</div>
    </div>
    <div class="stat-card glass animate-in animate-delay-2">
        <div class="stat-icon green"><i class="fas fa-signal"></i></div>
        <div class="stat-value">{{ $stats['active_events'] }}</div>
        <div class="stat-label">Event Aktif</div>
    </div>
    <div class="stat-card glass animate-in animate-delay-3">
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
        <div class="stat-value">{{ $stats['today_attendance'] }}</div>
        <div class="stat-label">Absensi Hari Ini</div>
    </div>
    <div class="stat-card glass animate-in animate-delay-4">
        <div class="stat-icon yellow"><i class="fas fa-database"></i></div>
        <div class="stat-value">{{ $stats['total_attendance'] }}</div>
        <div class="stat-label">Total Absensi</div>
    </div>
    <div class="stat-card glass animate-in animate-delay-4">
        <div class="stat-icon red"><i class="fas fa-shield-alt"></i></div>
        <div class="stat-value">{{ $stats['fraud_alerts'] }}</div>
        <div class="stat-label">Fraud Alerts</div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="glass p-6 animate-in animate-delay-3">
        <h3 class="section-title"><i class="fas fa-calendar-alt" style="color:var(--accent-primary);"></i> Event Terbaru</h3>
        @forelse($events as $event)
            <a href="{{ route('admin.events.show', $event) }}" class="event-mini-card" style="text-decoration:none;color:inherit;">
                <div>
                    <div class="event-name">{{ $event->nama_event }}</div>
                    <div class="event-count">{{ $event->hadir_count ?? 0 }} hadir</div>
                </div>
                <div>
                    @if($event->is_active)
                        <span class="badge badge-success"><i class="fas fa-circle" style="font-size:6px;"></i> Aktif</span>
                    @else
                        <span class="badge badge-danger">Nonaktif</span>
                    @endif
                </div>
            </a>
        @empty
            <div style="text-align:center; padding:32px; color:var(--text-muted);">
                <i class="fas fa-inbox" style="font-size:28px; margin-bottom:8px; display:block;"></i>
                Belum ada event
            </div>
        @endforelse
    </div>

    <div class="glass p-6 animate-in animate-delay-4">
        <h3 class="section-title"><i class="fas fa-clock" style="color:var(--info);"></i> Aktivitas Terbaru</h3>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NPM</th>
                        <th>Event</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLogs as $log)
                    <tr>
                        <td style="color:var(--text-primary); font-weight:500;">{{ $log->nama }}</td>
                        <td>{{ $log->npm }}</td>
                        <td>{{ $log->event->nama_event ?? '-' }}</td>
                        <td>
                            @if($log->status === 'hadir')
                                <span class="badge badge-success">Hadir</span>
                            @elseif($log->status === 'ditolak')
                                <span class="badge badge-danger">Ditolak</span>
                            @else
                                <span class="badge badge-warning">Invalid</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:24px; color:var(--text-muted);">
                            Belum ada data absensi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
