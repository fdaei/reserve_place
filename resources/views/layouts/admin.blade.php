<!DOCTYPE html>
@php
    $pageTitle = trim($__env->yieldContent('title')) ?: 'پنل مدیریت';
    $description = getConfigs('website-description') ?: 'پنل مدیریت';
@endphp
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} | {{ getConfigs('website-title') ?: 'اینجا' }}</title>
    <meta name="description" content="{{ $description }}">
    <style>
        :root {
            --primary-color: {{ getConfigs('mainColor') ?: '#2f7cf6' }};
            --secondary-color: {{ getConfigs('secondaryColor') ?: '#121b30' }};
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/js/app.js', 'resources/css/template/admin-app.less'])
    @stack('head')
</head>
<body class="rtl admin-body">
    <div class="admin-shell">
        <input type="checkbox" id="admin-sidebar-toggle" class="admin-sidebar-toggle">
        @include('admin.partials.sidebar')
        <label for="admin-sidebar-toggle" class="admin-overlay"></label>

        <main class="main-content">
            @include('admin.partials.topbar', ['pageTitle' => $pageTitle])

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
    @stack('scripts')
</body>
</html>
