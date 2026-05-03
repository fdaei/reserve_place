<div>
    <style>
        :root {
            --primary: #66ccff;
            --secondary: #0A2B4E;
            --accent: #F59E0B;
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-500: #64748B;
            --gray-700: #334155;
            --border: #E2E8F0;
        }
        
        /* منوی سریع */
        .nav-quick {
            display: flex;
            gap: 12px;
            margin: 20px 0 30px;
        }
        
        .nav-quick-item {
            flex: 1;
            background: white;
            text-align: center;
            padding: 14px 8px;
            border-radius: 60px;
            text-decoration: none;
            color: var(--secondary);
            font-weight: 500;
            font-size: 14px;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }
        
        .nav-quick-item i {
            color: var(--primary);
            margin-left: 8px;
        }
        
        .nav-quick-item:hover {
            transform: translateY(-2px);
            border-color: var(--primary);
        }
        
        /* باکس جستجو */
        .search-box {
            background: white;
            border-radius: 60px;
            padding: 6px;
            display: flex;
            gap: 8px;
            border: 1px solid var(--border);
            margin-bottom: 25px;
        }
        
        .search-input {
            flex: 1;
            border: none;
            padding: 14px 22px;
            border-radius: 60px;
            font-size: 14px;
            outline: none;
        }
        
        .search-btn {
            background: var(--secondary);
            border: none;
            padding: 12px 32px;
            border-radius: 60px;
            color: white;
            font-weight: 600;
            cursor: pointer;
        }
        
        /* فیلترها */
        .filters-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .filter-select {
            background: white;
            border: 1px solid var(--border);
            border-radius: 40px;
            padding: 10px 20px;
            font-size: 13px;
            color: var(--gray-700);
            cursor: pointer;
        }
        
        .filter-advance-btn {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 40px;
            padding: 10px 20px;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* پنل فیلتر پیشرفته */
        .advanced-panel {
            background: white;
            border-radius: 24px;
            padding: 24px;
            margin: 15px 0 25px;
            border: 1px solid var(--border);
            display: none;
        }
        
        .advanced-panel.show {
            display: block;
        }
        
        .filter-group {
            margin-bottom: 20px;
        }
        
        .filter-group-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 12px;
        }
        
        .filter-options {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }
        
        .filter-option {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            cursor: pointer;
        }
        
        /* عنوان صفحه */
        .page-header {
            margin: 30px 0 20px;
        }
        
        .page-header h1 {
            font-size: 26px;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 8px;
        }
        
        .page-header p {
            color: var(--gray-500);
            font-size: 14px;
        }
        
        /* گرید کارت‌ها */
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin: 30px 0;
        }
        
        .property-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s;
            border: 1px solid var(--border);
        }
        
        .property-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 30px -12px rgba(0,0,0,0.1);
        }
        
        .card-image {
            width: 100%;
            height: 210px;
            object-fit: cover;
        }
        
        .card-content {
            padding: 16px;
        }
        
        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 8px;
        }
        
        .card-location {
            font-size: 12px;
            color: var(--gray-500);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .card-location i {
            color: var(--primary);
        }
        
        .price-tag {
            background: var(--gray-50);
            padding: 10px 12px;
            border-radius: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 12px 0;
        }
        
        .price-amount {
            font-size: 18px;
            font-weight: 800;
            color: var(--secondary);
        }
        
        .price-amount small {
            font-size: 11px;
            font-weight: normal;
        }
        
        .rating-badge {
            background: #10B981;
            color: white;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .card-features {
            display: flex;
            gap: 14px;
            font-size: 12px;
            color: var(--gray-500);
            margin: 12px 0;
        }
        
        .card-features span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .card-features i {
            color: var(--primary);
        }
        
        .btn-reserve {
            display: block;
            width: 100%;
            background: var(--accent);
            text-align: center;
            padding: 12px;
            border-radius: 40px;
            color: white;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s;
            margin-top: 8px;
        }
        
        .btn-reserve:hover {
            background: #D97706;
        }
        
        /* صفحه‌بندی */
        .pagination-modern {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 40px 0 20px;
        }
        
        .page-btn {
            padding: 10px 16px;
            border-radius: 12px;
            background: white;
            border: 1px solid var(--border);
            color: var(--gray-700);
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .page-btn.active {
            background: var(--secondary);
            color: white;
            border-color: var(--secondary);
        }
        
        /* لودینگ */
        .loading-spinner {
            text-align: center;
            padding: 60px;
        }
        
/* بنر تمام واید فصلی - ساده و تمیز */
.hero-season {
    position: relative;
    width: calc(100% + 48px);
    margin-left: -24px;
    margin-right: -24px;
    min-height: 280px;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    margin-bottom: 30px;
    background-color: #0A2B4E;
}

/* اوورلی بسیار نازک (فقط 10% مشکی برای خوانایی متن) */
.hero-overlay-light {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.1);
}

.hero-content {
    position: relative;
    z-index: 2;
    padding: 50px 24px;
}

.hero-icon {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.hero-icon i {
    font-size: 40px;
    color: white;
}

.hero-title {
    font-size: 32px;
    font-weight: 800;
    color: white;
    margin-bottom: 12px;
    text-shadow: 0 4px 1px rgba(0,0,0,0.9);
}

.hero-desc {
    font-size: 16px;
    color: rgba(255,255,255,0.95);
    text-shadow: 0 1px 3px rgba(0,0,0,0.9);
}

@media (max-width: 768px) {
    .hero-season {
        width: calc(100% + 32px);
        margin-left: -16px;
        margin-right: -16px;
        min-height: 220px;
    }
    
    .hero-content {
        padding: 35px 20px;
    }
    
    .hero-icon {
        width: 60px;
        height: 60px;
    }
    
    .hero-icon i {
        font-size: 30px;
    }
    
    .hero-title {
        font-size: 24px;
    }
    
    .hero-desc {
        font-size: 14px;
    }
}
        
        /* شهرهای محبوب */
        .popular-cities {
            margin: 30px 0 40px;
        }
        
.cities-header {
    margin-bottom: 20px;
}

.cities-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--secondary);
    margin: 0;
}
        
        .cities-view-all {
            font-size: 13px;
            color: var(--primary);
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .cities-view-all:hover {
            color: var(--accent);
        }
        
        .cities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 16px;
        }
        
        .city-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            text-decoration: none;
            border: 1px solid var(--border);
            transition: all 0.3s;
        }
        
        .city-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px -12px rgba(0,0,0,0.15);
        }
        
        .city-image {
            width: 100%;
            height: 110px;
            overflow: hidden;
        }
        
        .city-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        
        .city-card:hover .city-image img {
            transform: scale(1.05);
        }
        
        .city-info {
            padding: 12px;
            text-align: center;
        }
        
        .city-name {
            display: block;
            font-size: 15px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 4px;
        }
        
        .city-count {
            display: block;
            font-size: 11px;
            color: var(--gray-500);
        }
        
        @media (max-width: 768px) {
            .search-box {
                flex-direction: column;
                border-radius: 28px;
            }
            
            .filters-row {
                overflow-x: auto;
                flex-wrap: nowrap;
                padding-bottom: 8px;
            }
            
            .filter-select, .filter-advance-btn {
                white-space: nowrap;
            }
            
            .page-header h1 {
                font-size: 20px;
            }
            
            .hero-season {
                width: calc(100% + 32px);
                margin-left: -16px;
                margin-right: -16px;
                min-height: 280px;
            }
            
            .hero-content {
                padding: 40px 20px;
            }
            
            .hero-icon {
                width: 60px;
                height: 60px;
            }
            
            .hero-icon i {
                font-size: 30px;
            }
            
            .hero-title {
                font-size: 24px;
            }
            
            .hero-desc {
                font-size: 14px;
            }
            
            .cities-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 12px;
            }
            
            .city-image {
                height: 90px;
            }
            
            .city-name {
                font-size: 13px;
            }
        }
        
        @media (max-width: 480px) {
            .nav-quick {
                gap: 8px;
            }
            
            .nav-quick-item {
                font-size: 11px;
                padding: 10px 6px;
            }
            
            .cities-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .cities-header {
                flex-direction: column;
                text-align: center;
            }
            
            .hero-datetime {
                font-size: 11px;
                padding: 6px 16px;
            }
        }
    </style>
    
