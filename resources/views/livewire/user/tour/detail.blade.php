<div>
    @php
        $schemaData = [
            "@context" => "https://schema.org",
            "@type" => "TouristTrip",
            "name" => $model->title,
            "description" => strip_tags($model->description),
            "image" => array_merge(
                [asset('storage/tours/' . $model->image)],
                $model->images
                      ->filter(fn($img) => $img->url != $model->image)
                      ->map(fn($img) => asset('storage/tours/' . $img->url))
                      ->values()
                      ->toArray()
            ),
            "touristType" => \App\Models\Tour::getTourType($model->tour_type),
            "itinerary" => [
                "@type" => "ItemList",
                "itemListElement" => [
                    [
                        "@type" => "TouristDestination",
                        "name" => $model->city->name,
                        "address" => [
                            "@type" => "PostalAddress",
                            "addressLocality" => $model->city->name,
                            "addressRegion" => $model->province->name,
                            "streetAddress" => $model->address,
                        ]
                    ]
                ],
            ],
            "offers" => [
                "@type" => "Offer",
                "price" => $model->amount,
                "priceCurrency" => "IRR",
                "availability" => "http://schema.org/InStock",
                "validFrom" => now()->toIso8601String(),
                "url" => url()->current(),
                "seller" => [
                    "@type" => "Organization",
                    "name" => $model->admin->name . ' ' . $model->admin->family,
                    "telephone" => \App\Models\User::find($model->user_id)->phone,
                ],
            ],
            "maximumAttendeeCapacity" => $model->max_people,
            "startDate" => ($model->tour_time_frame == "one") ? $model->expire_date : null,
            "duration" => "P" . $model->tour_duration . "D"
        ];
    @endphp

    @push('head')
        <script type="application/ld+json">
            {!! json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @endpush
    <link rel="stylesheet" href="{{asset("/plugin/swiper-slider/swiper-bundle.min.css")}}"/>
    @vite(['resources/css/user/detail.less'])
    <span id="ads-code">کد تور <span>{{$model->id}}</span></span>
    <h1 wire:ignore>{{$model->title}}</h1>
    <div wire:ignore class="row">
        <div class="col-7">
            <address>
                <i class="fa fa-map-marker " style="font-size: 22px"></i>
                استان {{$model->province->name}}، {{$model->city->name}}
            </address>
        </div>
        <div class="col-5">
          <span style="float: left" class="btn btn-sm btn-light" id="shareBtn">
    <i class="fa fa-share-alt"></i> اشتراک گذاری
</span>

            @script
            <script>
                document.getElementById("shareBtn").addEventListener("click", function() {
                    const pageUrl = window.location.href; // لینک صفحه فعلی
                    const pageTitle = document.title;     // عنوان صفحه

                    // اگر مرورگر از Web Share API پشتیبانی کنه (موبایل و بعضی دسکتاپ‌ها)
                    if (navigator.share) {
                        navigator.share({
                            title: pageTitle,
                            url: pageUrl
                        }).then(() => {
                            console.log('لینک با موفقیت به اشتراک گذاشته شد');
                        }).catch((error) => {
                            console.log('خطا در اشتراک گذاری:', error);
                        });
                    } else {
                        // fallback برای مرورگرهایی که Web Share API ندارند
                        const shareText = `${pageTitle} \n${pageUrl}`;
                        const shareOptions = `
            <div style="text-align:center;">
                <p>لینک سفر را کپی یا در شبکه‌های اجتماعی به اشتراک بگذارید:</p>
                <input type="text" value="${pageUrl}" id="shareLink" style="width:80%; padding:5px;" readonly>
                <button onclick="copyShareLink()">کپی لینک</button>
                <br><br>
                <a href="https://telegram.me/share/url?url=${encodeURIComponent(pageUrl)}&text=${encodeURIComponent(pageTitle)}" target="_blank">تلگرام</a> |
                <a href="https://wa.me/?text=${encodeURIComponent(pageTitle + ' ' + pageUrl)}" target="_blank">واتس‌اپ</a> |
                <a href="https://twitter.com/intent/tweet?url=${encodeURIComponent(pageUrl)}&text=${encodeURIComponent(pageTitle)}" target="_blank">توییتر</a>
            </div>
        `;
                        const shareWindow = window.open("", "Share", "width=400,height=400");
                        shareWindow.document.write(shareOptions);
                    }
                });

                function copyShareLink() {
                    const copyText = document.getElementById("shareLink");
                    copyText.select();
                    copyText.setSelectionRange(0, 99999);
                    document.execCommand("copy");
                    alert("لینک کپی شد!");
                }
            </script>
            @endscript

        </div>
    </div>
    <div id="detail" class="row">
        <div wire:ignore class="col-xl-6">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                            <img 
        src="{{asset('storage/tours/' . $model->image)}}" 
        alt="{{$model->title}} - تور {{$model->city->name}}"
        loading="lazy"
        onerror="this.onerror=null; this.src='{{ asset('storage/static/onerror.jpg') }}'"
    >
                    </div>
                    @foreach($model->images as $image)
                        @if($model->image!=$image->url)

                            <div class="swiper-slide">
                                <img src="{{asset("storage/tours/".$image->url)}}"
								alt="{{$model->title}} - تور {{$model->city->name}}"
								loading="lazy"
								onerror="this.onerror=null; this.src='{{ asset('storage/onerror-tour.jpg') }}'"
								>
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
                    {{number_format($model->amount)}} تومان
                </span>
                <span class="color-c3">
                    / برای هر نفر
                </span>
            </div>
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
            @script
            <script>
                $(function () {
                    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
                    const callLink = document.getElementById("callLink");
                    const phone = "{{\App\Models\User::find($model->user_id)->phone}}"
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

            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-map-marker"></i>
                {{$model->address}}
            </div>
            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-users"></i>
                ظرفیت تور:
                {{$model->min_people}}
                @if($model->min_people!=$model->max_people)
                    تا
                    {{$model->max_people}}
                @endif
                نفر
            </div>
            <div wire:ignore style="margin-top: 5px">
                مدت تور:
                {{$model->tour_duration}}
                روز
            </div>
            <div wire:ignore style="margin-top: 5px">
                نوع تور:
                {{\App\Models\Tour::getTourType($model->tour_type)}}
            </div>
            <div wire:ignore style="margin-top: 5px">
                محل اقامت:
                {{\App\Models\Tour::getResidenceType($model->residence_type)}}
            </div>
            <div wire:ignore style="margin-top: 5px">
                تاریخ برگزاری:
                @if($model->tour_time_frame=="one")
                    {{str_replace("-","/",$model->expire_date)}}
                @elseif($model->tour_time_frame=="weekly")
                    @switch($model->open_tour_time)
                        @case("saturday")شنبه هر هفته@break
                        @case("sunday")یکشنبه هر هفته@break
                        @case("monday")دوشنبه هر هفته@break
                        @case("tuesday")سه شنبه هر هفته@break
                        @case("wednesday")چهارشنبه هر هفته@break
                        @case("thursday")پنجشنبه هر هفته@break
                        @case("friday")جمعه هر هفته@break
                    @endswitch
                @else
                    {{$model->open_tour_time}} هر ماه
                @endif
            </div>
            <div wire:ignore style="margin-top: 5px">
                {{$model->description}}
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
    <p class="font-weight-bold">
        <i class="fa fa-file-text-o"></i>
        قوانین عمومی
    </p>
<p style="padding-right: 12px">
    ارائه کارت ملی یا گذرنامه معتبر در زمان ثبت‌نام الزامی است.
    <br>
    کنسلی تور با اطلاع حداقل ۷۲ ساعت قبل امکان‌پذیر است.
    <br>
    تغییر برنامه سفر تنها با هماهنگی آژانس ممکن می‌باشد.
    <br>
    مسئولیت همراه داشتن مدارک لازم (ویزا، تست پزشکی) بر عهده مسافر است.
    <br>
    رعایت قوانین کشور مقصد و مقررات پروازی الزامی است.
    <br>
    بیمه مسافرتی شامل موارد اعلام‌شده در قرارداد می‌باشد.
    <br>
</p>
    <div class="bg-c3" style="height: 3px;margin-top: 20px"></div>
    <p class="font-weight-bold">
        اقامتگاه های {{$model->city->name}}
    </p>
    <swiper-container  wire:ignore id="residences" space-between="30" slides-per-view="auto" class="nav nav-tabs" id="nav-tab" role="tablist">

        @php
            $cities=\App\Models\City::where("is_use",true)->get()->keyBy("id");
            $provinces=\App\Models\Province::where("is_use",true)->get()->keyBy("id");
        @endphp
        @foreach(\App\Models\Residence::where("city_id",$model->city_id)->limit(5)->get() as $model)

            <swiper-slide>
                <div>
                    <h3>{{$model->title}}</h3>
                    <span class="line"></span>
                    <div class="image-container">
                        <img src="{{asset("storage/residences/".$model->image)}}">
                    </div>
                    <span class="line"></span>
                    <div class="d-flex flex-row justify-content-around p-2">
                        <span  style="font-size: 12px">برای 1 شب</span>
                        <span style="font-size: 14px">
                                <span class="font-weight-bold">
                                    {{convertEnglishToPersianNumbers(number_format($model->amount))}}
                                </span>                                تومان
                            </span>
                    </div>
                    <span class="line"></span>
                    <div class="services d-flex flex-row justify-content-right p-2">
                        <p>استان {{$provinces[$model->province_id]->name}} - {{$cities[$model->city_id]->name}} </p>
                    </div>
                    <span class="line"></span>
                    <div class="d-flex flex-row justify-content-between p-2">
                        <a style="padding-left: 1.5rem !important;font-size: 14px" class="w-100 pt-1 pb-1 pl-4 pr-4 btn btn-success" href="{{\Illuminate\Support\Facades\URL::to("detail/".$model->id)}}">
                            ثبت رزرو
                        </a>
                    </div>
                    <span class="line"></span>
                    <div class="services d-flex flex-row justify-content-right p-2">
                            <span>
                                <i class="fa fa-users"></i>
                                {{$model->people_number}}
                                نفر
                            </span>
                        &nbsp;
                        &nbsp;
                        <span>
                                <i class="fa fa-expand"></i>
                                {{$model->area}}
                                متر
                            </span>
                        &nbsp;
                        &nbsp;
                        <span>
                                <i class="fa fa-bed"></i>
                                {{$model->room_number}}
                                اتاق
                            </span>
                    </div>
                </div>
            </swiper-slide>
        @endforeach
    </swiper-container>
    @php
        $foodstores=\App\Models\FoodStore::where("city_id",$model->city_id)->limit(5)->get()
    @endphp
    @if(!$foodstores->isEmpty())
        <div class="bg-c3" style="height: 3px;margin-top: 20px"></div>
        <p class="font-weight-bold">
            مکان های نزدیک
        </p>
        <swiper-container  wire:ignore id="residences" space-between="30" slides-per-view="auto" class="nav nav-tabs" id="nav-tab" role="tablist">

            @foreach($foodstores as $model)

                <swiper-slide>
                    <div>
                        <h3>{{$model->title}}</h3>
                        <span class="line"></span>
                        <div class="image-container">
                            <img src="{{asset("storage/food_store/".$model->image)}}">
                        </div>
                        <span class="line"></span>
                        <div class="services d-flex flex-row justify-content-right p-2">
                            <p>استان {{$provinces[$model->province_id]->name}} - {{$cities[$model->city_id]->name}} </p>
                        </div>
                        <span class="line"></span>
                        <div class="d-flex flex-row justify-content-between p-2">
                            <a style="padding-left: 1.5rem !important;font-size: 14px" class="w-100 pt-1 pb-1 pl-4 pr-4 btn btn-success" href="{{\Illuminate\Support\Facades\URL::to("detail/".$model->id)}}">
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
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js" onerror="this.onerror=null;this.src='{{ asset(\"/plugin/swiper-slider/swiper-element-bundle.min.js\") }}';"></script>
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
