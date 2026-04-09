@extends('layouts.app')
@section('title', 'Admin')

@section('styles')
<style>
    .admin-layout {
        display: flex;
        min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
        width: 260px;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(30px);
        border-right: 1px solid var(--border-glass);
        padding: 24px 0;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 100;
        display: flex;
        flex-direction: column;
        transition: var(--transition);
    }

    .sidebar-brand {
        padding: 0 24px 24px;
        border-bottom: 1px solid var(--border-glass);
        margin-bottom: 16px;
    }

    .sidebar-brand h1 {
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .sidebar-brand p {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .sidebar-nav { flex: 1; padding: 0 12px; }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        color: var(--text-secondary);
        text-decoration: none;
        border-radius: var(--radius-sm);
        font-size: 14px;
        font-weight: 500;
        transition: var(--transition);
        margin-bottom: 4px;
    }
    .nav-item:hover { background: var(--bg-glass); color: var(--text-primary); }
    .nav-item.active {
        background: var(--gradient-primary);
        color: white;
        box-shadow: 0 4px 15px var(--accent-glow);
    }
    .nav-item i { width: 20px; text-align: center; font-size: 15px; }

    .sidebar-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--border-glass);
    }

    /* Main Content */
    .main-content {
        flex: 1;
        margin-left: 260px;
        padding: 24px;
        min-height: 100vh;
    }

    .top-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
    }

    .top-bar h2 {
        font-size: 24px;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .top-bar-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    /* Mobile menu toggle */
    .menu-toggle {
        display: none;
        background: var(--bg-glass);
        border: 1px solid var(--border-glass);
        color: var(--text-primary);
        padding: 10px;
        border-radius: var(--radius-sm);
        cursor: pointer;
        font-size: 18px;
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        z-index: 99;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }
        .sidebar.open {
            transform: translateX(0);
        }
        .sidebar-overlay.open {
            display: block;
        }
        .main-content {
            margin-left: 0;
            padding: 16px;
        }
        .menu-toggle { display: block; }
    }
</style>
@endsection

@section('content')
<div class="admin-layout">
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h1><span class="text-gradient">📡 Absensi QR</span></h1>
            <p>Universitas PGRI Semarang</p>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a href="{{ route('admin.events.index') }}" class="nav-item {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Events
            </a>
            <a href="{{ route('admin.events.create') }}" class="nav-item {{ request()->routeIs('admin.events.create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle"></i> Buat Event
            </a>
        </nav>

        <div class="sidebar-footer">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                <div style="width:36px;height:36px;border-radius:50%;background:var(--gradient-primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">Administrator</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm w-full">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="flex items-center gap-3">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h2>@yield('page-title', 'Dashboard')</h2>
            </div>
            <div class="top-bar-actions">
                @yield('actions')
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success animate-in">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error animate-in">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @yield('page-content')
    </main>
</div>
@endsection

@section('scripts')
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
}
</script>
@yield('page-scripts')
@endsection
