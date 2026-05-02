<div class="admin-topbar top-bar">
    <div class="topbar-start">
        <label for="admin-sidebar-toggle" class="admin-menu-toggle" aria-label="باز و بسته کردن منو">
            <i class="fa fa-bars"></i>
        </label>

        <div class="topbar-title-group">
            <div>
                <h1 class="page-title">{{ $pageTitle }}</h1>
                <div class="admin-breadcrumbs">
                    <a href="{{ route('admin.dashboard') }}">داشبورد</a>
                    <i class="fa fa-angle-left"></i>
                    <span>{{ $pageTitle }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="site-mode-badge">
        <div class="employees-online">
            <div class="employee-avatars">
                @forelse($onlineEmployees->take(4) as $employee)
                    <div class="avatar avatar--sky" title="{{ $employee->full_name }} - آنلاین">
                        @if($employee->profile_image_url)
                            <img src="{{ $employee->profile_image_url }}" alt="{{ $employee->full_name }}">
                        @else
                            {{ mb_substr($employee->full_name, 0, 1) }}
                        @endif
                    </div>
                @empty
                    <div class="avatar avatar--amber" title="کارمند آنلاینی ثبت نشده است">-</div>
                @endforelse

                @if($onlineEmployeesCount > 4)
                    <div class="more-indicator" title="{{ $onlineEmployeesCount - 4 }} نفر دیگر">+{{ $onlineEmployeesCount - 4 }}</div>
                @endif
            </div>

            <div class="online-indicator">
                <i class="fa fa-circle"></i>
                {{ number_format($onlineEmployeesCount) }} کارمند آنلاین
            </div>
        </div>

        <span class="mode-indicator">
            <i class="fa {{ $siteRevenueModeIcon ?? 'fa-phone' }}"></i>
            {{ $siteRevenueModeLabel ?? 'حالت رایگان - نمایش مستقیم شماره تماس' }}
        </span>
    </div>
</div>
