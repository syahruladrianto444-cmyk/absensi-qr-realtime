@extends('layouts.app')
@section('title', 'Login Admin')

@section('styles')
<style>
    .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .login-card {
        width: 100%;
        max-width: 420px;
        padding: 40px;
        animation: fadeInUp 0.8s cubic-bezier(0.16,1,0.3,1);
    }
    .login-icon {
        width: 72px; height: 72px;
        border-radius: 20px;
        background: var(--gradient-primary);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 24px;
        font-size: 28px;
        box-shadow: 0 8px 30px var(--accent-glow);
    }
    .login-title {
        font-size: 24px; font-weight: 800;
        text-align: center; margin-bottom: 8px;
        letter-spacing: -0.02em;
    }
    .login-subtitle {
        text-align: center;
        font-size: 14px;
        color: var(--text-muted);
        margin-bottom: 32px;
    }
    .login-footer {
        text-align: center;
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 32px;
        padding-top: 20px;
        border-top: 1px solid var(--border-glass);
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-card glass-strong">
        <div class="login-icon">📡</div>
        <h1 class="login-title"><span class="text-gradient">Absensi QR</span></h1>
        <p class="login-subtitle">Masuk ke dashboard administrator</p>

        @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                       placeholder="admin@upgris.ac.id" required autofocus>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-full mt-4">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </form>

        <div class="login-footer">
            <p>Universitas PGRI Semarang</p>
            <p style="margin-top:4px;">Sistem Absensi QR Code Real-Time &copy; {{ date('Y') }}</p>
        </div>
    </div>
</div>
@endsection
