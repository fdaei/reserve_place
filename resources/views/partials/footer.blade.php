<footer class="footer-art">
    {{-- نوارهای رنگی - المان اصلی و متفاوت --}}
    <div class="art-bars">
        {{-- نوار ۱: برند و لوگو + توضیحات کامل --}}
        <div class="art-bar bar-brand">
            <div class="bar-content">
                <div class="brand-full">
                    <div class="brand-mini">
                        <i class="fa fa-compass"></i>
                        <span>{{ getConfigs("website-title") }}</span>
                    </div>
                    <p class="brand-desc-full">{{ getConfigs("website-description") }}</p>
                </div>
            </div>
        </div>

        {{-- نوار ۲: دسترسی سریع --}}
        <div class="art-bar bar-links">
            <div class="bar-content">
                <div class="bar-header">
                    <i class="fa fa-bolt"></i>
                    <span>دسترسی سریع</span>
                </div>
                <div class="bar-links-group">
                    <a href="{{ url('/') }}">صفحه اصلی</a>
                    <a href="{{ url('/contact') }}">تماس با ما</a>
                </div>
            </div>
        </div>

        {{-- نوار ۳: صفحات و اطلاعات --}}
        <div class="art-bar bar-pages">
            <div class="bar-content">
                <div class="bar-header">
                    <i class="fa fa-file-text-o"></i>
                    <span>اطلاعات</span>
                </div>
                <div class="bar-links-group">
                    @foreach(\App\Models\Page::where("status",true)->get() as $item)
                    <a href="{{ url('/p/'.$item->url_text) }}">{{ $item->title }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- نوار ۴: شبکه‌های اجتماعی --}}
        <div class="art-bar bar-social">
            <div class="bar-content">
                <div class="bar-header">
                    <i class="fa fa-share-alt"></i>
                    <span>ما را دنبال کنید</span>
                </div>
                <div class="social-mini-group">
                    <a href="{{ getConfigs("instagramLink") }}" class="social-mini-icon" target="_blank">
                        <i class="fa fa-instagram"></i>
                        <span>اینستاگرام</span>
                    </a>
                    <a href="{{ getConfigs("telegramLink") }}" class="social-mini-icon" target="_blank">
                        <i class="fa fa-telegram"></i>
                        <span>تلگرام</span>
                    </a>
                    <a href="{{ getConfigs("whatsappLink") }}" class="social-mini-icon" target="_blank">
                        <i class="fa fa-whatsapp"></i>
                        <span>واتساپ</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- نوار ۵: تماس با ما (آدرس کامل) --}}
        <div class="art-bar bar-contact">
            <div class="bar-content">
                <div class="bar-header">
                    <i class="fa fa-headphones"></i>
                    <span>پشتیبانی</span>
                </div>
                <div class="contact-full-group">
                    <div class="contact-full-item">
                        <i class="fa fa-phone"></i>
                        <div>
                            <span>تلفن پشتیبانی</span>
                            <strong>{{ getConfigs("phone1") }}</strong>
                        </div>
                    </div>
                    <div class="contact-full-item">
                        <i class="fa fa-envelope"></i>
                        <div>
                            <span>ایمیل</span>
                            <strong>{{ getConfigs("email") }}</strong>
                        </div>
                    </div>
                    <div class="contact-full-item">
                        <i class="fa fa-map-marker"></i>
                        <div>
                            <span>آدرس</span>
                            <strong>{{ getConfigs("address") }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- نوار ۶: نمادهای اعتماد --}}
        <div class="art-bar bar-trust">
            <div class="bar-content">
                <div class="bar-header">
                    <i class="fa fa-shield"></i>
                    <span>نمادهای اعتماد</span>
                </div>
                <div class="trust-full-group">
                    <img src="/storage/static/enamad.png" alt="اینماد" loading="lazy">
                    <img src="/storage/static/iwfm.png" alt="آی دبلیو اف ام" loading="lazy">
                    <img src="/storage/static/sole.png" alt="سول" loading="lazy">
                </div>
            </div>
        </div>
    </div>

    {{-- کپی‌رایت مینیمال --}}
    <div class="art-copyright">
        <span>© {{ jalaliDate("Y",time()) }} {{ getConfigs("website-title") }}</span>
        <span class="copyright-sign">✦</span>
        <span>طراحی شده در شمال ایران</span>
    </div>
</footer>

