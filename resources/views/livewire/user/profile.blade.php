<div>
    @vite(['resources/css/user/profile.less'])
    <form wire:submit="save" class="row">
        <p class="font-weight-bold col-12">
            اطلاعات شخصی
        </p>
        @php
        $user=auth()->user();
        @endphp
        <div style="padding-bottom: 20px" class="col-12" id="image-container">
            @if ($image)
            <i style="background-image: url('{{ $image->temporaryUrl() }}')" class="fa user-ic">
            @else
            <i style="background-image: url('{{asset("storage/user/".($user->profile_image!=""?$user->profile_image:"User.png"))}}')" class="fa user-ic">
            @endif
                <i style="opacity: .5;font-size: 24px;position: relative;top: -25px;" wire:loading wire:target="image" class="color-c1 fa-spinner fa-spin fa"></i>
            </i>
            <div>
                <br>
                <span style="">
                    عکس شما
                </span>
                <br>
                <span style="">
                    PNG یا JPG یا WEBP یا GIF
                </span>
                <div>
                    <input wire:model.live="image" wire:loading.attr="disabled"
                           accept="image/jpeg,image/png,image/webp,image/gif" type="file" class="bg-c1">
                </div>
            </div>

        </div>
       <div class="col-12" style="margin-top: 0">
           @error("image")
           <span class="text-danger">
                {{$message}}
            </span>
           @enderror
       </div>
        <div class="col-6">
            <input wire:model="name" type="text" min="0" placeholder="نام"
                   class="form-control form-control-sm w-100">
            @error("name")
            <span class="text-danger">
                {{$message}}
            </span>
            @enderror
        </div>
        <div class="col-6">
            <input wire:model="family" type="text" min="0" placeholder="نام خانوادگی"
                   class="form-control form-control-sm w-100">
            @error("family")
            <span class="text-danger">
                {{$message}}
            </span>
            @enderror
        </div>
        <div class="col-6">
            <input  wire:model="nationalCode" type="text" min="0" placeholder="کدملی"
                   class="form-control form-control-sm w-100">
            @error("nationalCode")
            <span class="text-danger">
                {{$message}}
            </span>
            @enderror
        </div>
        <div class="col-6">
            <input wire:model="birthDay" id="birthday" accept="image/jpeg" type="text"  maxlength="10" min="0" placeholder="تاریخ تولد"
                   class="form-control form-control-sm w-100">

            <script>
                const input = document.getElementById('birthday');

                input.addEventListener('focus', function() {
                    // هنگام فوکوس، اگر کاربر بخواهد، می‌تواند تاریخ را پاک کند
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
            @error("birthDay")
            <span class="text-danger">
                {{$message}}
            </span>
            @enderror
        </div>

        <div style="margin-top: 24px" class="col-12">
            <div class="d-flex">
                <input disabled type="text" min="0" value="{{$user->phone}}"
                       class="form-control form-control-sm col-8">
                <div class="" style="padding-top: 4px;margin-right: 8px">
                    <span class="text-white bg-c3" style="border-radius: 4px;padding: 2px 6px;font-size: 13px">
                        غیرقابل ویرایش
                    </span>
                </div>
            </div>
        </div>
        <div class="col-12">
            <button class=" mt-4 btn btn-success w-100">
                ذخیره اطلاعات
            </button>
        </div>
    </form>
</div>
