<div class="">
    @vite(['resources/css/user/add-residence.less'])
    <div class="{{$page!=1?"d-none":""}}">
        <h1 style="font-size: 22px" class="font-weight-bold">
            {{$title==null?"ثبت تور مسافرتی":"ویرایش ".$title}}
        </h1>
        <br>
        <form wire:submit="save" class="row">
            <p class="font-weight-bold col-12" style="padding-right: 20px">
                اطلاعات پایه
            </p>
            <div class="col-6">
                <p class="m-0">استان</p>
                <select wire:model.live="province" class="form-control form-control-sm">
                    @foreach(\App\Models\Province::where("country_id",1)->get() as $p)
                        <option
                            value="{{$p->id}}" {{$p->id==$province?"selected":""}}>{{$p->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <p class="m-0">
                    <i class="fa fa-spin fa-spinner" wire:loading wire:target="province"></i>
                    شهر
                </p>
                <select wire:model.prevent="city" class="form-control form-control-sm">
                    @foreach(\App\Models\City::where("province_id",$province)->get() as $c)
                        <option value="{{$c->id}}">{{$c->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="mt-3 col-12">
                <p class="m-0">نقطه شروع</p>
                <input wire:model.prevent="address"
                       placeholder="مثال: تهران، میدان آزادی، پمپ بنزین"
                       class="form-control form-control-sm">
                @error("address")
                <span class="text-danger">
                {{$message}}
                </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">نوع تور</p>
                <select wire:model.prevent="tourType" class="form-control form-control-sm">
                    @foreach(\App\Models\Tour::getTourType() as $key=>$type)
                        <option value="{{$key}}">{{$type}}</option>
                    @endforeach
                </select>
                @error("tourType")
                <span class="text-danger">
                {{$message}}
                </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">محل اقامت</p>
                <select wire:model.prevent="residenceType" class="form-control form-control-sm">
                    @foreach(\App\Models\Tour::getResidenceType() as $key=>$type)
                        <option value="{{$key}}">{{$type}}</option>
                    @endforeach
                </select>
                @error("residenceType")
                <span class="text-danger">
                {{$message}}
                </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">حداقل ظرفیت برای اجرا</p>
                <input wire:model.prevent="minPeople" type="number" max="5" min="0"
                       placeholder="مثال: 2" class="form-control form-control-sm">
                @error("minPeople")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">حداکثر ظرفیت</p>
                <input wire:model.prevent="maxPeople" type="number" max="5" min="0"
                       placeholder="مثال: 2" class="form-control form-control-sm">
                @error("maxPeople")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">بازه زمانی تور</p>
                <select wire:model.live="tourTimeFrame" class="form-control form-control-sm">
                    <option value="one">فقط یکبار</option>
                    <option value="weekly">هفتگی</option>
                    <option value="monthly">ماهانه</option>
                </select>
                @error("tourTimeFrame")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-6 position-relative">
                <p class="m-0"> تاریخ برگزاری تور</p>
            @if($tourTimeFrame=="one")
                <div wire:ignore>

                    <input id="date-holding" wire:model.prevent="openTourTime"  min="0" placeholder="مثال: 1404/02/03"
                           class="form-control form-control-sm">
                    @script
                    <script>
                        const input = document.getElementById('date-holding');

                        input.addEventListener('focus', function() {
                            if (this.value === "----/--/--") {
                                this.value = "";
                            }
                        });

                        input.addEventListener('blur', function() {
                            // اگر کاربر هیچ چیزی وارد نکرد، تاریخ را به placeholder بازمی‌گرداند
                            if (this.value === "") {
                                this.value = "";
                            }
                        });

                        input.addEventListener('input', function() {
                            // حذف هر چیزی که غیر از اعداد باشد
                            let value = this.value.replace(/\D/g, '');
                            let formattedValue = "----/--/--"; // فرمت پیش‌فرض

                            // پر کردن تاریخ به فرمت YYYY/MM/DD
                            if (value.length > 0) {
                                formattedValue = value.slice(0, 4);
                            }
                            if (value.length > 4) {
                                formattedValue += '/' + value.slice(4, 6);
                            }
                            if (value.length > 6) {
                                formattedValue += '/' + value.slice(6, 8);
                            }
                            if(value.length==0)formattedValue="";

                            this.value = formattedValue;
                        });
                    </script>
                    @endscript
                </div>
                @error("openTourTimeValidate")
                    <span class="text-danger">
                        {{$message}}
                    </span>
                @enderror
                @elseif($tourTimeFrame=="weekly")
                    <select wire:model.prevent="openTourTime" class="form-control form-control-sm">
                        <option value="saturday">هر شنبه</option>
                        <option value="sunday">هر یکشنبه</option>
                        <option value="monday">هر دوشنبه</option>
                        <option value="tuesday">هر سه شنبه</option>
                        <option value="wednesday">هر چهارشنبه</option>
                        <option value="thursday">هر پنجشنبه</option>
                        <option value="friday" selected>هر جمعه</option>
                    </select>
                @elseif($tourTimeFrame=="monthly")
                    <input type="number" max="31" wire:model.prevent="openTourTime" class="form-control form-control-sm">
                    <span class="op-5" style="position: absolute;top:28px;left: 40px;">هر ماه</span>
                @endif
                @error("openTourTime")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-6 position-relative">
                <p class="m-0">مدت تور</p>
                <input type="number" wire:model.prevent="tourDuration" class="form-control form-control-sm">
                <span class="op-5" style="position: absolute;top:26px;left: 28px;">روز</span>
                @error("tourDuration")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">قیمت برای هر نفر (تومان)</p>
                <input wire:model.prevent="amount" oninput="onInputAmount(this)" type="text" id="amount" placeholder="مثال: 500,000"
                       class="form-control form-control-sm">
                <script>
                    function formatNumber(num){
                        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")
                    }
                    function onInputAmount(tag){
                        let value=tag.value.replace(/,/g,'')
                        if(!value)return tag.value="";
                        tag.value=formatNumber(value)
                    }
                </script>
                @error("amountValidation")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-12">
                <p class="m-0">توضیحات برنامه سفر</p>
                <textarea wire:model.prevent="description" style="min-height: 160px"
                       placeholder="جزییات برنامه، وعده ها، خدمات و... "
                          class="form-control form-control-sm"></textarea>
                @error("description")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-12">
                <br>
                <br>
                <p class="m-0" style="font-size: 14px;opacity: .8">
                    آپلود تصاویر (حداکثر 3 عکس)
                    <i class="fa fa-spin fa-spinner" wire:loading wire:target="image"></i>
                </p>
                <div
                    x-data="{ uploading: false, progress: 0 }"
                    x-on:livewire-upload-start="uploading = true"
                    x-on:livewire-upload-finish="uploading = false"
                    x-on:livewire-upload-cancel="uploading = false"
                    x-on:livewire-upload-error="uploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                >
                    <label for="file-upload"
                           class="{{sizeof($gallery)>=3?"op-5":""}} custom-upload-btn">آپلود
                        فایل</label>
                    <input {{sizeof($gallery)>=3?"disabled":""}} wire:loading.attr="image"
                           wire:target="image" wire:model.live="image"
                           type="file" id="file-upload" accept="image/jpeg" class="file-input"/>

                    <div style="width: 100%" x-show="uploading">
                        <progress style="width: 100%" max="100" x-bind:value="progress"></progress>
                    </div>
                </div>
                @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                <div class="gallery">
                    @foreach($gallery as $image)
                        <div wire:key="{{ $image }}"
                             class="selected-container {{$image==$mainImage?"active":""}}">
                            <span class="main-image-text">
                                    تصویر اصلی
                            </span>
                            <img wire:click="changeMainImage('{{$image}}')"
                                 src="{{asset("storage/tours/".$image)}}">
                            <i class="fa fa-spin fa-spinner" wire:loading
                               wire:target="changeMainImage('{{$image}}')"></i>
                            <i class="fa fa-spin fa-spinner" wire:loading
                               wire:target="delete('{{$image}}')"></i>
                            <i wire:loading.remove wire:target="delete('{{$image}}')"
                               wire:click="delete('{{$image}}')"
                               class="btn btn-sm text-danger fa fa-trash-o"></i>
                        </div>
                    @endforeach
                    @for($i =0;$i<=3-sizeof($gallery)-1;$i++)
                        <div class="empty selected-container">
                            <div class=""></div>
                        </div>

                    @endfor
                </div>
                <br>
                <br>
                <button class="btn btn-success w-100">
                    <i wire:loading wire:target="save" class="fa fa-spin fa-spinner"></i>
                    ثبت رایگان تور
                </button>
            </div>
        </form>
    </div>
                <br>
                <br>


</div>
