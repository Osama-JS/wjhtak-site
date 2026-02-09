{{-- Header/Navbar Component --}}
@php
    $siteLogo = \App\Models\Setting::get('site_logo');
    $siteName = app()->getLocale() === 'ar'
        ? \App\Models\Setting::get('site_name_ar', 'وجهتك')
        : \App\Models\Setting::get('site_name_en', 'Wjhtak');
@endphp
<header class="navbar" id="navbar">
    <div class="container navbar-container">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="navbar-logo">
            @if($siteLogo)
                <img src="{{ asset($siteLogo) }}" alt="{{ $siteName }}" onerror="this.src='{{ asset('images/logo-full.png') }}'">
            @else
                <img src="{{ asset('images/logo-full.png') }}" alt="{{ $siteName }}">
            @endif
            <span class="navbar-logo-text">{{ $siteName }}</span>
        </a>

        {{-- Navigation Links --}}
        <nav class="nav-links">
            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                {{ __('Home') }}
            </a>
            <a href="{{ route('trips.index') }}" class="nav-link {{ request()->routeIs('trips.*') ? 'active' : '' }}">
                {{ __('Trips') }}
            </a>
            <a href="{{ route('destinations') }}" class="nav-link {{ request()->routeIs('destinations') ? 'active' : '' }}">
                {{ __('Destinations') }}
            </a>
            <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
                {{ __('About Us') }}
            </a>
            <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                {{ __('Contact') }}
            </a>
        </nav>

        {{-- Actions --}}
        <div class="nav-actions">
            {{-- Language Switcher --}}
            @php
                $currentLocale = app()->getLocale();
                $switchLocale = $currentLocale === 'ar' ? 'en' : 'ar';
                $switchLabel = $currentLocale === 'ar' ? 'EN' : 'ع';
                $switchFlag = $currentLocale === 'ar' ? 'us' : 'sa';
            @endphp
            <a href="{{ route('lang.switch', $switchLocale) }}" class="lang-switch" title="{{ __('Switch Language') }}">
                <img src="{{ asset('images/flags/' . $switchFlag . '.svg') }}" alt="" class="lang-flag">
                <span>{{ $switchLabel }}</span>
            </a>

            {{-- Search Button --}}
            <button class="nav-action-btn" id="searchToggle" aria-label="{{ __('Search') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
            </button>

            {{-- User Menu --}}
            @auth
                <a href="{{ route('customer.dashboard') }}" class="nav-action-btn" aria-label="{{ __('My Account') }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-accent btn-sm">
                    {{ __('Login') }}
                </a>
            @endauth

            {{-- Mobile Menu Toggle --}}
            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="{{ __('Menu') }}">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>