<style>
    /* ============================================
       فوتر هنری نهایی - بدون نارنجی اضافی
       متن‌ها راست‌چین
    ============================================ */
    
    .footer-art {
        background: #0A2B4E;
        margin-top: 60px;
        position: relative;
    }
    
    /* نوارهای اصلی */
    .art-bars {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    
    .art-bar {
        padding: 24px 40px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        transition: all 0.35s ease;
        position: relative;
        overflow: hidden;
        text-align: right;
    }
    
    /* رنگ‌بندی حرفه‌ای هر نوار - فقط آبی‌های مختلف */
    .bar-brand { background: linear-gradient(135deg, #1A4A6E 0%, #0F3550 100%); }
    .bar-links { background: linear-gradient(135deg, #1E5580 0%, #134065 100%); }
    .bar-pages { background: linear-gradient(135deg, #226092 0%, #174C78 100%); }
    .bar-social { background: linear-gradient(135deg, #266BA4 0%, #1A558A 100%); }
    .bar-contact { background: linear-gradient(135deg, #2A76B6 0%, #1D609C 100%); }
    .bar-trust { background: linear-gradient(135deg, #2E81C8 0%, #206AAE 100%); }
    
    /* افکت حرکت نور روی نوارها */
    .art-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
        transition: left 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        pointer-events: none;
    }
    
    .art-bar:hover::before {
        left: 100%;
    }
    
    .art-bar:hover {
        padding-right: 55px;
        background: #1A4A6E;
    }
    
    .art-bar:hover .bar-header i,
    .art-bar:hover .bar-header span,
    .art-bar:hover .brand-mini span,
    .art-bar:hover .brand-desc-full,
    .art-bar:hover .bar-links-group a,
    .art-bar:hover .social-mini-icon,
    .art-bar:hover .contact-full-item,
    .art-bar:hover .contact-full-item i,
    .art-bar:hover .contact-full-item span,
    .art-bar:hover .contact-full-item strong {
        color: #66ccff;
    }
    
    .art-bar:hover .trust-full-group img {
        background: white;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    /* محتوای داخل نوار */
    .bar-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    /* برند با توضیحات کامل */
    .brand-full {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100%;
    }
    
    .brand-mini {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .brand-mini i {
        font-size: 28px;
        color: #66ccff;
    }
    
    .brand-mini span {
        font-size: 20px;
        font-weight: 800;
        color: white;
    }
    
    .brand-desc-full {
        font-size: 13px;
        text-align: right;
        line-height: 1.6;
        color: #B0D4F0;
        margin: 0;
        max-width: 90%;
    }
    
    /* هدر هر نوار */
    .bar-header {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .bar-header i {
        font-size: 18px;
        color: #66ccff;
    }
    
    .bar-header span {
        font-size: 15px;
        font-weight: 700;
        color: white;
        letter-spacing: 0.5px;
    }
    
    /* گروه لینک‌ها */
    .bar-links-group {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
    }
    
    .bar-links-group a {
        color: #B0D4F0;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s;
        position: relative;
    }
    
    .bar-links-group a::after {
        content: '';
        position: absolute;
        bottom: -3px;
        right: 0;
        width: 0;
        height: 1px;
        background: #66ccff;
        transition: width 0.3s;
    }
    
    .bar-links-group a:hover::after {
        width: 100%;
        right: auto;
        left: 0;
    }
    
    .bar-links-group a:hover {
        color: #66ccff;
        transform: translateX(3px);
    }
    
    /* شبکه اجتماعی */
    .social-mini-group {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .social-mini-icon {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #B0D4F0;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s;
        padding: 5px 10px;
        border-radius: 40px;
        background: rgba(255,255,255,0.03);
    }
    
    .social-mini-icon i {
        font-size: 16px;
    }
    
    .social-mini-icon:hover {
        color: #66ccff;
        background: rgba(255,255,255,0.08);
        transform: translateY(-2px);
    }
    
    /* اطلاعات تماس کامل */
    .contact-full-group {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
    }
    
    .contact-full-item {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        color: #B0D4F0;
    }
    
    .contact-full-item i {
        width: 32px;
        height: 32px;
        background: rgba(102, 204, 255, 0.12);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #66ccff;
    }
    
    .contact-full-item div {
        display: flex;
        flex-direction: column;
    }
    
    .contact-full-item span {
        font-size: 10px;
        color: #7FA8C9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .contact-full-item strong {
        font-size: 13px;
        font-weight: 600;
        color: white;
    }
    
    /* نمادهای اعتماد */
    .trust-full-group {
        display: flex;
        gap: 15px;
    }
    
    .trust-full-group img {
        width: 48px;
        height: 48px;
        background: white;
        border-radius: 12px;
        padding: 6px;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    /* کپی‌رایت مینیمال */
    .art-copyright {
        text-align: center;
        padding: 16px;
        background: #051a2f;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .art-copyright span {
        font-size: 11px;
        color: rgba(255,255,255,0.35);
        letter-spacing: 0.5px;
    }
    
    .copyright-sign {
        font-size: 10px;
        color: #66ccff;
    }
    
    /* ریسپانسیو موبایل */
    @media (max-width: 992px) {
        .art-bar {
            padding: 20px 24px;
        }
        
        .bar-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .brand-desc-full {
            max-width: 100%;
        }
    }
    
    @media (max-width: 768px) {
        .art-bar {
            padding: 18px 20px;
        }
        
        .bar-links-group {
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .social-mini-group {
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .contact-full-group {
            flex-direction: column;
            gap: 12px;
        }
        
        .trust-full-group {
            flex-wrap: wrap;
        }
        
        .art-bar:hover {
            padding-right: 20px;
        }
        
        .brand-mini span {
            font-size: 18px;
        }
        
        .brand-desc-full {
            font-size: 12px;
        }
    }
    
    @media (max-width: 480px) {
        .bar-links-group {
            flex-direction: column;
            gap: 8px;
        }
        
        .social-mini-group {
            flex-direction: column;
            gap: 8px;
        }
        
        .social-mini-icon {
            width: fit-content;
        }
    }
</style>

<script>
    // قابلیت کلیک روی نوارها
    document.querySelectorAll('.art-bar').forEach(bar => {
        bar.addEventListener('click', function(e) {
            if (!e.target.closest('a') && !e.target.closest('.trust-full-group') && !e.target.closest('.contact-full-item')) {
                window.location.href = '{{ url('/') }}';
            }
        });
    });
</script>