{{-- ===== بنر تمام واید فصلی (بدون ساعت، بدون دکمه) ===== --}}
@php
    $currentMonth = date('n');
    $seasonData = [
        'winter' => ['months' => [12,1,2], 'title' => 'زمستان سفید شمال', 'desc' => 'برف و کوهستان‌های رویایی منتظر شماست', 'icon' => 'fa-snowflake-o', 'bgImage' => 'winter-bg.jpg'],
        'spring' => ['months' => [3,4,5], 'title' => 'بهار شمال، جشن شکوفه‌ها', 'desc' => 'دل‌انگیزترین فصل برای سفر به شمال', 'icon' => 'fa-leaf', 'bgImage' => 'spring-bg.jpg'],
        'summer' => ['months' => [6,7,8], 'title' => 'تابستان داغ، شمال خنک', 'desc' => 'فرار از گرما به جنگل‌های شمال', 'icon' => 'fa-sun-o', 'bgImage' => 'summer-bg.jpg'],
        'autumn' => ['months' => [9,10,11], 'title' => 'پاییز رنگی شمال', 'desc' => 'زیباترین فصل برای عکاسی و طبیعت‌گردی', 'icon' => 'fa-tree', 'bgImage' => 'autumn-bg.jpg'],
    ];
    
    $currentSeason = 'spring';
    foreach ($seasonData as $season => $data) {
        if (in_array($currentMonth, $data['months'])) {
            $currentSeason = $season;
            break;
        }
    }
    $season = $seasonData[$currentSeason];
    
    $bgImagePath = asset('storage/static/seasons/' . $season['bgImage']);
    $hasBgImage = file_exists(public_path('storage/static/seasons/' . $season['bgImage']));
