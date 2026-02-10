<div class="">
    @vite(['resources/css/user/add-residence.less'])
    <div class="{{$page!=1?"d-none":""}}">
        <h1 style="font-size: 22px" class="font-weight-bold">
            {{$title==null?"ثبت رستوران":"ویرایش ".$title}}
            <span class='level-span bg-success text-white'>مرحله 1 از 2</span>
        </h1>
        <br>
        <form wire:submit="continue" class="row">
            <p class="font-weight-bold col-12" style="padding-right: 20px">
                اطلاعات پایه
            </p>
            <div class="col-12">
                <p class="m-0">عنوان رستوران</p>
                <input wire:model.prevent="title" type="text" id="title" placeholder=""
                       class="form-control form-control-sm">
                @error("title")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
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
            <div class="col-6">
                <p class="m-0">مدل رستوران</p>
                <select wire:model.prevent="storeType" class="form-control form-control-sm">
                    @foreach(\App\Models\FoodStore::getStoreType() as $key=>$type)
                        <option value="{{$key}}">{{$type}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <p class="m-0">نوع غذا</p>
                <select wire:model.prevent="foodType" class="form-control form-control-sm">
                    @foreach(\App\Models\FoodStore::getFoodType() as $key=>$type)
                        <option value="{{$key}}">{{$type}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <p class="m-0">ساعت باز کردن</p>
                <input
                    min="00:00"
                    max="23:59"
                    step="60"
                    wire:model.prevent="openTime" type="time"
                       placeholder="مثال: 08:00" class="form-control form-control-sm">
                @error("openTime")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-6">
                <p class="m-0">ساعت تعطیلی</p>
                <input wire:model.prevent="closeTime"   type="time"
                       min="00:00"
                       max="23:59"
                       step="60"
                       placeholder="مثال: 23:00" class="form-control form-control-sm">
                @error("closeTime")
                <span class="text-danger">
                {{$message}}
            </span>
                @enderror
            </div>
            <div class="col-12">
                <br>
                <link rel="stylesheet" href="{{asset("plugin/leaflet.css")}}"/>

                <p style="font-size: 14px;opacity: .8">مختصات جغرافیایی</p>

                @error("latLen")
                <span class="text-danger">{{$message}}</span>
                @enderror

                <div wire:ignore id="map" style="height: 400px;"></div>

                <script src="{{asset("/plugin/leaflet.js")}}"></script>
                <script>
                    var letLng = [{{ $latLen!="" ? explode(":", $latLen)[0] : "36.907681" }}, {{ $latLen!="" ? explode(":", $latLen)[1] : "50.675039" }}];

                    var map = L.map('map').setView(letLng, 12);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    var userIcon = L.icon({
                        iconUrl: '{{ asset("storage/".getConfigs("markerMapFoodstoreIcon")) }}',
                        iconSize: [48, 48],
                        iconAnchor: [24, 48],
                    });

                    var marker = L.marker(letLng, {icon: userIcon}).addTo(map);

                    var fixedLocations = [
                        @foreach(\App\Models\Residence::all() as $item)
                        { lat: {{$item->lat}}, lng: {{$item->lng}}, name: "{{$item->title}}" },
                        @endforeach
                    ];

                    var customFixedIcon = L.icon(
                        {
                        iconUrl: '{{ asset("storage/".getConfigs("markerMapIcon")) }}',
                        iconSize: [20, 20],
                        iconAnchor: [20, 40],
                        }
                   );

                    fixedLocations.forEach(function(location){
                        L.marker([location.lat, location.lng], {icon: customFixedIcon})
                            .addTo(map)
                            .bindPopup(location.name);
                    });

                    map.on('click', function (e) {
                        var lat = e.latlng.lat.toFixed(6);
                        var lng = e.latlng.lng.toFixed(6);

                        if (marker) {
                            map.removeLayer(marker);
                        }

                        marker = L.marker([lat, lng], {icon: userIcon}).addTo(map);

                    @this.set('latLen', lat + ":" + lng);
                    });
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
                                 src="{{asset("storage/food_store/".$image)}}">
                            <i class="fa fa-spin fa-spinner" wire:loading
                               wire:target="changeMainI mage('{{$image}}')"></i>
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
            @foreach(\App\Models\OptionCategory::where("type","foodstore")->get() as $key=>$category)
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

</div>
