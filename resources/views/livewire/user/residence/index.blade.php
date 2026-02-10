<div>
    @vite(['resources/css/user/index.less'])
    @php
        // Schema اصلی برای صفحه لیست اقامتگاه‌ها
        $schemaData = [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "name" => "لیست اقامتگاه‌های ایران - اجاره ویلا، سوئیت، آپارتمان و کلبه",
            "description" => getConfigs("website-description"),
            "url" => url()->current(),
            "numberOfItems" => $residences->count(),
            "itemListElement" => $residences->map(function ($residence, $index) use ($provinces, $cities) {
                $provinceName = $provinces[$residence->province_id]->name ?? 'نامشخص';
                $cityName = $cities[$residence->city_id]->name ?? 'نامشخص';
                
                // نوع اقامتگاه
                $residenceType = \App\Models\Residence::getResidenceType()[$residence->residence_type] ?? 'اقامتگاه';
                $areaType = \App\Models\Residence::getAreaType()[$residence->area_type] ?? '';
                
                return [
                    "@type" => "ListItem",
                    "position" => $index + 1,
                    "item" => [
                        "@type" => "VacationRental",
                        "name" => $residence->title,
                        "description" => "{$residenceType} {$areaType} در {$cityName}، {$provinceName} با {$residence->room_number} اتاق و ظرفیت {$residence->people_number} نفر",
                        "image" => url("storage/residences/{$residence->image}"),
                        "url" => url("detail/{$residence->id}"),
                        "address" => [
                            "@type" => "PostalAddress",
                            "addressLocality" => $cityName,
                            "addressRegion" => $provinceName,
                            "addressCountry" => "IR"
                        ],
                        "numberOfRooms" => (int)$residence->room_number,
                        "occupancy" => [
                            "@type" => "QuantitativeValue",
                            "maxValue" => (int)$residence->people_number
                        ],
                        "floorSize" => [
                            "@type" => "QuantitativeValue",
                            "value" => (int)$residence->area,
                            "unitCode" => "MTK"
                        ],
                        "priceRange" => number_format($residence->amount) . " تومان",
                        "aggregateRating" => $residence->comments->count() > 0 ? [
                            "@type" => "AggregateRating",
                            "ratingValue" => number_format($residence->point, 1),
                            "ratingCount" => $residence->comments->count(),
                            "bestRating" => "5",
                            "worstRating" => "0"
                        ] : null
                    ]
                ];
            })->toArray(),
        ];
        
        // Breadcrumb Schema
        $breadcrumbSchema = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                [
                    "@type" => "ListItem",
                    "position" => 1,
                    "name" => "صفحه اصلی",
                    "item" => url('/')
                ],
                [
                    "@type" => "ListItem",
                    "position" => 2,
                    "name" => "اقامتگاه‌ها",
                    "item" => url()->current()
                ]
            ]
        ];
        
        // WebPage Schema
        $webPageSchema = [
            "@context" => "https://schema.org",
            "@type" => "WebPage",
            "name" => "اجاره ویلا، سوئیت، آپارتمان و کلبه در سراسر ایران | " . getConfigs("website-title"),
            "description" => getConfigs("website-description"),
            "url" => url()->current(),
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => url()->current() . "?searchText={search_term_string}",
                "query-input" => "required name=search_term_string"
            ],
            "breadcrumb" => [
                "@type" => "BreadcrumbList",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => "صفحه اصلی",
                        "item" => url('/')
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 2,
                        "name" => "اقامتگاه‌ها",
                        "item" => url()->current()
                    ]
                ]
            ]
        ];
        
        // Organization Schema
        $organizationSchema = [
            "@context" => "https://schema.org",
            "@type" => "Organization",
            "name" => getConfigs("website-title"),
            "description" => getConfigs("website-description"),
            "url" => url('/'),
            "logo" => url('storage/injaa_iconInput_1761765907.png'),
            "contactPoint" => [
                "@type" => "ContactPoint",
                "telephone" => "+98-4846",
                "contactType" => "customer service",
                "availableLanguage" => "Persian"
            ],
            "sameAs" => [
                "https://instagram.com/injaa_com",
                "https://t.me/injaa_com"
            ]
        ];
        
        // FAQ Schema
        $faqSchema = [
            "@context" => "https://schema.org",
            "@type" => "FAQPage",
            "mainEntity" => [
                [
                    "@type" => "Question",
                    "name" => "چگونه می‌توانم اقامتگاه رزرو کنم؟",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "برای رزرو اقامتگاه در سایت اینجا، ابتدا مقصد، تاریخ و تعداد نفرات را انتخاب کنید. سپس از بین اقامتگاه‌های موجود، مورد نظر خود را انتخاب و با چند کلیک رزرو را تکمیل نمایید."
                    ]
                ],
                [
                    "@type" => "Question",
                    "name" => "آیا امکان لغو رزرو وجود دارد؟",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "بله، سیاست لغو رزرو برای هر اقامتگاه متفاوت است و در صفحه توضیحات اقامتگاه قابل مشاهده می‌باشد."
                    ]
                ],
                [
                    "@type" => "Question",
                    "name" => "چه نوع اقامتگاه‌هایی موجود است؟",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "انواع ویلا، سوئیت، آپارتمان و کلبه در مناطق جنگلی، ساحلی، کوهستانی، روستایی و شهری در سراسر ایران."
                    ]
                ]
            ]
        ];
    @endphp

    {{-- Structured Data در head --}}
    @push('head')
        {{-- ItemList Schema --}}
        <script type="application/ld+json">
            {!! json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        
        {{-- Breadcrumb Schema --}}
        <script type="application/ld+json">
            {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        
        {{-- WebPage Schema --}}
        <script type="application/ld+json">
            {!! json_encode($webPageSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        
        {{-- Organization Schema --}}
        <script type="application/ld+json">
            {!! json_encode($organizationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        
        {{-- FAQ Schema --}}
        <script type="application/ld+json">
            {!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        
        {{-- LocalBusiness Schema (اختیاری) --}}
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": ["TravelAgency", "LocalBusiness"],
            "name": "{{ getConfigs('website-title') }}",
            "image": "{{ url('storage/injaa_iconInput_1761765907.png') }}",
            "description": "{{ getConfigs('website-description') }}",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "تهران ژاندارمری خیابان ایثار نبش خیابان مالک",
                "addressLocality": "تهران",
                "addressCountry": "IR"
            },
            "telephone": "+98-4846",
            "openingHours": "Mo-Su 00:00-23:59",
            "url": "{{ url('/') }}",
            "priceRange": "$$",
            "sameAs": [
                "https://instagram.com/injaa_com",
                "https://t.me/injaa_com"
            ]
        }
        </script>
    @endpush

    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb" style="padding: 10px 0; background-color: #f8f9fa; border-radius: 5px; margin-bottom: 20px;">
        <ol class="breadcrumb mb-0" style="background: transparent; padding: 0 15px;">
            <li class="breadcrumb-item">
                <a href="{{ url('/') }}" class="text-decoration-none text-primary">
                    <i class="fa fa-home"></i> صفحه اصلی
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fa fa-building"></i> اقامتگاه‌ها
            </li>
        </ol>
    </nav>

<div class="row text-center align-items-center" id="btns">
    <div class="col-3 mb-3">
        <a href="{{ \Illuminate\Support\Facades\URL::to('') }}" class="d-flex flex-column align-items-center text-decoration-none text-dark" aria-label="اقامتگاه ها">
            <i class="fa fa-home mb-1" style="font-size: 1.4rem;"></i>
            <span class="fw-semibold" style="font-size: 0.75rem;">اقامتگاه ها</span>
        </a>
    </div>
    <div class="col-3 mb-3">
        <a href="{{ \Illuminate\Support\Facades\URL::to('tours') }}" class="d-flex flex-column align-items-center text-decoration-none text-dark" aria-label="تورها">
            <i class="fa fa-map-pin mb-1" style="font-size: 1.4rem;"></i>
            <span class="fw-semibold" style="font-size: 0.75rem;">تورها</span>
        </a>
    </div>
    <div class="col-3 mb-3">
        <a href="{{ \Illuminate\Support\Facades\URL::to('stores') }}" class="d-flex flex-column align-items-center text-decoration-none text-dark" aria-label="رستوران‌ها">
            <i class="fa fa-cutlery mb-1" style="font-size: 1.4rem;"></i>
            <span class="fw-semibold" style="font-size: 0.75rem;">رستوران‌ها</span>
        </a>
    </div>
    <div class="col-3 mb-3">
        <a href="{{ \Illuminate\Support\Facades\URL::to('friends') }}" class="d-flex flex-column align-items-center text-decoration-none text-dark" aria-label="همسفران">
            <i class="fa fa-users mb-1" style="font-size: 1.4rem;"></i>
            <span class="fw-semibold" style="font-size: 0.75rem;">همسفران</span>
        </a>
    </div>
</div>
    <hr>
    <section itemscope itemtype="https://schema.org/WebPage">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-6 col-md-8 col-sm-10">
                <form wire:submit.prevent="search" role="search" aria-label="جستجوی اقامتگاه">
                    <input wire:model.defer="searchText"
                        type="search"
                        placeholder="جستجوی کد یا عنوان اقامتگاه"
                        class="form-control form-control-sm"
                        style="padding: 1.15rem"
                        aria-label="عبارت جستجو">

                    <button type="submit" class="btn btn-primary"
                            style="position: absolute; top: 0; left: 16px; padding-left: 25px!important; padding-right: 25px!important;"
                            aria-label="انجام جستجو">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
        <br>
        <form  wire:submit="filter">
        <ul class="filter-items">
            <li>
                <select wire:model.live="p" class="form-control form-control-sm" aria-label="انتخاب استان">
                    <option value="0">استان (همه)</option>
                    @foreach($provinces as $province)
                        <option {{$p==$province->id?"selected":""}} value="{{$province->id}}">{{$province->name}}</option>
                    @endforeach
                </select>
            </li>
            <li>
                <select wire:model.live="c" {{$p==0?"disabled":""}} class="form-control form-control-sm" aria-label="انتخاب شهر">
                    <option value="0">شهر(همه)</option>
                    @foreach(\App\Models\City::where("is_use",true)->where("province_id",$p)->get() as $province)
                        <option {{$p==$province->id?"selected":""}} value="{{$province->id}}">{{$province->name}}</option>
                    @endforeach
                </select>
            </li>
            <li>
                <select  wire:model.live="n" class="form-control form-control-sm" aria-label="تعداد نفرات">
                    <option value="0">تعداد نفرات</option>
                    <option value="1">1 نفر</option>
                    <option value="2">2 نفر</option>
                    <option value="3">3 نفر</option>
                    <option value="4">4 نفر</option>
                    <option value="5">5 نفر</option>
                    <option value="6">6 نفر</option>
                    <option value="7">7 نفر</option>
                    <option value="8">8 نفر</option>
                    <option value="9">9 نفر</option>
                    <option value="10">10 نفر</option>
                    <option value="11">11 نفر</option>
                </select>
            </li>
            <li>
                <select  wire:model.live="r" class="form-control form-control-sm" aria-label="تعداد اتاق">
                    <option value="0">تعداد اتاق</option>
                    <option value="1">1 اتاق</option>
                    <option value="2">2 اتاق</option>
                    <option value="3">3 اتاق</option>
                    <option value="4">4 اتاق</option>
                    <option value="5">5 اتاق</option>
                </select>
            </li>
            <li>
                <select  wire:model.live="a"  class="form-control form-control-sm" aria-label="مرتب سازی قیمت">
                    <option value="0">مرتب سازی (قیمت)</option>
                    <option value="1">ارزان ترین</option>
                    <option value="2">گران ترین</option>
                </select>
            </li>
            <li>
                <select wire:model.live="area"  class="form-control form-control-sm" aria-label="نوع ملک">
                    <option value="0">نوع ملک</option>
                    @foreach(\App\Models\Residence::getAreaType() as $key=>$item)
                        <option value="{{$key}}">{{$item}}</option>
                    @endforeach
                </select>
            </li>
            <li style="margin-right: 16px">
                <button style="border: 1px solid #999" class="btn btn-sm btn-light" type="button"
                        data-toggle="collapse" data-target="#collapse-filter" aria-expanded="false"
                        aria-controls="collapse-filter" aria-label="جستجوی پیشرفته">
                    جستجوی پیشرفته
                    <i class="fa fa-search"></i>
                </button>
            </li>
        </ul>
        <ul wire:ignore class="collapse filter-items" id="collapse-filter">
            @foreach(\App\Models\Residence::getResidenceType() as $key=>$item)
            <li>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" wire:model.live="residenceType" value="{{$key}}" id="customSwitch-{{$key}}" aria-label="{{$item}}">
                    <label class="custom-control-label" for="customSwitch-{{$key}}">{{$item}}</label>
                </div>
            </li>
            @endforeach
            @foreach(\App\Models\Option::where("show_filter",true)->where("type","residence")->get() as $key=>$item)
            <li>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" wire:model.live="options" value="{{$item->id}}" id="option-{{$item->id}}" aria-label="{{$item->title}}">
                    <label class="custom-control-label" for="option-{{$item->id}}">{{$item->title}}</label>
                </div>
            </li>
            @endforeach
        </ul>
        </form>
    </section>
    <hr>
    <section>
        <h1 style="font-size: 30px;margin-top: 16px" itemprop="headline">اجاره ویلا، سوئیت، آپارتمان و کلبه در سراسر ایران</h1>
        <p class="text-muted" style="font-size: 16px; margin-bottom: 20px;" itemprop="description">
            رزرو آنلاین بهترین اقامتگاه‌های ایران با تضمین کیفیت و قیمت مناسب - بیش از {{ $residences->total() }} اقامتگاه در سراسر کشور
        </p>
        
        <div  style="text-align: center">
            <img wire:loading src="{{asset('storage/static/loading.gif')}}" style="margin: 40px auto;width: 200px;opacity: .5;" alt="در حال بارگذاری اقامتگاه‌ها...">
        </div>
        
<!-- در بخش لیست اقامتگاه‌ها -->
<ul wire:loading.remove id="residences" itemscope itemtype="https://schema.org/ItemList">
    @foreach($residences as $index => $residence)
        @php
            $provinceName = $provinces[$residence->province_id]->name ?? 'نامشخص';
            $cityName = $cities[$residence->city_id]->name ?? 'نامشخص';
            $residenceType = \App\Models\Residence::getResidenceType()[$residence->residence_type] ?? 'اقامتگاه';
            $areaType = \App\Models\Residence::getAreaType()[$residence->area_type] ?? '';
        @endphp
        
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <meta itemprop="position" content="{{ $index + 1 }}">
            <div itemprop="item" itemscope itemtype="https://schema.org/VacationRental">
                <meta itemprop="url" content="{{ url('detail/' . $residence->id) }}">
                <meta itemprop="address" content="{{ $cityName }}, {{ $provinceName }}">
                
                <h3 itemprop="name">{{ $residence->title }}</h3>
                <span class="line"></span>
                <div class="image-container">
                    <img src="{{ asset('storage/residences/' . $residence->image) }}"
                         alt="{{ $residence->title }} - {{ $residenceType }} {{ $areaType }} در {{ $cityName }}"
                         loading="lazy"
                         width="400"
                         height="300"
                         onerror="this.onerror=null; this.src='{{ asset('storage/static/onerror.jpg') }}'"
                         itemprop="image">
                </div>
                <span class="line"></span>
                <div class="d-flex flex-row justify-content-around p-2">
                    <span style="font-size: 12px">برای 1 شب</span>
                    <span style="font-size: 14px" itemprop="priceRange">
                        <span class="font-weight-bold" itemprop="price">
                            {{ convertEnglishToPersianNumbers(number_format($residence->amount)) }}
                        </span> تومان
                    </span>
                </div>
                <span class="line"></span>
                <div class="services d-flex flex-row justify-content-between align-items-center p-2">
                    <div class="d-flex align-items-center">
                        <span itemprop="occupancy" class="ml-3">
                            <i class="fa fa-users"></i>
                            {{ $residence->people_number }} نفر
                        </span>
                        <span itemprop="floorSize" class="ml-3">
                            <i class="fa fa-expand"></i>
                            {{ $residence->area }} متر
                        </span>
                        <span itemprop="numberOfRooms">
                            <i class="fa fa-bed"></i>
                            {{ $residence->room_number }} اتاق
                        </span>
                    </div>
                    
                    @if($residence->point > 0)
                    <div class="d-flex align-items-center"
                         itemprop="aggregateRating"
                         itemscope
                         itemtype="https://schema.org/AggregateRating">
                        <span class="badge d-flex align-items-center px-2 py-1"
                              style="background:#1fa463;color:#fff;font-size:12px;font-weight:600">
                            <i class="fa fa-star ml-1" style="color:#fff"></i>
                            <span itemprop="ratingValue" style="color:#fff">
                                {{ number_format($residence->point,1) }}
                            </span>
                        </span>
                        <small class="mr-2" style="color:#666">
                            {{ $residence->comments->count() }} نظر
                        </small>
                        <meta itemprop="ratingCount" content="{{ $residence->comments->count() }}">
                        <meta itemprop="bestRating" content="5">
                        <meta itemprop="worstRating" content="1">
                    </div>
                    @endif
                </div>
                
                <!-- بخش دکمه ثبت رزرو که برگردانده شد -->
                <span class="line"></span>
                <div class="d-flex flex-row justify-content-between p-2">
                    <a href="{{ url('detail/' . $residence->id) }}"
                       class="w-100 pt-1 pb-1 pl-4 pr-4 btn btn-success"
                       style="padding-left: 1.5rem !important; font-size: 14px"
                       aria-label="ثبت رزرو {{ $residence->title }}"
                       title="ثبت رزرو {{ $residence->title }}">
                        ثبت رزرو
                        <i class="fa fa-calendar-check-o mr-1"></i>
                    </a>
                </div>
            </div>
        </li>
    @endforeach
</ul>
        <br>
        <div class="row justify-content-center">
            {{ $residences->links('vendor.pagination.default') }}
        </div>
    </section>

<div class="container mt-4 mb-4" itemscope itemtype="https://schema.org/FAQPage">
    <h2 style="font-size: 18px; margin-bottom: 15px; color: #333; font-weight: bold;">
        رزرو آنلاین بهترین اقامتگاه های ایران
    </h2>
    
    <div itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
        <h3 itemprop="name" style="font-size: 16px; color: #444; margin-top: 20px;">چگونه در سایت اینجا اقامتگاه رزرو کنم؟</h3>
        <div itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
            <p itemprop="text" style="font-size: 14px; line-height: 1.7; text-align: justify; color: #555;">
                <strong>اینجا</strong> با پوشش کامل <strong>اقامتگاه های ویلا، سوئیت، آپارتمان و کلبه</strong> در مناطق گردشگری سراسر ایران، شما را بیش از هر سامانه رزرو آنلاین دیگری برای اجاره و رزرو اقامتگاه یاری می‌کند. برای رزرو اقامتگاه، مثلاً اجاره ویلا در شمال، کافی است پس از ورود به وب‌سایت اینجا، وارد صفحه جستجوی اقامتگاه شده و اطلاعات مورد نیاز مانند مقصد، تاریخ و تعداد نفرات را وارد کنید.
            </p>
        </div>
    </div>
    
    <div itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
        <h3 itemprop="name" style="font-size: 16px; color: #444; margin-top: 20px;">چه استان‌هایی تحت پوشش هستند؟</h3>
        <div itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
            <p itemprop="text" style="font-size: 14px; line-height: 1.7; text-align: justify; color: #555;">
                به‌این‌ترتیب، در کوتاه‌ترین زمان ممکن، با تنوع گسترده‌ای از اقامتگاه‌های <strong>استخردار، ساحلی، جنگلی و روستایی</strong> در استان‌های <strong>البرز، گلستان، گیلان، مازندران، هرمزگان، بوشهر و خوزستان</strong> مواجه می‌شوید.
            </p>
        </div>
    </div>
    
    <div itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
        <h3 itemprop="name" style="font-size: 16px; color: #444; margin-top: 20px;">چه مزایایی نسبت به سایر سایت‌ها دارید؟</h3>
        <div itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
            <p itemprop="text" style="font-size: 14px; line-height: 1.7; text-align: justify; color: #555;">
                تنها با چند کلیک ساده، رزرو آنلاین اقامتگاه به تمام مقاصد گردشگری ایران را از میان انواع ویلاهای لوکس، آپارتمان‌های مبله، سوئیت‌های اقتصادی و کلبه‌های بوم‌گردی در سایت اینجا انجام دهید. از اجاره ویلا در رامسر گرفته تا کلبه در انزلی، آپارتمان در اصفهان و سوئیت در کیش، کافی است به‌صورت اینترنتی اقامتگاه‌های موجود را در اینجا جستجو کنید. در این صورت می‌توانید به‌راحتی به این نکته پی ببرید که چه اقامتگاه‌هایی با چه امکاناتی، در چه موقعیت مکانی و با چه قیمتی می‌توانند میزبان سفر شما باشند. با توجه به تنوع گسترده اقامتگاه‌ها و همچنین درج کامل اطلاعات، تصاویر باکیفیت و نظرات مسافران قبلی، این امکان فراهم شده تا با مقایسه امکانات و قیمت‌ها، همواره مناسب‌ترین و باکیفیت‌ترین اقامتگاه را از اینجا رزرو کنید. رزرو مستقیم با مالکان معتبر، تضمین بهترین قیمت بدون کمیسیون اضافه، پشتیبانی ۲۴ ساعته و امکان بازدید قبل از پرداخت، از مزایای انحصاری رزرو از طریق اینجا می‌باشد.
            </p>
        </div>
    </div>
</div>

{{-- Additional Structured Data --}}
@push('scripts')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "SiteNavigationElement",
    "name": "منوی اصلی",
    "url": "{{ url('/') }}",
    "potentialAction": [
        {
            "@type": "Action",
            "name": "اقامتگاه‌ها",
            "url": "{{ url('/') }}"
        },
        {
            "@type": "Action",
            "name": "تورها",
            "url": "{{ url('tours') }}"
        },
        {
            "@type": "Action",
            "name": "رستوران‌ها",
            "url": "{{ url('stores') }}"
        },
        {
            "@type": "Action",
            "name": "همسفران",
            "url": "{{ url('friends') }}"
        }
    ]
}
</script>

<script>
// Dynamic Structured Data for current page
document.addEventListener('DOMContentLoaded', function() {
    const dynamicSchema = {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": document.title,
        "description": "{{ getConfigs('website-description') }}",
        "url": window.location.href,
        "datePublished": "{{ now()->toIso8601String() }}",
        "dateModified": "{{ now()->toIso8601String() }}",
        "publisher": {
            "@type": "Organization",
            "name": "{{ getConfigs('website-title') }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ url('storage/injaa_iconInput_1761765907.png') }}"
            }
        }
    };
    
    const script = document.createElement('script');
    script.type = 'application/ld+json';
    script.textContent = JSON.stringify(dynamicSchema);
    document.head.appendChild(script);
});
</script>
@endpush

</div>