<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Absensi QR Code Real-Time - Universitas PGRI Semarang">
    <title>@yield('title', 'Absensi QR') - UPGRIS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0e1a;
            --bg-secondary: #111827;
            --bg-card: rgba(17, 24, 39, 0.8);
            --bg-glass: rgba(255, 255, 255, 0.05);
            --border-glass: rgba(255, 255, 255, 0.1);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-primary: #6366f1;
            --accent-secondary: #8b5cf6;
            --accent-glow: rgba(99, 102, 241, 0.3);
            --success: #10b981;
            --success-glow: rgba(16, 185, 129, 0.3);
            --danger: #ef4444;
            --danger-glow: rgba(239, 68, 68, 0.3);
            --warning: #f59e0b;
            --warning-glow: rgba(245, 158, 11, 0.3);
            --info: #06b6d4;
            --gradient-primary: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a78bfa 100%);
            --gradient-dark: linear-gradient(135deg, #0a0e1a 0%, #1e1b4b 100%);
            --gradient-card: linear-gradient(135deg, rgba(99,102,241,0.1) 0%, rgba(139,92,246,0.05) 100%);
            --shadow-glow: 0 0 30px rgba(99, 102, 241, 0.15);
            --radius: 16px;
            --radius-sm: 10px;
            --radius-full: 9999px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Animated Background */
        .bg-mesh {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse 80% 50% at 20% 40%, rgba(99,102,241,0.12) 0%, transparent 50%),
                radial-gradient(ellipse 60% 40% at 80% 20%, rgba(139,92,246,0.08) 0%, transparent 50%),
                radial-gradient(ellipse 50% 60% at 50% 80%, rgba(6,182,212,0.06) 0%, transparent 50%);
            pointer-events: none;
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        /* Glass Card */
        .glass {
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius);
        }

        .glass-strong {
            background: rgba(17, 24, 39, 0.9);
            backdrop-filter: blur(40px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: var(--radius);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: var(--radius-sm);
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 4px 15px var(--accent-glow);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px var(--accent-glow);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 15px var(--success-glow);
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px var(--success-glow);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 4px 15px var(--danger-glow);
        }

        .btn-outline {
            background: transparent;
            color: var(--text-primary);
            border: 1px solid var(--border-glass);
        }
        .btn-outline:hover {
            background: var(--bg-glass);
            border-color: var(--accent-primary);
        }

        .btn-sm { padding: 8px 16px; font-size: 13px; }
        .btn-lg { padding: 16px 32px; font-size: 16px; }

        /* Form Inputs */
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 8px;
            letter-spacing: 0.02em;
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            transition: var(--transition);
            outline: none;
        }
        .form-control:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }
        .form-control::placeholder { color: var(--text-muted); }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: var(--radius-full);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }
        .badge-success { background: rgba(16,185,129,0.15); color: #34d399; }
        .badge-danger { background: rgba(239,68,68,0.15); color: #f87171; }
        .badge-warning { background: rgba(245,158,11,0.15); color: #fbbf24; }
        .badge-info { background: rgba(6,182,212,0.15); color: #22d3ee; }

        /* Table */
        .table-wrapper { overflow-x: auto; border-radius: var(--radius); }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 14px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-glass);
            background: rgba(0,0,0,0.2);
        }
        tbody td {
            padding: 12px 16px;
            font-size: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.03);
            color: var(--text-secondary);
        }
        tbody tr { transition: var(--transition); }
        tbody tr:hover { background: rgba(99,102,241,0.05); }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px var(--accent-glow); }
            50% { box-shadow: 0 0 40px var(--accent-glow), 0 0 60px rgba(99,102,241,0.1); }
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes checkmark {
            0% { stroke-dashoffset: 100; }
            100% { stroke-dashoffset: 0; }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        @keyframes countdown-ring {
            from { stroke-dashoffset: 0; }
            to { stroke-dashoffset: 283; }
        }

        .animate-in {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .animate-delay-1 { animation-delay: 0.1s; }
        .animate-delay-2 { animation-delay: 0.2s; }
        .animate-delay-3 { animation-delay: 0.3s; }
        .animate-delay-4 { animation-delay: 0.4s; }

        /* Spinner */
        .spinner {
            width: 48px; height: 48px;
            border: 3px solid var(--border-glass);
            border-top-color: var(--accent-primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        /* Utilities */
        .text-center { text-align: center; }
        .text-gradient {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .gap-4 { gap: 16px; }
        .w-full { width: 100%; }
        .mt-2 { margin-top: 8px; }
        .mt-4 { margin-top: 16px; }
        .mt-6 { margin-top: 24px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .p-4 { padding: 16px; }
        .p-6 { padding: 24px; }
        .hidden { display: none !important; }

        /* Alert */
        .alert {
            padding: 14px 20px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #34d399; }
        .alert-error { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #f87171; }

        /* Responsive */
        @media (max-width: 768px) {
            .p-6 { padding: 16px; }
            .btn-lg { padding: 14px 24px; font-size: 15px; }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="bg-mesh"></div>
    <div class="bg-grid"></div>
    <div style="position: relative; z-index: 1;">
        @yield('content')
    </div>
    <script>
        // Global CSRF setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    </script>
    @yield('scripts')
</body>
</html>
