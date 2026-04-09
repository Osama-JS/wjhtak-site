@extends('frontend.layouts.app')

@section('title', __('إنشاء حساب جديد') )

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

.auth-illustration-panel::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,.03) 0%, transparent 70%);
    animation: floatGlow 8s ease-in-out infinite alternate;
    z-index: 1;
}

@keyframes floatGlow {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(-30px, 30px) scale(1.1); }
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
    line-height: 1.5;
}

.illustration-content p {
    color: rgba(255,255,255,.7);
    font-size: .95rem;
    line-height: 1.8;
}

.illustration-features {
    list-style: none;
    padding: 0;
    margin-top: 28px;
    text-align: start;
}

.illustration-features li {
    color: rgba(255,255,255,.85);
    font-size: .9rem;
    padding: 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.illustration-features li i {
    color: #22d3ee;
    font-size: .8rem;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(34,211,238,.15);
    border-radius: 50%;
    flex-shrink: 0;
}

/* ─── Floating particles ─── */
.particle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
    z-index: 1;
    animation: particleFloat linear infinite;
}

.particle:nth-child(1) { width: 80px; height: 80px; top: 10%; left: 10%; animation-duration: 12s; }
.particle:nth-child(2) { width: 40px; height: 40px; top: 60%; left: 80%; animation-duration: 8s; animation-delay: 2s; }
.particle:nth-child(3) { width: 60px; height: 60px; top: 80%; left: 30%; animation-duration: 15s; animation-delay: 4s; }
.particle:nth-child(4) { width: 25px; height: 25px; top: 30%; left: 70%; animation-duration: 10s; animation-delay: 1s; }

@keyframes particleFloat {
    0%, 100% { transform: translateY(0) rotate(0deg); opacity: .3; }
    50% { transform: translateY(-40px) rotate(180deg); opacity: .6; }
}

/* ─── Form Panel ─── */
.auth-form-panel {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    background: #f8fafc;
    position: relative;
    overflow-y: auto;
}

.auth-form-panel::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(232,83,46,.05) 0%, transparent 70%);
    pointer-events: none;
}

.auth-form-container {
    width: 100%;
    max-width: 520px;
    animation: formSlideIn .5s ease;
}

@keyframes formSlideIn {
    from { opacity: 0; transform: translateX(20px); }
    to   { opacity: 1; transform: translateX(0); }
}

.auth-brand-mobile {
    display: none;
    text-align: center;
    margin-bottom: 24px;
}

.auth-brand-mobile img {
    height: 45px;
    object-fit: contain;
}

.auth-form-header {
    margin-bottom: 32px;
}

.auth-form-header .auth-welcome-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, rgba(232,83,46,.1), rgba(14,116,144,.1));
    color: #e8532e;
    font-size: .8rem;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 20px;
    margin-bottom: 16px;
}

.auth-form-header h2 {
    font-size: 1.75rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 6px;
}

.auth-form-header p {
    color: #64748b;
    font-size: .92rem;
}

/* ─── Input styles ─── */
.auth-field {
    margin-bottom: 18px;
}

.auth-field label {
    display: block;
    font-weight: 600;
    font-size: .85rem;
    margin-bottom: 6px;
    color: #334155;
}

.auth-field .input-box {
    position: relative;
}

.auth-field .input-box .field-icon {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: .85rem;
    transition: color .2s;
    z-index: 2;
}

html[dir="ltr"] .auth-field .input-box .field-icon { left: 14px; }
html[dir="rtl"] .auth-field .input-box .field-icon { right: 14px; }

.auth-field input {
    width: 100%;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 16px 12px 42px;
    font-size: .9rem;
    background: #fff;
    outline: none;
    transition: all .25s ease;
    color: #1e293b;
}

html[dir="rtl"] .auth-field input {
    padding: 12px 42px 12px 16px;
}

.auth-field input::placeholder {
    color: #cbd5e1;
}

.auth-field input:focus {
    border-color: #0e7490;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(14,116,144,.08);
}