@endphp

<div class="hero-season" @if($hasBgImage) style="background-image: url('{{ $bgImagePath }}');" @endif>
    <div class="hero-overlay-light"></div>
    <div class="hero-content">
        <div class="hero-icon">
            <i class="fa {{ $season['icon'] }}"></i>
        </div>
        <h1 class="hero-title">{{ $season['title'] }}</h1>
        <p class="hero-desc">{{ $season['desc'] }}</p>
    </div>
</div>
    
{{-- ===== شهرهای محبوب ===== --}}
<div class="popular-cities">
    <div class="cities-header">
        <h2 class="cities-title">🏙️ شهرهای محبوب برای اجاره ویلا</h2>
        {{-- لینک "مشاهده همه" حذف شد --}}
    </div>
    <div class="cities-grid">
        @php
            $popularCities = [
                ['id' => 1, 'slug' => 'chalus', 'name' => 'چالوس', 'count' => 24, 'image' => 'chalus.jpg'],
                ['id' => 2, 'slug' => 'ramsar', 'name' => 'رامسر', 'count' => 18, 'image' => 'ramsar.jpg'],
                ['id' => 3, 'slug' => 'gorgan', 'name' => 'گرگان', 'count' => 22, 'image' => 'gorgan.jpg'],
                ['id' => 4, 'slug' => 'masal', 'name' => 'ماسال', 'count' => 15, 'image' => 'masal.jpg'],
                ['id' => 5, 'slug' => 'rasht', 'name' => 'رشت', 'count' => 28, 'image' => 'rasht.jpg'],
                ['id' => 6, 'slug' => 'sari', 'name' => 'ساری', 'count' => 31, 'image' => 'sari.jpg'],
                ['id' => 7, 'slug' => 'noor', 'name' => 'نور', 'count' => 12, 'image' => 'noor.jpg'],
                ['id' => 8, 'slug' => 'behshahr', 'name' => 'بهشهر', 'count' => 9, 'image' => 'behshahr.jpg'],
            ];
        @endphp
        
        @foreach($popularCities as $city)
            <a href="{{ url('/?city_id=' . $city['id']) }}" class="city-card">
                <div class="city-image">
                    <img src="{{ asset('storage/static/cities/' . $city['image']) }}" 
                         alt="{{ $city['name'] }}"
                         loading="lazy"
                         onerror="this.src='{{ asset('storage/static/city-placeholder.jpg') }}'">
                </div>
                <div class="city-info">
                    <span class="city-name">{{ $city['name'] }}</span>
                    <span class="city-count">{{ $city['count'] }} اقامتگاه</span>
                </div>
            </a>
        @endforeach
    </div>
