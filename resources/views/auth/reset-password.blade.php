@extends('frontend.layouts.app')

@section('title', __('إعادة تعيين كلمة المرور'))

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
    max-width: 480px;
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
    margin-top: var(--space-4);
}

.btn-auth-submit:hover { background: #d04525; }
.btn-auth-submit:active { transform: scale(.99); }
.btn-auth-submit:disabled { opacity: .7; cursor: not-allowed; }

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

        <h2 class="auth-title">{{ __('إعادة تعيين كلمة المرور') }}</h2>
        <p class="auth-subtitle">{{ __('يرجى إدخال كلمة المرور الجديدة الخاصة بك أدناه.') }}</p>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="alert-auth alert-auth-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}" id="resetPasswordForm">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <div class="auth-input-group">
                <label for="email">{{ __('البريد الإلكتروني') }}</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope field-icon"></i>
                    <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required autofocus placeholder="name@example.com">
                </div>
                @error('email')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- New Password --}}
            <div class="auth-input-group">
                <label for="password">{{ __('كلمة المرور الجديدة') }}</label>
                <div class="input-wrap">
                    <i class="fas fa-lock field-icon"></i>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                    <button type="button" class="btn-toggle-pwd" onclick="togglePwd('password', 'eyeIcon1')">
                        <i class="fas fa-eye" id="eyeIcon1"></i>
                    </button>
                </div>
                @error('password')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="auth-input-group">
                <label for="password_confirmation">{{ __('تأكيد كلمة المرور') }}</label>
                <div class="input-wrap">
                    <i class="fas fa-lock field-icon"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••">
                    <button type="button" class="btn-toggle-pwd" onclick="togglePwd('password_confirmation', 'eyeIcon2')">
                        <i class="fas fa-eye" id="eyeIcon2"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-auth-submit" id="submitBtn">
                <span>{{ __('إعادة تعيين كلمة المرور') }}</span>
                <i class="fas fa-key"></i>
            </button>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePwd(inputId, iconId) {
    const pwd = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        pwd.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.getElementById('resetPasswordForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> <span>{{ __("جاري الحفظ...") }}</span>';
});
</script>
@endpush