.auth-field input:focus ~ .field-icon,
.auth-field input:focus + .field-icon {
    color: #0e7490;
}

.auth-field .field-error {
    color: #ef4444;
    font-size: .78rem;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.auth-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

/* ─── Submit button ─── */
.btn-register {
    width: 100%;
    padding: 14px 24px;
    background: linear-gradient(135deg, #0e7490 0%, #0891b2 100%);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .3s ease;
    margin-top: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    letter-spacing: .02em;
    position: relative;
    overflow: hidden;
}

.btn-register::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.15), transparent);
    transition: left .5s;
}

.btn-register:hover {
    background: linear-gradient(135deg, #0c6478 0%, #0780a0 100%);
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(14,116,144,.3);
}

.btn-register:hover::before {
    left: 100%;
}

.btn-register:active {
    transform: translateY(0);
}

.btn-register:disabled {
    opacity: .7;
    cursor: not-allowed;
    transform: none;
}

/* ─── Footer Link ─── */
.auth-form-footer {
    text-align: center;
    margin-top: 24px;
    font-size: .9rem;
    color: #64748b;
}

.auth-form-footer a {
    color: #0e7490;
    font-weight: 600;
    text-decoration: none;
    transition: color .2s;
}

.auth-form-footer a:hover {
    color: #e8532e;
    text-decoration: underline;
}

/* ─── Divider ─── */
.auth-divider {
    display: flex;
    align-items: center;
    gap: 16px;
    margin: 20px 0;
    color: #94a3b8;
    font-size: .82rem;
}

.auth-divider::before,
.auth-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e2e8f0;
}

/* ─── Alerts ─── */
.auth-alert {
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 18px;
    font-size: .85rem;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    animation: alertSlide .3s ease;
}

@keyframes alertSlide {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}

.auth-alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.auth-alert-error i {
    color: #ef4444;
    margin-top: 2px;
}

.auth-alert-warning {
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #92400e;
}

.auth-alert-info {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    color: #075985;
}

/* ─── Password strength ─── */
.password-strength {
    display: flex;
    gap: 4px;
    margin-top: 8px;
}

.password-strength .bar {
    flex: 1;
    height: 3px;
    border-radius: 2px;
    background: #e2e8f0;
    transition: background .3s;
}

