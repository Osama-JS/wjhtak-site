@extends('layouts.auth')

@section('title', __('auth.login_title'))

@section('content')
<!-- Logo -->
<div class="auth-logo">
    <a href="{{ url('/') }}">
        <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="{{ config('app.name') }}">
    </a>
</div>

<!-- Welcome Text -->
<div class="auth-welcome">
    <h2>{{ __('auth.welcome_back') ?? 'مرحباً بعودتك' }}</h2>
    <p>{{ __('auth.login_subtitle') ?? 'قم بتسجيل الدخول للوصول إلى لوحة التحكم' }}</p>
</div>

<!-- Login Card -->
<div class="auth-card">
    <!-- Status Message -->
    @if (session('status'))
        <div class="auth-success">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('status') }}
        </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <!-- Email Input -->
        <div class="auth-input-group">
            <label for="email">{{ __('auth.email') }}</label>
            <input
                type="email"
                id="email"
                name="email"
                class="@error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                required
                autofocus
                placeholder="example@email.com"
            >
            <i class="fas fa-envelope auth-input-icon"></i>
            @error('email')
                <div class="auth-error mt-2">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password Input -->
        <div class="auth-input-group">
            <label for="password">{{ __('auth.password_label') }}</label>
            <input
                type="password"
                id="password"
                name="password"
                class="@error('password') is-invalid @enderror"
                required
                placeholder="••••••••"
            >
            <i class="fas fa-lock auth-input-icon"></i>
            <span class="password-toggle">
                <i class="fas fa-eye"></i>
            </span>
            @error('password')
                <div class="auth-error mt-2">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="auth-remember">
            <div class="remember-checkbox">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">{{ __('auth.remember_me') ?? 'تذكرني' }}</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-password">
                    {{ __('auth.forgot_password') ?? 'نسيت كلمة المرور؟' }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="auth-submit-btn">
            <i class="fas fa-sign-in-alt me-2"></i>
            {{ __('auth.sign_in') }}
        </button>
    </form>

    <!-- Language Switcher -->
    <div class="auth-lang-switcher">
        @if(app()->getLocale() == 'ar')
            <a href="{{ route('lang.switch', 'en') }}" class="lang-btn">
                <i class="fas fa-globe"></i>
                English
            </a>
        @else
            <a href="{{ route('lang.switch', 'ar') }}" class="lang-btn">
                <i class="fas fa-globe"></i>
                العربية
            </a>
        @endif
    </div>
</div>
@endsection
