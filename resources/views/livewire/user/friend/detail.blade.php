<div>
    @php
        $schemaData = [
            "@context" => "https://schema.org",
            "@type" => "Trip",
            "name" => $model->title,
            "description" => strip_tags(Str::limit($model->description, 160)),
            "image" => array_merge(
                [asset('storage/friends/' . $model->image)],
                $model->images->filter(fn($img) => $img->url != $model->image)
                              ->map(fn($img) => asset('storage/friends/' . $img->url))
                              ->values()
                              ->toArray()
            ),
            "startDate" => $model->start_date,
            "endDate" => date('Y-m-d', strtotime($model->start_date. ' + '.$model->travel_duration.' days')),
            "itinerary" => [
                "@type" => "Place",
                "name" => $model->province->name,
                "address" => [
                    "@type" => "PostalAddress",
                    "addressRegion" => $model->country->name,
                    "addressLocality" => $model->province->name,
                ],
            ],
            "offers" => [
                "@type" => "Offer",
                "price" => $model->amount ?? 0,
                "priceCurrency" => "IRR",
            ],
            "traveler" => [
                "@type" => "Person",
                "name" => $model->admin->name.' '.$model->admin->family,
                "telephone" => \App\Models\User::find($model->user_id)->phone,
                "gender" => \App\Models\Friend::getGrnders($model->my_gender),
                "age" => $model->my_age
            ],
            "additionalProperty" => [
                [
                    "@type" => "PropertyValue",
                    "name" => "نوع سفر",
                    "value" => \App\Models\Friend::getTravelType($model->travel_type)
                ],
                [
                    "@type" => "PropertyValue",
                    "name" => "سبک سفر",
                ],
                [
                    "@type" => "PropertyValue",
                    "name" => "نوع پیمایش مسیر",
                    "value" => \App\Models\Friend::getMachineType($model->machine_type)
                ],
                [
                    "@type" => "PropertyValue",
                    "name" => "وضعیت همسفر",
                    "value" => $model->friend_gender != 4 ? \App\Models\Friend::getGrnders($model->friend_gender) : "فرقی ندارد"
                ]
            ]
        ];
        
        // اضافه کردن Breadcrumb Schema
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
                    "item" => url('friends')
                ],
                [
                    "@type" => "ListItem",
                    "position" => 3,
                    "name" => $model->title,
                    "item" => url()->current()
                ]
            ]
        ];
        
        // اضافه کردن WebPage Schema
        $webPageSchema = [
            "@context" => "https://schema.org",
            "@type" => "WebPage",
            "name" => $model->title . " | همسفر برای سفر | " . getConfigs("website-title"),
            "description" => strip_tags(Str::limit($model->description, 160)),
            "url" => url()->current()
        ];
    @endphp

    @push('head')
        <script type="application/ld+json">
            {!! json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
        
        {{-- اضافه کردن Breadcrumb Schema --}}
        <script type="application/ld+json">
            {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        
        {{-- اضافه کردن WebPage Schema --}}
        <script type="application/ld+json">
            {!! json_encode($webPageSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        
        {{-- اضافه کردن Organization Schema --}}
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "{{ getConfigs('website-title') }}",
            "url": "{{ url('/') }}",
            "logo": "{{ url('storage/injaa_iconInput_1761765907.png') }}",
            "sameAs": [
                "https://instagram.com/injaa_com",
                "https://t.me/injaa_com"
            ]
        }
        </script>
    @endpush

    {{-- اضافه کردن Breadcrumb Navigation در بالای صفحه --}}
    <nav aria-label="breadcrumb" style="padding: 10px 0; background-color: #f8f9fa; border-radius: 5px; margin-bottom: 20px;">
        <ol class="breadcrumb mb-0" style="background: transparent; padding: 0 15px;">
            <li class="breadcrumb-item">
                <a href="{{ url('/') }}" class="text-decoration-none text-primary">
                    <i class="fa fa-home"></i> صفحه اصلی
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ url('friends') }}" class="text-decoration-none text-primary">
                    <i class="fa fa-users"></i> همسفران
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ Str::limit($model->title, 30) }}
            </li>
        </ol>
    </nav>

    <link rel="stylesheet" href="{{asset("/plugin/swiper-slider/swiper-bundle.min.css")}}"/>
    @vite(['resources/css/user/detail.less'])
    
    <span id="ads-code">کد همسفر <span>{{$model->id}}</span></span>
    <h1 wire:ignore>{{$model->title}}</h1>
    
    <div wire:ignore class="row">
        <div class="col-7">
            <address>
                <i class="fa fa-map-marker " style="font-size: 22px"></i>
                کشور {{$model->country->name}}، {{$model->province->name}}
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
                        <img src="{{asset("storage/friends/".$model->image)}}" 
                             alt="{{ $model->title }} - همسفر سفر"
                             loading="lazy">
                    </div>
                    @foreach($model->images as $image)
                        @if($model->image!=$image->url)
                            <div class="swiper-slide">
                                <img src="{{asset("storage/friends/".$image->url)}}" 
                                     alt="{{ $model->title }} - تصویر {{ $loop->iteration }}"
                                     loading="lazy">
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        
        <div wire:ignore class="col-xl-6">
            <div wire:ignore style="margin-top: 10px">
                <i class="fa fa-user-circle-o"></i>
                @if($model->admin->name!="")
                    {{$model->admin->name}} {{$model->admin->family}}
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
                <i class="fa fa-venus-mars"></i>
                وضعیت من:
                {{\App\Models\Friend::getGrnders($model->my_gender)}}
                ،
                {{$model->my_age}}
                ساله
            </div>
            
            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-venus-mars"></i>
                وضعیت همسفر:
                @if($model->friend_gender!=4)
                    {{\App\Models\Friend::getGrnders($model->friend_gender)}}
                @else
                    فرقی ندارد
                @endif
            </div>
            
            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-bus"></i>
                نوع پیمایش مسیر:
                {{\App\Models\Friend::getMachineType($model->machine_type)}}
            </div>
            
            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-diamond"></i>
                سبک سفر:
                @switch($model->travel_version)
                    @case(1)اقتصادی@break
                    @case(2)معمولی@break
                    @case(3)لاکچری@break
                @endswitch
            </div>
            
            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-briefcase"></i>
                نوع سفر:
                {{\App\Models\Friend::getTravelType($model->travel_type)}}
            </div>
            
            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-calendar"></i>
                تاریخ شروع سفر:
                {{jalaliDate("Y/m/d",strtotime($model->start_date))}}
            </div>
            
            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-plane"></i>
                مدت سفر:
                {{$model->travel_duration}}
                روزه
            </div>
        </div>
    </div>
    
    <script src="{{asset("/plugin/swiper-slider/swiper-bundle.min.js")}}"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            pagination: {
                el: ".swiper-pagination",
            },
        });
    </script>
    
    <div class="bg-c3" style="height: 3px;margin-top: 20px"></div>
    
    <div wire:ignore>
        @php
            $options=$model->optionValues->keyBy("option_id");
        @endphp
        @foreach(\App\Models\OptionCategory::where("type","friend")->get() as $key=>$category)
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
                    $comment=\App\Models\Comment::where("user_id",auth()->user()->id)->where("store_id",$model->id)->first();
                    if ($comment){
                        $hasComment=true;
                    }
                }
            @endphp
        </div>
    </div>

    <div class="bg-c3" style="height: 3px;margin-top: 20px"></div>
    
    <p class="font-weight-bold">
        <i class="fa fa-file-text-o"></i>
        قوانین عمومی
    </p>
    
    <p style="padding-right: 12px">
        ارائه مدارک شناسایی معتبر و تأیید هویت الزامی است.
        <br>
        احترام متقابل و رعایت شئونات اخلاقی در تمام مراحل الزامی است.
        <br>
        مسئولیت شخصی هر فرد در قبال اموال خود می‌باشد.
        <br>
        هرگونه قرارداد مالی خارج از پلتفرم، خارج از مسئولیت اینجا می‌باشد.
        <br>
        در صورت بروز مشکل، پلتفرم به عنوان میانجی عمل خواهد کرد.
        <br>
        ارائه اطلاعات نادرست منجر به حذف حساب کاربری می‌شود.
        <br>
    </p>
    
    <div class="bg-c3" style="height: 3px;margin-top: 20px"></div>
    
    <p class="font-weight-bold">
        اقامتگاه های نزدیک
    </p>
    
    <swiper-container wire:ignore id="residences" space-between="30" slides-per-view="auto" class="nav nav-tabs" id="nav-tab" role="tablist">
        @php
            $cities=\App\Models\City::where("is_use",true)->get()->keyBy("id");
            $provinces=\App\Models\Province::where("is_use",true)->get()->keyBy("id");
        @endphp
        
        @foreach(\App\Models\Residence::where("province_id",$model->province_id)->limit(5)->get() as $residence)
            <swiper-slide>
                <div>
                    <h3>{{$residence->title}}</h3>
                    <span class="line"></span>
                    <div class="image-container">
                        <img src="{{asset("storage/residences/".$residence->image)}}" 
                             alt="{{ $residence->title }} - اقامتگاه در {{ $cities[$residence->city_id]->name ?? '' }}"
                             loading="lazy">
                    </div>
                    <span class="line"></span>
                    <div class="d-flex flex-row justify-content-around p-2">
                        <span style="font-size: 12px">برای 1 شب</span>
                        <span style="font-size: 14px">
                            <span class="font-weight-bold">
                                {{convertEnglishToPersianNumbers(number_format($residence->amount))}}
                            </span> تومان
                        </span>
                    </div>
                    <span class="line"></span>
                    <div class="services d-flex flex-row justify-content-right p-2">
                        <p>استان {{$provinces[$residence->province_id]->name}} - {{$cities[$residence->city_id]->name}}</p>
                    </div>
                    <span class="line"></span>
                    <div class="d-flex flex-row justify-content-between p-2">
                        <a style="padding-left: 1.5rem !important;font-size: 14px" class="w-100 pt-1 pb-1 pl-4 pr-4 btn btn-success" href="{{url("detail/".$residence->id)}}">
                            ثبت رزرو
                        </a>
                    </div>
                    <span class="line"></span>
                    <div class="services d-flex flex-row justify-content-right p-2">
                        <span>
                            <i class="fa fa-users"></i>
                            {{$residence->people_number}}
                            نفر
                        </span>
                        &nbsp; &nbsp;
                        <span>
                            <i class="fa fa-expand"></i>
                            {{$residence->area}}
                            متر
                        </span>
                        &nbsp; &nbsp;
                        <span>
                            <i class="fa fa-bed"></i>
                            {{$residence->room_number}}
                            اتاق
                        </span>
                    </div>
                </div>
            </swiper-slide>
        @endforeach
    </swiper-container>
    
    @php
        $foodstores=\App\Models\FoodStore::where("province_id",$model->province_id)->limit(5)->get()
    @endphp
    
    @if(!$foodstores->isEmpty())
        <div class="bg-c3" style="height: 3px;margin-top: 20px"></div>
        <p class="font-weight-bold">
            مکان های نزدیک
        </p>
        
        <swiper-container wire:ignore id="residences" space-between="30" slides-per-view="auto" class="nav nav-tabs" id="nav-tab" role="tablist">
            @foreach($foodstores as $residence)
                <swiper-slide>
                    <div>
                        <h3>{{$residence->title}}</h3>
                        <span class="line"></span>
                        <div class="image-container">
                            <img src="{{asset("storage/food_store/".$residence->image)}}" 
                                 alt="{{ $residence->title }} - رستوران در {{ $cities[$residence->city_id]->name ?? '' }}"
                                 loading="lazy">
                        </div>
                        <span class="line"></span>
                        <div class="services d-flex flex-row justify-content-right p-2">
                            <p>استان {{$provinces[$residence->province_id]->name}} - {{$cities[$residence->city_id]->name}}</p>
                        </div>
                        <span class="line"></span>
                        <div class="d-flex flex-row justify-content-between p-2">
                            <a style="padding-left: 1.5rem !important;font-size: 14px" class="w-100 pt-1 pb-1 pl-4 pr-4 btn btn-success" href="{{url("store/".$residence->id)}}">
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
    @endif
    
    <br>
    <br>
    <br>
</div>
