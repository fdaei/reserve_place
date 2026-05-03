@extends('app')

@section('title', 'صفحه پیدا نشد | اینجا')

@section('content')
<style>
    :root {
        --primary: #66ccff;
        --secondary: #0A2B4E;
        --accent: #F59E0B;
        --gray-text: #475569;
        --border: #E2E8F0;
    }
    
    .error-container {
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }
    
    .error-card {
        max-width: 550px;
        width: 100%;
        background: white;
        border-radius: 32px;
        border: 1px solid var(--border);
        padding: 48px 32px;
        text-align: center;
        box-shadow: 0 20px 35px -12px rgba(0,0,0,0.08);
    }
    
    .error-code {
        font-size: 100px;
        font-weight: 800;
        color: var(--secondary);
        line-height: 1;
        margin-bottom: 16px;
        letter-spacing: -4px;
    }
    
    .error-code span:first-child {
        color: var(--primary);
    }
    
    .error-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
    }
    
    .error-icon i {
        font-size: 50px;
        color: #D97706;
    }
    
    .error-title {
        font-size: 28px;
        font-weight: 800;
        color: var(--secondary);
        margin-bottom: 12px;
    }
    
    .error-message {
        color: var(--gray-text);
        font-size: 15px;
        margin-bottom: 28px;
        line-height: 1.6;
    }
    
    .error-image {
        margin: 20px 0;
    }
    
    .error-image img {
        max-width: 220px;
        width: 100%;
        opacity: 0.7;
    }
    
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--accent);
        color: white;
        text-decoration: none;
        padding: 12px 28px;
        border-radius: 60px;
        font-weight: 700;
        font-size: 14px;
        transition: all 0.2s;
        margin-top: 8px;
    }
    
    .btn-back:hover {
        background: #D97706;
        transform: translateY(-2px);
    }
    
    .suggestions {
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--border);
    }
    
    .suggestions-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--secondary);
        margin-bottom: 12px;
    }
    
    .suggestions-links {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .suggestions-links a {
        color: var(--primary);
        text-decoration: none;
        font-size: 13px;
        transition: color 0.2s;
    }
    
    .suggestions-links a:hover {
        color: var(--accent);
    }
    
    @media (max-width: 480px) {
        .error-card {
            padding: 32px 20px;
        }
        
        .error-code {
            font-size: 70px;
        }
        
        .error-title {
            font-size: 22px;
        }
        
        .error-icon {
            width: 80px;
            height: 80px;
        }
        
        .error-icon i {
            font-size: 38px;
        }
        
        .error-image img {
            max-width: 160px;
        }
    }
</style>

<div class="error-container">
    <div class="error-card">
        <div class="error-icon">
            <i class="fa fa-search"></i>
        </div>
        
        <div class="error-code">
            <span>4</span><span>0</span><span>4</span>
        </div>
        
        <h1 class="error-title">صفحه پیدا نشد!</h1>
        
        <p class="error-message">
            متأسفیم، صفحه‌ای که به دنبال آن هستید وجود ندارد یا حذف شده است.
            <br>
            ممکن است آدرس را اشتباه وارد کرده باشید.
        </p>
        
        @if(getConfigs('page404Icon'))
        <div class="error-image">
            <img src="{{ asset('storage/' . getConfigs('page404Icon')) }}" alt="صفحه پیدا نشد">
        </div>
        @endif
        
        <a href="{{ url('/') }}" class="btn-back">
            <i class="fa fa-arrow-right"></i>
            بازگشت به صفحه اصلی
        </a>
        
        <div class="suggestions">
            <div class="suggestions-title">شاید این pages مفید باشند:</div>
            <div class="suggestions-links">
                <a href="{{ url('/') }}">صفحه اصلی</a>
                <a href="{{ url('/contact') }}">تماس با ما</a>
                <a href="{{ url('/p/terms') }}">قوانین و مقررات</a>
            </div>
        </div>
    </div>
</div>
@endsection