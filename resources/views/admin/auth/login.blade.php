<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ورود مدیر | {{ getConfigs('website-title') ?: 'اینجا' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/js/app.js', 'resources/css/template/admin-app.less'])
</head>
<body class="admin-body admin-auth-body">
    <main class="admin-auth-page">
        <section class="admin-auth-card">
            <div class="admin-auth-brand">
                <span><i class="fa fa-umbrella"></i></span>
                <div>
                    <h1>پنل مدیریت</h1>
                    <p>ورود امن با شماره موبایل مدیر</p>
                </div>
            </div>

            @include('admin.partials.flash')

            @if(!$codeSent)
                <form method="POST" action="{{ route('admin.login.send') }}" class="admin-auth-form">
                    @csrf
                    <label for="phone">شماره موبایل</label>
                    <input id="phone" name="phone" value="{{ old('phone', $phone) }}" class="form-control" placeholder="09xxxxxxxxx" autofocus>
                    <button type="submit" class="toolbar-btn toolbar-btn--dark admin-auth-submit">
                        دریافت کد ورود
                        <i class="fa fa-angle-left"></i>
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.login.verify') }}" class="admin-auth-form">
                    @csrf
                    <input type="hidden" name="phone" value="{{ $phone }}">

                    <div class="admin-auth-phone">
                        <span>{{ $phone }}</span>
                        <a href="{{ route('admin.login') }}">ویرایش شماره</a>
                    </div>

                    <label for="code">کد تایید</label>
                    <input id="code" name="code" class="form-control admin-code-input" placeholder="1111" maxlength="4" inputmode="numeric" autofocus>

                    @if(session('admin_demo_code'))
                        <small>کد آزمایشی این محیط: {{ session('admin_demo_code') }}</small>
                    @endif

                    <button type="submit" class="toolbar-btn toolbar-btn--success admin-auth-submit">
                        ورود به پنل
                        <i class="fa fa-sign-in"></i>
                    </button>
                </form>
            @endif
        </section>
    </main>
</body>
</html>
