@extends('frontend.layouts.app')

@section('title', __('تسجيل الدخول'))

@push('styles')
<style>
/* ─── Auth Page Styles ─── */
.auth-page-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 80px var(--space-4);
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    font-family: 'Tajawal', sans-serif !important;
}

.auth-card * {
    font-family: 'Tajawal', sans-serif !important;
}

.auth-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,.12);
    width: 100%;
    max-width: 460px;
    padding: var(--space-10) var(--space-8);
    animation: fadeInUp .4s ease;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

.auth-card .auth-logo {
    text-align: center;
    margin-bottom: var(--space-6);
}

.auth-card .auth-logo img {
    height: 60px;
    object-fit: contain;
}

.auth-title {
    font-size: 1.6rem;
    font-weight: 700;
    text-align: center;
    color: var(--text-primary, #1a2537);
    margin-bottom: .3rem;
}

.auth-subtitle {
    text-align: center;
    color: var(--text-muted, #6b7280);
    font-size: .9rem;
    margin-bottom: var(--space-6);
}

.auth-input-group {
    margin-bottom: var(--space-4);
}

.auth-input-group label {
    display: block;
    font-weight: 600;
    font-size: .85rem;
    margin-bottom: .4rem;
    color: var(--text-secondary, #374151);
}

.auth-input-group .input-wrap {
    position: relative;
}

.auth-input-group .input-wrap i.field-icon {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: .9rem;
}

html[dir="ltr"] .auth-input-group .input-wrap i.field-icon { left: 14px; }
html[dir="rtl"] .auth-input-group .input-wrap i.field-icon { right: 14px; }

.auth-input-group input {
    width: 100%;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    padding: 11px 16px 11px 42px;
    font-size: .9rem;
    outline: none;
    transition: border .2s;
    background: #fafafa;
}

html[dir="rtl"] .auth-input-group input { padding: 11px 42px 11px 16px; }

.auth-input-group input:focus {
    border-color: var(--accent-color, #e8532e);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(232,83,46,.1);
}

.auth-input-group .error-msg {
    color: #ef4444;
    font-size: .8rem;
    margin-top: .3rem;
}

.auth-form-options {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--space-5);
    font-size: .85rem;
}

.auth-form-options label {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    color: var(--text-secondary, #374151);
}

.auth-form-options a {
    color: var(--accent-color, #e8532e);
    text-decoration: none;
    font-weight: 600;
}

.auth-form-options a:hover { text-decoration: underline; }

.btn-auth-submit {
    width: 100%;
    padding: 13px;
    background: var(--accent-color, #e8532e);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: background .2s, transform .1s;
    letter-spacing: .02em;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-auth-submit:hover { background: #d04525; }
.btn-auth-submit:active { transform: scale(.99); }
.btn-auth-submit:disabled { opacity: .7; cursor: not-allowed; }

.auth-footer-link {
    text-align: center;
    margin-top: var(--space-5);
    font-size: .9rem;
    color: var(--text-muted, #6b7280);
}

.auth-footer-link a {
    color: var(--accent-color, #e8532e);
    font-weight: 600;
    text-decoration: none;
}

.auth-footer-link a:hover { text-decoration: underline; }

.alert-auth {
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: var(--space-4);
    font-size: .88rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.alert-auth-success {
    background: #f0fdf4;
    border: 1px solid #86efac;
    color: #166534;
}

.alert-auth-error {
    background: #fef2f2;
    border: 1px solid #fca5a5;
    color: #b91c1c;
}

.btn-toggle-pwd {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #9ca3af;
    padding: 0 12px;
}

html[dir="ltr"] .btn-toggle-pwd { right: 0; }
html[dir="rtl"] .btn-toggle-pwd { left: 0; }
</style>
@endpush

@section('content')
<div class="auth-page-wrapper">
    <div class="auth-card">

        {{-- Logo --}}
        <div class="auth-logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="{{ config('app.name') }}">
            </a>
        </div>

        <h2 class="auth-title">{{ __('مرحباً بك') }}</h2>
        <p class="auth-subtitle">{{ __('سجّل دخولك لعرض حجوزاتك ومفضلاتك') }}</p>

        {{-- Status Messages --}}
        @if (session('status'))
            <div class="alert-auth alert-auth-success">
                <i class="fas fa-check-circle"></i>
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-auth alert-auth-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            {{-- Email --}}
            <div class="auth-input-group">
                <label for="email">{{ __('البريد الإلكتروني') }}</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope field-icon"></i>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                </div>
                @error('email')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="auth-input-group">
                <label for="password">{{ __('كلمة المرور') }}</label>
                <div class="input-wrap">
                    <i class="fas fa-lock field-icon"></i>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                    <button type="button" class="btn-toggle-pwd" onclick="togglePwd()" id="toggleEye">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- Options --}}
            <div class="auth-form-options">
                <label>
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    {{ __('تذكرني') }}
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">{{ __('نسيت كلمة المرور؟') }}</a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-auth-submit" id="submitBtn">
                <span>{{ __('تسجيل الدخول') }}</span>
                <i class="fas fa-sign-in-alt"></i>
            </button>
        </form>

        <div class="auth-footer-link">
            {{ __('ليس لديك حساب؟') }}
            <a href="{{ route('register') }}">{{ __('أنشئ حساباً مجاناً') }}</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePwd() {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        pwd.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.getElementById('loginForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> <span>{{ __("جاري التحقق...") }}</span>';
});
</script>
@endpush
