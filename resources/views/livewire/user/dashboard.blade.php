<div class="row">
    @vite(['resources/css/user/dashboard.less'])
    @if(session('message'))
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
                title: '{{session('message')}}'
            });
        </script>
        @php
            session()->forget('message');
        @endphp
        @endscript
    @endif

    <div class="col-12" style="text-align: center" id="btns">
        <p class="font-weight-bold pt-2 text-left">
            خدمات من
            <span wire:click="logout" class="float-left  logout" style="">
                خروج
            </span>
        </p>
        @php
            $ids = \App\Models\Ticket::where('user_id', auth()->id())
             ->distinct()
             ->pluck('id')
             ->filter()
             ->toArray();
        @endphp
        @if( \App\Models\TicketChat::whereIn("ticket_id",$ids)->where("seen",0)
            ->where("user_id","!=",auth()->id())->count()>0)
            <div class="alert alert-primary">
    <i class="fa fa-bell"></i>
                شما پیام خوانده نشده دارید
                <span id="read-messages" class="btn btn-primary " style="padding: 3px 6px !important;">
                    خواندن پیام
                </span>
            </div>
        @endif
        <a href="{{\Illuminate\Support\Facades\URL::to("add-residence")}}" class="btn btn-light">
            <span class="icon" style="">
                <i style="" class="fa fa-home"></i>
            </span>
            <h4>
                ثبت اقامتگاه
            </h4>
            <p>
                ثبت و مدیریت اقامتگاه شما به صورت رایگان
            </p>
            <span class="badge">میزبان</span>
        </a>
        <a href="{{\Illuminate\Support\Facades\URL::to("add-tour")}}" class="btn btn-light">
            <span class="icon" style="">
                <i style="" class="fa fa-map-pin"></i>
            </span>
            <h4>ثبت تور مسافرتی</h4>
            <p>ثبت و مدیریت تورها توسط آژانس‌های مسافرتی</p>
            <span class="badge">مدیر آژانس</span>
        </a>
        <a href="{{\Illuminate\Support\Facades\URL::to("add-foodstore")}}" class="btn btn-light">
            <span class="icon" style="">
                <i style="" class="fa fa-cutlery"></i>
            </span>
            <h4>ثبت رستوران‌</h4>
            <p>ثبت و مدیریت مراکز غذایی و کافه‌های نزدیک</p>
            <span class="badge">مشارکت کننده</span>
        </a>
        <a href="{{\Illuminate\Support\Facades\URL::to("add-friend")}}" class="btn btn-light">
            <span class="icon" style="">
                <i style="" class="fa fa-users"></i>
            </span>
            <h4>ثبت همسفر</h4>
            <p>پیدا کردن همراه مطمئن برای سفرهای شما</p>
            <span class="badge">کاربر ویژه</span>
        </a>
        <a href="{{\Illuminate\Support\Facades\URL::to("profile")}}" class="btn btn-light">
            <span class="icon" style="">
                <i style="" class="fa fa-user"></i>
            </span>
            <h4>پروفایل کاربری</h4>
            <p>مدیریت اطلاعات شخصی و تنظیمات حساب</p>
            <span class="badge">همه کاربران</span>
        </a>
        <br>
        <br>
        <br>
    </div>
    <br>
    <br>
    <div id="nav-tabs" class="col-12">
        <nav>
            <swiper-container space-between="30" slides-per-view="auto" class="nav nav-tabs"
                              id="nav-tab" role="tablist">
                <swiper-slide>
                    <button class="nav-link active" id="nav-residences-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-residences" type="button" role="tab"
                            aria-controls="nav-residences" aria-selected="true">
                        اقامتگاه های من
                    </button>
                </swiper-slide>
                <swiper-slide class="nav-item" role="presentation">
                    <button class="nav-link" id="nav-tours-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-tours" type="button" role="tab"
                            aria-controls="nav-tours" aria-selected="false">
                        تور های من
                    </button>
                </swiper-slide>
                <swiper-slide class="nav-item" role="presentation">
                    <button class="nav-link" id="nav-resturants-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-resturants" type="button" role="tab"
                            aria-controls="nav-resturants" aria-selected="false">
                        رستوران ها
                    </button>
                </swiper-slide>
                <swiper-slide class="nav-item" role="presentation">
                    <button class="nav-link" id="nav-freinds-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-freinds" type="button" role="tab"
                            aria-controls="nav-freinds" aria-selected="false">
                        همسفران
                    </button>
                </swiper-slide>
                <swiper-slide class="nav-item" role="presentation">
                    <button class="nav-link" id="nav-tickets-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-tickets" type="button" role="tab"
                            aria-controls="nav-tickets" aria-selected="false">
                        پیام های من
                    </button>
                </swiper-slide>
            </swiper-container>
        </nav>

        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-residences" role="tabpanel"
                 aria-labelledby="nav-residences-tab">

                <p class="font-weight-bold pt-2">
                    اقامتگاه های من
                </p>
                <ul id="residences">
                    @foreach(auth()->user()->residences as $item)
                        <li>
                            <div>
                                <h3>{{$item->title}}</h3>
                                <span class="line"></span>
                                <div class="image-container">
                                    <img src="{{asset("storage/residences/".$item->image)}}">
                                </div>
                                <span class="line"></span>
                                <div class="d-flex flex-row justify-content-right p-2">
                                    <a href="{{\Illuminate\Support\Facades\URL::to("detail/".$item->id)}}"
                                       class="btn btn-sm btn-light">
                                        مشاهده
                                    </a>
                                    &nbsp;
                                    &nbsp;
                                    <a href="{{\Illuminate\Support\Facades\URL::to("edit-residence/".$item->id)}}"
                                       class="btn btn-sm btn-primary">
                                        ویرایش
                                    </a>
                                    &nbsp;
                                    &nbsp;
                                    <button
                                        type="button"
                                        wire:click="removeResidence({{ $item->id }})"
                                        wire:confirm="از حذف کردن این اقامتگاه اطمینان دارید؟"
                                        class="btn btn-sm btn-danger">
                                        حذف
                                    </button>
                                </div>
                                <span class="line"></span>
                                <div class="services d-flex flex-row justify-content-right p-2">
                            <span>
                        <i class="fa fa-eye"></i>
                            {{$item->view}}
                                بازدید
                            </span>

                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

            </div>
            <div class="tab-pane fade" id="nav-tours" role="tabpanel"
                 aria-labelledby="nav-tours-tab">

                <p class="font-weight-bold pt-2">
                    تور های من
                </p>
                <ul id="residences">
                    @foreach(auth()->user()->tours as $item)
                        <li>
                            <div>
                                <h3>{{$item->title}}</h3>
                                <span class="line"></span>
                                <div class="image-container">
                                    <img src="{{asset("storage/tours/".$item->image)}}">
                                </div>
                                <span class="line"></span>
                                <div class="d-flex flex-row justify-content-right p-2">
                                    <a href="{{\Illuminate\Support\Facades\URL::to("tour/".$item->id)}}"
                                       class="btn btn-sm btn-light">
                                        مشاهده
                                    </a>
                                    &nbsp;
                                    &nbsp;
                                    <a href="{{\Illuminate\Support\Facades\URL::to("edit-tour/".$item->id)}}"
                                       class="btn btn-sm btn-primary">
                                        ویرایش
                                    </a>
                                    &nbsp;
                                    &nbsp;
                                    <button
                                        type="button"
                                        wire:click="removeTour({{ $item->id }})"
                                        wire:confirm="از حذف کردن این تور اطمینان دارید؟"
                                        class="btn btn-sm btn-danger">
                                        حذف
                                    </button>
                                </div>
                                <span class="line"></span>
                                <div class="services d-flex flex-row justify-content-right p-2">
                            <span>
                        <i class="fa fa-eye"></i>
                            {{$item->view}}
                                بازدید
                            </span>

                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

            </div>
            <div class="tab-pane fade" id="nav-resturants" role="tabpanel"
                 aria-labelledby="nav-resturants-tab">
                <p class="font-weight-bold pt-2">
                    رستوران های ثبت شده
                </p>
                <ul id="residences">
                    @foreach(auth()->user()->foodstores as $item)
                        <li>
                            <div>
                                <h3>{{$item->title}}</h3>
                                <span class="line"></span>
                                <div class="image-container">
                                    <img src="{{asset("storage/food_store/".$item->image)}}">
                                </div>
                                <span class="line"></span>
                                <div class="d-flex flex-row justify-content-right p-2">
                                    <a href="{{\Illuminate\Support\Facades\URL::to("store/".$item->id)}}"
                                       class="btn btn-sm btn-light">
                                        مشاهده
                                    </a>
                                    &nbsp;
                                    &nbsp;
                                    <a href="{{\Illuminate\Support\Facades\URL::to("edit-foodstore/".$item->id)}}"
                                       class="btn btn-sm btn-primary">
                                        ویرایش
                                    </a>
                                    &nbsp;
                                    &nbsp;
                                    <button
                                        type="button"
                                        wire:click="removeFoodstore({{ $item->id }})"
                                        wire:confirm="از حذف کردن این رستوران اطمینان دارید؟"
                                        class="btn btn-sm btn-danger">
                                        حذف
                                    </button>
                                </div>
                                <span class="line"></span>
                                <div class="services d-flex flex-row justify-content-right p-2">
                            <span>
                        <i class="fa fa-eye"></i>
                            {{$item->view}}
                                بازدید
                            </span>

                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="tab-pane fade" id="nav-freinds" role="tabpanel"
                 aria-labelledby="nav-freinds-tab">


                <p class="font-weight-bold pt-2">
                    درخواست های من برای همسفر
                </p>
                <ul id="residences">
                    @foreach(auth()->user()->friends as $item)
                        <li>
                            <div>
                                <h3>{{$item->title}}</h3>
                                <span class="line"></span>
                                <div class="image-container">
                                    <img src="{{asset("storage/friends/".$item->image)}}">
                                </div>
                                <span class="line"></span>
                                <div class="d-flex flex-row justify-content-right p-2">
                                    <a href="{{\Illuminate\Support\Facades\URL::to("friend/".$item->id)}}"
                                       class="btn btn-sm btn-light">
                                        مشاهده
                                    </a>
                                    &nbsp;
                                    &nbsp;
                                    <a href="{{\Illuminate\Support\Facades\URL::to("edit-friend/".$item->id)}}"
                                       class="btn btn-sm btn-primary">
                                        ویرایش
                                    </a>
                                    &nbsp;
                                    &nbsp;
                                    <button
                                        type="button"
                                        wire:click="removeFriend({{ $item->id }})"
                                        wire:confirm="از حذف کردن این درخواست همسفر اطمینان دارید؟"
                                        class="btn btn-sm btn-danger">
                                        حذف
                                    </button>
                                </div>
                                <span class="line"></span>
                                <div class="services d-flex flex-row justify-content-right p-2">
                            <span>
                        <i class="fa fa-eye"></i>
                            {{$item->view}}
                                بازدید
                            </span>

                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

            </div>
            <div class="tab-pane fade" id="nav-tickets" role="tabpanel"
                 aria-labelledby="nav-tickets-tab">


                <p class="font-weight-bold pt-2">
                    پیام های من
                </p>
                <table class="table responsive-table">
                    <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>عنوان</th>
                        <th>واحد</th>
                        <th>وضعیت</th>
                        <th>تاریخ ثبت</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $areas=\App\Models\SupportAreaTickets::all()->keyBy("id");
                    @endphp
                    @foreach(auth()->user()->tickets as $item)

                        <tr>
                            <td data-label="Id">{{$item->id}}</td>
                            <td data-label="عنوان">{{$item->title}}</td>
                            <td data-label="بخش">{{$areas[$item->area]->title}}</td>
                            <td data-label="وضعیت">
                                <span class="   {{$item->status==0?"text-warning":"text-success"}}">
                                    {{$item->status==0?"درحال بررسی":""}}
                                    {{$item->status==1?"پاسخ داده شده":""}}
                                </span>
                            </td>
                            @php
                                $gregorianDate = new \DateTime($item["created_at"]);
                                $jalaliDate = \Morilog\Jalali\Jalalian::fromDateTime($gregorianDate);
                            @endphp
                            <td data-label="تاریخ ثبت">
                                {{$jalaliDate->format('%Y/%m/%d')}}
                                <br>
                                <span class="op-5">
                            {{$jalaliDate->format('H:i')}}
                            </span>
                            </td>
                            <td data-label="">
                                <a href="{{\Illuminate\Support\Facades\URL::to("ticket/".$item->id)}}" class="btn btn-sm btn-primary" >
                                    مشاهده
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div wire:ignore>
        @script
        <script>
            $("#read-messages").click(function (){
                $("#nav-tickets-tab").trigger("click");
                window.location.href = "#nav-tabs";
            })
            $(".nav-tabs button").click(function () {
                $(".nav-tabs button").removeClass("active")
                $(".nav-tabs button").attr("aria-selected", "false")
                let btn = $(this)
                btn.addClass("active")
                btn.attr("aria-selected", "true")
                $("#nav-tabContent div").removeClass("show active")
                $("#nav-tabContent div" + btn.attr("data-bs-target")).addClass("show active")

            })
        </script>
        @endscript
        </div>
        <style>
            swiper-container {
                width: 100%;
                height: 100%;
            }

            swiper-slide {
                text-align: center;
                font-size: 18px;
                margin-left: 0 !important;
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
    <div class="col-12 "></div>
    <div class="col-12 "></div>

    <br>
    <br>
    <br>
    <br>
</div>
