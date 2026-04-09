@extends('frontend.layouts.app')

@section('title', __('تسجيل الدخول'))

@push('styles')
<style>
/* ─── Premium Auth Redesign ─── */
.auth-split-wrapper {
    min-height: 100vh;
    display: flex;
    font-family: 'Tajawal', sans-serif !important;
    overflow: hidden;
    padding-top: 70px;
}

.auth-split-wrapper * {
    font-family: 'Tajawal', sans-serif !important;
}

/* ─── Illustration Panel ─── */
.auth-illustration-panel {
    flex: 0 0 42%;
    background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 40%, #0e7490 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    padding: 40px;
}

.auth-illustration-panel::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 20% 80%, rgba(14,116,144,.4) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(232,83,46,.15) 0%, transparent 50%);
    z-index: 1;
}

.illustration-content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 380px;
}

.illustration-content img {
    width: 280px;
    height: auto;
    margin-bottom: 32px;
    filter: drop-shadow(0 20px 40px rgba(0,0,0,.3));
    animation: floatImage 6s ease-in-out infinite;
}

@keyframes floatImage {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-12px); }
}

.illustration-content h3 {
    color: #fff;
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 12px;
}

.illustration-content p {
    color: rgba(255,255,255,.7);
    font-size: .95rem;
    line-height: 1.8;
}

/* ─── Form Panel ─── */
.auth-form-panel {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    background: #f8fafc;
}

.auth-form-container {
    width: 100%;
    max-width: 460px;
    animation: formSlideIn .5s ease;
}

@keyframes formSlideIn {
    from { opacity: 0; transform: translateX(20px); }
    to   { opacity: 1; transform: translateX(0); }
}

.auth-form-header {
    margin-bottom: 32px;
}

.auth-form-header h2 {
    font-size: 1.85rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 8px;
}

.auth-form-header p {
    color: #64748b;
    font-size: .95rem;
}

/* ─── Input styles ─── */
.auth-field {
    margin-bottom: 20px;
}

.auth-field label {
    display: block;
    font-weight: 600;
    font-size: .88rem;
    margin-bottom: 8px;
    color: #334155;
}

.input-box {
    position: relative;
}

.input-box .field-icon {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: .9rem;
    z-index: 2;
}

html[dir="ltr"] .input-box .field-icon { left: 14px; }
html[dir="rtl"] .input-box .field-icon { right: 14px; }

.auth-field input {
    width: 100%;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 16px 14px 44px;
    font-size: .95rem;
    background: #fff;
    outline: none;
    transition: all .25s ease;
}

html[dir="rtl"] .auth-field input { padding: 14px 44px 14px 16px; }

.auth-field input:focus {
    border-color: #0e7490;
    box-shadow: 0 0 0 4px rgba(14,116,144,.08);
}

.auth-form-options {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    font-size: .9rem;
}

.auth-form-options label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    color: #64748b;
}

.auth-form-options a {
    color: #0e7490;
    font-weight: 600;
    text-decoration: none;
}

/* ─── Submit button ─── */
.btn-auth-submit {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #0e7490 0%, #0891b2 100%);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-auth-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(14,116,144,.3);
}

.btn-toggle-pwd {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #94a3b8;
    padding: 0 14px;
}

html[dir="ltr"] .btn-toggle-pwd { right: 0; }
html[dir="rtl"] .btn-toggle-pwd { left: 0; }

.auth-form-footer {
    text-align: center;
    margin-top: 32px;
    font-size: .95rem;
    color: #64748b;
}

.auth-form-footer a {
    color: #0e7490;
    font-weight: 700;
    text-decoration: none;
}

.auth-alert {
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 24px;
    font-size: .9rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.auth-alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.auth-alert-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
}

@media (max-width: 992px) {
    .auth-illustration-panel { display: none; }
    .auth-form-panel { padding: 24px 20px; }
}
</style>
@endpush

@section('content')
<div class="auth-split-wrapper">


    {{-- Right Form Panel --}}
    <div class="auth-form-panel">
        <div class="auth-form-container">

            <div class="auth-form-header">
                <h2>{{ __('تسجيل الدخول') }}</h2>
                <p>{{ __('أدخل بياناتك للمتابعة إلى حسابك') }}</p>
            </div>

            {{-- Alerts --}}
            @if (session('status'))
                <div class="auth-alert auth-alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="auth-alert auth-alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="auth-field">
                    <label for="email">{{ __('البريد الإلكتروني') }}</label>
                    <div class="input-box">
                        <i class="fas fa-envelope field-icon"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                    </div>
                </div>

                <div class="auth-field">
                    <label for="password">{{ __('كلمة المرور') }}</label>
                    <div class="input-box">
                        <i class="fas fa-lock field-icon"></i>
                        <input type="password" id="password" name="password" required placeholder="••••••••">
                        <button type="button" class="btn-toggle-pwd" onclick="togglePwd()" id="toggleEye">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="auth-form-options">
                    <label>
                        <input type="checkbox" name="remember">
                        {{ __('تذكرني') }}
                    </label>
                    <a href="{{ route('password.request') }}">{{ __('نسيت كلمة المرور؟') }}</a>
                </div>

                <button type="submit" class="btn-auth-submit" id="submitBtn">
                    <span>{{ __('تسجيل الدخول') }}</span>
                    <i class="fas fa-sign-in-alt"></i>
                </button>
            </form>

            <div class="auth-form-footer">
                {{ __('ليس لديك حساب؟') }}
                <a href="{{ route('register') }}">{{ __('أنشئ حساباً مجاناً') }}</a>
            </div>
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
