@push('head')
    <style>
        .banner-manager {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .banner-manager-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .banner-manager-head p {
            margin: 8px 0 0;
            color: var(--admin-muted);
            font-size: 0.86rem;
            line-height: 1.9;
        }

        .banner-manager-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(300px, 0.85fr);
            gap: 18px;
            align-items: start;
        }

        .banner-settings-card,
        .banner-previews-card {
            background: #fff;
            border: 1px solid var(--admin-border);
            border-radius: 18px;
            padding: 18px;
        }

        .banner-card-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 14px;
            font-size: 0.96rem;
            font-weight: 700;
            color: var(--admin-text);
        }

        .banner-card-title i {
            color: var(--admin-primary);
        }

        .banner-upload-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .banner-upload-field {
            padding: 14px;
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            background: #f8fafc;
        }

        .banner-upload-field small,
        .banner-form-note {
            display: block;
            margin-top: 8px;
            color: var(--admin-muted);
            font-size: 0.76rem;
            line-height: 1.8;
        }

        .banner-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .banner-form-grid .form-group--full {
            grid-column: 1 / -1;
        }

        .banner-save-row {
            display: flex;
            justify-content: flex-start;
            margin-top: 8px;
        }

        .banner-save-row .btn {
            min-width: 160px;
        }

        .banner-preview-stack {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .banner-preview-item {
            border: 1px solid var(--admin-border);
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
        }

        .banner-preview-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 14px 0;
        }

        .banner-preview-head h4 {
            margin: 0;
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--admin-text);
        }

        .banner-preview-meta {
            padding: 10px 14px 14px;
            color: var(--admin-muted);
            font-size: 0.78rem;
            line-height: 1.9;
        }

        .banner-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .banner-status-pill.is-active {
            background: #dcfce7;
            color: #166534;
        }

        .banner-status-pill.is-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .banner-preview-frame {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 176px;
            margin: 0 14px 14px;
            border-radius: 16px;
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.18), transparent 35%),
                linear-gradient(135deg, #eff6ff 0%, #f8fafc 48%, #eef2ff 100%);
            border: 1px dashed #bfdbfe;
        }

        .banner-preview-frame img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .banner-preview-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #64748b;
            text-align: center;
            padding: 24px;
        }

        .banner-preview-empty i {
            font-size: 1.45rem;
            color: #475569;
        }

        .banner-preview-empty span {
            font-size: 0.8rem;
            line-height: 1.9;
        }

        .banner-preview-link {
            color: var(--admin-primary);
            word-break: break-all;
        }

        @media (max-width: 1080px) {
            .banner-manager-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .banner-upload-grid,
            .banner-form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<div class="section banner-manager">
    <div class="banner-manager-head">
        <div>
            <h2>
                <i class="fa fa-picture-o"></i>
                مدیریت بنرها
            </h2>
            <p>
                بنرهای اصلی صفحه را از این بخش بارگذاری و مدیریت کنید. پیش‌نمایش سمت چپ وضعیت فعلی
                بنر اصلی و بنر تخفیف را نشان می‌دهد.
            </p>
        </div>
    </div>

    <div class="banner-manager-grid">
        <form wire:submit="save" class="banner-settings-card">
            <h3 class="banner-card-title">
                <i class="fa fa-sliders"></i>
                تنظیمات بنر
            </h3>

            <div class="banner-upload-grid">
                <div class="banner-upload-field">
                    <label>بنر اصلی</label>
                    <input wire:model="mainBannerImageTemp" type="file" class="form-control" accept="image/*">
                    <small>برای هدر اصلی سایت. فرمت پیشنهادی: PNG یا JPG با عرض زیاد.</small>
                    @error('mainBannerImageTemp')
                        <div class="text-danger text-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="banner-upload-field">
                    <label>بنر تخفیف</label>
                    <input wire:model="discountBannerImageTemp" type="file" class="form-control" accept="image/*">
                    <small>برای نمایش پیشنهاد ویژه یا کمپین‌های کوتاه‌مدت.</small>
                    @error('discountBannerImageTemp')
                        <div class="text-danger text-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="banner-form-grid">
                <div class="form-group">
                    <label>موقعیت بنر</label>
                    <select wire:model="bannerPosition" class="form-control">
                        <option value="home">صفحه اصلی</option>
                        <option value="listing">صفحه لیست</option>
                        <option value="inner">صفحه داخلی</option>
                    </select>
                    @error('bannerPosition')
                        <div class="text-danger text-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>وضعیت</label>
                    <select wire:model="bannerStatus" class="form-control">
                        <option value="1">فعال</option>
                        <option value="0">غیرفعال</option>
                    </select>
                    @error('bannerStatus')
                        <div class="text-danger text-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group form-group--full">
                    <label>لینک بنر</label>
                    <input wire:model="bannerLink" type="text" class="form-control" placeholder="https://example.com/offer">
                    <span class="banner-form-note">اگر لینک خالی بماند، بنر فقط به‌صورت نمایشی نمایش داده می‌شود.</span>
                    @error('bannerLink')
                        <div class="text-danger text-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="banner-save-row">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i>
                    ذخیره تغییرات
                </button>
            </div>
        </form>

        <div class="banner-previews-card">
            <h3 class="banner-card-title">
                <i class="fa fa-image"></i>
                مدیریت بنرها
            </h3>

            <div class="banner-preview-stack">
                <article class="banner-preview-item">
                    <div class="banner-preview-head">
                        <h4>بنر اصلی</h4>
                        <span class="banner-status-pill {{ $bannerStatus === '1' ? 'is-active' : 'is-inactive' }}">
                            <i class="fa fa-circle"></i>
                            {{ $bannerStatus === '1' ? 'فعال' : 'غیرفعال' }}
                        </span>
                    </div>

                    <div class="banner-preview-frame">
                        @if($mainBannerImageTemp)
                            <img src="{{ $mainBannerImageTemp->temporaryUrl() }}" alt="پیش‌نمایش بنر اصلی">
                        @elseif($mainBannerImage)
                            <img src="{{ asset('storage/' . $mainBannerImage) }}" alt="بنر اصلی">
                        @else
                            <div class="banner-preview-empty">
                                <i class="fa fa-picture-o"></i>
                                <span>هنوز تصویری برای بنر اصلی انتخاب نشده است.</span>
                            </div>
                        @endif
                    </div>

                    <div class="banner-preview-meta">
                        موقعیت: {{ $bannerPosition === 'home' ? 'صفحه اصلی' : ($bannerPosition === 'listing' ? 'صفحه لیست' : 'صفحه داخلی') }}
                        <br>
                        لینک:
                        @if($bannerLink)
                            <span class="banner-preview-link">{{ $bannerLink }}</span>
                        @else
                            بدون لینک
                        @endif
                    </div>
                </article>

                <article class="banner-preview-item">
                    <div class="banner-preview-head">
                        <h4>بنر تخفیف</h4>
                        <span class="banner-status-pill {{ $bannerStatus === '1' ? 'is-active' : 'is-inactive' }}">
                            <i class="fa fa-circle"></i>
                            {{ $bannerStatus === '1' ? 'فعال' : 'غیرفعال' }}
                        </span>
                    </div>

                    <div class="banner-preview-frame">
                        @if($discountBannerImageTemp)
                            <img src="{{ $discountBannerImageTemp->temporaryUrl() }}" alt="پیش‌نمایش بنر تخفیف">
                        @elseif($discountBannerImage)
                            <img src="{{ asset('storage/' . $discountBannerImage) }}" alt="بنر تخفیف">
                        @else
                            <div class="banner-preview-empty">
                                <i class="fa fa-tag"></i>
                                <span>بنر تخفیف برای کمپین‌های موقت یا اطلاع‌رسانی ویژه اینجا نمایش داده می‌شود.</span>
                            </div>
                        @endif
                    </div>

                    <div class="banner-preview-meta">
                        وضعیت نمایشی این کارت با تنظیمات کلی فرم سمت راست کنترل می‌شود.
                    </div>
                </article>
            </div>
        </div>
    </div>

    @script
    <script>
        Livewire.on("edited", () => {
            Toast.fire({
                icon: 'success',
                title: 'بنرها با موفقیت بروزرسانی شدند'
            })
        })
    </script>
    @endscript
</div>
