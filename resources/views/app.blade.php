<!DOCTYPE html>
@php
    $title = isset($title) ? $title : "";
    $description = isset($description) ? $description : getConfigs("website-description");
    $image = isset($image) ? $image : asset("storage/".getConfigs("website-icon"));
    $url = url()->current();
    $keywords = isset($keywords) ? $keywords : getConfigs("website-words");
    $isAdminPage = request()->is('admin') || request()->is('admin/*');
@endphp

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <title>{{ $title ? $title . " | " : "" }}{{ getConfigs("website-title") }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $url }}">
    
    <meta property="og:title" content="{{ $title ? $title . " | " : "" }}{{ getConfigs("website-title") }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:url" content="{{ $url }}">
    <meta property="og:type" content="website">
    
    <link rel="icon" href="{{ asset('storage/injaa_iconInput_1761765907.png') }}" type="image/x-icon">
    
    {{-- استایل‌های Leaflet --}}
    <link rel="stylesheet" href="https://injaa.com/plugin/leaflet.css">
    <script src="https://injaa.com/plugin/leaflet.js"></script>
    
    <meta name="theme-color" content="#66ccff">
    
    <style>
        :root {
            --primary: #66ccff;
            --secondary: #0A2B4E;
            --accent: #F59E0B;
        }
    </style>
    
    <script src="/plugin/jquery/jquery.js"></script>
    @vite(['resources/js/app.js'])
    @vite(['resources/css/app.less'])
    @livewireStyles
    
    <style>
        @font-face {
            font-family: 'shabnam';
            src: url('{{asset("plugin/shabnam-font-v1.1.0/Farsi-Digits/Shabnam-FD.woff")}}') format('woff');
            font-display: swap;
        }
        * { font-family: 'shabnam', Tahoma, sans-serif; }
        
        body { background: #F8FAFC; color: #1E293B; }
        
        .text-primary { color: #66ccff !important; }
        .bg-primary { background: #0A2B4E !important; }
        .btn-primary { background: #0A2B4E !important; border-color: #0A2B4E !important; }
        .btn-success { background: #F59E0B !important; border-color: #F59E0B !important; }
        .btn-success:hover { background: #D97706 !important; }
        
        .promo-banner {
            background: #F59E0B;
            color: #1E293B;
            text-align: center;
            padding: 8px;
            font-size: 13px;
            font-weight: 500;
        }
        .promo-banner a { color: #1E293B; text-decoration: none; font-weight: 700; margin-right: 8px; }
        .promo-banner a:hover { color: white; }
    </style>
    
    @stack('head')
</head>

<body id="body" class="rtl {{ $isAdminPage ? 'admin-body' : '' }}">
    
    {{-- ========== بخش ادمین (مدیریت) ========== --}}
    @if($isAdminPage)
        {{-- استایل‌های ادمین --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler-rtl.min.css">
        @vite(['resources/css/template/admin-app.less'])
        
        @php
            $path = trim(request()->path(), '/');
            $segments = explode('/', $path);
            $activeAdminPage = $segments[1] ?? 'dashboard';
            if ($path === 'admin') {
                $activeAdminPage = 'dashboard';
            }

            $adminPageTitles = [
                'dashboard' => 'داشبورد',
                'users' => 'مدیریت کاربران عادی',
                'hosts' => 'مدیریت میزبان‌ها',
                'employees' => 'مدیریت کارمندان',
                'booking-requests' => 'درخواست‌های رزرو',
                'bookings' => 'رزروها',
                'host-wallet' => 'کیف پول میزبان‌ها',
                'withdraw-requests' => 'درخواست‌های برداشت',
                'settlements' => 'تسویه حساب‌ها',
                'commissions' => 'کمیسیون‌ها',
                'wallet-transactions' => 'همه تراکنش‌ها',
                'discounts' => 'کد تخفیف',
                'export' => 'خروجی اکسل',
                'role-assign' => 'اتصال نقش به کارمند',
                'roles' => 'مدیریت نقش‌ها',
                'permissions' => 'مدیریت مجوزها',
                'message' => 'تیکت‌ها',
                'supportAreas' => 'دسته‌بندی پیام‌ها',
                'provinces' => 'موقعیت‌ها',
                'properties' => 'اقامتگاه‌ها',
                'residences' => 'اقامتگاه‌ها',
                'pending-properties' => 'در انتظار تایید اقامتگاه‌ها',
                'tours' => 'تورها',
                'pending-tours' => 'در انتظار تایید تورها',
                'restaurants' => 'کافه و رستوران',
                'pending-restaurants' => 'در انتظار تایید رستوران‌ها',
                'travel-partners' => 'همسفر',
                'pending-partners' => 'در انتظار تایید (همسفر)',
                'blog' => 'مدیریت وبلاگ',
                'tools' => 'امکانات',
                'tools-foodstore' => 'امکانات رستوران',
                'tools-friends' => 'آپشن همسفر',
                'comments' => 'نظرات',
                'pages' => 'مدیریت صفحات',
                'banners' => 'مدیریت بنرها',
                'seasonal-banners' => 'بنرهای فصلی',
                'footer-links' => 'فوتر و لینک‌ها',
                'locations' => 'شهرها و استان‌ها',
                'payment-settings' => 'تنظیمات پرداخت',
                'sms-settings' => 'تنظیمات پیامک',
                'seo-settings' => 'تنظیمات SEO',
                'website-settings' => 'تنظیمات سایت',
            ];
            $adminPresence = [
                ['name' => 'محمدی', 'initial' => 'م', 'tone' => 'sky'],
                ['name' => 'کریمی', 'initial' => 'ک', 'tone' => 'amber'],
                ['name' => 'رضایی', 'initial' => 'ر', 'tone' => 'rose'],
                ['name' => 'احمدی', 'initial' => 'ا', 'tone' => 'violet'],
            ];
        @endphp

        <div class="admin-shell">
            <input type="checkbox" id="admin-sidebar-toggle" class="admin-sidebar-toggle">
            @include("partials.admin-sidebar")
            <label for="admin-sidebar-toggle" class="admin-overlay"></label>

            <main class="main-content">
                <div class="admin-topbar top-bar">
                    <div class="topbar-start">
                        <label for="admin-sidebar-toggle" class="admin-menu-toggle" aria-label="باز و بسته کردن منو">
                            <i class="fa fa-bars"></i>
                        </label>

                        <div class="topbar-title-group">
                            <h1 class="page-title" id="pageTitle">{{ $adminPageTitles[$activeAdminPage] ?? 'پنل مدیریت' }}</h1>
                        </div>
                    </div>

                    <div class="site-mode-badge">
                        <div class="employees-online">
                            <div class="employee-avatars">
                                @foreach($adminPresence as $presence)
                                    <div class="avatar avatar--{{ $presence['tone'] }}" title="{{ $presence['name'] }} - آنلاین">{{ $presence['initial'] }}</div>
                                @endforeach
                                <div class="more-indicator" title="دو نفر دیگر">+۲</div>
                            </div>

                            <div class="online-indicator">
                                <i class="fa fa-circle"></i>
                                ۶ نفر آنلاین
                            </div>
                        </div>

                        <span class="mode-indicator" id="siteModeIndicator">
                            <i class="fa fa-chart-line"></i>
                            حالت عادی
                        </span>
                    </div>
                </div>

                <div class="admin-content">
                    @yield("content")
                </div>
            </main>
        </div>
        
    {{-- ========== بخش فرانت سایت (غیر ادمین) ========== --}}
    @else
        <header>
            <div style="background-image: url('{{asset("storage/".getConfigs("bannerSeasonImage"))}}'); background-size: cover; background-position: center; background-color: #0A2B4E;">
                <div class="container">
                    <div class="row">
                        @include("partials.header")
                    </div>
                </div>
            </div>
            
            <div class="promo-banner">
                <i class="fa fa-bullhorn"></i>
                {{ getConfigs("mainBannerText") }}
                <a href="{{ auth()->check() ? url('/dashboard') : url('/login') }}">
                    شروع میزبانی <i class="fa fa-arrow-left"></i>
                </a>
            </div>
        </header>
        
        @if(getConfigs("websiteStatus")==1)
            <div class="container">
                <main>
                    @yield("content")
                </main>
            </div>
        @else
            <div class="container">
                <main>
                    <div style="text-align: center; padding: 50px;">
                        <h1>درحال تعمیرات</h1>
                        <p>{{ getConfigs("OfflineModeText") }}</p>
                        <img src="{{ asset("storage/".getConfigs("offlineModeIcon")) }}" style="max-width: 300px;" alt="تعمیرات">
                    </div>
                </main>
            </div>
        @endif
        
        <footer>
            @include("partials.footer")
        </footer>
    @endif
    
    @livewireScripts
    @stack('scripts')
    
    {{-- ===== چت بات ===== --}}
    @if(!$isAdminPage)
    <style>
        .chat-wrapper {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            font-family: 'shabnam', Tahoma, sans-serif;
        }
        
        .chat-toggle-btn {
            width: 55px;
            height: 55px;
            background: #F59E0B;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            transition: all 0.2s;
        }
        
        .chat-toggle-btn:hover {
            transform: scale(1.05);
            background: #D97706;
        }
        
        .chat-toggle-btn i {
            font-size: 26px;
            color: white;
        }
        
        .chat-box {
            position: absolute;
            bottom: 70px;
            right: 0;
            width: 320px;
            max-width: calc(100vw - 40px);
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            display: none;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #E2E8F0;
        }
        
        .chat-box.show {
            display: flex;
        }
        
        .chat-header {
            background: #0A2B4E;
            color: white;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .chat-header h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
        }
        
        .chat-header h4 i {
            color: #66ccff;
            margin-left: 6px;
        }
        
        .chat-header .close-btn {
            cursor: pointer;
            font-size: 18px;
            opacity: 0.7;
            padding: 0 5px;
        }
        
        .chat-messages {
            height: 350px;
            padding: 12px;
            overflow-y: auto;
            background: #F8FAFC;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .message-bot {
            background: white;
            padding: 8px 12px;
            border-radius: 16px;
            border-top-right-radius: 4px;
            max-width: 85%;
            align-self: flex-start;
            font-size: 12px;
            color: #1E293B;
            border: 1px solid #E2E8F0;
            line-height: 1.5;
        }
        
        .message-user {
            background: #66ccff;
            color: #0A2B4E;
            padding: 8px 12px;
            border-radius: 16px;
            border-top-left-radius: 4px;
            max-width: 85%;
            align-self: flex-end;
            font-size: 12px;
        }
        
        .quick-buttons {
            padding: 10px 12px;
            border-top: 1px solid #E2E8F0;
            background: white;
        }
        
        .quick-title {
            font-size: 10px;
            color: #64748B;
            margin-bottom: 8px;
        }
        
        .btn-group-custom {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        
        .quick-btn {
            background: #F1F5F9;
            padding: 5px 10px;
            border-radius: 30px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s;
            color: #0A2B4E;
        }
        
        .quick-btn:hover {
            background: #66ccff;
            color: white;
        }
        
        .chat-input-area {
            display: flex;
            padding: 10px 12px;
            border-top: 1px solid #E2E8F0;
            background: white;
            gap: 8px;
        }
        
        .chat-input-area input {
            flex: 1;
            border: 1px solid #E2E8F0;
            border-radius: 40px;
            padding: 8px 14px;
            font-size: 12px;
            outline: none;
        }
        
        .chat-input-area input:focus {
            border-color: #66ccff;
        }
        
        .chat-input-area button {
            background: #F59E0B;
            border: none;
            border-radius: 40px;
            padding: 8px 16px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            font-size: 12px;
        }
        
        @media (max-width: 480px) {
            .chat-box {
                width: 300px;
                right: -10px;
                bottom: 65px;
            }
            
            .chat-messages {
                height: 300px;
            }
            
            .chat-toggle-btn {
                width: 48px;
                height: 48px;
            }
            
            .chat-toggle-btn i {
                font-size: 22px;
            }
        }
    </style>

    <div class="chat-wrapper">
        <div class="chat-toggle-btn" onclick="toggleChat()">
            <i class="fa fa-commenting"></i>
        </div>
        
        <div class="chat-box" id="chatBox">
            <div class="chat-header">
                <h4><i class="fa fa-robot"></i> راهنمای اینجا</h4>
                <span class="close-btn" onclick="toggleChat()">&times;</span>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <div class="message-bot">
                    👋 سلام! به راهنمای اینجا خوش آمدی.<br>
                    چطور می‌توانم به تو کمک کنم؟
                </div>
            </div>
            
            <div class="quick-buttons">
                <div class="quick-title">⚡ سوالات سریع:</div>
                <div class="btn-group-custom">
                    <span class="quick-btn" onclick="askQuestion('چطوری ویلا اجاره کنم؟')">🏠 چطوری اجاره کنم؟</span>
                    <span class="quick-btn" onclick="askQuestion('بهترین شهر شمال کجاست؟')">📍 بهترین شهر شمال</span>
                    <span class="quick-btn" onclick="askQuestion('قیمت ویلاها چقدر است؟')">💰 قیمت ویلاها</span>
                    <span class="quick-btn" onclick="askQuestion('قوانین لغو رزرو چیست؟')">❌ قوانین لغو</span>
                </div>
            </div>
            
            <div class="chat-input-area">
                <input type="text" id="chatInput" placeholder="سوال خود را بنویسید..." onkeypress="handleKeyPress(event)">
                <button onclick="sendMessage()">ارسال</button>
            </div>
        </div>
    </div>

    <script>
        function toggleChat() {
            const box = document.getElementById('chatBox');
            box.classList.toggle('show');
            if (box.classList.contains('show')) {
                scrollToBottom();
            }
        }
        
        function scrollToBottom() {
            const messages = document.getElementById('chatMessages');
            messages.scrollTop = messages.scrollHeight;
        }
        
        function addMessage(text, isUser) {
            const messages = document.getElementById('chatMessages');
            const div = document.createElement('div');
            div.className = isUser ? 'message-user' : 'message-bot';
            div.innerHTML = text.replace(/\n/g, '<br>');
            messages.appendChild(div);
            scrollToBottom();
        }
        
        function sendMessage() {
            const input = document.getElementById('chatInput');
            const question = input.value.trim();
            if (question === '') return;
            
            addMessage(question, true);
            input.value = '';
            
            setTimeout(() => {
                const answer = getAnswer(question);
                addMessage(answer, false);
            }, 400);
        }
        
        function askQuestion(question) {
            addMessage(question, true);
            
            setTimeout(() => {
                const answer = getAnswer(question);
                addMessage(answer, false);
            }, 400);
        }
        
        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }
        
        function getAnswer(question) {
            const q = question.toLowerCase();
            
            if (q.includes('اجاره') && q.includes('ویلا')) {
                return '🏠 <b>راهنمای اجاره ویلا:</b><br><br>1️⃣ از فیلترهای بالا استان و شهر را انتخاب کنید<br>2️⃣ تعداد نفرات را مشخص کنید<br>3️⃣ اقامتگاه مورد نظر را انتخاب کنید<br>4️⃣ روی دکمه "ثبت رزرو" کلیک کنید<br><br>✅ همین! رزرو انجام شد.';
            }
            
            if (q.includes('بهترین شهر') || q.includes('شمال')) {
                return '🌲 <b>بهترین شهرهای شمال:</b><br><br>📍 <b>چالوس</b> - لوکس و شیک<br>📍 <b>رامسر</b> - خانوادگی و آرام<br>📍 <b>گرگان</b> - جنگلی و بکر<br>📍 <b>ماسال</b> - روستایی و ارزان<br><br>کدام شهر مد نظر شماست؟';
            }
            
            if (q.includes('قیمت') || q.includes('هزینه')) {
                return '💰 <b>محدوده قیمت ویلاها:</b><br><br>🟢 ارزان: ۵۰۰ هزار - ۱ میلیون تومان<br>🟡 متوسط: ۱ - ۲ میلیون تومان<br>🔴 لوکس: ۲ - ۵ میلیون تومان<br><br>⚠️ قیمت آخر هفته و تعطیلات بیشتر است.';
            }
            
            if (q.includes('لغو') || q.includes('رزرو')) {
                return '❌ <b>قوانین لغو رزرو:</b><br><br>• لغو ۴۸ ساعت قبل: ۱۰۰٪ بازپرداخت<br>• لغو ۲۴ ساعت قبل: ۵۰٪ بازپرداخت<br>• لغو کمتر از ۲۴ ساعت: بدون بازپرداخت<br><br>برای لغو به بخش "رزروهای من" در پروفایل بروید.';
            }
            
            if (q.includes('سلام') || q.includes('خوبی') || q.includes('هی')) {
                return '👋 سلام! خوبم، ممنون که پرسیدی.<br><br>چطور می‌توانم به تو کمک کنم؟<br><br>👇 می‌توانی از دکمه‌های پایین صفحه استفاده کنی.';
            }
            
            return '🙏 <b>راهنمای اینجا:</b><br><br>روی دکمه‌های زیر کلیک کن:<br><br>🏠 <b>چطوری ویلا اجاره کنم؟</b><br>📍 <b>بهترین شهر شمال کجاست؟</b><br>💰 <b>قیمت ویلاها چقدر است؟</b><br>❌ <b>قوانین لغو رزرو چیست؟</b><br><br>یک گزینه رو انتخاب کن تا راهنمایی شوی.';
        }
    </script>
    @endif
    
</body>
</html>