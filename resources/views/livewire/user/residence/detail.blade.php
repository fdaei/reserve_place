<div>
    @php
        // ایجاد متادسکریپشن پویا بر اساس ویژگی‌های اقامتگاه
        $residenceType = \App\Models\Residence::getResidenceType()[$residence->residence_type] ?? 'اقامتگاه';
        $areaType = \App\Models\Residence::getAreaType()[$residence->area_type] ?? '';
        
        // جمع‌آوری ویژگی‌های مهم برای توضیحات
        $importantFeatures = [];
        $options = $residence->optionValues->keyBy("option_id");
        
        // ویژگی‌های مهم برای سئو
        $featureKeywords = ['استخر', 'جکوزی', 'سونا', 'پارکینگ', 'آشپزخانه', 'بالکن', 'تراس', 'حیاط', 'باربیکیو', 'شومینه'];
        
        foreach ($residence->optionValues as $optionValue) {
            $optionTitle = $optionValue->option->title ?? '';
            foreach ($featureKeywords as $keyword) {
                if (strpos($optionTitle, $keyword) !== false) {
                    $importantFeatures[] = $optionTitle;
                    break;
                }
            }
        }
        
        // محدود کردن تعداد ویژگی‌های مهم
        $importantFeatures = array_slice($importantFeatures, 0, 5);
        
        // نسخه meta description (حداکثر 160 کاراکتر برای گوگل)
        $dynamicDescriptionMeta = $residence->title . ' در ' . $residence->city->name . '، ' . $residence->province->name;
        $dynamicDescriptionMeta .= ' - ' . $residenceType . ' ' . $areaType;
        
        if ($residence->room_number > 0) {
            $dynamicDescriptionMeta .= ' با ' . $residence->room_number . ' اتاق';
        }
        
        // اضافه کردن 1-2 ویژگی برتر
        if (!empty($importantFeatures)) {
            $topFeatures = array_slice($importantFeatures, 0, 2);
            $dynamicDescriptionMeta .= ' دارای ' . implode('، ', $topFeatures);
        }
        
        $dynamicDescriptionMeta .= ' - رزرو آنلاین';
        $dynamicDescriptionMeta = Str::limit($dynamicDescriptionMeta, 160);
        
        // نسخه نمایشی در صفحه
        $dynamicDescriptionDisplay = $residence->title . ' در ' . $residence->city->name . '، ' . $residence->province->name;
        $dynamicDescriptionDisplay .= ' - ' . $residenceType . ' ' . $areaType;
        
        if ($residence->room_number > 0) {
            $dynamicDescriptionDisplay .= ' با ' . $residence->room_number . ' اتاق خواب';
        }
        
        if ($residence->people_number > 0) {
            $dynamicDescriptionDisplay .= ' و ظرفیت ' . $residence->people_number . ' نفر';
        }
        
        if (!empty($importantFeatures)) {
            $dynamicDescriptionDisplay .= ' ✅ دارای ' . implode('، ', $importantFeatures);
        }
        
        $dynamicDescriptionDisplay .= ' 🏡 رزرو آنلاین با تضمین بهترین قیمت';
        
        // Schema اصلی
        $schemaData = [
            "@context" => "https://schema.org",
            "@type" => "VacationRental",
            "@id" => url()->current(),
            "url" => url()->current(),
            "name" => $residence->title,
            "description" => $dynamicDescriptionMeta,
            "image" => array_merge(
                [asset('storage/residences/' . $residence->image)],
                $residence->images->filter(fn($img) => $img->url != $residence->image)
                                  ->map(fn($img) => asset('storage/residences/' . $img->url))
                                  ->values()
                                  ->toArray()
            ),
            "address" => [
                "@type" => "PostalAddress",
                "addressCountry" => "IR",
                "addressRegion" => $residence->province->name,
                "addressLocality" => $residence->city->name,
                "streetAddress" => $residence->address ?? '',
            ],
            "geo" => [
                "@type" => "GeoCoordinates",
                "latitude" => $residence->lat,
                "longitude" => $residence->lng,
            ],
            "priceRange" => number_format($residence->amount) . " تومان",
            "telephone" => \App\Models\User::find($residence->user_id)->phone,
            "contactPoint" => [
                "@type" => "ContactPoint",
                "telephone" => \App\Models\User::find($residence->user_id)->phone,
                "contactType" => "customer service",
            ],
            "aggregateRating" => $residence->comments->count() > 0 ? [
                "@type" => "AggregateRating",
                "ratingValue" => number_format($residence->point, 1),
                "reviewCount" => $residence->comments->count(),
                "bestRating" => "5",
                "worstRating" => "0"
            ] : null,
            "checkinTime" => "14:00",
            "checkoutTime" => "12:00",
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
            "amenityFeature" => $residence->optionValues->map(function($option) {
                return [
                    "@type" => "LocationFeatureSpecification",
                    "name" => $option->option->title,
                    "value" => true
                ];
            })->values(),
            "additionalProperty" => [
                [
                    "@type" => "PropertyValue",
                    "name" => "نوع اقامتگاه",
                    "value" => $residenceType
                ],
                [
                    "@type" => "PropertyValue",
                    "name" => "نوع منطقه",
                    "value" => $areaType
                ],
                [
                    "@type" => "PropertyValue",
                    "name" => "قیمت آخر هفته",
                    "value" => number_format($residence->last_week_amount) . " تومان"
                ]
            ]
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
                    "item" => url('/')
                ],
                [
                    "@type" => "ListItem",
                    "position" => 3,
                    "name" => $residence->title,
                    "item" => url()->current()
                ]
            ]
        ];

        // WebPage Schema
        $webPageSchema = [
            "@context" => "https://schema.org",
            "@type" => "WebPage",
            "name" => $residence->title . " | " . $residenceType . " در " . $residence->city->name . " | " . getConfigs("website-title"),
            "description" => $dynamicDescriptionMeta,
            "url" => url()->current(),
            "primaryImageOfPage" => asset('storage/residences/' . $residence->image),
            "datePublished" => $residence->created_at->toIso8601String(),
            "dateModified" => $residence->updated_at->toIso8601String(),
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

        // LocalBusiness Schema
        $localBusinessSchema = [
            "@context" => "https://schema.org",
            "@type" => ["TravelAgency", "LocalBusiness"],
            "name" => getConfigs("website-title"),
            "image" => url('storage/injaa_iconInput_1761765907.png'),
            "description" => getConfigs("website-description"),
            "address" => [
                "@type" => "PostalAddress",
                "streetAddress" => "تهران ژاندارمری خیابان ایثار نبش خیابان مالک",
                "addressLocality" => "تهران",
                "addressCountry" => "IR"
            ],
            "telephone" => "+98-4846",
            "openingHours" => "Mo-Su 00:00-23:59",
            "url" => url('/'),
            "priceRange" => "$$",
            "sameAs" => [
                "https://instagram.com/injaa_com",
                "https://t.me/injaa_com"
            ]
        ];

        // FAQ Schema با محتوای پویا
        $faqSchema = [
            "@context" => "https://schema.org",
            "@type" => "FAQPage",
            "mainEntity" => [
                [
                    "@type" => "Question",
                    "name" => "ساعت تحویل و تخلیه این " . $residenceType . " چه زمانی است؟",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "ساعت تحویل: 14:00 - ساعت تخلیه: 12:00. امکان تغییر ساعت با هماهنگی قبلی وجود دارد."
                    ]
                ],
                [
                    "@type" => "Question",
                    "name" => "آیا امکان لغو رزرو " . $residence->title . " وجود دارد؟",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "سیاست لغو رزرو با مالک اقامتگاه هماهنگ می‌شود. معمولاً در صورت اطلاع 48 ساعت قبل امکان لغو وجود دارد."
                    ]
                ],
                [
                    "@type" => "Question",
                    "name" => "این " . $residenceType . " چه امکاناتی دارد؟",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => $residenceType . " " . $areaType . " در " . $residence->city->name . " دارای " . 
                                 $residence->room_number . " اتاق خواب، ظرفیت " . $residence->people_number . " نفر" .
                                 (!empty($importantFeatures) ? ' و امکانات ' . implode('، ', array_slice($importantFeatures, 0, 3)) : '') . ' می‌باشد.'
                    ]
                ]
            ]
        ];

        $schemaData = array_filter($schemaData);
    @endphp

    @push('head')
        {{-- اضافه کردن Meta Description پویا --}}
        <meta name="description" content="{{ $dynamicDescriptionMeta }}">
        
        {{-- اضافه کردن Meta Keywords پویا --}}
        <meta name="keywords" content="{{ $residence->title }}, {{ $residenceType }}, {{ $areaType }}, {{ $residence->city->name }}, {{ $residence->province->name }}, اجاره ویلا, رزرو اقامتگاه{{ !empty($importantFeatures) ? ', ' . implode(', ', $importantFeatures) : '' }}">
        
        {{-- Title Tag بهینه --}}
        <title>{{ $residence->title }} | {{ $residenceType }} در {{ $residence->city->name }} | {{ getConfigs('website-title') }}</title>
        
        {{-- Open Graph Meta Tags --}}
        <meta property="og:title" content="{{ $residence->title }} | {{ $residenceType }} در {{ $residence->city->name }}">
        <meta property="og:description" content="{{ $dynamicDescriptionMeta }}">
        <meta property="og:image" content="{{ asset('storage/residences/' . $residence->image) }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:type" content="website">
        <meta property="og:locale" content="fa_IR">
        <meta property="og:site_name" content="{{ getConfigs('website-title') }}">
        
        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $residence->title }} | {{ $residenceType }} در {{ $residence->city->name }}">
        <meta name="twitter:description" content="{{ $dynamicDescriptionMeta }}">
        <meta name="twitter:image" content="{{ asset('storage/residences/' . $residence->image) }}">
        
        {{-- Canonical URL --}}
        <link rel="canonical" href="{{ url()->current() }}">
        
        {{-- Alternate Languages --}}
        <link rel="alternate" hreflang="fa-ir" href="{{ url()->current() }}">
        
        {{-- Structured Data --}}
        <script type="application/ld+json">
            {!! json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        
        <script type="application/ld+json">
            {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        
        <script type="application/ld+json">
            {!! json_encode($webPageSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        
        <script type="application/ld+json">
            {!! json_encode($organizationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        
        <script type="application/ld+json">
            {!! json_encode($localBusinessSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        
        <script type="application/ld+json">
            {!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        
        {{-- SiteLinks Search Box --}}
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "url": "{{ url('/') }}",
            "potentialAction": {
                "@type": "SearchAction",
                "target": "{{ url('/') }}?searchText={search_term_string}",
                "query-input": "required name=search_term_string"
            }
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
            <li class="breadcrumb-item">
                <a href="{{ url('/') }}" class="text-decoration-none text-primary">
                    <i class="fa fa-building"></i> اقامتگاه‌ها
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ Str::limit($residence->title, 30) }}
            </li>
        </ol>
    </nav>

    <link rel="stylesheet" href="{{asset("/plugin/swiper-slider/swiper-bundle.min.css")}}"/>
    @vite(['resources/css/user/detail.less'])
    
    <span id="ads-code">کد اقامتگاه <span>{{$residence->id}}</span></span>
    
    <h1 wire:ignore>{{$residence->title}}</h1>
    
    <div wire:ignore class="row">
        <div class="col-7">
            <address>
                <i class="fa fa-map-marker text-primary" style="font-size: 22px"></i>
                استان {{$residence->province->name}}، {{$residence->city->name}}
            </address>
        </div>
        <div class="col-5">
            <span style="float: left" class="btn btn-sm btn-light" id="shareBtn">
                <i class="fa fa-share-alt"></i> اشتراک گذاری
            </span>
        </div>
    </div>
    
    <div id="detail" class="row">
        <div wire:ignore class="col-xl-6">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="{{asset("storage/residences/".$residence->image)}}"
                             alt="{{ $residence->title }} - {{ $residenceType }} {{ $areaType }} در {{ $residence->city->name }}"
                             loading="lazy"
                             width="800"
                             height="600"
                             onerror="this.onerror=null; this.src='{{ asset('storage/static/onerror.jpg') }}'">
                    </div>
                    @foreach($residence->images as $image)
                        @if($residence->image!=$image->url)
                            <div class="swiper-slide">
                                <img src="{{asset("storage/residences/".$image->url)}}"
                                     alt="{{ $residence->title }} - تصویر {{ $loop->iteration }}"
                                     loading="lazy"
                                     width="800"
                                     height="600"
                                     onerror="this.onerror=null; this.src='{{ asset('storage/static/onerror.jpg') }}'">
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        
        <div wire:ignore class="col-xl-6">
            <div style="text-align: left;font-size: 20px" class="">
                <span class="font-weight-bold">
                    {{number_format($residence->amount)}} تومان
                </span>
                <span class="color-c3">
                    / هرشب
                </span>
            </div>
            
            {{-- اطلاعات نوع اقامتگاه --}}
            <div wire:ignore style="margin-top: 10px; background: #e9f7ef; padding: 8px; border-radius: 5px;">
                <i class="fa fa-info-circle text-success"></i>
                <strong>نوع اقامتگاه:</strong> {{ $residenceType }} {{ $areaType }}
            </div>
            
            <div wire:ignore style="margin-top: 10px">
                <i class="fa fa-user-circle-o"></i>
                @if($residence->admin->name!="")
                    {{$residence->admin->name}} {{$residence->admin->family}}
                @endif
            </div>
            
            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-phone"></i>
                @if(!auth()->check())
                    <span wire:click="login" class="bg-success"
                          style="cursor: pointer;padding: 0 6px;border-radius: 4px;color: white">
                        برای مشاهده شماره تماس <a style="text-decoration: underline">ثبت نام</a> کنید
                    </span>
                @else
                    <a wire:click="callToPhone" id="callLink" class="text-primary"
                       style="cursor: pointer; padding: 0 6px; border-radius: 4px; color: white">
                        تماس بگیرید
                        <i class="fa fa-spin fa-spinner" wire:loading wire:target="callToPhone"
                           style="margin-right: 5px;"></i>
                    </a>
                @endif
            </div>
            
            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-map-marker"></i>
                {{$residence->address}}
            </div>
            
            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-users"></i>
                ظرفیت تا {{$residence->people_number}} نفر
            </div>
            
            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-bed"></i>
                {{$residence->room_number}} اتاق خواب
            </div>
            
            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-expand"></i>
                {{$residence->area}} متر مربع
            </div>
            
            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-frown-o"></i>
                تومان در آخر هفته ها:
                {{number_format($residence->last_week_amount)}}
                تومان برای هرشب
            </div>
            
            <div style="margin-top: 5px">
                <i class="fa fa-star"></i>
                امتیاز کلی اقامتگاه {{number_format($residence->point,1)}}
                <span class="color-c3">(از {{$residence->comments->count()}} نظر)</span>
            </div>
            
            @if($residence->point>0)
                <div style="margin-top: 30px;text-align: center">
                    @php
                        $isEchoHalf=false;
                    @endphp
                    @for($i=0;$i<floor($residence->point);$i++)
                        <i class="fa fa-star star"></i>
                    @endfor
                    @if(is_float($residence->point))
                        @if(fmod($residence->point,1)!=(float)0)
                            @php
                                $isEchoHalf=true;
                            @endphp
                            <i class="fa fa-star-half-o star"></i>
                        @endif
                    @endif
                    @for($i=($isEchoHalf==true?4:5)-($residence->point);$i>0;$i--)
                        <i class="fa fa-star-o star"></i>
                    @endfor
                </div>
            @endif
            
            {{-- ویژگی‌های مهم --}}
            @if(!empty($importantFeatures))
                <div wire:ignore style="margin-top: 15px; background: #fff3cd; padding: 10px; border-radius: 5px; border-right: 3px solid #ffc107;">
                    <p style="margin: 0 0 8px 0; font-weight: bold;">
                        <i class="fa fa-check-circle text-warning"></i> ویژگی‌های برجسته:
                    </p>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @foreach($importantFeatures as $feature)
                            <span style="background: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                <i class="fa fa-check text-success" style="font-size: 10px;"></i> {{ $feature }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
            
            {{-- کد EXACTLY مثل قدیم --}}
            @script
            <script>
                // کد دقیقاً مثل نسخه قدیمی
                $(function () {
                    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
                    const callLink = document.getElementById("callLink");
                    const phone = "{{\App\Models\User::find($residence->user_id)->phone}}"
                    if (isMobile) {
                        callLink.textContent = " تماس بگیرید";
                    } else {
                        callLink.textContent = phone;
                    }
                    $("#callLink").click(function () {
                        window.location.href = 'tel:' + phone;
                    })
                });
            </script>
            @endscript
        </div>
    </div>
    
    
    {{-- توضیحات میزبان (بعد از عکس‌ها) --}}
    <div class="bg-c3" style="height: 3px; margin-top: 20px"></div>
    
    <div class="host-description-container" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 20px; border-radius: 8px; margin-top: 20px; margin-bottom: 20px; border-right: 4px solid #28a745; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3 style="color: #333; margin-bottom: 15px;">
            <i class="fa fa-info-circle text-primary"></i> توضیحات میزبان
        </h3>
        <p style="margin: 0; font-size: 16px; color: #555; line-height: 1.7; text-align: justify;">
            {{ $dynamicDescriptionDisplay }}
        </p>
        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
            <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1; min-width: 200px;">
                    <p style="margin: 5px 0; font-size: 14px;">
                        <i class="fa fa-map-marker text-primary"></i> 
                        <strong>موقعیت:</strong> {{ $residence->city->name }}، {{ $residence->province->name }}
                    </p>
                    <p style="margin: 5px 0; font-size: 14px;">
                        <i class="fa fa-home text-success"></i>
                        <strong>نوع:</strong> {{ $residenceType }} {{ $areaType }}
                    </p>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <p style="margin: 5px 0; font-size: 14px;">
                        <i class="fa fa-users text-info"></i>
                        <strong>ظرفیت:</strong> تا {{ $residence->people_number }} نفر
                    </p>
                    <p style="margin: 5px 0; font-size: 14px;">
                        <i class="fa fa-bed text-warning"></i>
                        <strong>اتاق:</strong> {{ $residence->room_number }} اتاق خواب
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{asset("/plugin/swiper-slider/swiper-bundle.min.js")}}"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            pagination: {
                el: ".swiper-pagination",
            },
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            loop: true,
        });
    </script>
    
    <div class="bg-c3" style="height: 3px; margin-top: 20px"></div>
    
    <div wire:ignore>
        @php
            $options=$residence->optionValues->keyBy("option_id");
        @endphp
        @foreach(\App\Models\OptionCategory::where("type","residence")->get() as $key=>$category)
            <p class="font-weight-bold">{{($key+1)."-".$category->title}}</p>
            <ul class="options">
                @foreach($category->options as $option)
                    <li style="opacity: {{!isset($options[$option->id])?"0.2":"1"}}">
                        <i class="ic"
                           style="background-image: url('{{asset("storage/options/".$option->icon)}}')"></i>
                        <p style="">
                            {{$option->title}}
                        </p>
                    </li>
                @endforeach
            </ul>
        @endforeach
    </div>

    <div class="stars-container">
        <div id="star-content">
            @php
                $hasComment=false;
                if (auth()->check()){
                    $comment=\App\Models\Comment::where("user_id",auth()->user()->id)->where("residence_id",$residence->id)->first();
                    if ($comment){
                        $hasComment=true;
                    }
                }
            @endphp
            <div class="star-rating">
                <input
                    {{$hasComment==true?"disabled":""}} {{!auth()->check()?"disabled":""}} wire:model.live="point"
                    type="radio" id="star5" name="rating" value="5">
                <label class="{{!$hasComment and auth()->check()?"active":""}}" for="star5"
                       onclick="showDesc('desc5')">&#9733;</label>

                <input
                    {{$hasComment==true?"disabled":""}}  {{!auth()->check()?"disabled":""}} wire:model.live="point"
                    type="radio" id="star4" name="rating" value="4">
                <label class="{{!$hasComment and auth()->check()?"active":""}}" for="star4"
                       onclick="showDesc('desc4')">&#9733;</label>

                <input
                    {{$hasComment==true?"disabled":""}}  {{!auth()->check()?"disabled":""}} wire:model.live="point"
                    type="radio" id="star3" name="rating" value="3">
                <label class="{{!$hasComment and auth()->check()?"active":""}}" for="star3"
                       onclick="showDesc('desc3')">&#9733;</label>

                <input
                    {{$hasComment==true?"disabled":""}}  {{!auth()->check()?"disabled":""}} wire:model.live="point"
                    type="radio" id="star2" name="rating" value="2">
                <label class="{{!$hasComment and auth()->check()?"active":""}}" for="star2"
                       onclick="showDesc('desc2')">&#9733;</label>

                <input
                    {{$hasComment==true?"disabled":""}}  {{!auth()->check()?"disabled":""}} wire:model.live="point"
                    type="radio" id="star1" name="rating" value="1">
                <label class="{{!$hasComment and auth()->check()?"active":""}}" for="star1"
                       onclick="showDesc('desc1')">&#9733;</label>
            </div>
            <div class="mt-3">
                <textarea
                    {{$hasComment==true?"disabled":""}}
                    {{!auth()->check()?"disabled":""}}
                    wire:model.defer="commentBody"
                    class="form-control"
                    rows="4"
                    placeholder="نظر شما درباره این اقامتگاه"></textarea>
                @error('commentBody')
                    <small class="text-danger d-block mt-2">{{ $message }}</small>
                @enderror
            </div>
            <div class="" style="padding-top: 17px">
                <button {{$hasComment==true?"disabled":""}}  wire:click="submitPoint"
                        {{!auth()->check()?"disabled":""}} class="btn btn-sm btn-success">ثبت امتیاز
                </button>
            </div>
        </div>
        <div class="star-description" id="starDesc"></div>
    </div>

    <script>
        function showDesc(id) {
            @if(!$hasComment and auth()->check())
            const descriptions = {
                'desc5': 'بی‌نقص و فوق‌العاده',
                'desc4': 'بسیار خوب',
                'desc3': 'معمولی',
                'desc2': 'نیاز به بهبود',
                'desc1': 'کاملاً ناامیدکننده'
            };
            document.getElementById('starDesc').style.display = 'block';
            document.getElementById('starDesc').innerHTML = descriptions[id];
            @endif
        }
        @if(!$hasComment and auth()->check())
        function hideDesc() {
            document.getElementById('starDesc').style.display = 'none';
        }

        document.body.addEventListener('click', function (event) {
            let star = $(event)
            console.log(star.attr("disabled"))

            const starDesc = document.getElementById('starDesc');
            if (!starDesc.contains(event.target) && !event.target.closest('.star-rating')) {
                hideDesc();
            }
        });
        @endif
    </script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"/>
    <div wire:ignore>
        <div id="map"></div>
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script>
            if (typeof L === 'undefined') {
                console.warn('Leaflet not loaded');
                document.getElementById('map').innerHTML =
                    '<div class="text-danger" style="padding:12px">نقشه لود نشد. اتصال اینترنت یا فایل‌های Leaflet را بررسی کنید.</div>';
            } else {
            var map = L.map('map').setView([{{$residence->lat}}, {{$residence->lng}}], 13);

            // markerهای مکان‌های ثابت
            var fixedLocations = [
                    @foreach(\App\Models\FoodStore::all() as $item)
                { lat: {{$item->lat}}, lng: {{$item->lng}}, name: "{{$item->title}}", url: "{{url('store/'.$item->id)}}" },
                @endforeach
            ];

            var pizzaIcon = L.icon({
                iconUrl: '{{ asset("storage/".getConfigs("markerMapFoodstoreIcon")) }}',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            fixedLocations.forEach(function(location) {
                L.marker([location.lat, location.lng], { icon: pizzaIcon })
                    .addTo(map)
                    .bindPopup(`<b>${location.name}</b><br><a href="${location.url}" target="_blank">مشاهده جزئیات</a>`);
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // نقطه اصلی
            var mainLat = {{$residence->lat}};
            var mainLng = {{$residence->lng}};

            var currentIcon = L.icon({
                iconUrl: '{{ asset("storage/".getConfigs("markerMapIcon")) }}',
                iconSize: [48, 48],
                iconAnchor: [24, 48],
                popupAnchor: [0, -40],
            });

            L.marker([mainLat, mainLng], { icon: currentIcon })
                .addTo(map)
                .bindPopup(`<b>{{$residence->title}}</b><br>{{$residence->province->name}}، {{$residence->city->name}}<br><small>{{$residenceType}} {{$areaType}}</small>`)
                .openPopup();

            // event کلیک روی کل نقشه
            map.on('click', function(e) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var userLat = position.coords.latitude;
                        var userLng = position.coords.longitude;
                        var gmapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${mainLat},${mainLng}`;
                        window.open(gmapsUrl, "_blank");
                    }, function(error) {
                        // اگر موقعیت کاربر پیدا نشد، فقط مقصد باز کن
                        var gmapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${mainLat},${mainLng}`;
                        window.open(gmapsUrl, "_blank");
                    });
                } else {
                    var gmapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${mainLat},${mainLng}`;
                    window.open(gmapsUrl, "_blank");
                }
            });
            }
        </script>
    </div>
    
    <div class="bg-c3" style="height: 3px; margin-top: 20px"></div>
    
    <p class="font-weight-bold">
        <i class="fa fa-file-text-o"></i>
        قوانین عمومی
    </p>
    <p style="padding-right: 12px">
        همراه داشتن کارت ملی الزامی است.
        <br>
        تغییر ساعت تحویل تنها با هماهنگی قبلی ممکن است.
        <br>
        حضور مهمانان اضافی ممنوع و مشمول جریمه خواهد بود.
        <br>
        در صورت آسیب به وسایل، هزینه تعمیر از کاربر دریافت می‌شود.
        <br>
        استفاده از استخر، سونا و باربیکیو فقط طبق دستورالعمل‌های مشخص شده مجاز است.
        <br>
        مسئولیت نظارت بر کودکان و افراد نیازمند مراقبت بر عهده همراهان است.
        <br>
    </p>
    
    <div class="bg-c3" style="height: 3px; margin-top: 20px"></div>
    
    <p class="font-weight-bold">
        اقامتگاه مشابه
    </p>
    
    <swiper-container wire:ignore id="residences" space-between="30" slides-per-view="auto" class="nav nav-tabs" id="nav-tab" role="tablist">
        @php
            $cities=\App\Models\City::where("is_use",true)->get()->keyBy("id");
            $provinces=\App\Models\Province::where("is_use",true)->get()->keyBy("id");
        @endphp
        
        @foreach(\App\Models\Residence::where("city_id",$residence->city_id)->where("status", true)->limit(5)->get() as $similarResidence)
            <swiper-slide>
                <div>
                    <h3>{{$similarResidence->title}}</h3>
                    <span class="line"></span>
                    <div class="image-container">
                        <img src="{{asset("storage/residences/".$similarResidence->image)}}"
                             alt="{{ $similarResidence->title }} - اقامتگاه در {{ $cities[$similarResidence->city_id]->name ?? '' }}"
                             loading="lazy"
                             width="300"
                             height="200"
                             onerror="this.onerror=null; this.src='{{ asset('storage/static/onerror.jpg') }}'">
                    </div>
                    <span class="line"></span>
                    <div class="d-flex flex-row justify-content-around p-2">
                        <span style="font-size: 12px">برای 1 شب</span>
                        <span style="font-size: 14px">
                            <span class="font-weight-bold">
                                {{convertEnglishToPersianNumbers(number_format($similarResidence->amount))}}
                            </span> تومان
                        </span>
                    </div>
                    <span class="line"></span>
                    <div class="services d-flex flex-row justify-content-right p-2">
                        <p>استان {{$provinces[$similarResidence->province_id]->name}} - {{$cities[$similarResidence->city_id]->name}}</p>
                    </div>
                    <span class="line"></span>
                    <div class="d-flex flex-row justify-content-between p-2">
                        <a style="padding-left: 1.5rem !important;font-size: 14px" class="w-100 pt-1 pb-1 pl-4 pr-4 btn btn-success" href="{{url("detail/".$similarResidence->id)}}">
                            ثبت رزرو
                        </a>
                    </div>
                    <span class="line"></span>
                    <div class="services d-flex flex-row justify-content-right p-2">
                        <span>
                            <i class="fa fa-users"></i>
                            {{$similarResidence->people_number}}
                            نفر
                        </span>
                        &nbsp; &nbsp;
                        <span>
                            <i class="fa fa-expand"></i>
                            {{$similarResidence->area}}
                            متر
                        </span>
                        &nbsp; &nbsp;
                        <span>
                            <i class="fa fa-bed"></i>
                            {{$similarResidence->room_number}}
                            اتاق
                        </span>
                    </div>
                </div>
            </swiper-slide>
        @endforeach
    </swiper-container>
    
    @php
        $foodstores=\App\Models\FoodStore::where("city_id",$residence->city_id)->where("status", 1)->limit(5)->get()
    @endphp
    
    @if(!$foodstores->isEmpty())
        <div class="bg-c3" style="height: 3px; margin-top: 20px"></div>
        <p class="font-weight-bold">
            مکان های نزدیک
        </p>
        
        <swiper-container wire:ignore id="residences" space-between="30" slides-per-view="auto" class="nav nav-tabs" id="nav-tab" role="tablist">
            @foreach($foodstores as $store)
                <swiper-slide>
                    <div>
                        <h3>{{$store->title}}</h3>
                        <span class="line"></span>
                        <div class="image-container">
                            <img src="{{asset("storage/food_store/".$store->image)}}"
                                 alt="{{ $store->title }} - رستوران در {{ $cities[$store->city_id]->name ?? '' }}"
                                 loading="lazy"
                                 width="300"
                                 height="200">
                        </div>
                        <span class="line"></span>
                        <div class="services d-flex flex-row justify-content-right p-2">
                            <p>استان {{$provinces[$store->province_id]->name}} - {{$cities[$store->city_id]->name}}</p>
                        </div>
                        <span class="line"></span>
                        <div class="d-flex flex-row justify-content-between p-2">
                            <a style="padding-left: 1.5rem !important;font-size: 14px" class="w-100 pt-1 pb-1 pl-4 pr-4 btn btn-success" href="{{url("store/".$store->id)}}">
                                مشاهده جزییات
                            </a>
                        </div>
                    </div>
                </swiper-slide>
            @endforeach
        </swiper-container>
    @endif
    
    <div wire:ignore>
        <style>
            swiper-container {
                width: 100%;
                height: 100%;
            }

            swiper-slide {
                text-align: center;
                font-size: 18px;
                margin-left: 0!important;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            swiper-slide img {
                display: block;
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            swiper-slide {
                width: auto;
            }

            swiper-slide:nth-child(2n) {
            }

            swiper-slide:nth-child(3n) {
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js" onerror="this.onerror=null;this.src='{{ asset('/plugin/swiper-slider/swiper-element-bundle.min.js') }}';"></script>
    </div>
    
    @if(session('comment'))
        @script
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: '{{session('comment')}}'
            });
        </script>
        @php
            session()->forget('comment');
        @endphp
        @endscript
    @endif
    
    <br>
    <br>
    <br>
</div>
