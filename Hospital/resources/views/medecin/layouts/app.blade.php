<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Médecin') — Clinique Santé+</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; display: flex; min-height: 100vh; background: #f8f8f8; color: #1a1a1a; }

        /* ── Sidebar ─────────────────────────────────────── */
        .sidebar {
            width: 220px; min-height: 100vh;
            background: #fff; border-right: 1px solid #e5e5e5;
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; bottom: 0;
        }
        .sidebar-header { padding: 20px 18px; border-bottom: 1px solid #e5e5e5; }
        .sidebar-header h2 { font-size: 15px; font-weight: 600; }
        .sidebar-header p  { font-size: 12px; color: #888; margin-top: 2px; }

        .nav-section { padding: 10px 0; flex: 1; }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 18px; font-size: 13px; color: #555;
            text-decoration: none; border-left: 2px solid transparent;
            transition: background 0.15s;
        }
        .nav-item:hover  { background: #f5f5f5; color: #1a1a1a; }
        .nav-item.active { background: #f5f0ff; color: #4a3a9a; font-weight: 500; border-left-color: #7F77DD; }

        .badge { font-size: 11px; padding: 2px 7px; border-radius: 10px; font-weight: 500; }
        .badge-ok     { background: #d1e7dd; color: #0a3622; }
        .badge-warn   { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #58151c; }
        .badge-gray   { background: #f0f0f0; color: #666; }
        .badge-purple { background: #ece8fd; color: #4a3a9a; }

        .sidebar-footer {
            padding: 14px 18px; border-top: 1px solid #e5e5e5;
            display: flex; align-items: center; gap: 10px;
        }
        .avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: #ece8fd; display: flex; align-items: center;
            justify-content: center; font-size: 12px; font-weight: 600;
            color: #4a3a9a; flex-shrink: 0;
        }
        .sidebar-footer p    { font-size: 13px; font-weight: 500; }
        .sidebar-footer span { font-size: 11px; color: #888; }

        /* ── Main ────────────────────────────────────────── */
        .main { margin-left: 220px; flex: 1; padding: 28px; }
        .page-header {
            display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 24px;
        }
        .page-header h1 { font-size: 20px; font-weight: 600; }

        /* ── Stats ───────────────────────────────────────── */
        .stats-grid { display: flex; gap: 14px; margin-bottom: 26px; }
        .stat-card {
            flex: 1; background: #fff;
            border: 1px solid #e5e5e5; border-radius: 10px; padding: 16px 18px;
        }
        .stat-card .label { font-size: 12px; color: #888; margin-bottom: 6px; }
        .stat-card .value { font-size: 26px; font-weight: 600; }

        /* ── Boutons ─────────────────────────────────────── */
        .btn {
            padding: 8px 16px; font-size: 13px; border-radius: 8px;
            border: 1px solid #d0d0d0; background: #fff; cursor: pointer;
            color: #1a1a1a; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
            transition: background 0.15s;
        }
        .btn:hover    { background: #f5f5f5; }
        .btn-primary  { background: #7F77DD; color: #fff; border-color: #7F77DD; }
        .btn-primary:hover { background: #534AB7; }
        .btn-danger   { background: #f8d7da; border-color: #58151c; color: #58151c; }
        .btn-danger:hover  { background: #f1aeb5; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }

        /* ── Table ───────────────────────────────────────── */
        .table-wrapper {
            background: #fff; border: 1px solid #e5e5e5;
            border-radius: 10px; overflow: hidden;
        }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead tr { background: #f8f8f8; }
        th { padding: 11px 16px; text-align: left; font-weight: 500; color: #666; font-size: 12px; }
        td { padding: 11px 16px; border-top: 1px solid #efefef; }
        tr:hover td { background: #fafafa; }

        /* ── Card ────────────────────────────────────────── */
        .card {
            background: #fff; border: 1px solid #e5e5e5;
            border-radius: 10px; padding: 20px;
        }

        /* ── Formulaire ──────────────────────────────────── */
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; color: #555; margin-bottom: 6px; }
        .form-control {
            width: 100%; padding: 9px 12px; font-size: 13px;
            border: 1px solid #d0d0d0; border-radius: 8px;
            background: #fff; color: #1a1a1a;
        }
        .form-control:focus { outline: none; border-color: #7F77DD; box-shadow: 0 0 0 3px rgba(127,119,221,0.15); }
        .form-row { display: grid; gap: 14px; }
        .form-row.cols-2 { grid-template-columns: 1fr 1fr; }
        .form-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }

        /* ── Alert ───────────────────────────────────────── */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
        .alert-success { background: #d1e7dd; color: #0a3622; border: 1px solid #a3cfbb; }
        .alert-danger  { background: #f8d7da; color: #58151c; border: 1px solid #f1aeb5; }

        /* ── Planning ────────────────────────────────────── */
        .week-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; }
        .day-col {
            background: #fff; border: 1px solid #e5e5e5;
            border-radius: 10px; padding: 14px;
        }
        .day-col.today { border-color: #7F77DD; border-width: 2px; }
        .day-title { font-size: 12px; font-weight: 600; margin-bottom: 12px; color: #555; }
        .day-col.today .day-title { color: #4a3a9a; }
        .slot {
            padding: 8px 10px; border-radius: 7px;
            font-size: 12px; margin-bottom: 6px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .slot-booked  { background: #d1e7dd; color: #0a3622; }
        .slot-waiting { background: #fff3cd; color: #856404; }
        .slot-free    { background: #f5f5f5; color: #aaa; }
        .slot-canceled{ background: #f8d7da; color: #58151c; }

        /* ── Empty state ─────────────────────────────────── */
        .empty-state { text-align: center; padding: 40px; color: #aaa; }
        .empty-state p { font-size: 14px; margin-bottom: 14px; }
    </style>
</head>
<body>

{{-- ── SIDEBAR ──────────────────────────────────────────── --}}
<aside class="sidebar">
    <div class="sidebar-header">
        <h2>Clinique Santé+</h2>
        <p>Espace médecin</p>
    </div>

    <nav class="nav-section">
        <a href="{{ route('medecin.dashboard') }}"
           class="nav-item {{ request()->routeIs('medecin.dashboard') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('medecin.planning') }}"
           class="nav-item {{ request()->routeIs('medecin.planning') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Mon planning
        </a>

        <a href="{{ route('medecin.disponibilites') }}"
           class="nav-item {{ request()->routeIs('medecin.disponibilites*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="16"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Disponibilités
        </a>

        <a href="{{ route('medecin.historique') }}"
           class="nav-item {{ request()->routeIs('medecin.historique') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
            Historique
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="avatar">
            {{ strtoupper(substr(auth()->user()->prenom, 0, 1) . substr(auth()->user()->nom, 0, 1)) }}
        </div>
        <div>
            <p>Dr. {{ auth()->user()->prenom }} {{ auth()->user()->nom }}</p>
            <span>{{ auth()->user()->medecin->specialite ?? 'Médecin' }}</span>
        </div>
    </div>
</aside>

{{-- ── MAIN ──────────────────────────────────────────────── --}}
<main class="main">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @yield('content')
</main>

</body>
</html>
