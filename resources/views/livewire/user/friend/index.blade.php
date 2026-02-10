<div>
    @vite(['resources/css/user/index.less'])
    
    @php
        // Schema اصلی برای صفحه لیست همسفران
        $schemaData = [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "name" => "همسفران و همراهان سفر | پیدا کردن همسفر مطمئن",
            "description" => "پیدا کردن همسفر مطمئن برای سفرهای داخلی و خارجی - جامعه مسافران و همسفران ایران",
            "url" => url()->current(),
            "numberOfItems" => $list->count(),
            "itemListElement" => $list->map(function ($friend, $index) use ($countries, $provinces) {
                return [
                    "@type" => "ListItem",
                    "position" => $index + 1,
                    "item" => [
                        "@type" => "Person",
                        "name" => $friend->title,
                        "description" => "همسفر برای سفر به کشور " . ($countries[$friend->country_id]->name ?? '') . "، استان " . ($provinces[$friend->province_id]->name ?? ''),
                        "image" => url("storage/friends/{$friend->image}"),
                        "url" => url("friend/{$friend->id}"),
                        "nationality" => [
                            "@type" => "Country",
                            "name" => "ایران"
                        ],
                        "knowsAbout" => [
                            "مسافرت",
                            "گردشگری",
                            "همسفری"
                        ]
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
                    "name" => "همسفران",
                    "item" => url()->current()
                ]
            ]
        ];
        
        // WebPage Schema
        $webPageSchema = [
            "@context" => "https://schema.org",
            "@type" => "WebPage",
            "name" => "همسفران و همراهان سفر | پیدا کردن همسفر مطمئن | " . getConfigs("website-title"),
            "description" => "پیدا کردن همسفر مطمئن برای سفرهای داخلی و خارجی - جامعه مسافران و همسفران ایران",
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
                        "name" => "همسفران",
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
                    "name" => "چگونه می‌توانم همسفر پیدا کنم؟",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "برای پیدا کردن همسفر در سایت اینجا، ابتدا مقصد سفر و تاریخ مورد نظر خود را انتخاب کنید. سپس از بین همسفران موجود، شخص مورد نظر خود را انتخاب و با او ارتباط برقرار نمایید."
                    ]
                ],
                [
                    "@type" => "Question",
                    "name" => "آیا امنیت ارتباط با همسفران تضمین شده است؟",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "بله، تمام همسفران از طریق سیستم احراز هویت تأیید می‌شوند و سوابق سفرهای قبلی آن‌ها قابل مشاهده است."
                    ]
                ],
                [
                    "@type" => "Question",
                    "name" => "برای چه مقاصدی می‌توان همسفر پیدا کرد؟",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "برای تمام مقاصد داخلی ایران و همچنین سفرهای خارجی به کشورهای مختلف می‌توانید همسفر مناسب پیدا کنید."
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
                <i class="fa fa-users"></i> همسفران
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
        <br>
        <form wire:submit="filter">
            <ul class="filter-items">
                <li>
                    <select wire:model.live="c" class="form-control form-control-sm" aria-label="انتخاب کشور">
                        <option value="0">کشور (همه)</option>
                        @foreach($countries as $country)
                            <option {{$c==$country->id?"selected":""}} value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                </li>
                <li>
                    <select wire:model.live="p" {{$c==0?"disabled":""}} class="form-control form-control-sm" aria-label="انتخاب استان">
                        <option value="0">استان (همه)</option>
                        @foreach(\App\Models\Province::where("is_use",true)->where("country_id",$c)->get() as $province)
                            <option {{$p==$province->id?"selected":""}} value="{{$province->id}}">{{$province->name}}</option>
                        @endforeach
                    </select>
                </li>
            </ul>
        </form>
    </section>
    <hr>
    <section>
        <h1 style="font-size: 30px;margin-top: 16px" itemprop="headline">همسفران و همراهان سفر</h1>
        <p class="text-muted" style="font-size: 16px; margin-bottom: 20px;" itemprop="description">
            پیدا کردن همسفر مطمئن برای سفرهای داخلی و خارجی - {{ $list->total() }} همسفر فعال
        </p>
        
        <div style="text-align: center">
            <img wire:loading src="{{asset('storage/static/loading.gif')}}" style="margin: 40px auto;width: 200px;opacity: .5;" alt="در حال بارگذاری همسفران...">
        </div>
        
        <ul wire:loading.remove id="residences" itemscope itemtype="https://schema.org/ItemList">
            @foreach($list as $index => $item)
                @php
                    $countryName = $countries[$item->country_id]->name ?? 'نامشخص';
                    $provinceName = $provinces[$item->province_id]->name ?? 'نامشخص';
                @endphp
                
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <meta itemprop="position" content="{{ $index + 1 }}">
                    <div itemprop="item" itemscope itemtype="https://schema.org/Person">
                        <meta itemprop="url" content="{{ url('friend/' . $item->id) }}">
                        <meta itemprop="nationality" content="ایران">
                        
                        <h3 itemprop="name">{{ $item->title }}</h3>
                        <span class="line"></span>
                        <div class="image-container">
                            <img src="{{ asset('storage/friends/' . $item->image) }}"
                                 alt="{{ $item->title }} - همسفر برای سفر به {{ $provinceName }}"
                                 loading="lazy"
                                 width="400"
                                 height="300"
                                 onerror="this.onerror=null; this.src='{{ asset('storage/static/onerror.jpg') }}'"
                                 itemprop="image">
                        </div>
                        <span class="line"></span>
                        <div class="services d-flex flex-row justify-content-right p-2">
                            <div itemprop="homeLocation" itemscope itemtype="https://schema.org/Place">
                                <meta itemprop="name" content="{{ $provinceName }}">
                                <p>کشور {{ $countryName }} - {{ $provinceName }}</p>
                            </div>
                        </div>
                        
                        @if($item->travel_type ?? false)
                        <div class="services d-flex flex-row justify-content-right p-2">
                            <p itemprop="knowsAbout">
                                <i class="fa fa-suitcase"></i>
                                نوع سفر: {{ $item->travel_type }}
                            </p>
                        </div>
                        @endif
                        
                        @if($item->travel_date ?? false)
                        <div class="services d-flex flex-row justify-content-right p-2">
                            <p>
                                <i class="fa fa-calendar"></i>
                                تاریخ سفر: {{ $item->travel_date }}
                            </p>
                        </div>
                        @endif
                        
                        <span class="line"></span>
                        <div class="d-flex flex-row justify-content-between p-2">
                            <a style="padding-left: 1.5rem !important;font-size: 14px" 
                               class="w-100 pt-1 pb-1 pl-4 pr-4 btn btn-success" 
                               href="{{ \Illuminate\Support\Facades\URL::to('friend/' . $item->id) }}"
                               aria-label="مشاهده پروفایل {{ $item->title }}">
                                مشاهده پروفایل
                            </a>
                        </div>
                        
                        @if(($item->travel_count ?? 0) > 0)
                        <div class="text-center mt-2">
                            <small class="text-info">
                                <i class="fa fa-plane"></i>
                                {{ $item->travel_count }} سفر قبلی
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
        پیدا کردن همسفر مطمئن برای سفرهای شما
    </h2>
    
    <div itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
        <h3 itemprop="name" style="font-size: 16px; color: #444; margin-top: 20px;">چگونه در سایت اینجا همسفر پیدا کنم؟</h3>
        <div itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
            <p itemprop="text" style="font-size: 14px; line-height: 1.7; text-align: justify; color: #555;">
                <strong>اینجا</strong> با ایجاد بزرگترین جامعه <strong>همسفران و مسافران</strong> در ایران، شما را بیش از هر شبکه اجتماعی و پلتفرم دیگری برای پیدا کردن همراه مطمئن در سفر یاری می‌کند. برای پیدا کردن همسفر، مثلاً برای سفر به مشهد، سفر به کیش یا سفرهای خارجی، کافی است پس از ورود به وب‌سایت اینجا، وارد صفحه همسفران شده و اطلاعات مورد نیاز مانند مقصد سفر، تاریخ و جنسیت همسفر را وارد کنید.
            </p>
        </div>
    </div>
    
    <div itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
        <h3 itemprop="name" style="font-size: 16px; color: #444; margin-top: 20px;">آیا امنیت ارتباط با همسفران تضمین شده است؟</h3>
        <div itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
            <p itemprop="text" style="font-size: 14px; line-height: 1.7; text-align: justify; color: #555;">
                به‌این‌ترتیب، در کوتاه‌ترین زمان ممکن، با تنوع گسترده‌ای از <strong>همسفران مرد و زن</strong> در رده‌های سنی مختلف و با سوابق سفر متنوع مواجه می‌شوید. سیستم اعتبارسنجی کامل، احراز هویت دقیق، نظرات سفرهای قبلی و امکان گفت‌وگوی مستقیم، امنیت ارتباط را تضمین می‌کند.
            </p>
        </div>
    </div>
    
    <div itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
        <h3 itemprop="name" style="font-size: 16px; color: #444; margin-top: 20px;">چه مزایایی نسبت به سایر سایت‌ها دارید؟</h3>
        <div itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
            <p itemprop="text" style="font-size: 14px; line-height: 1.7; text-align: justify; color: #555;">
                ارتباط مستقیم با مسافران تأییدشده، مشاهده سوابق سفرهای قبلی، امکان چت و گفت‌وگوی قبل از سفر، سیستم امتیازدهی و نظردهی و امنیت کامل اطلاعات، از مزایای انحصاری استفاده از اینجا برای پیدا کردن همسفر می‌باشد.
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
        "description": "پیدا کردن همسفر مطمئن برای سفرهای داخلی و خارجی - جامعه مسافران و همسفران ایران",
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