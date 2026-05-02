@php
    $path = trim(request()->path(), '/');
    $activePage = explode('/', $path)[1] ?? 'dashboard';
    $resourceUrl = fn (string $key) => route('admin.resources.index', $key);
    $resourceActive = fn (array $keys) => in_array($activePage, $keys, true);

    $sections = [
        [
            'title' => 'داشبورد',
            'icon' => 'fa-pie-chart',
            'items' => [
                ['href' => route('admin.dashboard'), 'icon' => 'fa-pie-chart', 'label' => 'داشبورد اصلی', 'active' => request()->routeIs('admin.dashboard') || $path === 'admin'],
            ],
        ],
        [
            'title' => 'مدیریت دسترسی‌ها',
            'icon' => 'fa-lock',
            'items' => [
                ['href' => $resourceUrl('roles'), 'icon' => 'fa-user', 'label' => 'نقش‌ها', 'active' => $activePage === 'roles'],
                ['href' => $resourceUrl('permissions'), 'icon' => 'fa-key', 'label' => 'مجوزها', 'active' => $activePage === 'permissions'],
                ['href' => route('admin.role-assign.index'), 'icon' => 'fa-users', 'label' => 'اتصال نقش به کاربر پنل', 'active' => $activePage === 'role-assign'],
            ],
        ],
        [
            'title' => 'مدیریت اقامتگاه و محتوا',
            'icon' => 'fa-building',
            'items' => [
                ['href' => $resourceUrl('residences'), 'icon' => 'fa-building', 'label' => 'اقامتگاه‌ها', 'active' => $resourceActive(['residences', 'properties'])],
                ['href' => $resourceUrl('pending-properties'), 'icon' => 'fa-clock-o', 'label' => 'در انتظار تأیید اقامتگاه‌ها', 'active' => $activePage === 'pending-properties'],
                ['href' => $resourceUrl('tours'), 'icon' => 'fa-bus', 'label' => 'تورها', 'active' => $activePage === 'tours'],
                ['href' => $resourceUrl('pending-tours'), 'icon' => 'fa-clock-o', 'label' => 'در انتظار تأیید تورها', 'active' => $activePage === 'pending-tours'],
                ['href' => $resourceUrl('restaurants'), 'icon' => 'fa-cutlery', 'label' => 'کافه و رستوران', 'active' => $activePage === 'restaurants'],
                ['href' => $resourceUrl('pending-restaurants'), 'icon' => 'fa-clock-o', 'label' => 'در انتظار تأیید رستوران‌ها', 'active' => $activePage === 'pending-restaurants'],
                ['href' => $resourceUrl('travel-partners'), 'icon' => 'fa-users', 'label' => 'همسفر', 'active' => $activePage === 'travel-partners'],
                ['href' => $resourceUrl('pending-partners'), 'icon' => 'fa-clock-o', 'label' => 'در انتظار تأیید همسفر', 'active' => $activePage === 'pending-partners'],
            ],
        ],
        [
            'title' => 'کاربران و پرسنل',
            'icon' => 'fa-users',
            'items' => [
                ['href' => $resourceUrl('users'), 'icon' => 'fa-users', 'label' => 'کاربران عادی', 'active' => $activePage === 'users'],
                ['href' => $resourceUrl('hosts'), 'icon' => 'fa-star', 'label' => 'میزبانان', 'active' => $activePage === 'hosts'],
                ['href' => $resourceUrl('employees'), 'icon' => 'fa-user', 'label' => 'کارمندان', 'active' => $activePage === 'employees'],
            ],
        ],
        [
            'title' => 'رزرو و مالی',
            'icon' => 'fa-credit-card',
            'items' => [
                ['href' => $resourceUrl('booking-requests'), 'icon' => 'fa-phone', 'label' => 'درخواست‌های رزرو', 'active' => $activePage === 'booking-requests'],
                ['href' => $resourceUrl('bookings'), 'icon' => 'fa-calendar-check-o', 'label' => 'رزروها', 'active' => $activePage === 'bookings'],
                ['href' => route('admin.host-wallet.index'), 'icon' => 'fa-money', 'label' => 'کیف پول میزبانان', 'active' => $activePage === 'host-wallet'],
                ['href' => $resourceUrl('withdraw-requests'), 'icon' => 'fa-bank', 'label' => 'درخواست برداشت', 'active' => $activePage === 'withdraw-requests'],
                ['href' => $resourceUrl('settlements'), 'icon' => 'fa-check-square-o', 'label' => 'تسویه حساب‌ها', 'active' => $activePage === 'settlements'],
                ['href' => route('admin.commissions.index'), 'icon' => 'fa-percent', 'label' => 'کمیسیون‌ها', 'active' => $activePage === 'commissions'],
                ['href' => $resourceUrl('wallet-transactions'), 'icon' => 'fa-exchange', 'label' => 'همه تراکنش‌ها', 'active' => $activePage === 'wallet-transactions'],
                ['href' => $resourceUrl('discounts'), 'icon' => 'fa-ticket', 'label' => 'کد تخفیف', 'active' => $activePage === 'discounts'],
                ['href' => route('admin.export.index'), 'icon' => 'fa-file-excel-o', 'label' => 'خروجی اکسل', 'active' => $activePage === 'export'],
            ],
        ],
        [
            'title' => 'پشتیبانی',
            'icon' => 'fa-life-ring',
            'items' => [
                ['href' => route('admin.tickets.index'), 'icon' => 'fa-ticket', 'label' => 'تیکت‌ها', 'active' => $activePage === 'message'],
                ['href' => $resourceUrl('notifications'), 'icon' => 'fa-bell', 'label' => 'اعلان‌ها', 'active' => $activePage === 'notifications'],
                ['href' => $resourceUrl('sms-templates'), 'icon' => 'fa-commenting', 'label' => 'قالب‌های پیامک', 'active' => $activePage === 'sms-templates'],
                ['href' => $resourceUrl('sms-logs'), 'icon' => 'fa-commenting-o', 'label' => 'ارسال و لاگ پیامک', 'active' => $activePage === 'sms-logs'],
                ['href' => $resourceUrl('supportAreas'), 'icon' => 'fa-list-alt', 'label' => 'دسته‌بندی پیام‌ها', 'active' => $activePage === 'supportAreas'],
            ],
        ],
        [
            'title' => 'محتوای سایت',
            'icon' => 'fa-file-text-o',
            'items' => [
                ['href' => $resourceUrl('blog'), 'icon' => 'fa-pencil', 'label' => 'وبلاگ', 'active' => $activePage === 'blog'],
                ['href' => $resourceUrl('blog-categories'), 'icon' => 'fa-folder-open-o', 'label' => 'دسته‌بندی وبلاگ', 'active' => $activePage === 'blog-categories'],
                ['href' => $resourceUrl('pages'), 'icon' => 'fa-file-text-o', 'label' => 'صفحات', 'active' => $activePage === 'pages'],
                ['href' => $resourceUrl('banners'), 'icon' => 'fa-picture-o', 'label' => 'بنرها', 'active' => $activePage === 'banners'],
                ['href' => route('admin.seasonal-banners.edit'), 'icon' => 'fa-leaf', 'label' => 'بنرهای فصلی', 'active' => $activePage === 'seasonal-banners'],
                ['href' => route('admin.footer-links.index'), 'icon' => 'fa-link', 'label' => 'فوتر و لینک‌ها', 'active' => $activePage === 'footer-links'],
            ],
        ],
        [
            'title' => 'مکان‌ها',
            'icon' => 'fa-map-marker',
            'items' => [
                ['href' => $resourceUrl('countries'), 'icon' => 'fa-globe', 'label' => 'کشورها', 'active' => $activePage === 'countries'],
                ['href' => route('admin.locations.index'), 'icon' => 'fa-map', 'label' => 'شهرها و استان‌ها', 'active' => $activePage === 'locations'],
                ['href' => $resourceUrl('provinces'), 'icon' => 'fa-map-marker', 'label' => 'استان‌ها', 'active' => $activePage === 'provinces'],
                ['href' => $resourceUrl('cities'), 'icon' => 'fa-map-pin', 'label' => 'شهرها', 'active' => $activePage === 'cities'],
                ['href' => $resourceUrl('popular-cities'), 'icon' => 'fa-star', 'label' => 'شهرهای محبوب', 'active' => $activePage === 'popular-cities'],
                ['href' => $resourceUrl('tools'), 'icon' => 'fa-tags', 'label' => 'امکانات', 'active' => $activePage === 'tools'],
            ],
        ],
        [
            'title' => 'تنظیمات',
            'icon' => 'fa-sliders',
            'items' => [
                ['href' => route('admin.settings.edit'), 'icon' => 'fa-sliders', 'label' => 'تنظیمات عمومی سایت', 'active' => $activePage === 'website-settings'],
                ['href' => route('admin.settings.seo'), 'icon' => 'fa-search', 'label' => 'تنظیمات SEO', 'active' => $activePage === 'seo-settings'],
                ['href' => route('admin.settings.payment'), 'icon' => 'fa-credit-card', 'label' => 'تنظیمات پرداخت', 'active' => $activePage === 'payment-settings'],
                ['href' => route('admin.settings.sms'), 'icon' => 'fa-commenting', 'label' => 'تنظیمات پیامک', 'active' => $activePage === 'sms-settings'],
                ['href' => $resourceUrl('activity-logs'), 'icon' => 'fa-history', 'label' => 'لاگ فعالیت‌ها', 'active' => $activePage === 'activity-logs'],
                ['href' => $resourceUrl('security-events'), 'icon' => 'fa-shield', 'label' => 'رخدادهای امنیتی', 'active' => $activePage === 'security-events'],
                ['href' => $resourceUrl('tools-foodstore'), 'icon' => 'fa-cutlery', 'label' => 'امکانات رستوران', 'active' => $activePage === 'tools-foodstore'],
                ['href' => $resourceUrl('tools-friends'), 'icon' => 'fa-users', 'label' => 'آپشن همسفر', 'active' => $activePage === 'tools-friends'],
            ],
        ],
    ];
