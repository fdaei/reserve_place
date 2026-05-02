<div class="section">
    <h3>
        <i class="fas fa-chart-pie"></i>
        داشبورد مدیریت
    </h3>

    @php
        use Carbon\Carbon;

        $today = Carbon::today();
        $usersCount = \App\Models\User::count();
        $todayUsersCount = \App\Models\User::whereDate('created_at', $today)->count();
        $residencesCount = \App\Models\Residence::count();
        $todayResidencesCount = \App\Models\Residence::whereDate('created_at', $today)->count();

        $callsResidenceCount = \App\Models\CallResidences::where('type', 'residence')->count();
        $todayCallsResidenceCount = \App\Models\CallResidences::where('type', 'residence')->whereDate('created_at', $today)->count();

        $callsTourCount = \App\Models\CallResidences::where('type', 'tour')->count();
        $todayCallsTourCount = \App\Models\CallResidences::where('type', 'tour')->whereDate('created_at', $today)->count();

        $callsFriendCount = \App\Models\CallResidences::where('type', 'friend')->count();
        $todayCallsFriendCount = \App\Models\CallResidences::where('type', 'friend')->whereDate('created_at', $today)->count();

        $callsStoreCount = \App\Models\CallResidences::where('type', 'store')->count();
        $todayCallsStoreCount = \App\Models\CallResidences::where('type', 'store')->whereDate('created_at', $today)->count();
    @endphp

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <h4>کاربران</h4>
                <span class="stat-badge badge-free">عضویت</span>
            </div>
            <p class="stat-number">{{ $usersCount }}</p>
            <div class="stat-footer">
                <span>کل: {{ $usersCount }}</span>
                <span>امروز: {{ $todayUsersCount }}</span>
            </div>
            <i class="fas fa-users fa-2x stat-icon"></i>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h4>اقامتگاه‌ها</h4>
                <span class="stat-badge badge-broker">محتوا</span>
            </div>
            <p class="stat-number">{{ $residencesCount }}</p>
            <div class="stat-footer">
                <span>کل: {{ $residencesCount }}</span>
                <span>امروز: {{ $todayResidencesCount }}</span>
            </div>
            <i class="fas fa-building fa-2x stat-icon"></i>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h4>تماس اقامتگاه</h4>
            </div>
            <p class="stat-number">{{ $callsResidenceCount }}</p>
            <div class="stat-footer">
                <span>کل: {{ $callsResidenceCount }}</span>
                <span>امروز: {{ $todayCallsResidenceCount }}</span>
            </div>
            <i class="fas fa-phone-volume fa-2x stat-icon"></i>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h4>تماس تور</h4>
            </div>
            <p class="stat-number">{{ $callsTourCount }}</p>
            <div class="stat-footer">
                <span>کل: {{ $callsTourCount }}</span>
                <span>امروز: {{ $todayCallsTourCount }}</span>
            </div>
            <i class="fas fa-bus fa-2x stat-icon"></i>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h4>تماس همسفر</h4>
            </div>
            <p class="stat-number">{{ $callsFriendCount }}</p>
            <div class="stat-footer">
                <span>کل: {{ $callsFriendCount }}</span>
                <span>امروز: {{ $todayCallsFriendCount }}</span>
            </div>
            <i class="fas fa-users-line fa-2x stat-icon"></i>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h4>تماس رستوران</h4>
            </div>
            <p class="stat-number">{{ $callsStoreCount }}</p>
            <div class="stat-footer">
                <span>کل: {{ $callsStoreCount }}</span>
                <span>امروز: {{ $todayCallsStoreCount }}</span>
            </div>
            <i class="fas fa-utensils fa-2x stat-icon"></i>
        </div>
    </div>
</div>
