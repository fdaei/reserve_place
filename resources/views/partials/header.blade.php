<div class="header-wrapper">
    <div class="header-logo">
        <a href="{{ url('/') }}" itemprop="url" aria-label="صفحه اصلی {{ getConfigs('website-title') }}">
            <img src="{{ asset('storage/'.getConfigs('website-icon')) }}" 
                 alt="لوگوی {{ getConfigs('website-title') }}"
                 width="50" 
                 height="50"
                 loading="lazy"
                 itemprop="logo">
        </a>
    </div>
    
    <div class="header-user">
        @if(auth()->check())
            @if(auth()->user()->profile_image != "")
                <div class="user-avatar" style="background-image: url('{{ asset("storage/user/".auth()->user()->profile_image) }}')"></div>
            @else
                <div class="user-icon">
                    <i class="fa fa-user" aria-hidden="true"></i>
                </div>
            @endif
            <a class="user-link" href="{{ url('dashboard') }}">حساب کاربری</a>
        @else
            <div class="user-icon">
                <i class="fa fa-user" aria-hidden="true"></i>
            </div>
            <a class="user-link" href="{{ url('login') }}">ورود یا ثبت نام</a>
        @endif
    </div>
</div>

<style>
    .header-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        padding: 10px 0;
    }
    
    .header-logo {
        flex-shrink: 0;
    }
    
    .header-logo img {
        max-height: 50px;
        width: auto;
        display: block;
    }
    
    .header-user {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }
    
    .user-icon {
        background: rgba(102, 204, 255, 0.15);
        color: #66ccff;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-size: cover;
        background-position: center;
    }
    
    .user-link {
        color: white;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: color 0.2s;
        white-space: nowrap;
    }
    
    .user-link:hover {
        color: #F59E0B;
    }
    
    /* موبایل - فاصله از حاشیه */
    @media (max-width: 768px) {
        .header-wrapper {
            padding: 10px 12px;
        }
    }
    
    @media (max-width: 480px) {
        .header-wrapper {
            padding: 8px 10px;
        }
        
        .user-link {
            font-size: 12px;
        }
        
        .user-icon, .user-avatar {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }
        
        .header-logo img {
            max-height: 42px;
        }
    }
</style>