<!DOCTYPE html>
@php
    $pageTitle = trim($__env->yieldContent('title')) ?: 'پنل مدیریت';
    $description = getConfigs('website-description') ?: 'پنل مدیریت';
@endphp
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} | {{ getConfigs('website-title') ?: 'اینجا' }}</title>
    <meta name="description" content="{{ $description }}">

    <script src="/plugin/jquery/jquery.js"></script>
    @vite(['resources/js/app.js'])
    @livewireStyles

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @font-face {
            font-family: 'shabnam';
            src: url('{{ asset("plugin/shabnam-font-v1.1.0/Farsi-Digits/Shabnam-FD.woff") }}') format('woff');
            font-display: swap;
        }

        :root {
            --admin-bg: #f4f7fb;
            --admin-sidebar: #17263a;
            --admin-sidebar-2: #22354f;
            --admin-primary: #2f8dcc;
            --admin-text: #172033;
            --admin-muted: #64748b;
            --admin-border: #e2e8f0;
            --admin-card: #ffffff;
        }

        * {
            box-sizing: border-box;
            font-family: shabnam, Tahoma, Arial, sans-serif;
        }

        body {
            margin: 0;
            background: var(--admin-bg);
            color: var(--admin-text);
            direction: rtl;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .admin-shell {
            display: grid;
            grid-template-columns: 260px minmax(0, 1fr);
            min-height: 100vh;
            width: 100%;
        }

        .sidebar {
            background: var(--admin-sidebar);
            color: #dbe7f5;
            min-height: 100vh;
            height: 100vh;
            overflow-y: auto;
            position: sticky;
            top: 0;
            padding: 20px 14px;
        }

        .sidebar-brand {
            padding: 8px 10px 18px;
            margin-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-brand h2 {
            margin: 0;
            color: #fff;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .menu-section {
            margin: 8px 0;
        }

        .menu-title {
            cursor: pointer;
            list-style: none;
            color: rgba(255,255,255,.72);
            font-weight: 700;
            padding: 10px 12px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .menu-title:hover {
            background: rgba(255,255,255,.06);
        }

        .menu-title::-webkit-details-marker {
            display: none;
        }

        .menu-section-items {
            padding: 4px 0 6px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 10px 12px;
            margin: 3px 0;
            border-radius: 10px;
            color: rgba(255,255,255,.82);
            font-size: 14px;
        }

        .menu-item i {
            width: 18px;
            text-align: center;
            opacity: .85;
        }

        .menu-item:hover,
        .menu-item.active {
            background: var(--admin-primary);
            color: #fff;
        }

        .menu-footer {
            margin-top: 20px;
            padding-top: 14px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .logout-link {
            color: #ffd6d6;
        }

        .main-content {
            min-width: 0;
            padding: 24px;
        }

        .admin-topbar {
            background: #fff;
            border: 1px solid var(--admin-border);
            border-radius: 18px;
            padding: 16px 20px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        }

        .topbar-title-group h1,
        .page-title {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
        }

        .admin-menu-toggle,
        .admin-sidebar-toggle,
        .admin-overlay {
            display: none;
        }

        .admin-content {
            width: 100%;
            min-width: 0;
        }

        .section,
        .listing-panel,
        .admin-dashboard-panel,
        .stats-card,
        .card {
            background: var(--admin-card);
            border: 1px solid var(--admin-border);
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
        }

        .listing-panel-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .listing-panel-title,
        .section h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-page-description {
            color: var(--admin-muted);
            margin: 8px 0 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 18px;
        }

        .admin-dashboard-grid,
        .admin-dashboard-grid--wide {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th,
        td {
            padding: 12px 10px;
            border-bottom: 1px solid var(--admin-border);
            text-align: right;
            vertical-align: middle;
        }

        th {
            color: #475569;
            font-weight: 800;
            background: #f8fafc;
        }

        input,
        select,
        textarea {
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            padding: 8px 10px;
            background: #fff;
        }

        button,
        .btn,
        [type="submit"] {
            border: 0;
            border-radius: 8px;
            padding: 8px 14px;
            background: var(--admin-primary);
            color: #fff;
            cursor: pointer;
        }

        .admin-footer {
            margin-top: 24px;
            color: var(--admin-muted);
            font-size: 13px;
            display: flex;
            justify-content: space-between;
        }

        @media (max-width: 992px) {
            .admin-shell {
                display: block;
            }

            .sidebar {
                position: fixed;
                right: 0;
                top: 0;
                z-index: 1000;
                width: 260px;
                transform: translateX(100%);
                transition: transform .2s ease;
            }

            .admin-sidebar-toggle:checked ~ .sidebar {
                transform: translateX(0);
            }

            .admin-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,.35);
                z-index: 900;
            }

            .admin-sidebar-toggle:checked ~ .admin-overlay {
                display: block;
            }

            .admin-menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                background: #fff;
                border: 1px solid var(--admin-border);
                border-radius: 10px;
                color: var(--admin-text);
            }

            .main-content {
                padding: 16px;
            }

            .stats-grid,
            .admin-dashboard-grid,
            .admin-dashboard-grid--wide {
                grid-template-columns: 1fr;
            }

            .admin-topbar {
                gap: 12px;
            }
        }
    </style>

    @stack('head')
<style id="dashboard-fix">.stats-grid{display:grid!important;grid-template-columns:repeat(4,minmax(0,1fr))!important;gap:16px!important;margin-bottom:18px!important}.stats-card,.admin-stat-card{background:#fff!important;border:1px solid #e2e8f0!important;border-radius:18px!important;padding:18px!important;box-shadow:0 8px 24px rgba(15,23,42,.04)!important}.admin-dashboard-grid,.admin-dashboard-grid--wide{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:16px!important}.admin-dashboard-panel{background:#fff!important;border:1px solid #e2e8f0!important;border-radius:18px!important;padding:18px!important;box-shadow:0 8px 24px rgba(15,23,42,.04)!important}.status-summary-grid{display:grid!important;grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:12px!important}.status-summary-grid>div{background:#f8fafc!important;border:1px solid #e2e8f0!important;border-radius:14px!important;padding:12px!important}.mini-chart{display:flex!important;align-items:end!important;gap:6px!important;min-height:140px!important}.mini-chart>*{background:#2f8dcc!important;border-radius:8px 8px 0 0!important;min-width:12px!important}@media(max-width:992px){.stats-grid,.admin-dashboard-grid,.admin-dashboard-grid--wide,.status-summary-grid{grid-template-columns:1fr!important}}</style></head>

<body class="rtl admin-body">
    <div class="admin-shell">
        <input type="checkbox" id="admin-sidebar-toggle" class="admin-sidebar-toggle">
        @include('admin.partials.sidebar')
        <label for="admin-sidebar-toggle" class="admin-overlay"></label>

        <main class="main-content">
            <div class="admin-topbar top-bar">
                <div class="topbar-start">
                    <label for="admin-sidebar-toggle" class="admin-menu-toggle" aria-label="باز و بسته کردن منو">
                        <i class="fa fa-bars"></i>
                    </label>
                    <div class="topbar-title-group">
                        <h1 class="page-title">{{ $pageTitle }}</h1>
                    </div>
                </div>
            </div>

            <div class="admin-content">
                @include('admin.partials.flash')
                @yield('content')
            </div>

            <footer class="admin-footer">
                <span>پنل مدیریت</span>
                <span>{{ now()->format('Y') }}</span>
            </footer>
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
