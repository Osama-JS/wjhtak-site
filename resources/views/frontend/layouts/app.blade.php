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
    @php
        $siteFavicon = \App\Models\Setting::get('site_favicon');
    @endphp
    @if($siteFavicon)
        <link rel="icon" type="image/x-icon" href="{{ asset($siteFavicon) }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset($siteFavicon) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    @endif

    {{-- Preconnect to external resources --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- CSS Files --}}
    <link rel="stylesheet" href="{{ asset('css/frontend/app.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/frontend/components.css') }}?v={{ time() }}">
    <link href="{{ asset('icons/font-awesome/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @if(app()->getLocale() === 'ar')
    <link rel="stylesheet" href="{{ asset('css/frontend/rtl.css') }}">
    <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/material_orange.css">
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

        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px; /* Default LTR */
            z-index: 999;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--color-primary, #3b4bd3);
            color: #fff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
        }

        [dir="rtl"] .back-to-top {
            right: auto;
            left: 30px;
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .back-to-top:hover {
            background: var(--color-primary-dark, #2a3aa0);
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(59, 75, 211, 0.4);
        }

        /* Footer Crystal Animation */
        .footer {
            position: relative;
            overflow: hidden;
            background: #1a1a1a; /* Fallback */
            z-index: 1;
        }

        .footer::before, .footer::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(59, 75, 211, 0.2), rgba(255, 255, 255, 0.05));
            filter: blur(60px);
            z-index: -1;
            animation: floatCrystals 15s infinite ease-in-out alternate;
        }

        .footer::before {
            top: -100px;
            left: -100px;
        }

        .footer::after {
            bottom: -50px;
            right: -50px;
            animation-delay: -7s;
            background: linear-gradient(45deg, rgba(255, 0, 150, 0.15), rgba(59, 75, 211, 0.1));
        }
        .search-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.8);
            backdrop-filter: blur(5px);
        }

        .search-modal-content {
            background: white;
            margin: 5% auto;
            padding: 20px;
            width: 90%;
            max-width: 800px;
            border-radius: 15px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .search-box-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        #searchInput {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #eee;
            border-radius: 10px;
            font-size: 18px;
        }

        .search-results-container {
            margin-top: 20px;
        }
        :root {
            --primary-color: #007bff;
            --bg-light: #f8f9fa;
        }

        .search-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: 10000;
            padding: 20px;
            animation: fadeIn 0.3s ease;
        }

        .search-container {
            max-width: 700px;
            margin: 0 auto;
            width: 100%;
        }

        .search-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
        }

        .search-input-group {
            position: relative;
            flex: 1;
            display: flex;
            align-items: center;
            background: var(--bg-light);
            border-radius: 15px;
            padding: 12px 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .search-icon { color: #888; margin-right: 15px; }

        #searchInput {
            border: none;
            background: transparent;
            width: 100%;
            font-size: 1.1rem;
            outline: none;
        }

        .close-search {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #444;
            cursor: pointer;
            padding: 5px;
        }

        .search-body {
            margin-top: 30px;
            max-height: 70vh;
            overflow-y: auto;
        }

        /* تنسيق النتائج */
        .search-result-card {
            display: flex;
            align-items: center;
            padding: 12px;
            background: #fff;
            border-radius: 12px;
            margin-bottom: 12px;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .search-result-card:hover {
            transform: translateX(10px); /* للعربي استخدم -10px */
            border-color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .result-img {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            object-fit: cover;
            margin-right: 15px;
        }

        /* تنسيقات الجوال */
        @media (max-width: 600px) {
            .search-overlay { padding: 10px; }
            .search-header { margin-top: 10px; }
            .result-img { width: 55px; height: 55px; }
        }

        /* Spinner للحمل */
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

                @keyframes floatCrystals {
                    0% { transform: translate(0, 0) rotate(0deg); }
                    50% { transform: translate(50px, 50px) rotate(180deg); }
                    100% { transform: translate(-30px, 20px) rotate(360deg); }
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

    {{-- serach --}}
    <!-- <div id="searchModal" class="search-modal">
        <div class="search-modal-content">
            <div class="search-modal-header">
                <h3>{{ __('Search Trips') }}</h3>
                <button class="close-modal" onclick="toggleSearchModal()">&times;</button>
            </div>

            <div class="search-modal-body">
                <div class="search-box-wrapper">
                    <input type="text" id="searchInput" placeholder="{{ __('Search trips, destinations...') }}" autocomplete="off">
                    <div id="searchLoader" class="loader" style="display: none;"></div>
                </div>

                <div id="searchResults" class="search-results-container">
                    <p class="text-muted text-center">{{ __('Start typing to see results...') }}</p>
                </div>
            </div>
        </div>
    </div> -->
    <div id="searchModal" class="search-overlay">
        <div class="search-container">
            <div class="search-header">
                <div class="search-input-group">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" placeholder="{{ __('Where to next?') }}" autocomplete="off">
                    <div id="searchLoader" class="spinner" style="display: none;"></div>
                </div>
                <button class="close-search" onclick="toggleSearchModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="search-body">
                <div id="searchResults">
                    <div class="recent-searches">
                        <h5>{{ __('Suggested Destinations') }}</h5>
                        <div class="tags">
                            <span class="tag">Egypt</span>
                            <span class="tag">Dubai</span>
                            <span class="tag">Saudi Arabia</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript Files --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @if(app()->getLocale() === 'ar')
    <script src="https://npmcdn.com/flatpickr/dist/l10n/ar.js"></script>
    @endif
    <script src="{{ asset('js/frontend/app.js') }}"></script>
    <script src="{{ asset('js/frontend/slider.js') }}"></script>

    {{-- Page-specific JavaScript --}}
    @stack('scripts')

    {{-- Global Initializations --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Flatpickr Initialization
            flatpickr(".date-picker", {
                dateFormat: "Y-m-d",
                minDate: "today",
                locale: "{{ app()->getLocale() == 'ar' ? 'ar' : 'default' }}",
                disableMobile: "true",
                animate: true
            });

            // Custom Premium Dropdowns Logic
            const dropdowns = document.querySelectorAll('.custom-dropdown');

            dropdowns.forEach(dropdown => {
                const trigger = dropdown.querySelector('.dropdown-trigger');
                const menu = dropdown.querySelector('.dropdown-menu-premium');
                const options = dropdown.querySelectorAll('.dropdown-option');
                const hiddenInput = dropdown.querySelector('.hidden-input');
                const selectedText = dropdown.querySelector('.selected-value');
                const searchInput = dropdown.querySelector('.dropdown-search-input');

                // Toggle Dropdown
                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();

                    // Close other dropdowns
                    dropdowns.forEach(d => {
                        if (d !== dropdown) d.classList.remove('active');
                    });

                    dropdown.classList.toggle('active');

                    // Focus search if exists
                    if (dropdown.classList.contains('active') && searchInput) {
                        setTimeout(() => searchInput.focus(), 100);
                    }
                });

                // Selection Logic
                options.forEach(option => {
                    option.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const value = option.dataset.value;
                        const text = option.textContent.trim();

                        // Update hidden input and UI
                        hiddenInput.value = value;
                        selectedText.textContent = text;

                        // Update active state in list
                        options.forEach(opt => opt.classList.remove('active'));
                        option.classList.add('active');

                        // Close dropdown
                        dropdown.classList.remove('active');
                    });
                });

                // Search Filter for Destination
                if (searchInput) {
                    searchInput.addEventListener('input', (e) => {
                        const term = e.target.value.toLowerCase();
                        options.forEach(option => {
                            const text = option.textContent.toLowerCase();
                            option.style.display = text.includes(term) ? 'block' : 'none';
                        });
                    });

                    // Prevent closing when clicking search
                    searchInput.addEventListener('click', e => e.stopPropagation());
                }
            });

            // Close on outside click
            document.addEventListener('click', () => {
                dropdowns.forEach(d => d.classList.remove('active'));
            });
        });
    </script>

    {{-- Hide loader when page is ready --}}
    <script>
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');
            document.getElementById('pageLoader').classList.add('hidden');
        });
    </script>
    <script>
        let searchTimeout = null;

        function toggleSearchModal() {
            const modal = document.getElementById('searchModal');
            const isVisible = modal.style.display === 'block';
            modal.style.display = isVisible ? 'none' : 'block';
            if(!isVisible) document.getElementById('searchInput').focus();
        }

        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value;
            const resultsContainer = document.getElementById('searchResults');
            const loader = document.getElementById('searchLoader');

            clearTimeout(searchTimeout);

            if (query.length < 2) {
                resultsContainer.innerHTML = '<p class="text-center text-muted">Start typing to explore...</p>';
                return;
            }

            loader.style.display = 'block';

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('searchModel') }}?q=${encodeURIComponent(query)}`, {
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                })
                .then(response => response.text())
                .then(html => {
                    resultsContainer.innerHTML = html;
                    loader.style.display = 'none';
                })
                .catch(err => {
                    console.error(err);
                    loader.style.display = 'none';
                });
            }, 400); // ينتظر 400 مللي ثانية بعد توقف المستخدم عن الكتابة
        });
    </script>
</body>
</html>
