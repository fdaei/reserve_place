<div>
    <div id="nav-tabs" class="col-12">
        <nav wire:ignore>
            <swiper-container space-between="30" slides-per-view="auto" class="nav nav-tabs"
                              id="nav-tab" role="tablist">
                <swiper-slide>
                    <button class="nav-link active" id="nav-general-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-general" type="button" role="tab"
                            aria-controls="nav-general" aria-selected="true">
                        اطلاعات عمومی
                    </button>
                </swiper-slide>
                <swiper-slide class="nav-item" role="presentation">
                    <button class="nav-link" id="nav-skin-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-skin" type="button" role="tab"
                            aria-controls="nav-skin" aria-selected="false">
                        تنظیمات ظاهری
                    </button>
                </swiper-slide>
                <swiper-slide class="nav-item" role="presentation">
                    <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-contact" type="button" role="tab"
                            aria-controls="nav-contact" aria-selected="false">
                        تماس و پشتیبانی
                    </button>
                </swiper-slide>
                <swiper-slide class="nav-item" role="presentation">
                    <button class="nav-link" id="nav-banner-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-banner" type="button" role="tab"
                            aria-controls="nav-banner" aria-selected="false">
                        اطلاع رسانی و بنر ها
                    </button>
                </swiper-slide>
                <swiper-slide class="nav-item" role="presentation">
                    <button class="nav-link" id="nav-finance-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-finance" type="button" role="tab"
                            aria-controls="nav-finance" aria-selected="false">
                        تنظیمات مالی
                    </button>
                </swiper-slide>
                <swiper-slide class="nav-item" role="presentation">
                    <button class="nav-link" id="nav-filters-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-filters" type="button" role="tab"
                            aria-controls="nav-filters" aria-selected="false">
                        تنظیمات جستجو و فیلترها
                    </button>
                </swiper-slide>
                <swiper-slide class="nav-item" role="presentation">
                    <button class="nav-link" id="nav-socialmedia-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-socialmedia" type="button" role="tab"
                            aria-controls="nav-socialmedia" aria-selected="false">
                        شبکه های اجتماعی
                    </button>
                </swiper-slide>
                <swiper-slide class="nav-item" role="presentation">
                    <button class="nav-link" id="nav-config-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-config" type="button" role="tab"
                            aria-controls="nav-config" aria-selected="false">
                        تنظیمات فنی
                    </button>
                </swiper-slide>
            </swiper-container>
        </nav>

        <div  class="tab-content" id="nav-tabContent">
            <div wire:ignore.self class="tab-pane fade show active" id="nav-general" role="tabpanel"
                 aria-labelledby="nav-general-tab">


                <form wire:submit="updateGeneral" class="settings-form">
                    <div class="form-group">
                        <label>عنوان سایت</label>
                        <input wire:model="title" type="text" class="form-control" name="site_title" required>
                        @error('title')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>عنوان انگلیسی سایت</label>
                        <input wire:model="titleEn" type="text" class="form-control" name="site_title" required>
                        @error('titleEn')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>متای عنوان</label>
                        <input wire:model="mainTitle" type="text" class="form-control" name="mainTitle" required>
                        @error('mainTitle')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>متای توضیحات</label>
                        <textarea wire:model="description" class="form-control" name="meta_description" rows="3"></textarea>
                        @error('description')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>متای کلمات کلیدی (با , جدا کنید)</label>
                        <textarea wire:model="words" class="form-control" name="meta_keywords" rows="2"></textarea>
                        @error('words')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>





                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i>
                        ذخیره تغییرات
                    </button>
                </form>

            </div>



            <div wire:ignore.self class="tab-pane fade" id="nav-skin" role="tabpanel"
                 aria-labelledby="nav-skin-tab">
                <form wire:submit="updateSkin" class="settings-form">
                    <div class="form-group">
                        <label>رنگ اصلی سایت</label>
                        <input wire:model="mainColor" type="color" class="form-control" name="site_title" required>
                        @error('mainColor')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>رنگ ثانویه سایت</label>
                        <input wire:model="secondaryColor" type="color" class="form-control" name="site_title" required>
                        @error('secondaryColor')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group file-upload">
                        <label>لوگو سایت</label>
                        <input wire:model="iconInput" type="file" class="form-control" accept="image/*" name="site_logo">
                        <small>فرمت‌های مجاز: PNG, JPG - اندازه پیشنهادی: 200x200 پیکسل</small>
                        <div class="preview-box">
                            @if($iconInput)
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="iconInput" id=""
                                     src="{{$iconInput->temporaryUrl()}}">
                            @elseif($icon!="")
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="iconInput" id=""
                                     src="{{asset("storage/".$icon)}}">
                            @else
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="iconInput" id=""
                                     src="{{asset("storage/icon.png")}}">
                            @endif
                        </div>
                        @error('iconInput')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group file-upload">
                        <label>تصویر بنر فصل ها</label>
                        <input wire:model="bannerSeasonImageTemp" type="file" class="form-control" accept="image/*" name="bannerSeasonImage">
                        <small>فرمت‌های مجاز: PNG, JPG - اندازه پیشنهادی: 200x200 پیکسل</small>
                        <div class="preview-box">
                            @if($bannerSeasonImageTemp)
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="bannerSeasonImageTemp" id=""
                                     src="{{$bannerSeasonImageTemp->temporaryUrl()}}">
                            @elseif($bannerSeasonImage!="")
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="bannerSeasonImageTemp" id=""
                                     src="{{asset("storage/".$bannerSeasonImage)}}">
                            @else
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="bannerSeasonImageTemp" id=""
                                     src="{{asset("storage/icon.png")}}">
                            @endif
                        </div>
                        @error('bannerSeasonImageTemp')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group file-upload">
                        <label>تصویر مارکر مپ ویلا</label>
                        <input wire:model="markerMapIconTemp" type="file" class="form-control" accept="image/*" name="site_logo">
                        <small>فرمت‌های مجاز: PNG, JPG - اندازه پیشنهادی: 200x200 پیکسل</small>
                        <div class="preview-box">
                            @if($markerMapIconTemp)
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="markerMapIconTemp" id=""
                                     src="{{$markerMapIconTemp->temporaryUrl()}}">
                            @elseif($markerMapIcon!="")
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="iconInput" id=""
                                     src="{{asset("storage/".$markerMapIcon)}}">
                            @else
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="iconInput" id=""
                                     src="{{asset("storage/icon.png")}}">
                            @endif
                        </div>
                        @error('markerMapIconTemp')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group file-upload">
                        <label>تصویر مارکر مپ غذاخوری</label>
                        <input wire:model="markerMapFoodstoreIconTemp" type="file" class="form-control" accept="image/*" name="site_logo">
                        <small>فرمت‌های مجاز: PNG, JPG - اندازه پیشنهادی: 200x200 پیکسل</small>
                        <div class="preview-box">
                            @if($markerMapFoodstoreIconTemp)
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="markerMapFoodstoreIconTemp" id=""
                                     src="{{$markerMapFoodstoreIconTemp->temporaryUrl()}}">
                            @elseif($markerMapFoodstoreIcon!="")
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="iconInput" id=""
                                     src="{{asset("storage/".$markerMapFoodstoreIcon)}}">
                            @else
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="iconInput" id=""
                                     src="{{asset("storage/icon.png")}}">
                            @endif
                        </div>
                        @error('markerMapFoodstoreIconTemp')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group file-upload">
                        <label>تصویر حالت آفلاین </label>
                        <input wire:model="offlineModeIconTemp" type="file" class="form-control" accept="image/*" name="site_logo">
                        <small>فرمت‌های مجاز: PNG, JPG - اندازه پیشنهادی: 200x200 پیکسل</small>
                        <div class="preview-box">
                            @if($offlineModeIconTemp)
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="offlineModeIconTemp" id=""
                                     src="{{$offlineModeIconTemp->temporaryUrl()}}">
                            @elseif($offlineModeIcon!="")
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="iconInput" id=""
                                     src="{{asset("storage/".$offlineModeIcon)}}">
                            @else
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="iconInput" id=""
                                     src="{{asset("storage/icon.png")}}">
                            @endif
                        </div>
                        @error('offlineModeIconTemp')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group file-upload">
                        <label>تصویر صفحه 404 </label>
                        <input wire:model="page404IconTemp" type="file" class="form-control" accept="image/*" name="site_logo">
                        <small>فرمت‌های مجاز: PNG, JPG - اندازه پیشنهادی: 200x200 پیکسل</small>
                        <div class="preview-box">
                            @if($page404IconTemp)
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="page404IconTemp" id=""
                                     src="{{$page404IconTemp->temporaryUrl()}}">
                            @elseif($page404Icon!="")
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="page404IconTemp" id=""
                                     src="{{asset("storage/".$page404Icon)}}">
                            @else
                                <img style="max-width: 135px;margin-top: 0" wire:loading.remove
                                     wire:target="iconInput" id=""
                                     src="{{asset("storage/icon.png")}}">
                            @endif
                        </div>
                        @error('page404IconTemp')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i>
                        ذخیره تغییرات
                    </button>
                </form>
            </div>



            <div wire:ignore.self  class="tab-pane fade" id="nav-contact" role="tabpanel"
                 aria-labelledby="nav-contact-tab">
                <form wire:submit="updateContactUs" class="settings-form">
                    <div class="form-group">
                        <label>نشانی</label>
                        <input wire:model="address" type="text" class="form-control" name="site_title" required>
                        @error('address')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>تلفن اول</label>
                        <input wire:model="phone1" type="text" class="form-control" name="site_title" required>
                        @error('phone1')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>تلفن دوم</label>
                        <input wire:model="phone2" type="text" class="form-control" name="site_title" required>
                        @error('phone2')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>ایمیل سایت</label>
                        <input wire:model="email" type="text" class="form-control" name="site_title" required>
                        @error('email')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i>
                        ذخیره تغییرات
                    </button>
                </form>
            </div>

            <div  wire:ignore.self  class="tab-pane fade" id="nav-banner" role="tabpanel"
                 aria-labelledby="nav-banner-tab">
                <form wire:submit="updateBanners" class="settings-form">
                    <div class="form-group">
                        <label>متن بنر صفحه اصلی</label>
                        <input wire:model="mainBannerText" type="text" class="form-control" name="site_title" required>
                        @error('mainBannerText')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>شماره همراه مدیر برای مطلع شدن از پیام ها</label>
                        <input wire:model="adminMessagesPhone" type="text" class="form-control" name="site_title" required>
                        @error('adminMessagesPhone')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i>
                        ذخیره تغییرات
                    </button>
                </form>
            </div>
            <div wire:ignore.self class="tab-pane fade" id="nav-finance" role="tabpanel"
                 aria-labelledby="nav-finance-tab">
                <form wire:submit="updateFinance" class="settings-form">
                    <div class="form-group">
                        <label>درصد کمیسیون سایت از هر رزرو</label>
                        <input wire:model="commissionReserve" type="text" class="form-control" name="site_title" required>
                        @error('commissionReserve')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i>
                        ذخیره تغییرات
                    </button>
                </form>
            </div>
            <div class="tab-pane fade" id="nav-filters" role="tabpanel"
                 aria-labelledby="nav-filters-tab">
                <form wire:submit="updateFiltersControls" class="settings-form">
                    <div class="form-group">
                        <label>حداکثر تعداد نتایج در هر صفحه</label>
                        <input wire:model="paginationItemCount" type="text" class="form-control" name="site_title" required>
                        @error('paginationItemCount')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i>
                        ذخیره تغییرات
                    </button>
                </form>
            </div>




            <div wire:ignore.self class="tab-pane fade" id="nav-socialmedia" role="tabpanel"
                 aria-labelledby="nav-socialmedia-tab">
                <form wire:submit="updateSocialMedia" class="settings-form">
                <div class="form-group">
                    <label>لینک اینستاگرام</label>
                    <input wire:model="instagramLink" type="text" class="form-control" name="site_title" required>
                    @error('instagramLink')
                    <div class="text-danger text-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>لینک تلگرام</label>
                    <input wire:model="telegramLink" type="text" class="form-control" name="site_title" required>
                    @error('telegramLink')
                    <div class="text-danger text-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>لینک واتساپ</label>
                    <input wire:model="whatsappLink" type="text" class="form-control" name="site_title" required>
                    @error('whatsappLink')
                    <div class="text-danger text-error">{{ $message }}</div>
                    @enderror
                </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i>
                        ذخیره تغییرات
                    </button>
                </form>
            </div>
            <div wire:ignore.self class="tab-pane fade" id="nav-config" role="tabpanel"
                 aria-labelledby="nav-config-tab">
                <form wire:submit="updateConfig" class="settings-form">
                    <div class="form-group">
                        <label>وضعیت سایت</label>
                        <select wire:model="websiteStatus" type="text" class="form-control" name="site_title" required>
                            <option value="1">فعال</option>
                            <option value="0">آفلاین</option>
                        </select>
                        @error('websiteStatus')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>متن حالت آفلاین</label>
                        <input wire:model="OfflineModeText" type="text" class="form-control" name="site_title" required>
                        @error('OfflineModeText')
                        <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i>
                        ذخیره تغییرات
                    </button>
                </form>
            </div>
        </div>
        <div wire:ignore>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
            @script
            <script>
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
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>
    </div>
    @script
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        Livewire.on("edited", event => {
            Toast.fire({
                icon: 'success',
                title: 'اطلاعات با موفقیت بروز شد'
            })
        })
        Livewire.on("create", event => {
            Toast.fire({
                icon: 'success',
                title: 'اطلاعات موفقیت ثبت شد'
            })
        })

        Livewire.on("removed", event => {
            Toast.fire({
                icon: 'success',
                title: 'سطر با موفقیت حذف شد'
            })
        })
    </script>
    @endscript
</div>
