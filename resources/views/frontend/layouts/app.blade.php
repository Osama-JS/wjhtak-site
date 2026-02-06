<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <title>@yield('title', config('app.name', 'Wjhtak')) - {{ __('Tourism Platform') }}</title>
    <meta name="description" content="@yield('meta_description', __('Discover amazing travel destinations and book your dream vacation with Wjhtak - Your trusted tourism partner'))">
    <meta name="keywords" content="@yield('meta_keywords', __('travel, tourism, vacation, trips, destinations, booking'))">
    <meta name="author" content="Wjhtak Tourism">

    {{-- Open Graph --}}
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', __('Discover amazing travel destinations'))">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', config('app.name'))">
    <meta name="twitter:description" content="@yield('og_description', __('Discover amazing travel destinations'))">

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">

    {{-- Preconnect to external resources --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- CSS Files --}}
    <link rel="stylesheet" href="{{ asset('css/frontend/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/frontend/components.css') }}">
    @if(app()->getLocale() === 'ar')
    <link rel="stylesheet" href="{{ asset('css/frontend/rtl.css') }}">
    @endif

    {{-- Page-specific CSS --}}
    @stack('styles')

    {{-- Inline critical CSS for above-the-fold content --}}
    <style>
        /* Preloader */
        .page-loader {
            position: fixed;
            inset: 0;
            background: var(--color-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .page-loader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loader-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid var(--color-border);
            border-top-color: var(--color-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Hide content until loaded */
        body:not(.loaded) .page-content {
            opacity: 0;
        }

        body.loaded .page-content {
            opacity: 1;
            transition: opacity 0.3s ease;
        }
    </style>
</head>
<body>
    {{-- Page Loader --}}
    <div class="page-loader" id="pageLoader">
        <div class="loader-spinner"></div>
    </div>

    {{-- Mobile Menu Overlay --}}
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    {{-- Mobile Menu --}}
    @include('frontend.partials.mobile-menu')

    {{-- Header/Navbar --}}
    @include('frontend.partials.header')

    {{-- Main Content --}}
    <main class="page-content">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('frontend.partials.footer')

    {{-- Back to Top Button --}}
    <button class="back-to-top" id="backToTop" aria-label="{{ __('Back to top') }}">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </button>

    {{-- JavaScript Files --}}
    <script src="{{ asset('js/frontend/app.js') }}"></script>
    <script src="{{ asset('js/frontend/slider.js') }}"></script>

    {{-- Page-specific JavaScript --}}
    @stack('scripts')

    {{-- Hide loader when page is ready --}}
    <script>
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');
            document.getElementById('pageLoader').classList.add('hidden');
        });
    </script>
</body>
</html>