</div>
    
    {{-- ===== جستجو ===== --}}
    <div class="search-box">
        <input type="text" wire:model.defer="searchText" placeholder="جستجوی نام یا کد اقامتگاه..." class="search-input" wire:keydown.enter="search">
        <button type="button" wire:click="search" class="search-btn"><i class="fa fa-search"></i> جستجو</button>
    </div>
    
    {{-- ===== فیلترها ===== --}}
    <div class="filters-row">
        <select wire:model.live="p" class="filter-select">
            <option value="0">🏠 همه استان‌ها</option>
            @foreach($provinces as $province)
                <option value="{{ $province->id }}">{{ $province->name }}</option>
            @endforeach
        </select>
        
        <select wire:model.live="c" class="filter-select" {{ $p == 0 ? 'disabled' : '' }}>
            <option value="0">📍 همه شهرها</option>
            @foreach(\App\Models\City::where("is_use",true)->where("province_id",$p)->get() as $city)
                <option value="{{ $city->id }}">{{ $city->name }}</option>
            @endforeach
        </select>
        
        <select wire:model.live="n" class="filter-select">
            <option value="0">👥 تعداد نفرات</option>
            @for($i=1; $i<=12; $i++)
                <option value="{{ $i }}">{{ $i }} نفر</option>
            @endfor
        </select>
        
        <select wire:model.live="a" class="filter-select">
            <option value="0">💰 مرتب‌سازی</option>
            <option value="1">ارزان‌ترین</option>
            <option value="2">گران‌ترین</option>
        </select>
        
        <button type="button" class="filter-advance-btn" onclick="toggleAdvanced()">
            <i class="fa fa-sliders-h"></i> بیشتر
        </button>
    </div>
    
    {{-- ===== فیلتر پیشرفته ===== --}}
    <div id="advancedPanel" class="advanced-panel">
        <div class="filter-group">
            <div class="filter-group-title">نوع اقامتگاه</div>
            <div class="filter-options">
                @foreach(\App\Models\Residence::getResidenceType() as $key => $item)
                    <label class="filter-option">
                        <input type="checkbox" wire:model.live="residenceType" value="{{ $key }}">
                        <span>{{ $item }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        
        <div class="filter-group">
            <div class="filter-group-title">امکانات ویژه</div>
            <div class="filter-options">
                @foreach(\App\Models\Option::where("show_filter",true)->where("type","residence")->get() as $item)
                    <label class="filter-option">
                        <input type="checkbox" wire:model.live="options" value="{{ $item->id }}">
                        <span>{{ $item->title }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
    
    {{-- ===== عنوان صفحه ===== --}}
    <div class="page-header">
        <h1>اجاره ویلا، سوئیت، آپارتمان و کلبه در ایران</h1>
        <p>{{ number_format($residences->total()) }} اقامتگاه | بهترین قیمت | پشتیبانی ۲۴ ساعته</p>
    </div>
    
    {{-- ===== لودینگ ===== --}}
    <div wire:loading class="loading-spinner">
        <img src="{{ asset('storage/static/loading.gif') }}" style="width: 50px; opacity: 0.4;" alt="loading">
    </div>
    
    {{-- ===== لیست اقامتگاه‌ها ===== --}}
    <div wire:loading.remove>
        @if($residences->count() > 0)
            <div class="properties-grid">
                @foreach($residences as $residence)
                    @php
                        $provinceName = $provinces[$residence->province_id]->name ?? '';
                        $cityName = $cities[$residence->city_id]->name ?? '';
                    @endphp
                    
                    <div class="property-card">
                        <img class="card-image" src="{{ asset('storage/residences/' . $residence->image) }}" alt="{{ $residence->title }}" loading="lazy" onerror="this.src='{{ asset('storage/static/onerror.jpg') }}'">
                        
                        <div class="card-content">
                            <h3 class="card-title">{{ $residence->title }}</h3>
                            <div class="card-location">
                                <i class="fa fa-map-marker"></i>
                                <span>{{ $cityName }}، {{ $provinceName }}</span>
                            </div>
                            
                            <div class="price-tag">
                                <span class="price-amount">{{ number_format($residence->amount) }} <small>تومان</small></span>
                                @if($residence->point > 0)
                                    <span class="rating-badge"><i class="fa fa-star"></i> {{ number_format($residence->point, 1) }}</span>
                                @endif
                            </div>
                            
                            <div class="card-features">
                                <span><i class="fa fa-users"></i> {{ $residence->people_number }} نفر</span>
                                <span><i class="fa fa-bed"></i> {{ $residence->room_number }} اتاق</span>
                                <span><i class="fa fa-expand"></i> {{ $residence->area }} متر</span>
                            </div>
                            
<a href="{{ url('detail/' . $residence->id) }}" class="btn-reserve">
    <i class="fa fa-calendar-check-o"></i> ثبت رزرو
</a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- صفحه‌بندی --}}
            <div class="pagination-modern">
                @if($residences->onFirstPage())
                    <span class="page-btn" style="opacity:0.4">قبلی</span>
                @else
                    <a href="#" wire:click="previousPage" class="page-btn">قبلی</a>
                @endif
                
                <span class="page-btn active">{{ $residences->currentPage() }}</span>
                
                @if($residences->hasMorePages())
                    <a href="#" wire:click="nextPage" class="page-btn">بعدی</a>
                @else
                    <span class="page-btn" style="opacity:0.4">بعدی</span>
                @endif
            </div>
        @else
            <div style="text-align: center; padding: 60px; background: white; border-radius: 24px; border: 1px solid var(--border);">
                <i class="fa fa-home" style="font-size: 48px; color: var(--border);"></i>
                <p style="margin-top: 16px; color: var(--gray-500);">هیچ اقامتگاهی یافت نشد</p>
            </div>
        @endif
    </div>
</div>

<script>
function toggleAdvanced() {
    document.getElementById('advancedPanel').classList.toggle('show');
}

</script>