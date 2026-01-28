<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" class="h-100">
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="{{ asset('icons/font-awesome/css/all.min.css') }}" rel="stylesheet">

    <!-- Template Styles -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Custom Auth Login CSS -->
    <link href="{{ asset('css/auth-login.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="auth-travel">

    <!-- Left Side - Travel Image -->
    <div class="auth-travel-image">
        <div class="travel-overlay-content">
            <!-- Travel Icon -->
            <div class="travel-icon">
                <i class="fas fa-plane-departure"></i>
            </div>

            <!-- Title -->
            <h1 class="travel-title">
                @if(app()->getLocale() == 'ar')
                    اكتشف العالم معنا
                @else
                    Discover The World With Us
                @endif
            </h1>

            <!-- Tagline -->
            <p class="travel-tagline">
                @if(app()->getLocale() == 'ar')
                    نوفر لك أفضل تجارب السفر والسياحة حول العالم. <br>
                    احجز رحلتك المقبلة بكل سهولة ويسر.
                @else
                    We provide you with the best travel and tourism experiences around the world. <br>
                    Book your next trip easily and conveniently.
                @endif
            </p>

            <!-- Features -->
            <div class="travel-features">
                <div class="feature-item">
                    <i class="fas fa-hotel"></i>
                    <span>{{ __('Hotels') }}</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-plane"></i>
                    <span>{{ __('Flights') }}</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-suitcase-rolling"></i>
                    <span>{{ __('Trips') }}</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-headset"></i>
                    <span>{{ __('Support') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Form -->
    <div class="auth-travel-form">
        <!-- Decorative Elements -->
        <div class="auth-decoration plane">
            <i class="fas fa-plane"></i>
        </div>
        <div class="auth-decoration globe">
            <i class="fas fa-globe-americas"></i>
        </div>

        <div class="auth-form-container">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Custom Auth Login JS -->
    <script src="{{ asset('js/auth-login.js') }}"></script>

    @stack('scripts')
</body>
</html>
