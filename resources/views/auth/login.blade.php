@extends('layouts.auth')

@section('title', __('auth.login_title') ?? 'تسجيل الدخول')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <!-- Logo -->
        <div class="auth-logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="{{ config('app.name') }}">
            </a>
        </div>

        <h1>{{ __('auth.welcome_back') ?? 'مرحباً بك مجدداً' }}</h1>
        <p>{{ __('auth.login_subtitle') ?? 'لوحة الإدارة - وجهتك للسياحة' }}</p>
    </div>

    <!-- Success Message -->
    @if (session('status'))
        <div class="alert-status alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('status') }}
        </div>
    @endif

    <!-- Form Errors -->
    @if ($errors->any())
        <div class="alert-status alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form" id="loginForm">
        @csrf

        <!-- Email -->
        <div class="form-group">
            <label for="email">{{ __('auth.email') ?? 'البريد الإلكتروني' }}</label>
            <div class="input-container">
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    placeholder="name@company.com"
                >
                <i class="fas fa-envelope icon"></i>
            </div>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">{{ __('auth.password_label') ?? 'كلمة المرور' }}</label>
            <div class="input-container">
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    required
                    placeholder="••••••••"
                >
                <i class="fas fa-lock icon"></i>
                <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility()">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </button>
            </div>
        </div>

        <!-- Options -->
        <div class="form-options">
            <label class="checkbox-container">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <span>{{ __('auth.remember_me') ?? 'تذكرني' }}</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-link">
                    {{ __('auth.forgot_password') ?? 'نسيت كلمة المرور؟' }}
                </a>
            @endif
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-auth" id="submitBtn">
            <span>{{ __('auth.sign_in') ?? 'تسجيل الدخول' }}</span>
            <i class="fas fa-arrow-right"></i>
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    // Add loading state on submit
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const isAr = document.documentElement.dir === 'rtl';
        btn.disabled = true;
        btn.innerHTML = `
            <span>${isAr ? 'جاري التحميل...' : 'Please wait...'}</span>
            <i class="fas fa-circle-notch fa-spin"></i>
        `;
    });
</script>
@endsection
