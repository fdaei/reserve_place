<div>
    @php
        $schemaData = [
            "@context" => "https://schema.org",
            "@type" => "Restaurant",
            "name" => $model->title,
            "description" => strip_tags(Str::limit($model->description, 160)),
            "image" => array_merge(
                [asset('storage/food_store/' . $model->image)],
                $model->images->filter(fn($img) => $img->url != $model->image)
                              ->map(fn($img) => asset('storage/food_store/' . $img->url))
                              ->values()
                              ->toArray()
            ),
            "address" => [
                "@type" => "PostalAddress",
                "streetAddress" => $model->address,
                "addressLocality" => $model->city->name,
                "addressRegion" => $model->province->name,
                "postalCode" => $model->postal_code ?? "",
                "addressCountry" => "IR"
            ],
            "geo" => [
                "@type" => "GeoCoordinates",
                "latitude" => $model->lat,
                "longitude" => $model->lng
            ],
            "telephone" => \App\Models\User::find($model->user_id)->phone,
            "priceRange" => number_format($model->amount) . " تومان هر شب",
            "servesCuisine" => \App\Models\FoodStore::getFoodType($model->food_type),
            "openingHours" => [
                "Mo-Su " . str_replace(':', '', $model->open_time) . "-" . str_replace(':', '', $model->close_time)
            ],
            "aggregateRating" => [
                "@type" => "AggregateRating",
                "ratingValue" => number_format($model->point, 1),
                "reviewCount" => $model->comments->count(),
            ],
            "menu" => asset('storage/food_store/menu/' . ($model->menu ?? '')),
        ];
    @endphp

    @push('head')
        <script type="application/ld+json">
            {!! json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @endpush

    <link rel="stylesheet" href="{{asset("/plugin/swiper-slider/swiper-bundle.min.css")}}"/>
    @vite(['resources/css/user/detail.less'])
    <span id="ads-code">کد رستوران <span>{{$model->id}}</span></span>
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
                        <img src="{{asset("storage/food_store/".$model->image)}}">
                    </div>
                    @foreach($model->images as $image)
                        @if($model->image!=$image->url)

                            <div class="swiper-slide">
                                <img src="{{asset("storage/food_store/".$image->url)}}">
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
            @script
            <script>
                $(function () {
                    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
                    const callLink = document.getElementById("callLink");
                    const phone = "{{\App\Models\User::find($this->model->user_id)->phone}}"
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
                <i class="fa fa-cutlery"></i>
                {{\App\Models\FoodStore::getStoreType($model->store_type)}} -
                غذای
                {{\App\Models\FoodStore::getFoodType($model->food_type)}}
            </div>
            <div wire:ignore style="margin-top: 5px">
                <i class="fa fa-clock-o"></i>
                ساعت کاری:
                {{explode(":",$model->open_time)[1]}}:{{explode(":",$model->open_time)[0]}}
                تا
                {{explode(":",$model->close_time)[1]}}:{{explode(":",$model->close_time)[0]}}
            </div>
            <div style="margin-top: 5px">
                <i class="fa fa-star"></i>
                امتیاز کلی رستوران {{number_format($model->point,1)}}
                <span class="color-c3">(از {{$model->comments->count()}} نظر)</span>
            </div>
            @if($model->point>0)
                <div style="margin-top: 30px;text-align: center">
                    @php
                        $isEchoHalf=false;
                    @endphp
                    @for($i=0;$i<floor($model->point);$i++)
                        <i class="fa fa-star star"></i>
                    @endfor
                    @if(is_float($model->point))
                        @if(fmod($model->point,1)!=(float)0)
                            @php
                                $isEchoHalf=true;
                            @endphp
                            <i class="fa fa-star-half-o star"></i>
                        @endif
                    @endif
                    @for($i=($isEchoHalf==true?4:5)-($model->point);$i>0;$i--)
                        <i class="fa fa-star-o star"></i>
                    @endfor

                </div>
            @endif
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
        @foreach(\App\Models\OptionCategory::where("type","foodstore")->get() as $key=>$category)
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
            var map = L.map('map').setView([{{$model->lat}}, {{$model->lng}}], 13);

            // markerهای سایر مکان‌ها
            var fixedLocations = [
                    @foreach(\App\Models\Residence::all() as $item)
                { lat: {{$item->lat}}, lng: {{$item->lng}}, name: "{{$item->title}}", url: "{{\Illuminate\Support\Facades\URL::to('detail/'.$item->id)}}" },
                @endforeach
            ];

            var otherIcon = L.icon({
                iconUrl: '{{ asset("storage/".getConfigs("markerMapIcon")) }}',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            fixedLocations.forEach(function(location) {
                L.marker([location.lat, location.lng], { icon: otherIcon })
                    .addTo(map)
                    .bindPopup(`<b>${location.name}</b>`);
            });

            // لایه نقشه
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // نقطه اصلی (markerMapFoodstoreIcon)
            var mainLat = {{$model->lat}};
            var mainLng = {{$model->lng}};

            var foodstoreIcon = L.icon({
                iconUrl: '{{ asset("storage/".getConfigs("markerMapFoodstoreIcon")) }}',
                iconSize: [48, 48],
                iconAnchor: [24, 48],
                popupAnchor: [0, -40],
            });

            L.marker([mainLat, mainLng], { icon: foodstoreIcon })
                .addTo(map)
                .bindPopup(`<b>استان {{$model->province->name}}، {{$model->city->name}}</b>`)
                .openPopup();

            // event کلیک روی کل نقشه فقط برای این نقطه
            map.on('click', function(e) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var userLat = position.coords.latitude;
                        var userLng = position.coords.longitude;
                        var gmapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${mainLat},${mainLng}`;
                        window.open(gmapsUrl, "_blank");
                    }, function(error) {
                        // اگر موقعیت کاربر پیدا نشد، فقط مقصد باز شود
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
    <div class="bg-c3" style="height: 3px;margin-top: 20px"></div>
    <p class="font-weight-bold">
        <i class="fa fa-file-text-o"></i>
        قوانین عمومی
    </p>
<p style="padding-right: 12px">
    رزرو میز فقط تا ۳۰ دقیقه پس از زمان مقرر حفظ می‌شود.
    <br>
    کنسلی رزرو با اطلاع حداقل ۲ ساعت قبل امکان‌پذیر است.
    <br>
    حضور مهمانان اضافی نیازمند اطلاع قبلی می‌باشد.
    <br>
    رعایت قوانین بهداشتی و پوشش مناسب در محیط الزامی است.
    <br>
    مسئولیت نظارت بر کودکان بر عهده همراهان است.
    <br>
    قیمت‌ها شامل مالیات بر ارزش افزوده می‌باشد.
    <br>
</p>
    <div class="bg-c3" style="height: 3px;margin-top: 20px"></div>
    <p class="font-weight-bold">
        اقامتگاه های نزدیک
    </p>
    <swiper-container  wire:ignore id="residences" space-between="30" slides-per-view="auto" class="nav nav-tabs" id="nav-tab" role="tablist">

        @php
            $cities=\App\Models\City::where("is_use",true)->get()->keyBy("id");
            $provinces=\App\Models\Province::where("is_use",true)->get()->keyBy("id");
        @endphp
        @foreach(\App\Models\Residence::where("city_id",$model->city_id)->limit(5)->get() as $residence)

            <swiper-slide>
                <div>
                    <h3>{{$residence->title}}</h3>
                    <span class="line"></span>
                    <div class="image-container">
                        <img src="{{asset("storage/residences/".$residence->image)}}">
                    </div>
                    <span class="line"></span>
                    <div class="d-flex flex-row justify-content-around p-2">
                        <span  style="font-size: 12px">برای 1 شب</span>
                        <span style="font-size: 14px">
                                <span class="font-weight-bold">
                                    {{convertEnglishToPersianNumbers(number_format($residence->amount))}}
                                </span>                                تومان
                            </span>
                    </div>
                    <span class="line"></span>
                    <div class="services d-flex flex-row justify-content-right p-2">
                        <p>استان {{$provinces[$residence->province_id]->name}} - {{$cities[$residence->city_id]->name}} </p>
                    </div>
                    <span class="line"></span>
                    <div class="d-flex flex-row justify-content-between p-2">
                        <a style="padding-left: 1.5rem !important;font-size: 14px" class="w-100 pt-1 pb-1 pl-4 pr-4 btn btn-success" href="{{\Illuminate\Support\Facades\URL::to("detail/".$residence->id)}}">
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
                        &nbsp;
                        &nbsp;
                        <span>
                                <i class="fa fa-expand"></i>
                                {{$residence->area}}
                                متر
                            </span>
                        &nbsp;
                        &nbsp;
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
        $foodstores=\App\Models\FoodStore::where("city_id",$model->city_id)->limit(5)->get()
    @endphp
    @if(!$foodstores->isEmpty())
        <div class="bg-c3" style="height: 3px;margin-top: 20px"></div>
        <p class="font-weight-bold">
            مکان های نزدیک
        </p>
        <swiper-container  wire:ignore id="residences" space-between="30" slides-per-view="auto" class="nav nav-tabs" id="nav-tab" role="tablist">

            @foreach($foodstores as $residence)

                <swiper-slide>
                    <div>
                        <h3>{{$residence->title}}</h3>
                        <span class="line"></span>
                        <div class="image-container">
                            <img src="{{asset("storage/food_store/".$residence->image)}}">
                        </div>
                        <span class="line"></span>
                        <div class="services d-flex flex-row justify-content-right p-2">
                            <p>استان {{$provinces[$residence->province_id]->name}} - {{$cities[$residence->city_id]->name}} </p>
                        </div>
                        <span class="line"></span>
                        <div class="d-flex flex-row justify-content-between p-2">
                            <a style="padding-left: 1.5rem !important;font-size: 14px" class="w-100 pt-1 pb-1 pl-4 pr-4 btn btn-success" href="{{\Illuminate\Support\Facades\URL::to("detail/".$residence->id)}}">
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
