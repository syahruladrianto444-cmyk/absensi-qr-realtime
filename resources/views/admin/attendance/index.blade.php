@extends('layouts.admin')
@section('page-title', 'Log Absensi — ' . $event->nama_event)

@section('actions')
<div style="display:flex;gap:8px;">
    <a href="{{ route('admin.attendance.export', $event) }}" class="btn btn-success btn-sm">
        <i class="fas fa-download"></i> Export CSV
    </a>
    <a href="{{ route('admin.events.show', $event) }}" class="btn btn-outline btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali ke Event
    </a>
</div>
@endsection

@section('styles')
@parent
<style>
.filter-bar { display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
.filter-bar input, .filter-bar select {
    padding: 10px 14px;
    background: rgba(0,0,0,0.3);
    border: 1px solid var(--border-glass);
    border-radius: var(--radius-sm);
    color: var(--text-primary);
    font-size:13px; font-family:'Inter',sans-serif;
    outline:none; transition:var(--transition);
}
.filter-bar input:focus, .filter-bar select:focus { border-color:var(--accent-primary); }
.filter-bar input { flex:1; min-width:200px; }

.fraud-bar {
    display:flex; height:6px; border-radius:3px;
    background:rgba(255,255,255,0.1); overflow:hidden;
}
.fraud-fill { border-radius:3px; }
</style>
@endsection

@section('page-content')
{{-- Summary Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
    @php
        $total = $logs->total();
        $hadir = $logs->getCollection()->where('status','hadir')->count();
        $ditolak = $logs->getCollection()->where('status','ditolak')->count();
        $suspicious = $logs->getCollection()->where('fraud_score','>',0.5)->count();
    @endphp
    <div class="glass p-4 animate-in" style="text-align:center;">
        <div style="font-size:28px;font-weight:800;" class="text-gradient">{{ $total }}</div>
        <div style="font-size:12px;color:var(--text-muted);">Total Record</div>
    </div>
    <div class="glass p-4 animate-in animate-delay-1" style="text-align:center;">
        <div style="font-size:28px;font-weight:800;color:var(--success);" >{{ $hadir }}</div>
        <div style="font-size:12px;color:var(--text-muted);">Hadir</div>
    </div>
    <div class="glass p-4 animate-in animate-delay-2" style="text-align:center;">
        <div style="font-size:28px;font-weight:800;color:var(--danger);">{{ $ditolak }}</div>
        <div style="font-size:12px;color:var(--text-muted);">Ditolak</div>
    </div>
    <div class="glass p-4 animate-in animate-delay-3" style="text-align:center;">
        <div style="font-size:28px;font-weight:800;color:var(--warning);">{{ $suspicious }}</div>
        <div style="font-size:12px;color:var(--text-muted);">Suspicious</div>
    </div>
</div>

{{-- Filter --}}
<div class="filter-bar animate-in animate-delay-2">
    <input type="text" id="searchInput" placeholder="🔍 Cari nama atau NPM..." oninput="filterTable()">
    <select id="statusFilter" onchange="filterTable()">
        <option value="">Semua Status</option>
        <option value="hadir">Hadir</option>
        <option value="ditolak">Ditolak</option>
    </select>
    <select id="fraudFilter" onchange="filterTable()">
        <option value="">Semua Fraud Level</option>
        <option value="high">High Risk (>0.7)</option>
        <option value="medium">Medium (0.4-0.7)</option>
        <option value="low">Low (<0.4)</option>
    </select>
</div>

<div class="glass animate-in animate-delay-3" style="padding:24px;">
    <div class="table-wrapper">
        <table id="logsTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NPM</th>
                    <th>Universitas</th>
                    <th>Jarak</th>
                    <th>Status</th>
                    <th>Fraud Score</th>
                    <th>IP Address</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $i => $log)
                <tr data-status="{{ $log->status }}" data-fraud="{{ $log->fraud_level }}" data-nama="{{ strtolower($log->nama) }}" data-npm="{{ $log->npm }}">
                    <td style="color:var(--text-muted);">{{ $logs->firstItem() + $i }}</td>
                    <td style="color:var(--text-primary);font-weight:600;">{{ $log->nama }}</td>
                    <td style="font-family:monospace;">{{ $log->npm }}</td>
                    <td style="font-size:13px; color:var(--text-secondary);">{{ $log->universitas ?? '-' }}</td>
                    <td>
                        <span style="color:{{ $log->distance <= $event->radius ? 'var(--success)' : 'var(--danger)' }};">
                            {{ number_format($log->distance, 1) }}m
                        </span>
                    </td>
                    <td>
                        @if($log->status === 'hadir')
                            <span class="badge badge-success"><i class="fas fa-check"></i> Hadir</span>
                        @elseif($log->status === 'ditolak')
                            <span class="badge badge-danger"><i class="fas fa-times"></i> Ditolak</span>
                        @else
                            <span class="badge badge-warning">Invalid</span>
                        @endif
                    </td>
                    <td>
                        @php $fl = $log->fraud_level; $fs = (float)$log->fraud_score; @endphp
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="fraud-bar" style="width:60px;">
                                <div class="fraud-fill" style="width:{{ $fs * 100 }}%;background:{{ $fl==='high'?'var(--danger)':($fl==='medium'?'var(--warning)':'var(--success)') }};"></div>
                            </div>
                            <span class="badge badge-{{ $fl === 'high' ? 'danger' : ($fl === 'medium' ? 'warning' : 'success') }}">
                                {{ number_format($fs, 2) }}
                            </span>
                        </div>
                    </td>
                    <td style="font-family:monospace;font-size:12px;">{{ $log->ip_address ?? '-' }}</td>
                    <td style="font-size:12px;color:var(--text-muted);">{{ $log->created_at->format('d/m H:i:s') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                        <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:12px;"></i>
                        Belum ada data absensi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $logs->links() }}</div>
</div>
@endsection

@section('page-scripts')
<script>
function filterTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const fraud = document.getElementById('fraudFilter').value;
    document.querySelectorAll('#logsTable tbody tr').forEach(row => {
        const nama = row.dataset.nama ?? '';
        const npm = row.dataset.npm ?? '';
        const rowStatus = row.dataset.status ?? '';
        const rowFraud = row.dataset.fraud ?? '';
        const matchSearch = nama.includes(search) || npm.includes(search);
        const matchStatus = !status || rowStatus === status;
        const matchFraud = !fraud || rowFraud === fraud;
        row.style.display = (matchSearch && matchStatus && matchFraud) ? '' : 'none';
    });
}
</script>
@endsection
