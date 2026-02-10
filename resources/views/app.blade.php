<!DOCTYPE html>
@php
    $title = isset($title) ? $title : "";
    $description = isset($description) ? $description : getConfigs("website-description");
    $image = isset($image) ? $image : asset("storage/".getConfigs("website-icon"));
    $url = url()->current();
    $keywords = isset($keywords) ? $keywords : getConfigs("website-words");
@endphp

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <style>
        :root {
            --primary-color: {{ getConfigs("mainColor") }};
            --secondary-color: {{ getConfigs("secondaryColor") }};
        }
    </style>

    {{-- Primary Meta Tags --}}
    <title>{{ $title ? $title . " | " : "" }}{{ getConfigs("website-title") }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ $keywords }}">
    <meta name="author" content="{{ getConfigs("website-title") }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    
    {{-- Geo Meta Tags for Iran --}}
    <meta name="geo.region" content="IR">
    <meta name="geo.placename" content="ایران">
    <meta name="geo.position" content="35.6892;51.3890">
    <meta name="ICBM" content="35.6892, 51.3890">
    <meta name="language" content="persian">
    <meta name="country" content="Iran">
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $url }}">
    <meta property="og:title" content="{{ $title ? $title . " | " : "" }}{{ getConfigs("website-title") }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ getConfigs("website-title") }}">
    <meta property="og:locale" content="fa_IR">
    <meta property="og:site_name" content="{{ getConfigs("website-title") }}">
    
    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@injaa_com">
    <meta name="twitter:creator" content="@injaa_com">
    <meta name="twitter:url" content="{{ $url }}">
    <meta name="twitter:title" content="{{ $title ? $title . " | " : "" }}{{ getConfigs("website-title") }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $image }}">
    <meta name="twitter:image:alt" content="{{ getConfigs("website-title") }}">
    
    {{-- Canonical --}}
    <link rel="canonical" href="{{ $url }}">
    
    {{-- Alternate Languages --}}
    <link rel="alternate" hreflang="fa-ir" href="{{ $url }}">
    <link rel="alternate" hreflang="x-default" href="{{ $url }}">
    
    {{-- Favicon and App Icons --}}
    <link rel="icon" href="{{ asset('storage/injaa_iconInput_1761765907.png') }}" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/injaa_iconInput_1761765907.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('storage/injaa_iconInput_1761765907.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/injaa_iconInput_1761765907.png') }}">
    <link rel="shortcut icon" href="{{ asset('storage/injaa_iconInput_1761765907.png') }}">
    <meta name="msapplication-TileImage" content="{{ asset('storage/injaa_iconInput_1761765907.png') }}">
    
    {{-- PWA / Mobile --}}
    <meta name="theme-color" content="{{ getConfigs("mainColor") }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ getConfigs("website-title") }}">
    
    {{-- Security Headers --}}
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    
    {{-- Preload Resources --}}
    <link rel="preload" href="{{ asset('plugin/shabnam-font-v1.1.0/Farsi-Digits/Shabnam-FD.woff') }}" as="font" type="font/woff" crossorigin>
    
    {{-- Schema.org Structured Data --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "{{ getConfigs('website-title') }}",
        "description": "{{ getConfigs('website-description') }}",
        "url": "{{ url('/') }}",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ url('/') }}?searchText={search_term_string}",
            "query-input": "required name=search_term_string"
        },
        "publisher": {
            "@type": "Organization",
            "name": "{{ getConfigs('website-title') }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ url('storage/injaa_iconInput_1761765907.png') }}",
                "width": "512",
                "height": "512"
            }
        }
    }
    </script>
    
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "{{ getConfigs('website-title') }}",
        "description": "{{ getConfigs('website-description') }}",
        "url": "{{ url('/') }}",
        "logo": "{{ url('storage/injaa_iconInput_1761765907.png') }}",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "تهران ژاندارمری خیابان ایثار نبش خیابان مالک",
            "addressLocality": "تهران",
            "addressCountry": "IR"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+98-4846",
            "contactType": "customer service",
            "availableLanguage": "Persian"
        },
        "sameAs": [
            "https://instagram.com/injaa_com",
            "https://t.me/injaa_com"
        ]
    }
    </script>


    {{-- jQuery and App Scripts --}}
    <script src="/plugin/jquery/jquery.js"></script>
    @vite(['resources/js/app.js'])
    
    {{-- Styles --}}
    @vite(['resources/css/app.less'])
    @livewireStyles
    
    {{-- Custom Font --}}
    <style>
        @font-face {
            font-family: 'shabnam';
            src: url('{{asset("plugin/shabnam-font-v1.1.0/Farsi-Digits/Shabnam-FD.eot")}}');
            src: url('{{asset("plugin/shabnam-font-v1.1.0/Farsi-Digits/Shabnam-FD.ttf")}}') format('truetype');
            src: url('{{asset("plugin/shabnam-font-v1.1.0/Farsi-Digits/Shabnam-FD.woff")}}') format('woff');
        }
        * {
            font-family: shabnam;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        .bg-primary {
            background: var(--primary-color) !important;
        }
        .bg-c1 {
            background: var(--primary-color) !important;
        }
        .color-c1 {
            color: var(--primary-color) !important;
        }
    </style>

    {{-- Page Specific Head Content --}}
    @stack('head')
</head>

<body id="body" class="rtl" itemscope itemtype="https://schema.org/WebPage">
    @if(explode("/",request()->path())[0]!="admin")
        <header>
            <div style="background-image: url('{{asset("storage/".getConfigs("bannerSeasonImage"))}}');background-repeat:no-repeat;padding-bottom: 8px;">
                <div class="container">
                    <div class="row">
                        @include("partials.header")
                    </div>
                </div>
            </div>
            <div class="bg-primary text-white">
                <div id="header-banner p-0" style="padding: 0!important;margin-top: 0!important;font-size: 14px!important;" class="col-12">
                    <div class="container p-0">
                        <p class="banner-text">
                            <i class="fa fa-bullhorn"></i>
                            <span class="animated-text">
                                {{getConfigs("mainBannerText")}}
                            </span>
                            <a href="/login" class="banner-btn">شروع میزبانی</a>
                        </p>
                    </div>
                </div>
            </div>
        </header>
        
        @if(getConfigs("websiteStatus")==1)
            <div class="container">
                <main itemprop="mainContentOfPage">
                    @yield("content")
                </main>
            </div>
        @else
            <div class="container">
                <main>
                    <div style="text-align: center; padding: 50px;">
                        <h1>درحال تعمیرات</h1>
                        <p>{{getConfigs("OfflineModeText")}}</p>
                        <br><br>
                        <img src="{{asset("storage/".getConfigs("offlineModeIcon"))}}"
                             style="max-width: 300px;min-width: 200px"
                             alt="سایت در حال تعمیرات">
                        <br><br><br>
                    </div>
                </main>
            </div>
        @endif
        
        <footer>
            @include("partials.footer")
        </footer>
    @else
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler-rtl.min.css">
        @vite(['resources/css/template/admin-app.less'])
        @include("partials.admin-sidebar")
        <div class="main-content">
            @yield("content")
        </div>
    @endif
    
    @livewireScripts
    
    {{-- Additional Scripts --}}
    @stack('scripts')
    
    {{-- Dynamic Schema for Current Page --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dynamicSchema = {
            "@context": "https://schema.org",
            "@type": "WebPage",
            "name": document.title,
            "description": "{{ $description }}",
            "url": "{{ $url }}",
            "datePublished": "{{ now()->toIso8601String() }}",
            "dateModified": "{{ now()->toIso8601String() }}",
            "publisher": {
                "@type": "Organization",
                "name": "{{ getConfigs('website-title') }}",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ url('storage/injaa_iconInput_1761765907.png') }}"
                }
            }
        };
        
        const script = document.createElement('script');
        script.type = 'application/ld+json';
        script.textContent = JSON.stringify(dynamicSchema);
        document.head.appendChild(script);
    });
    </script>
</body>
</html>