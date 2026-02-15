<div>
    @vite(['resources/css/user/index.less'])
    
    @php
        // Schema اصلی برای صفحه لیست رستوران‌ها
        $schemaData = [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "name" => "رستوران‌ها و کافه‌های ایران | رزرو میز رستوران",
            "description" => "رزرو آنلاین بهترین رستوران‌ها، کافه‌ها و مراکز غذایی در سراسر ایران - با منوی آنلاین و نظرات کاربران",
            "url" => url()->current(),
            "numberOfItems" => $list->count(),
            "itemListElement" => $list->map(function ($store, $index) use ($provinces, $cities) {
                return [
                    "@type" => "ListItem",
                    "position" => $index + 1,
                    "item" => [
                        "@type" => ["FoodEstablishment", "Restaurant"],
                        "name" => $store->title,
                        "description" => "رستوران/کافه در استان " . ($provinces[$store->province_id]->name ?? '') . "، شهر " . ($cities[$store->city_id]->name ?? ''),
                        "image" => url("storage/food_store/{$store->image}"),
                        "url" => url("store/{$store->id}"),
                        "address" => [
                            "@type" => "PostalAddress",
                            "addressLocality" => $cities[$store->city_id]->name ?? 'نامشخص',
                            "addressRegion" => $provinces[$store->province_id]->name ?? 'نامشخص',
                            "addressCountry" => "IR"
                        ],
                        "servesCuisine" => $store->cuisine_type ?? "ایرانی",
                        "priceRange" => "$$",
                        "aggregateRating" => $store->comments->count() > 0 ? [
                            "@type" => "AggregateRating",
                            "ratingValue" => number_format($store->point ?? 0, 1),
                            "ratingCount" => $store->comments->count(),
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
                    "name" => "رستوران‌ها",
                    "item" => url()->current()
                ]
            ]
        ];
        
        // WebPage Schema
        $webPageSchema = [
            "@context" => "https://schema.org",
            "@type" => "WebPage",
            "name" => "رستوران‌ها و کافه‌های ایران | رزرو میز رستوران | " . getConfigs("website-title"),
            "description" => "رزرو آنلاین بهترین رستوران‌ها، کافه‌ها و مراکز غذایی در سراسر ایران",
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
                        "name" => "رستوران‌ها",
                        "item" => url()->current()
                    ]
                ]
            ]
        ];
        
        // Organization Schema (همانند صفحه اقامتگاه‌ها)
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
                    "name" => "چگونه می‌توانم میز رستوران رزرو کنم؟",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "برای رزرو میز رستوران در سایت اینجا، ابتدا شهر و نوع غذای مورد نظر خود را انتخاب کنید. سپس از بین رستوران‌های موجود، مورد دلخواه خود را انتخاب و با چند کلیک رزرو را تکمیل نمایید."
                    ]
                ],
                [
                    "@type" => "Question",
                    "name" => "آیا امکان مشاهده منوی رستوران‌ها وجود دارد؟",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "بله، در صفحه هر رستوران می‌توانید منوی کامل، قیمت‌ها و تصاویر غذاها را مشاهده کنید."
                    ]
                ],
                [
                    "@type" => "Question",
                    "name" => "چه نوع رستوران‌هایی موجود است؟",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "انواع رستوران‌های ایرانی، فست‌فود، کافه‌رستوران، سفره‌خانه‌های سنتی، رستوران‌های بین‌المللی و کافه‌های دنج در سراسر ایران."
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
        
        {{-- LocalBusiness Schema --}}
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "LocalBusiness",
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
                <i class="fa fa-cutlery"></i> رستوران‌ها
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
                <form wire:submit.prevent="search" role="search" aria-label="جستجوی رستوران">
                    <input wire:model.live="searchText"
                           type="search"
                           placeholder="جستجوی نام رستوران یا کافه"
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
                    @foreach(\App\Models\City::where("is_use",true)->where("province_id",$p)->get() as $city)
                        <option {{$c==$city->id?"selected":""}} value="{{$city->id}}">{{$city->name}}</option>
                    @endforeach
                </select>
            </li>
            <li>
                <select  wire:model.live="a"  class="form-control form-control-sm" aria-label="مرتب سازی">
                    <option value="0">مرتب سازی</option>
                    <option value="1">جدیدترین</option>
                    <option value="2">قدیمی‌ترین</option>
                    <option value="3">پرطرفدارترین</option>
                </select>
            </li>
        </ul>
        </form>
    </section>
    <hr>
    <section>
        <h1 style="font-size: 30px;margin-top: 16px" itemprop="headline">رستوران‌ها و کافه‌های ایران</h1>
        <p class="text-muted" style="font-size: 16px; margin-bottom: 20px;" itemprop="description">
            رزرو آنلاین بهترین رستوران‌ها، کافه‌ها و مراکز غذایی در سراسر ایران - {{ $list->total() }} رستوران و کافه فعال
        </p>
        
        <div  style="text-align: center">
            <img wire:loading src="{{asset('storage/static/loading.gif')}}" style="margin: 40px auto;width: 200px;opacity: .5;" alt="در حال بارگذاری رستوران‌ها...">
        </div>
        
        <ul wire:loading.remove id="residences" itemscope itemtype="https://schema.org/ItemList">
            @foreach($list as $index => $item)
                @php
                    $provinceName = $provinces[$item->province_id]->name ?? 'نامشخص';
                    $cityName = $cities[$item->city_id]->name ?? 'نامشخص';
                @endphp
                
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <meta itemprop="position" content="{{ $index + 1 }}">
                    <div itemprop="item" itemscope itemtype="https://schema.org/FoodEstablishment">
                        <meta itemprop="url" content="{{ url('store/' . $item->id) }}">
                        <meta itemprop="address" content="{{ $cityName }}, {{ $provinceName }}">
                        
                        <h3 itemprop="name">{{ $item->title }}</h3>
                        <span class="line"></span>
                        <div class="image-container">
                            <img src="{{ asset('storage/food_store/' . $item->image) }}"
                                 alt="{{ $item->title }} - رستوران/کافه در {{ $cityName }}"
                                 loading="lazy"
                                 width="400"
                                 height="300"
                                 onerror="this.onerror=null; this.src='{{ asset('storage/static/onerror.jpg') }}'"
                                 itemprop="image">
                        </div>
                        <span class="line"></span>
                        <div class="services d-flex flex-row justify-content-right p-2" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                            <p itemprop="addressLocality">استان {{ $provinceName }} - {{ $cityName }}</p>
                            <meta itemprop="addressRegion" content="{{ $provinceName }}">
                            <meta itemprop="addressCountry" content="IR">
                        </div>
                        
                        @if($item->cuisine_type ?? false)
                        <div class="services d-flex flex-row justify-content-right p-2">
                            <p itemprop="servesCuisine">
                                <i class="fa fa-utensils"></i>
                                {{ $item->cuisine_type }}
                            </p>
                        </div>
                        @endif
                        
                        <span class="line"></span>
                        <div class="d-flex flex-row justify-content-between p-2">
                            <a style="padding-left: 1.5rem !important;font-size: 14px" 
                               class="w-100 pt-1 pb-1 pl-4 pr-4 btn btn-success" 
                               href="{{ \Illuminate\Support\Facades\URL::to('store/' . $item->id) }}"
                               aria-label="{{ $item->title }}">
                                مشاهده منو و رزرو
                            </a>
                        </div>
                        
                        {{-- Rating --}}
                        @if(($item->point ?? 0) > 0)
                        <div class="text-center mt-2" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                            <meta itemprop="ratingValue" content="{{ $item->point ?? 0 }}">
                            <meta itemprop="ratingCount" content="{{ $item->comments->count() ?? 0 }}">
                            <small class="text-warning" style="color:#ffc107">
                                <i class="fa fa-star"></i>
                                امتیاز: {{ number_format($item->point ?? 0, 1) }} 
                                @if($item->comments->count() > 0)
                                ({{ $item->comments->count() }} نظر)
                                @endif
                            </small>
                        </div>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
        <br>
        <div class="row justify-content-center">
            {{ $list->links('vendor.pagination.default') }}
        </div>
    </section>

<div class="container mt-4 mb-4" itemscope itemtype="https://schema.org/FAQPage">
    <h2 style="font-size: 18px; margin-bottom: 15px; color: #333; font-weight: bold;">
        رزرو آنلاین بهترین رستوران‌ها و کافه‌های ایران
    </h2>
    
    <div itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
        <h3 itemprop="name" style="font-size: 16px; color: #444; margin-top: 20px;">چگونه در سایت اینجا میز رستوران رزرو کنم؟</h3>
        <div itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
            <p itemprop="text" style="font-size: 14px; line-height: 1.7; text-align: justify; color: #555;">
                <strong>اینجا</strong> با ارائه کامل‌ترین لیست <strong>رستوران‌ها، کافه‌ها و مراکز غذایی</strong> در سراسر ایران، شما را بیش از هر پلتفرم رزرو آنلاین دیگری برای پیدا کردن و رزرو میز رستوران یاری می‌کند. برای پیدا کردن رستوران، مثلاً رستوران در تهران، رستوران در اصفهان یا کافه در شیراز، کافی است پس از ورود به وب‌سایت اینجا، وارد صفحه جستجوی رستوران شده و اطلاعات مورد نیاز مانند شهر، نوع غذا و محدوده قیمت را وارد کنید.
            </p>
        </div>
    </div>
    
    <div itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
        <h3 itemprop="name" style="font-size: 16px; color: #444; margin-top: 20px;">چه استان‌هایی تحت پوشش هستند؟</h3>
        <div itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
            <p itemprop="text" style="font-size: 14px; line-height: 1.7; text-align: justify; color: #555;">
                به‌این‌ترتیب، در کوتاه‌ترین زمان ممکن، با تنوع گسترده‌ای از <strong>رستوران‌های ایرانی، فست‌فود، کافه‌رستوران، سفره‌خانه‌های سنتی</strong> و <strong>رستوران‌های بین‌المللی</strong> در تمام شهرهای ایران از جمله تهران، اصفهان، شیراز، تبریز، مشهد و ... مواجه می‌شوید.
            </p>
        </div>
    </div>
    
    <div itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
        <h3 itemprop="name" style="font-size: 16px; color: #444; margin-top: 20px;">چه مزایایی نسبت به سایر سایت‌ها دارید؟</h3>
        <div itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
            <p itemprop="text" style="font-size: 14px; line-height: 1.7; text-align: justify; color: #555;">
                رزرو مستقیم با رستوران‌های معتبر، مشاهده منو و قیمت‌ها به‌روز، امکان پرداخت آنلاین، تخفیف‌های ویژه، نظرات کاربران واقعی و امکان نقد و بررسی، از مزایای انحصاری استفاده از اینجا می‌باشد.
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
        "description": "رزرو آنلاین بهترین رستوران‌ها، کافه‌ها و مراکز غذایی در سراسر ایران",
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
