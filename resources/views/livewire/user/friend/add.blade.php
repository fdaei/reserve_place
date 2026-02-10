<div class="">
    @vite(['resources/css/user/add-residence.less'])
    <div class="{{$page!=1?"d-none":""}}">
        <h1 style="font-size: 22px" class="font-weight-bold">
            {{$title==null?"ثبت همسفر":"ویرایش ".$title}}
        </h1>
        <br>
        <form wire:submit="continue" class="row">
            <p class="font-weight-bold col-12" style="padding-right: 20px">
                اطلاعات پایه
            </p>
            <div class="col-6">
                <p class="m-0">کشور مقصد</p>
                <select wire:model.live="country" class="form-control form-control-sm">
                    @foreach(\App\Models\Country::all() as $p)
                        <option
                            value="{{$p->id}}" {{$p->id==$country?"selected":""}}>{{$p->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <p class="m-0">
                    <i class="fa fa-spin fa-spinner" wire:loading wire:target="province"></i>
                    شهر
                </p>
                <select wire:model.prevent="province" class="form-control form-control-sm">
                    @foreach(\App\Models\Province::where("country_id",$country)->get() as $c)
                        <option
                            value="{{$c->id}}" {{$c->id==$province?"selected":""}}>{{$c->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 position-relative">
                <p class="m-0">مدت سفر</p>
                <input type="number" wire:model.prevent="travelDuration" class="form-control form-control-sm">
                <span class="op-5" style="position: absolute;top:26px;left: 28px;">روز</span>
                @error("travelDuration")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-6 position-relative">
                <p class="m-0"> تاریخ سفر</p>
                    <div wire:ignore>

                        <input id="date-holding" wire:model.prevent="startDate"  min="0" placeholder="مثال: 1404/02/03"
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
                    @error("startDateValidation")
                    <span class="text-danger">
                        {{$message}}
                    </span>
                    @enderror
            </div>
            <div class="col-6">
                <p class="m-0">نوع سفر</p>
                <select wire:model.prevent="travelType" class="form-control form-control-sm">
                    @foreach(\App\Models\Friend::getTravelType() as $key=>$type)
                        <option value="{{$key}}">{{$type}}</option>
                    @endforeach
                </select>
                @error("travelType")
                <span class="text-danger">
                {{$message}}
                </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">وضعیت من</p>
                <select wire:model.prevent="myGender" class="form-control form-control-sm">
                    @foreach(\App\Models\Friend::getGrnders() as $key=>$type)
                        <option value="{{$key}}">{{$type}}</option>
                    @endforeach
                </select>
                @error("myGender")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">به دنبال همسفر</p>
                <select wire:model.prevent="friendGender" class="form-control form-control-sm">
                    @foreach(\App\Models\Friend::getGrnders() as $key=>$type)
                        <option value="{{$key}}">{{$type}}</option>
                    @endforeach
                    <option value="4">فرقی ندارد</option>
                </select>
                @error("friendGender")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">بازه سنی من</p>
                <select wire:model.prevent="myAge" class="form-control form-control-sm">
                    <option value="-25">زیر 25 سال</option>
                    <option value="25-35">25 تا 35 سال</option>
                    <option value="35-45">35 تا 45 سال</option>
                    <option value="+45">بالای 45 سال</option>
                </select>
                @error("myAge")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>

            <div class="col-6">
                <p class="m-0">نوع مسیر سفر</p>
                <select wire:model.prevent="machineType" class="form-control form-control-sm">
                    @foreach(\App\Models\Friend::getMachineType() as $key=>$type)
                        <option value="{{$key}}">{{$type}}</option>
                    @endforeach
                </select>
                @error("machineType")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">سبک سفر</p>
                <select wire:model.prevent="travelVersion" class="form-control form-control-sm">
                    <option value="1">اقتصادی</option>
                    <option value="2">معمولی</option>
                    <option value="3">لاکچری</option>
                </select>
                @error("travelVersion")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-12">
                <br>
                <br>
                <p class="m-0" style="font-size: 14px;opacity: .8">
                    آپلود تصویر شخصی
                    <i class="fa fa-spin fa-spinner" wire:loading wire:target="image"></i>
                </p>
                <div class="gallery">
                        <div style="width: 140px;height: 140px"
                             class="selected-container text-center">
                            تصویر نمونه
                            <img style="width: 130px;height: 130px" src="{{asset("storage/static/profile-example.png")}}">
                        </div>
                </div>
                <div
                    x-data="{ uploading: false, progress: 0 }"
                    x-on:livewire-upload-start="uploading = true"
                    x-on:livewire-upload-finish="uploading = false"
                    x-on:livewire-upload-cancel="uploading = false"
                    x-on:livewire-upload-error="uploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                >
                    <label for="file-upload"
                           class="{{sizeof($gallery)>=1?"op-5":""}} custom-upload-btn">آپلود
                        فایل</label>
                    <input {{sizeof($gallery)>=1?"disabled":""}} wire:loading.attr="image"
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
{{--                            <span class="main-image-text">--}}
{{--                                    تصویر اصلی--}}
{{--                            </span>--}}
                            <img wire:click="changeMainImage('{{$image}}')"
                                 src="{{asset("storage/friends/".$image)}}">
                            <i class="fa fa-spin fa-spinner" wire:loading
                               wire:target="changeMainImage('{{$image}}')"></i>
                            <i class="fa fa-spin fa-spinner" wire:loading
                               wire:target="delete('{{$image}}')"></i>
                            <i wire:loading.remove wire:target="delete('{{$image}}')"
                               wire:click="delete('{{$image}}')"
                               class="btn btn-sm text-danger fa fa-trash-o"></i>
                        </div>
                    @endforeach
                    @for($i =0;$i<=1-sizeof($gallery)-1;$i++)
                        <div class="empty selected-container">
                            <div class=""></div>
                        </div>

                    @endfor
                </div>
                <br>
                <br>
                <button class="btn btn-success w-100">
                    <i wire:loading wire:target="save" class="fa fa-spin fa-spinner"></i>
                    ثبت و ادامه
                </button>
            </div>
        </form>
    </div>

    <form wire:submit="save" class="{{$page!=2?"d-none":""}}">
        <h1 style="font-size: 22px" class="font-weight-bold">
            {{$title==null?"ثبت همسفر جدید":"ویرایش ".$title}}
            <span class="level-span bg-success text-white">مرحله 2 از 2</span>
        </h1>
        <br>
        <div class="row">
            <p class="font-weight-bold col-12" style="padding-right: 20px">
                اطلاعات تکمیلی
            </p>
            @foreach(\App\Models\OptionCategory::where("type","friend")->get() as $key=>$category)
                <p class=" col-12" style="padding-right: 23px">
                    {{($key+1)."-".$category->title}}
                </p>
                <ul class="col-12 options">
                    @foreach($category->options as $option)
                        <li>
                            <div class="custom-control custom-switch">
                                <input wire:model.prevent="options" value="{{$option->id}}"
                                       type="checkbox" class="custom-control-input"
                                       id="customSwitch-{{$option->id}}">
                                <label class="custom-control-label"
                                       for="customSwitch-{{$option->id}}">{{$option->title}}</label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endforeach
            <br>
            <br>
            <br>
            <button class="btn btn-success w-100">
                {{$title==null?"ثبت و ارسال":"ویرایش اطلاعات "}}
            </button>
        </div>
    </form>
    <br>


</div>
