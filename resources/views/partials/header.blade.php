<div class="col-6" id="header-icon">
    <a href="{{ url('/') }}" itemprop="url" aria-label="صفحه اصلی {{ getConfigs('website-title') }}">
        <img src="{{ asset('storage/'.getConfigs('website-icon')) }}" 
             alt="لوگوی {{ getConfigs('website-title') }} - رزرو آنلاین اقامتگاه، تور، رستوران و همسفر"
             width="50" 
             height="50"
             loading="lazy"
             itemprop="logo">
    </a>
</div>

<div class="col-6" id="header-profile-icon">
    @if(auth()->check())
        @if(auth()->user()->profile_image!="")
            <i class="fa" 
               style="background-size: cover!important;background: url('{{ asset("storage/user/".auth()->user()->profile_image) }}') no-repeat"
               aria-hidden="true"></i>
        @else
            <i class="fa fa-user bg-light text-primary" aria-hidden="true"></i>
        @endif
        <div>
            <a class="text-light" href="{{ url('dashboard') }}" 
               itemprop="url"
               aria-label="حساب کاربری">
                حساب کاربری
            </a>
        </div>
    @else
        <i class="fa fa-user bg-light text-primary" aria-hidden="true"></i>
        <div class="text-light">
            <a class="text-light" href="{{ url('login') }}" 
               itemprop="url"
               aria-label="ورود یا ثبت نام">
                ورود یا ثبت نام
            </a>
        </div>
    @endif
</div>

<style>
    .banner-text {
        margin: 0;
        padding: 10px 5px;
        text-align: center;
        position: relative;
        z-index: 2;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .banner-btn {
        display: inline-block;
        color: white!important;
        position: relative;
        z-index: 2;
        border-bottom: 2px dashed white;
        padding-bottom: 4px;
        cursor: pointer;
        text-decoration: none;
        font-weight: bold;
    }
    
    .banner-btn:hover,
    .banner-btn:focus {
        border-bottom: 2px solid white;
        outline: none;
    }
    
    .animated-text {
        display: inline-block;
        animation: fadeInOut 5s infinite;
    }
    
    @keyframes fadeInOut {
        0%, 100% { opacity: 0.8; }
        50% { opacity: 1; }
    }
</style>