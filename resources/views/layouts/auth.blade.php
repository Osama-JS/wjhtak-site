<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="WJHTAK - Travel & Tourism Admin Dashboard">

    <title>@yield('title', 'Login') - {{ config('app.name', 'WJHTAK') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset(\App\Models\Setting::get('site_favicon', 'images/favicon.png')) }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="{{ asset('icons/font-awesome/css/all.min.css') }}" rel="stylesheet">

    <!-- Custom Auth Login CSS -->
    <link href="{{ asset('css/auth-login.css?v=' . time()) }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="auth-travel">

    <!-- Fixed Background -->
    <div class="auth-background"></div>

    <!-- Abstract Shapes -->
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <!-- Centered Container -->
    <div class="auth-container">
        @yield('content')

        <!-- Optional Footer with Language Switcher -->
        <div class="auth-footer">
            @if(app()->getLocale() == 'ar')
                <a href="{{ route('lang.switch', 'en') }}" class="lang-switch-btn">
                    <i class="fas fa-globe"></i>
                    English Version
                </a>
            @else
                <a href="{{ route('lang.switch', 'ar') }}" class="lang-switch-btn">
                    <i class="fas fa-globe"></i>
                    النسخة العربية
                </a>
            @endif
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/global/global.min.js') }}"></script>

    <!-- Custom Auth Login JS -->
    <script src="{{ asset('js/auth-login.js') }}"></script>

    @stack('scripts')
</body>
</html>
