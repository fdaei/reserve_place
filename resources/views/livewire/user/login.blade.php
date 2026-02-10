<div id="login-page" class="">
    @vite(['resources/css/user/login.less'])
    <br>
    <br>
    <form wire:submit="login" class="row {{$page==1?"":"d-none"}}" id="level-1">
        <div class="col-12">
            <img src="{{asset("/storage/static/login.png")}}">
            <h3 class="font-weight-bold" style="">ورود یا ثبت نام در اینجا</h3>
            <p class="" style="">برای ورود به اینجا شماره همراه خود را وارد کنید.</p>
            <input placeholder="09xxxxxxxxx" wire:model.prevent="phone" class="form-control w-auto"
                   style="margin: 0 auto">
            @error("phone")
            <span class="text-danger">
                {{$message}}
            </span>
            @enderror
            <p class="" style="margin: 20px auto">
                ورود و ثبت نام در اینجا به منزله پذیرفتن قوانین مقررات میباشد.
            </p>
            <button class="btn btn-dark bg-c2">
                <i class="fa fa-spin fa-spinner" wire:target="login" wire:loading></i>
                ادامه
            </button>
        </div>
    </form>
    <div class="row  {{$page==2?"":"d-none"}}" id="level-1">
        <form  class="col-12">
            <img src="{{asset("/img/injaa-marker.png")}}">
            <h3 class="font-weight-bold" style="">تایید شماره موبایل</h3>
            <p class="" style="">
                کد 4 رقمی ارسال شده به شماره "{{$phone}}" را وارد کنید.
            </p>
            <div
                style="text-align: left;margin: 0 auto;background: #e9ecef;width: 280px;padding: 8px ;border-radius: 4px">
                <span style="">{{$phone}}</span>
                <span wire:click="back" class="float-lg-right"
                      style="cursor: pointer;font-size: 11px;float: right;border-left: 1px solid #8a8a8a;padding-left: 6px;position: relative;top: 3px">
                    ویرایش شماره
                    <i wire:loading wire:target="back" class="fa fa-spin fa-spinner">

                    </i>
                </span>
            </div>
            <div class="otp-container ltr" style="direction: rtl">
                <input wire:model.live="code1" type="text" maxlength="1" class="form-control otp-input" id="digit1">
                <input wire:model.live="code2" type="text" maxlength="1" class="form-control otp-input" id="digit2">
                <input wire:model.live="code3" type="text" maxlength="1" class="form-control otp-input" id="digit3">
                <input wire:model.live="code4" wire:keydown="verify_code" type="text" maxlength="1" class="form-control otp-input" id="digit4">
                <button type="button" disabled wire:click="login" class="btn" id="timer">
                    <i class="fa fa-refresh" wire:target="login" wire:loading.class="fa-spin" ></i>
                    <span style="display: block;font-size: 10px">01:59</span>
                </button>
            </div>
            @error("code")
            <span class="text-danger">
                {{$message}}
            </span>
            @enderror
            <button  wire:submit="verify_code" id="submit-form-2"  style="margin-top: 20px" class="btn btn-dark bg-c2">
                <i class="fa fa-spinner fa-spin" wire:target="verify_code" wire:loading></i>
                ورود
            </button>
        </form>
        @script
        <script>
            let interval=null;
            let secends=120;
            $wire.on('start-timer', () => {
                secends=120;
                $("#timer span").removeClass("d-none");
                $("#timer .fa").removeClass("active");
                $("#timer").attr("disabled",true);
                interval=setInterval(function (){
                    secends--;
                    let timeStr="";
                    if(secends>60){
                        timeStr="01:"+(((secends-60)>=60&&(secends-60)<=70)?"0"+(secends-60):(secends-60));
                    }else {
                        timeStr="00:"+(((secends)>=0&&(secends)<=9)?"0"+(secends):(secends));
                    }
                    if(secends<=1){
                        clearInterval(interval)
                        timeStr="00:00";
                        $("#timer").attr("disabled",false);
                        $("#timer span").addClass("d-none");
                        $("#timer .fa").addClass("active");
                    }

                    $("#timer span").text(timeStr)
                },1000)
            });
            $(document).ready(function () {
                $(".otp-input").on("input", function () {
                    if (isNaN($(this).val())) {
                        $(this).val("");
                        return;
                    }

                    if ($(this).val().length === 1) {
                        $(this).next(".otp-input").focus();
                    }
                });

                $(".otp-input").on("keydown", function (e) {
                    if (e.key === "Backspace" && $(this).val() === "") {
                        $(this).prev(".otp-input").focus();
                    }
                });
            });
        </script>
        @endscript
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
</div>