@endphp

<aside class="sidebar" id="adminSidebar">
    <div class="sidebar-brand sidebar-brand--classic">
        <h2>
            <i class="fa fa-umbrella"></i>
            پنل مدیریت
        </h2>
    </div>

    <nav class="sidebar-nav" aria-label="منوی مدیریت">
        @foreach($sections as $section)
            @php($sectionActive = collect($section['items'])->contains(fn ($item) => !empty($item['active'])))
            <details class="menu-section" data-admin-menu-section {{ $sectionActive || ($loop->first && !collect($sections)->flatMap(fn ($menu) => $menu['items'])->contains(fn ($item) => !empty($item['active']))) ? 'open' : '' }}>
                <summary class="menu-title">
                    <span>
                        <i class="fa {{ $section['icon'] }}"></i>
                        {{ $section['title'] }}
                    </span>
                    <i class="fa fa-angle-left menu-section-chevron"></i>
                </summary>

                <div class="menu-section-items">
                    @foreach($section['items'] as $item)
                        <a href="{{ $item['href'] }}" @class(['menu-item', 'active' => !empty($item['active'])])>
                            <i class="fa {{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </details>
        @endforeach
    </nav>

    <div class="menu-footer">
        <a href="{{ route('admin.logout') }}" class="menu-item logout-link">
            <i class="fa fa-sign-out"></i>
            <span>خروج از حساب</span>
        </a>
    </div>
</aside>