.password-strength .bar.active-weak { background: #ef4444; }
.password-strength .bar.active-medium { background: #f59e0b; }
.password-strength .bar.active-strong { background: #22c55e; }

.strength-text {
    font-size: .75rem;
    margin-top: 4px;
    color: #64748b;
}

/* ─── Responsive ─── */
@media (max-width: 992px) {
    .auth-illustration-panel {
        display: none;
    }

    .auth-brand-mobile {
        display: block;
    }

    .auth-form-panel {
        padding: 24px 20px;
    }

    .auth-split-wrapper {
        background: #f8fafc;
    }
}

@media (max-width: 576px) {
    .auth-row {
        grid-template-columns: 1fr;
    }

    .auth-form-header h2 {
        font-size: 1.4rem;
    }
}
</style>
@endpush

@section('content')
<div class="auth-split-wrapper">


    {{-- Right Form Panel --}}
    <div class="auth-form-panel">
        <div class="auth-form-container">

            {{-- Mobile Logo --}}
            <div class="auth-brand-mobile">
                <a href="{{ url('/') }}">
                    <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="{{ config('app.name') }}">
                </a>
            </div>

            {{-- Header --}}
            <div class="auth-form-header">
                <div class="auth-welcome-badge">
                    <i class="fas fa-sparkles"></i>
                    {{ __('مجاناً بالكامل') }}
                </div>
                <h2>{{ __('إنشاء حساب جديد') }}</h2>
                <p>{{ __('أنشئ حسابك في دقيقة واحدة وابدأ حجز رحلتك') }}</p>
            </div>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="auth-alert auth-alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (session('info'))
                <div class="auth-alert auth-alert-info">
                    <i class="fas fa-info-circle"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            {{-- Unverified Account Alert --}}
            @if (session('unverified_email'))
                <div class="auth-alert auth-alert-warning">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>{{ __('هذا البريد مسجل مسبقاً ولكنه غير مفعل.') }}</strong>
                        <p style="margin: 6px 0 10px; font-size: .84rem;">{{ __('هل تريد إرسال كود التحقق لتفعيل الحساب؟') }}</p>
                        <form method="POST" action="{{ route('auth.resend-otp') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('unverified_email') }}">
                            <button type="submit" style="background: #92400e; color: #fff; border: none; border-radius: 8px; padding: 7px 16px; font-weight: 600; font-size: .82rem; cursor: pointer;">
                                {{ __('إرسال كود التحقق') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                {{-- Name row --}}
                <div class="auth-row">
                    <div class="auth-field">
                        <label>{{ __('الاسم الأول') }}</label>
                        <div class="input-box">
                            <i class="fas fa-user field-icon"></i>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required autofocus placeholder="{{ __('أحمد') }}">
                        </div>
                        @error('first_name')
                            <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                    <div class="auth-field">
                        <label>{{ __('الاسم الأخير') }}</label>
                        <div class="input-box">
                            <i class="fas fa-user field-icon"></i>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="{{ __('محمد') }}">
                        </div>
                        @error('last_name')
                            <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div class="auth-field">
                    <label>{{ __('البريد الإلكتروني') }}</label>
                    <div class="input-box">
                        <i class="fas fa-envelope field-icon"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com">
                    </div>
                    @error('email')
                        <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="auth-field">
                    <label>{{ __('رقم الهاتف') }}</label>
                    <div class="input-box">
                        <i class="fas fa-phone field-icon"></i>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+966 5x xxx xxxx" dir="ltr">
                    </div>
                    @error('phone')
                        <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Password row --}}
                <div class="auth-row">
                    <div class="auth-field">
                        <label>{{ __('كلمة المرور') }}</label>
                        <div class="input-box">
                            <i class="fas fa-lock field-icon"></i>
                            <input type="password" name="password" id="password" required placeholder="••••••••" minlength="8">
                        </div>
                        <div class="password-strength" id="strengthBars">
                            <div class="bar"></div>
                            <div class="bar"></div>
                            <div class="bar"></div>
                            <div class="bar"></div>
                        </div>
                        <div class="strength-text" id="strengthText"></div>
                        @error('password')
                            <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                    <div class="auth-field">
                        <label>{{ __('تأكيد كلمة المرور') }}</label>
                        <div class="input-box">
                            <i class="fas fa-lock field-icon"></i>
                            <input type="password" name="password_confirmation" required placeholder="••••••••" minlength="8">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-register" id="submitBtn">
                    <span>{{ __('إنشاء الحساب') }}</span>
                    <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'left' : 'right' }}"></i>
                </button>
            </form>

            <div class="auth-form-footer">
                {{ __('لديك حساب بالفعل؟') }}
                <a href="{{ route('login') }}">{{ __('تسجيل الدخول') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password strength meter
    const pwd = document.getElementById('password');
    const bars = document.querySelectorAll('#strengthBars .bar');
    const strengthText = document.getElementById('strengthText');

    if (pwd) {
        pwd.addEventListener('input', function() {
            const val = this.value;
            let score = 0;

            if (val.length >= 8) score++;
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
            if (/\d/.test(val)) score++;
            if (/[^a-zA-Z0-9]/.test(val)) score++;

            bars.forEach((bar, i) => {
                bar.className = 'bar';
                if (i < score) {
                    if (score <= 1) bar.classList.add('active-weak');
                    else if (score <= 2) bar.classList.add('active-medium');
                    else bar.classList.add('active-strong');
                }
            });

            const labels = ['', '{{ __("ضعيفة") }}', '{{ __("متوسطة") }}', '{{ __("جيدة") }}', '{{ __("قوية") }}'];
            strengthText.textContent = val.length > 0 ? labels[score] || '' : '';
        });
    }

    // Submit loading state
    document.getElementById('registerForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> <span>{{ __("جاري إنشاء الحساب...") }}</span>';
    });
});
</script>
@endpush
