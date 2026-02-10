<footer class="footer ocean-footer" role="contentinfo">
    {{-- Large Whale Background --}}
    <div class="whale-background">
        <div class="whale-container">
            <img src="/storage/static/blue-whale.png" 
                 alt="نهنگ آبی - نماد عمق و آرامش {{ getConfigs('website-title') }}"
                 class="whale-image"
                 loading="lazy"
                 width="300"
                 height="225">
        </div>
    </div>
    
    {{-- Footer Content --}}
    <div class="footer-content">
        {{-- Brand Column --}}
        <div class="footer-col brand-col">
            <h3 itemprop="name">{{ getConfigs("website-title") }}</h3>
            <p itemprop="description">{{ getConfigs("website-description") }}</p>
            <div class="social-icons">
                <a href="{{ getConfigs("instagramLink") }}" 
                   class="social-icon" 
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="صفحه اینستاگرام {{ getConfigs('website-title') }}">
                    <i class="fa fa-instagram"></i>
                </a>
                <a href="{{ getConfigs("telegramLink") }}" 
                   class="social-icon" 
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="کانال تلگرام {{ getConfigs('website-title') }}">
                    <i class="fa fa-telegram"></i>
                </a>
                <a href="{{ getConfigs("whatsappLink") }}" 
                   class="social-icon" 
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="واتساپ {{ getConfigs('website-title') }}">
                    <i class="fa fa-whatsapp"></i>
                </a>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="footer-col">
            <h4>دسترسی سریع</h4>
            <ul class="footer-links-list" role="list">
                <li role="listitem">
                    <a href="{{ url('/') }}" itemprop="url">صفحه اصلی</a>
                </li>
                <li role="listitem">
                    <a href="{{ url('/contact') }}" itemprop="url">تماس با ما</a>
                </li>
            </ul>
        </div>

        {{-- Pages Links --}}
        <div class="footer-col">
            <h4>ارتباط و همکاری</h4>
            <ul class="footer-links-list" role="list">
                @foreach(\App\Models\Page::where("status",true)->get() as $item)
                <li role="listitem">
                    <a href="{{ url('/p/'.$item->url_text) }}" 
                       itemprop="url"
                       title="{{ $item->title }}">
                        {{ $item->title }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>

        {{-- Contact Info --}}
        <div class="footer-col">
            <h4>تماس با ما</h4>
            <div class="contact-info" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                <div class="contact-item">
                    <i class="fa fa-phone" aria-hidden="true"></i>
                    <span itemprop="telephone">{{ getConfigs("phone1") }}</span>
                </div>
                <div class="contact-item">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                    <span itemprop="email">{{ getConfigs("email") }}</span>
                </div>
                <div class="contact-item">
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                    <span itemprop="streetAddress">{{ getConfigs("address") }}</span>
                </div>
            </div>
            
            {{-- Trust Badges --}}
            <div class="trust-badges">
                <img src="/storage/static/enamad.png" 
                     alt="نماد اعتماد الکترونیکی" 
                     loading="lazy"
                     width="60" 
                     height="60">
                <img src="/storage/static/iwfm.png" 
                     alt="جشنواره وب و موبایل ایران" 
                     loading="lazy"
                     width="60" 
                     height="60">
                <img src="/storage/static/sole.png" 
                     alt="کسب‌وکار تک‌نفره" 
                     loading="lazy"
                     width="60" 
                     height="60">
            </div>
        </div>
    </div>

    {{-- Footer Bottom --}}
    <div class="footer-bottom">
        <div class="copyright">
            © {{ jalaliDate("Y",time()) }} تمام حقوق برای 
            <strong itemprop="name">{{ getConfigs("website-title") }}</strong> 
            محفوظ است
        </div>
    </div>
</footer>

<style>
    /* Base Styles - با ارتفاع بیشتر برای نهنگ بزرگ */
    .ocean-footer {
        background: linear-gradient(to bottom, #001a35, #000b1a);
        color: #ecf0f1;
        padding: 60px 0 40px; /* Padding پایین بیشتر */
        margin-top: 50px;
        position: relative;
        overflow: hidden;
        border-top: 3px solid var(--primary-color);
        min-height: 500px; /* ارتفاع بیشتر */
    }
    
    /* Whale Background - بزرگتر */
    .whale-background {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 300px; /* ارتفاع بیشتر */
        z-index: 1;
        pointer-events: none;
        opacity: 0.9;
    }
    
    /* Whale Container - حداکثر سایز */
    .whale-container {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%) scaleX(-1);
        width: 300px; /* سایز ماکزیمم */
        height: 225px;
        z-index: 2;
        animation: whaleFloatLarge 35s infinite ease-in-out;
        filter: drop-shadow(0 5px 15px rgba(0, 150, 255, 0.5));
    }
    
    .whale-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        opacity: 0.9;
    }
    
    /* Animation برای نهنگ بزرگ */
    @keyframes whaleFloatLarge {
        0%, 100% {
            transform: translateX(-50%) translateY(0px) scaleX(-1);
        }
        25% {
            transform: translateX(calc(-50% - 40px)) translateY(-15px) scaleX(-1);
        }
        50% {
            transform: translateX(-50%) translateY(8px) scaleX(-1);
        }
        75% {
            transform: translateX(calc(-50% + 40px)) translateY(-12px) scaleX(-1);
        }
    }
    
    /* Footer Content - با margin-top بیشتر */
    .footer-content {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
        position: relative;
        z-index: 3;
        margin-top: 40px; /* فاصله از بالای فوتر */
    }
    
    .footer-col {
        flex: 1;
        min-width: 250px;
        margin-bottom: 30px;
        padding: 0 15px;
    }
    
    .footer-col h3 {
        color: #4dc3ff;
        margin-bottom: 15px;
        font-size: 18px;
    }
    
    .footer-col h4 {
        color: #66ccff;
        margin-bottom: 15px;
        font-size: 16px;
    }
    
    /* Social Icons */
    .social-icons {
        display: flex;
        gap: 12px;
        margin-top: 15px;
    }
    
    .social-icon {
        color: #88d3ff;
        font-size: 16px;
        transition: all 0.3s;
        background: rgba(0, 100, 255, 0.15);
        padding: 8px;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }
    
    .social-icon:hover {
        color: white;
        background: rgba(0, 100, 255, 0.3);
        transform: translateY(-2px);
    }
    
    /* Links */
    .footer-links-list {
        list-style: none;
        padding: 0;
    }
    
    .footer-links-list li {
        margin-bottom: 8px;
    }
    
    .footer-links-list a {
        color: #a3d9ff;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-block;
        padding: 3px 0;
        font-size: 14px;
    }
    
    .footer-links-list a:hover {
        color: white;
        transform: translateX(3px);
    }
    
    /* Contact Info */
    .contact-info {
        margin-bottom: 20px;
    }
    
    .contact-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }
    
    .contact-item i {
        margin-left: 8px;
        color: #4dc3ff;
        font-size: 14px;
        min-width: 16px;
        text-align: center;
    }
    
    .contact-item span {
        color: #b3e0ff;
        font-size: 13px;
    }
    
    /* Trust Badges */
    .trust-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 15px;
    }
    
    .trust-badges img {
        width: 55px;
        height: 55px;
        object-fit: contain;
        border-radius: 5px;
        background: rgba(255, 255, 255, 0.08);
        padding: 4px;
        border: 1px solid rgba(77, 195, 255, 0.2);
    }
    
    /* Footer Bottom */
    .footer-bottom {
        text-align: center;
        padding-top: 40px; /* بیشتر */
        margin-top: 40px; /* بیشتر */
        border-top: 1px solid rgba(77, 195, 255, 0.2);
        position: relative;
        z-index: 3;
    }
    
    .copyright {
        color: #88d3ff;
        font-size: 13px;
    }
    
    /* ========== RESPONSIVE DESIGN ========== */
    /* سایز نهنگ ثابت می‌ماند، فقط موقعیت آن تغییر می‌کند */
    
    /* Desktop Large (1400px و بالاتر) */
    @media (min-width: 1400px) {
        .whale-container {
            width: 350px; /* حتی بزرگتر */
            height: 263px;
            bottom: 40px;
        }
        
        .whale-background {
            height: 350px;
        }
        
        .ocean-footer {
            min-height: 600px;
        }
    }
    
    /* Desktop (1200px - 1399px) */
    @media (min-width: 1200px) and (max-width: 1399px) {
        .whale-container {
            width: 320px;
            height: 240px;
            bottom: 35px;
        }
        
        .whale-background {
            height: 320px;
        }
        
        .ocean-footer {
            min-height: 550px;
        }
    }
    
    /* Desktop Medium (992px - 1199px) */
    @media (min-width: 992px) and (max-width: 1199px) {
        .whale-container {
            width: 300px;
            height: 225px;
            bottom: 30px;
        }
        
        .whale-background {
            height: 300px;
        }
        
        .ocean-footer {
            min-height: 500px;
        }
    }
    
    /* Tablet Landscape (768px - 991px) */
    @media (min-width: 768px) and (max-width: 991px) {
        .whale-container {
            width: 300px; /* همان سایز بزرگ */
            height: 225px;
            bottom: 25px;
            left: 10%; /* کمی به چپ */
            transform: translateX(0%) scaleX(-1); /* بدون ترنسلیت مرکزی */
        }
        
        .whale-background {
            height: 280px;
        }
        
        .ocean-footer {
            min-height: 480px;
            padding: 50px 0 30px;
        }
        
        .footer-col {
            flex: 0 0 50%;
            margin-bottom: 30px;
        }
        
        .footer-content {
            margin-top: 30px;
        }
    }
    
    /* Mobile Landscape (576px - 767px) */
    @media (min-width: 576px) and (max-width: 767px) {
        .whale-container {
            width: 280px; /* کمی کوچکتر ولی هنوز بزرگ */
            height: 210px;
            bottom: 20px;
            left: 5%;
            transform: translateX(0%) scaleX(-1);
        }
        
        .whale-background {
            height: 250px;
        }
        
        .ocean-footer {
            min-height: 450px;
            padding: 40px 0 25px;
        }
        
        .footer-col {
            flex: 0 0 50%;
            padding: 0 10px;
            margin-bottom: 25px;
        }
        
        .footer-content {
            padding: 0 10px;
            margin-top: 20px;
        }
        
        .social-icons {
            gap: 10px;
        }
    }
    
    /* Mobile Portrait (375px - 575px) */
    @media (min-width: 375px) and (max-width: 575px) {
        .whale-container {
            width: 250px; /* بزرگ ولی متناسب با موبایل */
            height: 188px;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%) scaleX(-1); /* باز هم مرکزی */
        }
        
        .whale-background {
            height: 220px;
        }
        
        .ocean-footer {
            min-height: 420px;
            padding: 40px 0 20px;
        }
        
        .footer-content {
            padding: 0 10px;
            margin-top: 15px;
        }
        
        .footer-col {
            flex: 0 0 100%;
            padding: 0;
            margin-bottom: 25px;
        }
        
        .footer-col h3 {
            font-size: 16px;
            margin-bottom: 12px;
        }
        
        .footer-col h4 {
            font-size: 15px;
            margin-bottom: 12px;
        }
        
        .social-icons {
            justify-content: center;
            gap: 10px;
        }
        
        .social-icon {
            width: 34px;
            height: 34px;
            font-size: 15px;
            padding: 7px;
        }
    }
    
    /* Mobile Small (320px - 374px) */
    @media (min-width: 320px) and (max-width: 374px) {
        .whale-container {
            width: 220px; /* بزرگ ولی متناسب */
            height: 165px;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%) scaleX(-1);
        }
        
        .whale-background {
            height: 200px;
        }
        
        .ocean-footer {
            min-height: 400px;
            padding: 35px 0 18px;
        }
        
        .trust-badges img {
            width: 50px;
            height: 50px;
        }
        
        .copyright {
            font-size: 12px;
            padding: 0 10px;
        }
    }
    
    /* Very Small Mobile (max-width: 319px) */
    @media (max-width: 319px) {
        .whale-container {
            width: 200px; /* حداقل سایز بزرگ */
            height: 150px;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%) scaleX(-1);
        }
        
        .whale-background {
            height: 180px;
        }
        
        .ocean-footer {
            min-height: 380px;
            padding: 30px 0 15px;
        }
        
        .trust-badges img {
            width: 45px;
            height: 45px;
        }
    }
    
    /* Print Styles */
    @media print {
        .whale-background,
        .social-icons {
            display: none;
        }
        
        .ocean-footer {
            background: white !important;
            color: black !important;
            border-top: 1px solid #ccc;
            min-height: auto !important;
            padding: 30px 0 20px !important;
        }
    }
