<div>
    <div class="header">
        <h1>داشبورد مدیریت</h1>
        <div class="user-profile">
            <i class="fas fa-user-circle fa-2x"></i>
        </div>
    </div>
    @php
        use Carbon\Carbon;
        $users=\App\Models\User::all();
        $todayUsers = \App\Models\User::whereDate('created_at', Carbon::today())->get();
        $residences=\App\Models\Residence::all();
        $todayResidences = \App\Models\Residence::whereDate('created_at', Carbon::today())->get();
        $calls=\App\Models\CallResidences::where("type","residence")->get();
        $todayCalls = \App\Models\CallResidences::where("type","residence")->whereDate('created_at', Carbon::today())->get();

        $callsTour=\App\Models\CallResidences::where("type","tour")->get();
        $todayCallsTour = \App\Models\CallResidences::where("type","tour")->whereDate('created_at', Carbon::today())->get();

        $callsFriend=\App\Models\CallResidences::where("type","friend")->get();
        $todayCallsFriend = \App\Models\CallResidences::where("type","friend")->whereDate('created_at', Carbon::today())->get();

        $callsStore=\App\Models\CallResidences::where("type","store")->get();
        $todayCallsStore = \App\Models\CallResidences::where("type","store")->whereDate('created_at', Carbon::today())->get();
    @endphp
    <div class="stats-boxes">
        <div class="stat-box">
            <h4>کل کاربران</h4>
            <p class="stat-value">{{$users->count()}}</p>
            <i class="fas fa-users fa-2x"></i>
        </div>
        <div class="stat-box">
            <h4>کاربران امروز</h4>
            <p class="stat-value">{{$todayUsers->count()}}</p>
            <i class="fas fa-users fa-2x"></i>
        </div>
        <div class="stat-box">
            <h4>کل اقامتگاه ها</h4>
            <p class="stat-value">{{$residences->count()}}</p>
            <i class="fas fa-home fa-2x"></i>
        </div>
        <div class="stat-box">
            <h4>اقامتگاه های امروز</h4>
            <p class="stat-value">{{$todayResidences->count()}}</p>
            <i class="fas fa-home fa-2x"></i>
        </div>

        <div class="stat-box">
            <h4>تماس های اقامتگاه ها</h4>
            <p class="stat-value">{{$calls->count()}}</p>
            <i class="fas fa-comments fa-2x"></i>
        </div>
        <div class="stat-box">
            <h4>تماس های امروز اقامتگاه ها</h4>
            <p class="stat-value">{{$todayCalls->count()}}</p>
            <i class="fas fa-comments fa-2x"></i>
        </div>
        <div class="stat-box">
            <h4>تماس های تور ها</h4>
            <p class="stat-value">{{$callsTour->count()}}</p>
            <i class="fas fa-comments fa-2x"></i>
        </div>
        <div class="stat-box">
            <h4>تماس های امروز تور ها</h4>
            <p class="stat-value">{{$todayCallsTour->count()}}</p>
            <i class="fas fa-comments fa-2x"></i>
        </div>
        <div class="stat-box">
            <h4>تماس های تور ها</h4>
            <p class="stat-value">{{$callsFriend->count()}}</p>
            <i class="fas fa-comments fa-2x"></i>
        </div>
        <div class="stat-box">
            <h4>تماس های امروز تور ها</h4>
            <p class="stat-value">{{$todayCallsFriend->count()}}</p>
            <i class="fas fa-comments fa-2x"></i>
        </div>
        <div class="stat-box">
            <h4>تماس های رستوران ها</h4>
            <p class="stat-value">{{$callsStore->count()}}</p>
            <i class="fas fa-comments fa-2x"></i>
        </div>
        <div class="stat-box">
            <h4>تماس های امروز رستوران ها</h4>
            <p class="stat-value">{{$todayCallsStore->count()}}</p>
            <i class="fas fa-comments fa-2x"></i>
        </div>
    </div>
</div>
