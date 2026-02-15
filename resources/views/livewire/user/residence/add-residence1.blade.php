<div class="">
    @vite(['resources/css/user/add-residence.less'])
    <div class="{{$page!=1?"d-none":""}}">
        <h1 style="font-size: 22px" class="font-weight-bold">
            {{$title==null?"ثبت اقامتگاه جدید":"ویرایش ".$title}}
            <span class='level-span bg-success text-white'>مرحله 1 از 2</span>
        </h1>
        <br>
        <form wire:submit="continue" class="row">
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
            <div class="col-6">
                <p class="m-0">نوع اقامتگاه</p>
                <select wire:model.prevent="residenceType" class="form-control form-control-sm">
                    @foreach(\App\Models\Residence::getResidenceType() as $key=>$type)
                        <option value="{{$key}}">{{$type}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <p class="m-0">نوع منطقه</p>
                <select wire:model.prevent="areaType" class="form-control form-control-sm">
                    @foreach(\App\Models\Residence::getAreaType() as $key=>$type)
                        <option value="{{$key}}">{{$type}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <p class="m-0">تعداد اتاق</p>
                <input wire:model.prevent="roomNumber" type="number" max="5" min="0"
                       placeholder="مثال: 2" class="form-control form-control-sm">
                @error("roomNumber")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">متراژ</p>
                <input wire:model.prevent="area" type="number" min="0" placeholder="مثال: 100"
                       class="form-control form-control-sm">
                @error("area")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">حداکثر تعداد مسافران</p>
                <input wire:model.prevent="peopleNumber" type="number" min="1" placeholder="مثال: 8"
                       class="form-control form-control-sm">
                @error("peopleNumber")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">قیمت برای هرشب (تومان)</p>
                <input wire:model.prevent="amount" oninput="onInputAmount(this)" type="text" id="amount" placeholder="مثال: 500,000"
                       class="form-control form-control-sm">
                @error("amountValidate")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">قیمت آخر هفته برای هرشب (تومان)</p>
                <input wire:model.prevent="lastWeekAmount" oninput="onInputAmount(this)" type="text" id="lastWeekAmount" placeholder="مثال: 500,000"
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
                @error("lastWeekAmountValidate")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-12">
                <p class="m-0">آدرس</p>
                <input wire:model.prevent="address"
                       placeholder="مثال: رحیم آباد جنگل سموش، کمی بالاتر از کوچه اول"
                       class="form-control form-control-sm">
                @error("address")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-12">
                <br>
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"/>

                <p class="" style="font-size: 14px;opacity: .8">
                    مختصات جغرافیایی
                </p>
                @error("latLen")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
                <div wire:ignore id="map"></div>
                <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
                <script>
                    if (typeof L === 'undefined') {
                        console.warn('Leaflet not loaded');
                        document.getElementById('map').innerHTML =
                            '<div class="text-danger" style="padding:12px">نقشه لود نشد. اتصال اینترنت یا فایل‌های Leaflet را بررسی کنید.</div>';
                    } else {
                        var letLng=[{{ $latLen!=""?explode(":", $latLen)[0]:"36.907681"}},{{ $latLen!=""?explode(":", $latLen)[1]:"50.675039"}}];
                        console.log(letLng)
                        var map = L.map('map').setView(letLng, 12);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);
                        var marker;
                        var customIcon = L.icon({
                            iconUrl: '{{ asset("storage/".getConfigs("markerMapIcon")) }}',
                            iconSize: [48, 48],
                            iconAnchor: [16, 32],
                            popupAnchor: [0, -32],
                        });
                            if (marker) {
                                map.removeLayer(marker);
                            }
                            marker = L.marker(letLng, {icon: customIcon}).addTo(map);
                        map.on('click', function (e) {
                            var lat = e.latlng.lat.toFixed(6);
                            var lng = e.latlng.lng.toFixed(6);
                            if (marker) {
                                map.removeLayer(marker);
                            }
                            marker = L.marker([lat, lng], {icon: customIcon}).addTo(map);
                            console.log([lat , lng])
                        @this.set('latLen', lat + ":" + lng)
                            ;
                        });
                    }

                </script>
            </div>
            <div class="col-12">
                <br>
                <br>
                <p class="m-0" style="font-size: 14px;opacity: .8">
                    آپلود تصاویر (حداکثر 8 عکس)
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
                           class="{{sizeof($gallery)>=8?"op-5":""}} custom-upload-btn">آپلود
                        فایل</label>
                    <input {{sizeof($gallery)>=8?"disabled":""}} wire:loading.attr="disabled"
                           wire:target="image" wire:model.live="image"
                           type="file" id="file-upload" accept="image/jpeg,image/png,image/webp,image/gif" class="file-input"/>

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
                                 src="{{asset("storage/residences/".$image)}}">
                            <i class="fa fa-spin fa-spinner" wire:loading
                               wire:target="changeMainImage('{{$image}}')"></i>
                            <i class="fa fa-spin fa-spinner" wire:loading
                               wire:target="delete('{{$image}}')"></i>
                            <i wire:loading.remove wire:target="delete('{{$image}}')"
                               wire:click="delete('{{$image}}')"
                               class="btn btn-sm text-danger fa fa-trash-o"></i>
                        </div>
                    @endforeach
                    @for($i =0;$i<=8-sizeof($gallery)-1;$i++)
                        <div class="empty selected-container">
                            <div class=""></div>
                        </div>

                    @endfor
                </div>
                <br>
                <br>
                <button class="btn btn-success w-100">
                    <i wire:loading wire:target="continue" class="fa fa-spin fa-spinner"></i>
                    ثبت اطلاعات و مرحله بعد
                </button>

            </div>
        </form>
    </div>


    <form wire:submit="save" class="{{$page!=2?"d-none":""}}">
        <h1 style="font-size: 22px" class="font-weight-bold">
            {{$title==null?"ثبت اقامتگاه جدید":"ویرایش ".$title}}
            <span class="level-span bg-success text-white">مرحله 2 از 2</span>
        </h1>
        <br>
        <div class="row">
            <p class="font-weight-bold col-12" style="padding-right: 20px">
                اطلاعات تکمیلی
            </p>
            @foreach(\App\Models\OptionCategory::where("type","residence")->get() as $key=>$category)
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
    <br>
</div>