</style>

<script>
// JavaScript برای نهنگ بزرگ
document.addEventListener('DOMContentLoaded', function() {
    const whale = document.querySelector('.whale-container');
    
    // تنظیم انیمیشن بر اساس سایز صفحه
    if (whale) {
        // سرعت انیمیشن بر اساس عرض صفحه
        const screenWidth = window.innerWidth;
        let animationDuration = 35; // ثانیه
        
        if (screenWidth <= 767) {
            animationDuration = 25; // سریعتر در موبایل
        }
        
        whale.style.animationDuration = animationDuration + 's';
        
        // بهینه‌سازی عملکرد
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                whale.style.animationPlayState = 'paused';
            } else {
                whale.style.animationPlayState = 'running';
            }
        });
        
        // ریسایز کردن موقعیت نهنگ
        window.addEventListener('resize', function() {
            const currentWidth = window.innerWidth;
            let newDuration = 35;
            
            if (currentWidth <= 767) {
                newDuration = 25;
            }
            
            whale.style.animationDuration = newDuration + 's';
        });
    }
    
    // اضافه کردن افکت hover اختیاری
    const footer = document.querySelector('.ocean-footer');
    if (footer && whale) {
        footer.addEventListener('mouseenter', function() {
            whale.style.filter = 'drop-shadow(0 8px 20px rgba(0, 150, 255, 0.7))';
        });
        
        footer.addEventListener('mouseleave', function() {
            whale.style.filter = 'drop-shadow(0 5px 15px rgba(0, 150, 255, 0.5))';
        });
    }
});
</script>