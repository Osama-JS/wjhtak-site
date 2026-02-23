@extends('frontend.layouts.app')

@section('title', __('إنشاء حساب جديد') )

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
    max-width: 560px;
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

.auth-input-group .input-wrap i {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: .9rem;
}

html[dir="ltr"] .auth-input-group .input-wrap i { left: 14px; }
html[dir="rtl"] .auth-input-group .input-wrap i { right: 14px; }

.auth-input-group input {
    width: 100%;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    padding: 11px 16px 11px 40px;
    font-size: .9rem;
    outline: none;
    transition: border .2s;
    background: #fafafa;
}

html[dir="rtl"] .auth-input-group input { padding: 11px 40px 11px 16px; }

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

.auth-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-4);
}

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
    margin-top: var(--space-2);
    letter-spacing: .02em;
}

.btn-auth-submit:hover { background: #d04525; }
.btn-auth-submit:active { transform: scale(.99); }

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

.alert-auth-error {
    background: #fef2f2;
    border: 1px solid #fca5a5;
    color: #b91c1c;
}
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

        <h2 class="auth-title">{{ __('إنشاء حساب جديد') }}</h2>
        <p class="auth-subtitle">{{ __('انضم إلينا وابدأ رحلتك الآن') }}</p>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="alert-auth alert-auth-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Unverified Account Alert --}}
        @if (session('unverified_email'))
            <div class="alert alert-warning mb-4" style="border-radius: 12px; padding: 15px; border: 1px solid #ffeeba; background: #fff3cd; color: #856404; font-size: .9rem;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fas fa-info-circle"></i>
                    <strong>{{ __('هذا البريد مسجل مسبقاً ولكنه غير مفعل.') }}</strong>
                </div>
                <p class="mb-3">{{ __('هل تريد إرسال كود التحقق لتفعيل الحساب؟') }}</p>
                <form method="POST" action="{{ route('auth.resend-otp') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('unverified_email') }}">
                    <button type="submit" class="btn btn-sm" style="background: #856404; color: #fff; border-radius: 8px; font-weight: 600;">
                        {{ __('إرسال كود التحقق') }}
                    </button>
                </form>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Name row --}}
            <div class="auth-row-2">
                <div class="auth-input-group">
                    <label>{{ __('الاسم الأول') }}</label>
                    <div class="input-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required autofocus placeholder="{{ __('أحمد') }}">
                    </div>
                    @error('first_name')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>
                <div class="auth-input-group">
                    <label>{{ __('الاسم الأخير') }}</label>
                    <div class="input-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="{{ __('محمد') }}">
                    </div>
                    @error('last_name')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Email --}}
            <div class="auth-input-group">
                <label>{{ __('البريد الإلكتروني') }}</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com">
                </div>
                @error('email')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- Phone --}}
            <div class="auth-input-group">
                <label>{{ __('رقم الهاتف') }}</label>
                <div class="input-wrap">
                    <i class="fas fa-phone"></i>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+966 5x xxx xxxx">
                </div>
                @error('phone')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password row --}}
            <div class="auth-row-2">
                <div class="auth-input-group">
                    <label>{{ __('كلمة المرور') }}</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" required placeholder="••••••••" minlength="8">
                    </div>
                    @error('password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>
                <div class="auth-input-group">
                    <label>{{ __('تأكيد كلمة المرور') }}</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password_confirmation" required placeholder="••••••••" minlength="8">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-auth-submit">
                {{ __('إنشاء الحساب') }} <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'left' : 'right' }} ms-2"></i>
            </button>
        </form>

        <div class="auth-footer-link">
            {{ __('لديك حساب بالفعل؟') }}
            <a href="{{ route('login') }}">{{ __('تسجيل الدخول') }}</a>
        </div>
    </div>
</div>
@endsection
