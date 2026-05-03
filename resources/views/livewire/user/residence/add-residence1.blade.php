<div>
    <style>
        :root {
            --primary: #66ccff;
            --secondary: #0A2B4E;
            --accent: #F59E0B;
            --gray-bg: #F8FAFC;
            --gray-text: #475569;
            --border: #E2E8F0;
            --success: #10B981;
            --danger: #EF4444;
        }
        
        .form-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px 0 40px;
        }
        
        .step-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }
        
        .step-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--secondary);
            margin: 0;
        }
        
        .step-badge {
            background: var(--success);
            color: white;
            padding: 4px 16px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .form-card {
            background: white;
            border-radius: 24px;
            border: 1px solid var(--border);
            padding: 28px;
            margin-bottom: 24px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--primary);
            display: inline-block;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 0;
        }
        
        .form-group.full-width {
            grid-column: span 2;
        }
        
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 6px;
        }
        
        .form-label i {
            color: var(--primary);
            margin-left: 4px;
        }
        
        .form-control-custom {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
            background: white;
        }
        
        .form-control-custom:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102,204,255,0.1);
        }
        
        select.form-control-custom {
            cursor: pointer;
        }
        
        .error-text {
            color: var(--danger);
            font-size: 11px;
            margin-top: 4px;
            display: block;
        }
        
        /* نقشه */
        .map-container {
            margin-top: 8px;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border);
        }
        
        #map {
            height: 300px;
            width: 100%;
            background: var(--gray-bg);
        }
        
        /* گالری */
        .upload-area {
            margin-top: 8px;
        }
        
        .upload-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--gray-bg);
            border: 1px solid var(--border);
            padding: 10px 20px;
            border-radius: 40px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .upload-btn:hover {
            background: var(--border);
        }
        
        .upload-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .file-input {
            display: none;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 12px;
            margin-top: 16px;
        }
        
        .gallery-item {
            position: relative;
            aspect-ratio: 1 / 1;
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid var(--border);
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .gallery-item.active {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(102,204,255,0.3);
        }
        
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .main-badge {
            position: absolute;
            top: 6px;
            right: 6px;
            background: var(--primary);
            color: white;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 30px;
        }
        
        .delete-btn {
            position: absolute;
            bottom: 6px;
            left: 6px;
            background: rgba(0,0,0,0.6);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .delete-btn:hover {
            background: var(--danger);
        }
        
        .empty-item {
            background: var(--gray-bg);
            border: 1px dashed var(--border);
            border-radius: 16px;
            aspect-ratio: 1 / 1;
        }
        
        /* امکانات */
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }
        
        .option-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .option-item input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary);
        }
        
        .option-item label {
            font-size: 13px;
            color: var(--gray-text);
            cursor: pointer;
        }
        
        .btn-submit {
            width: 100%;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 40px;
            padding: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 16px;
        }
        
        .btn-submit:hover {
            background: #D97706;
            transform: translateY(-2px);
        }
        
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .progress-bar {
            width: 100%;
            height: 4px;
            background: var(--border);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 8px;
        }
        
        .progress-fill {
            height: 100%;
            background: var(--primary);
            transition: width 0.3s;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
            
            .form-card {
                padding: 20px;
            }
            
            .options-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <div class="form-container">
        
        {{-- مرحله 1 --}}
        <div class="{{ $page != 1 ? 'd-none' : '' }}">
            <div class="step-header">
                <h1 class="step-title">
                    {{ $title == null ? '➕ ثبت اقامتگاه جدید' : '✏️ ویرایش ' . $title }}
                </h1>
                <span class="step-badge">مرحله 1 از 2</span>
            </div>

            <form wire:submit="continue">
                <div class="form-card">
                    <h2 class="section-title"><i class="fa fa-info-circle"></i> اطلاعات پایه</h2>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label"><i class="fa fa-map-marker"></i> استان</label>
                            <select wire:model.live="province" class="form-control-custom">
                                <option value="">انتخاب استان</option>
                                @foreach(\App\Models\Province::where("country_id",1)->get() as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label"><i class="fa fa-city"></i> شهر</label>
                            <select wire:model.prevent="city" class="form-control-custom">
                                <option value="">انتخاب شهر</option>
                                @foreach(\App\Models\City::where("province_id", $province)->get() as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label"><i class="fa fa-home"></i> نوع اقامتگاه</label>
                            <select wire:model.prevent="residenceType" class="form-control-custom">
                                @foreach(\App\Models\Residence::getResidenceType() as $key => $type)
                                    <option value="{{ $key }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label"><i class="fa fa-tree"></i> نوع منطقه</label>
                            <select wire:model.prevent="areaType" class="form-control-custom">
                                @foreach(\App\Models\Residence::getAreaType() as $key => $type)
                                    <option value="{{ $key }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label"><i class="fa fa-bed"></i> تعداد اتاق</label>
                            <input type="number" wire:model.prevent="roomNumber" min="0" max="5" placeholder="مثال: 2" class="form-control-custom">
                            @error('roomNumber') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label"><i class="fa fa-expand"></i> متراژ (متر مربع)</label>
                            <input type="number" wire:model.prevent="area" min="0" placeholder="مثال: 100" class="form-control-custom">
                            @error('area') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label"><i class="fa fa-users"></i> حداکثر تعداد مسافران</label>
                            <input type="number" wire:model.prevent="peopleNumber" min="1" placeholder="مثال: 8" class="form-control-custom">
                            @error('peopleNumber') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label"><i class="fa fa-money"></i> قیمت هر شب (تومان)</label>
                            <input type="text" wire:model.prevent="amount" oninput="formatPrice(this)" placeholder="مثال: 500,000" class="form-control-custom">
                            @error('amountValidate') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label"><i class="fa fa-calendar"></i> قیمت آخر هفته (تومان)</label>
                            <input type="text" wire:model.prevent="lastWeekAmount" oninput="formatPrice(this)" placeholder="مثال: 500,000" class="form-control-custom">
                            @error('lastWeekAmountValidate') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="form-group full-width">
                            <label class="form-label"><i class="fa fa-location-arrow"></i> آدرس کامل</label>
                            <input type="text" wire:model.prevent="address" placeholder="مثال: رحیم آباد، جنگل سموش، کمی بالاتر از کوچه اول" class="form-control-custom">
                            @error('address') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- نقشه --}}
                <div class="form-card">
                    <h2 class="section-title"><i class="fa fa-map-pin"></i> موقعیت مکانی</h2>
                    <p style="font-size: 12px; color: var(--gray-text); margin-bottom: 12px;">
                        <i class="fa fa-info-circle"></i> روی نقشه کلیک کنید تا موقعیت دقیق اقامتگاه مشخص شود
                    </p>
                    <div class="map-container" wire:ignore>
    <div id="map"></div>
</div>
                    @error('latLen') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                {{-- گالری --}}
                <div class="form-card">
                    <h2 class="section-title"><i class="fa fa-image"></i> تصاویر اقامتگاه</h2>
                    <p style="font-size: 12px; color: var(--gray-text); margin-bottom: 12px;">
                        <i class="fa fa-info-circle"></i> حداکثر 8 عکس | اولین عکس به عنوان تصویر اصلی نمایش داده می‌شود
                    </p>
                    
                    <div class="upload-area">
                        <label class="upload-btn {{ sizeof($gallery) >= 8 ? 'disabled' : '' }}">
                            <i class="fa fa-upload"></i> آپلود تصویر
                            <input type="file" wire:model.live="image" accept="image/jpeg,image/png,image/webp" class="file-input" {{ sizeof($gallery) >= 8 ? 'disabled' : '' }}>
                        </label>
                        
                        <div wire:loading wire:target="image" class="progress-bar">
                            <div class="progress-fill" style="width: 50%"></div>
                        </div>
                        @error('image') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="gallery-grid">
                        @foreach($gallery as $image)
                      <div class="gallery-item {{ $image == $mainImage ? 'active' : '' }}" wire:key="{{ $image }}">
    <img src="{{ asset('storage/residences/' . $image) }}" alt="تصویر" style="cursor: pointer;" wire:click="changeMainImage('{{ $image }}')">
    @if($image == $mainImage)
        <div class="main-badge">اصلی</div>
    @endif
    <div class="delete-btn" wire:click="delete('{{ $image }}')" onclick="event.stopPropagation()" wire:confirm="آیا از حذف این تصویر اطمینان دارید؟">
        <i class="fa fa-trash"></i>
    </div>
</div>
                        @endforeach
                        @for($i = 0; $i < 8 - sizeof($gallery); $i++)
                            <div class="empty-item"></div>
                        @endfor
                    </div>
                </div>

                <button type="submit" class="btn-submit" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="fa fa-arrow-left"></i> ادامه و مرحله بعد</span>
                    <span wire:loading><i class="fa fa-spinner fa-spin"></i> در حال ذخیره...</span>
                </button>
            </form>
        </div>

        {{-- مرحله 2 --}}
        <form wire:submit="save" class="{{ $page != 2 ? 'd-none' : '' }}">
            <div class="step-header">
                <h1 class="step-title">
                    {{ $title == null ? '➕ ثبت اقامتگاه جدید' : '✏️ ویرایش ' . $title }}
                </h1>
                <span class="step-badge">مرحله 2 از 2</span>
            </div>

            <div class="form-card">
                <h2 class="section-title"><i class="fa fa-check-circle"></i> امکانات اقامتگاه</h2>
                <p style="font-size: 12px; color: var(--gray-text); margin-bottom: 20px;">
                    امکاناتی که اقامتگاه شما دارد را انتخاب کنید
                </p>
                
                @foreach(\App\Models\OptionCategory::where("type","residence")->get() as $category)
                    <div style="margin-bottom: 24px;">
                        <h3 style="font-size: 15px; font-weight: 700; color: var(--secondary); margin-bottom: 12px;">
                            {{ $category->title }}
                        </h3>
                        <div class="options-grid">
                            @foreach($category->options as $option)
                                <label class="option-item">
                                    <input type="checkbox" wire:model.prevent="options" value="{{ $option->id }}">
                                    <span>{{ $option->title }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="display: flex; gap: 12px; margin-top: 16px;">
    <button type="button" class="btn-submit" wire:click="backToStep1" style="background: #64748B; margin: 0;">
        <i class="fa fa-arrow-right"></i> بازگشت به مرحله قبل
    </button>
    
    <button type="submit" class="btn-submit" wire:loading.attr="disabled" style="margin: 0;">
        <span wire:loading.remove><i class="fa fa-save"></i> {{ $title == null ? 'ثبت اقامتگاه' : 'ویرایش اطلاعات' }}</span>
        <span wire:loading><i class="fa fa-spinner fa-spin"></i> در حال ثبت...</span>
    </button>
</div>
        </form>
    </div>

<script>
    function formatPrice(input) {
        let value = input.value.replace(/,/g, '');
        if (!value) return input.value = '';
        input.value = new Intl.NumberFormat().format(value);
    }
</script>

@push('scripts')
<script>
    document.addEventListener('livewire:init', function() {
        setTimeout(function() {
            if (typeof L !== 'undefined') {
                var latLng = [{{ $latLen != "" ? explode(":", $latLen)[0] : "36.907681" }}, {{ $latLen != "" ? explode(":", $latLen)[1] : "50.675039" }}];
                var map = L.map('map').setView(latLng, 12);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                
                var customIcon = L.icon({
                    iconUrl: '{{ asset("storage/".getConfigs("markerMapIcon")) }}',
                    iconSize: [40, 40],
                    iconAnchor: [20, 40]
                });
                
                var marker = L.marker(latLng, { icon: customIcon }).addTo(map);
                
                map.on('click', function(e) {
                    var lat = e.latlng.lat.toFixed(6);
                    var lng = e.latlng.lng.toFixed(6);
                    marker.setLatLng([lat, lng]);
                    @this.set('latLen', lat + ":" + lng);
                });
            }
        }, 500);
    });
</script>
@endpush
</div>