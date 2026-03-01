<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Agent Dashboard')) - {{ config('app.name') }}</title>

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/frontend/app.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/frontend/components.css') }}?v={{ time() }}">
    <link href="{{ asset('icons/font-awesome/css/all.min.css') }}" rel="stylesheet">
    @if(app()->getLocale() === 'ar')
    <link rel="stylesheet" href="{{ asset('css/frontend/rtl.css') }}">
    @endif

    @stack('styles')

    {{-- Reusing the same styles as customer dashboard for consistency --}}
    <style>
    body { margin: 0; background: #f8fafc; min-height: 100vh; color: #1e293b; font-family: 'Tajawal', sans-serif; }
    .cdash-wrapper { display: flex; min-height: 100vh; }
    .cdash-sidebar { width: 260px; min-height: 100vh; background: #fff; display: flex; flex-direction: column; position: fixed; top: 0; bottom: 0; z-index: 100; transition: transform .3s ease; border-inline-end: 1px solid #e2e8f0; box-shadow: 4px 0 24px rgba(0,0,0,0.02); }
    html[dir="ltr"] .cdash-sidebar { left: 0; } html[dir="rtl"] .cdash-sidebar { right: 0; }
    .cdash-sidebar-brand { padding: 30px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: center; }
    .cdash-sidebar-brand img { height: 48px; object-fit: contain; }
    .cdash-sidebar-user { padding: 24px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 12px; }
    .cdash-sidebar-user img { width: 48px; height: 48px; border-radius: 12px; object-fit: cover; border: 2px solid #f1f5f9; }
    .cdash-sidebar-user .user-info .user-name { color: #1e293b; font-weight: 700; font-size: .95rem; line-height: 1.2; }
    .cdash-sidebar-user .user-info .user-type { color: #64748b; font-size: .8rem; }
    .cdash-nav { flex: 1; padding: 16px 0; overflow-y: auto; }
    .cdash-nav-label { color: #94a3b8; font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; padding: 20px 24px 8px; font-weight: 700; }
    .cdash-nav-item a { display: flex; align-items: center; gap: 12px; padding: 12px 24px; color: #475569; text-decoration: none; font-size: .95rem; font-weight: 500; transition: all .2s; }
    .cdash-nav-item a:hover { background: #f8fafc; color: #0f172a; }
    .cdash-nav-item a.active { background: var(--accent-color, #e8532e); color: #fff; }
    html[dir="ltr"] .cdash-nav-item a.active { border-radius: 0 8px 8px 0; }
    html[dir="rtl"] .cdash-nav-item a.active { border-radius: 8px 0 0 8px; }
    .cdash-nav-item a i { width: 20px; text-align: center; font-size: 1rem; color: #64748b; }
    .cdash-nav-item a.active i { color: #fff; }
    .cdash-sidebar-footer { padding: 20px; border-top: 1px solid #f1f5f9; }
    .cdash-sidebar-footer form button { width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; color: #ef4444; padding: 12px 16px; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; font-size: .9rem; font-weight: 700; transition: all .2s; }
    .cdash-sidebar-footer form button:hover { background: #fef2f2; border-color: #fca5a5; }
    .cdash-main { flex: 1; display: flex; flex-direction: column; min-height: 100vh; transition: margin .3s; }
    html[dir="ltr"] .cdash-main { margin-left: 260px; }
    html[dir="rtl"] .cdash-main { margin-right: 260px; }
    .cdash-topbar { background: #fff; border-bottom: 1px solid #e5e7eb; padding: 14px 28px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 50; }
    .cdash-topbar-title { font-weight: 700; font-size: 1.1rem; color: #1a2537; }
    .cdash-topbar-actions { display: flex; align-items: center; gap: 12px; }
    .cdash-topbar-link { color: #6b7280; text-decoration: none; font-size: .88rem; transition: color .2s; }
    .cdash-topbar-link:hover { color: var(--accent-color, #e8532e); }
    .cdash-burger { display: none; background: none; border: none; font-size: 1.3rem; cursor: pointer; color: #1a2537; }
    .cdash-content { flex: 1; padding: 28px; }
    .cdash-flash { padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; font-size: .9rem; }
    .cdash-flash-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
    .cdash-flash-error   { background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; }
    .cdash-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 99; }
    @media (max-width: 768px) {
        .cdash-sidebar { transform: translateX(-100%); }
        html[dir="rtl"] .cdash-sidebar { transform: translateX(100%); }
        .cdash-sidebar.open { transform: translateX(0) !important; }
        html[dir="ltr"] .cdash-main { margin-left: 0; }
        html[dir="rtl"] .cdash-main { margin-right: 0; }
        .cdash-burger { display: block; }
        .cdash-content { padding: 18px; }
        .cdash-overlay.visible { display: block; }
    }
    </style>
</head>
<body>
<div class="cdash-overlay" id="cdashOverlay" onclick="closeSidebar()"></div>

<div class="cdash-wrapper">
    <aside class="cdash-sidebar" id="cdashSidebar">
        <div class="cdash-sidebar-brand">
            <a href="{{ url('/') }}">
                <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="{{ config('app.name') }}">
            </a>
        </div>
        <div class="cdash-sidebar-user">
            <div style="position: relative;">
                <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->full_name }}">
                @if(auth()->user()->company && auth()->user()->company->logo)
                    <img src="{{ auth()->user()->company->logo_url }}"
                         style="width: 20px; height: 20px; border-radius: 50%; position: absolute; bottom: -2px; right: -2px; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);"
                         alt="{{ auth()->user()->company->name }}">
                @endif
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->full_name }}</div>
                <div class="user-type">{{ __('Agent') }} @if(auth()->user()->company) ({{ auth()->user()->company->localized_name }}) @endif</div>
            </div>
        </div>
        <nav class="cdash-nav">
            <div class="cdash-nav-label">{{ __('Main Menu') }}</div>
            <div class="cdash-nav-item">
                <a href="{{ route('agent.dashboard') }}" class="{{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> <span>{{ __('Dashboard') }}</span>
                </a>
            </div>
            <div class="cdash-nav-item">
                <a href="{{ route('agent.trips.index') }}" class="{{ request()->routeIs('agent.trips.*') ? 'active' : '' }}">
                    <i class="fas fa-map-marked-alt"></i> <span>{{ __('My Trips') }}</span>
                </a>
            </div>


            <div class="cdash-nav-item">
                <a href="{{ route('agent.bookings.index') }}" class="{{ request()->routeIs('agent.bookings.*') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt"></i> <span>{{ __('Bookings') }}</span>
                </a>
            </div>
            <div class="cdash-nav-item">
                <a href="{{ route('agent.favorites.index') }}" class="{{ request()->routeIs('agent.favorites.*') ? 'active' : '' }}">
                    <i class="fas fa-heart"></i> <span>{{ __('My Favorites') }}</span>
                </a>
            </div>
            <div class="cdash-nav-item">
                <a href="{{ route('agent.profile.index') }}" class="{{ request()->routeIs('agent.profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i> <span>{{ __('My Profile') }}</span>
                </a>
            </div>
        </nav>
        <div class="cdash-sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ __('Logout') }}</span>
                </button>
            </form>
        </div>
    </aside>
    <main class="cdash-main">
        <div class="cdash-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="cdash-burger" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="cdash-topbar-title">@yield('page-title', __('Dashboard'))</span>
            </div>
            <div class="cdash-topbar-actions">
                {{-- Language Switcher --}}
                <div class="dropdown" style="position: relative; display: inline-block;">
                    <a href="#" class="cdash-topbar-link" onclick="event.preventDefault(); this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'block' ? 'none' : 'block'">
                        <i class="fas fa-globe"></i> {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                    </a>
                    <div class="cdash-lang-dropdown" style="display: none; position: absolute; top: 100%; inset-inline-end: 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); min-width: 120px; z-index: 1000; margin-top: 8px;">
                        <a href="{{ route('lang.switch', 'ar') }}" style="display: block; padding: 10px 16px; color: #1e293b; text-decoration: none; font-size: .85rem; @if(app()->getLocale() === 'ar') background: #f8fafc; font-weight: 700; @endif border-bottom: 1px solid #f1f5f9;">العربية</a>
                        <a href="{{ route('lang.switch', 'en') }}" style="display: block; padding: 10px 16px; color: #1e293b; text-decoration: none; font-size: .85rem; @if(app()->getLocale() === 'en') background: #f8fafc; font-weight: 700; @endif">English</a>
                    </div>
                </div>

                <a href="{{ url('/') }}" class="cdash-topbar-link">
                    <i class="fas fa-home"></i> {{ __('Site') }}
                </a>
                <a href="{{ route('trips.index') }}" class="cdash-topbar-link">
                    <i class="fas fa-map-marked-alt"></i> {{ __('Trips') }}
                </a>
            </div>
        </div>
        <div class="cdash-content">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="cdash-flash cdash-flash-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="cdash-flash cdash-flash-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>

<script>
function toggleSidebar() {
    document.getElementById('cdashSidebar').classList.toggle('open');
    document.getElementById('cdashOverlay').classList.toggle('visible');
}

function closeSidebar() {
    document.getElementById('cdashSidebar').classList.remove('open');
    document.getElementById('cdashOverlay').classList.remove('visible');
}
</script>
@stack('scripts')
</body>
</html>
