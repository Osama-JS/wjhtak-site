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

            @endphp
            <a href="{{ route('lang.switch', $switchLocale) }}" class="lang-switch" title="{{ __('Switch Language') }}">
                 <div class="globe-icon-wrapper">
                    <i class="fas fa-globe"></i>
                </div>
                <span class="lang-text-label">{{ $switchLabel }}</span>
            </a>

            {{-- Search Button --}}
            <button type="button" class="btn-search-trigger nav-action-btn" onclick="toggleSearchModal()" id="searchToggle" aria-label="{{ __('Search') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
            </button>

            {{-- User Menu --}}
            @auth
                @php $authUser = auth()->user(); @endphp
                <div class="user-dropdown-wrap" style="position:relative;">
                    <button class="user-avatar-btn" id="userDropdownToggle"
                        style="width:36px;height:36px;border-radius:50%;border:2px solid var(--accent-color,#e8532e);background:#fff;cursor:pointer;overflow:hidden;padding:0;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;color:var(--accent-color,#e8532e);"
                        aria-label="{{ __('My Account') }}">
                        @if($authUser->profile_photo_path)
                            <img src="{{ asset('storage/' . $authUser->profile_photo_path) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                        @else
                            {{ strtoupper(substr($authUser->first_name ?? $authUser->name ?? 'U', 0, 1)) }}
                        @endif
                    </button>
                    <div id="userDropdownMenu" style="display:none;position:absolute;top:calc(100% + 10px);inset-inline-end:0;min-width:220px;background:#fff;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,.15);z-index:2000;overflow:hidden;border:1px solid rgba(0,0,0,.07);">
                        <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;">
                            <div style="font-weight:700;font-size:.9rem;color:#111827;">{{ $authUser->full_name }}</div>
                            <div style="font-size:.75rem;color:#9ca3af;">{{ $authUser->email }}</div>
                        </div>
                        @if($authUser->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" style="display:flex;align-items:center;gap:10px;padding:11px 16px;text-decoration:none;color:#374151;font-size:.85rem;font-weight:600;transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                                <i class="fas fa-tachometer-alt" style="color:#6b7280;width:16px;"></i> {{ __('Admin Panel') }}
                            </a>
                        @else
                            <a href="{{ route('customer.dashboard') }}" style="display:flex;align-items:center;gap:10px;padding:11px 16px;text-decoration:none;color:#374151;font-size:.85rem;font-weight:600;transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                                <i class="fas fa-th-large" style="color:#6b7280;width:16px;"></i> {{ __('Dashboard') }}
                            </a>
                            <a href="{{ route('customer.bookings.index') }}" style="display:flex;align-items:center;gap:10px;padding:11px 16px;text-decoration:none;color:#374151;font-size:.85rem;font-weight:600;transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                                <i class="fas fa-ticket-alt" style="color:#6b7280;width:16px;"></i> {{ __('My Bookings') }}
                            </a>
                            <a href="{{ route('customer.favorites.index') }}" style="display:flex;align-items:center;gap:10px;padding:11px 16px;text-decoration:none;color:#374151;font-size:.85rem;font-weight:600;transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                                <i class="fas fa-heart" style="color:#6b7280;width:16px;"></i> {{ __('Favorites') }}
                            </a>
                            <a href="{{ route('customer.profile') }}" style="display:flex;align-items:center;gap:10px;padding:11px 16px;text-decoration:none;color:#374151;font-size:.85rem;font-weight:600;transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                                <i class="fas fa-user-cog" style="color:#6b7280;width:16px;"></i> {{ __('My Profile') }}
                            </a>
                        @endif
                        <div style="border-top:1px solid #f3f4f6;">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" style="display:flex;align-items:center;gap:10px;width:100%;padding:11px 16px;border:none;background:transparent;cursor:pointer;color:#ef4444;font-size:.85rem;font-weight:600;text-align:start;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background=''">
                                    <i class="fas fa-sign-out-alt" style="width:16px;"></i> {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-sm" style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;border:1.5px solid var(--accent-color,#e8532e);border-radius:8px;color:var(--accent-color,#e8532e);text-decoration:none;font-weight:700;font-size:.82rem;transition:all .2s;" onmouseover="this.style.background='var(--accent-color,#e8532e)';this.style.color='#fff'" onmouseout="this.style.background='';this.style.color='var(--accent-color,#e8532e)'">
                    <i class="fas fa-sign-in-alt"></i> {{ __('Login') }}
                </a>
                <a href="{{ route('register') }}" class="btn btn-accent-sm" style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;background:var(--accent-color,#e8532e);border-radius:8px;color:#fff;text-decoration:none;font-weight:700;font-size:.82rem;transition:all .2s;" onmouseover="this.style.background='#c0392b'" onmouseout="this.style.background='var(--accent-color,#e8532e)'">
                    <i class="fas fa-user-plus"></i> {{ __('Register') }}
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
