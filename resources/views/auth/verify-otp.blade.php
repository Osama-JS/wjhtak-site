@extends('frontend.layouts.app')

@section('title', __('تفعيل الحساب'))

@push('styles')
<style>
/* ─── Premium OTP Redesign ─── */
.otp-split-wrapper {
    min-height: 100vh;
    display: flex;
    font-family: 'Tajawal', sans-serif !important;
    overflow: hidden;
    padding-top: 70px;
}

.otp-split-wrapper * {
    font-family: 'Tajawal', sans-serif !important;
}

/* ─── Illustration Panel ─── */
.otp-illustration-panel {
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

.otp-illustration-panel::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 20% 80%, rgba(14,116,144,.4) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(232,83,46,.15) 0%, transparent 50%);
    z-index: 1;
}

.otp-illustration-panel::after {
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

.otp-illus-content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 360px;
}

.otp-illus-icon {
    width: 120px;
    height: 120px;
    background: rgba(255,255,255,.08);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 28px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.12);
    animation: pulseIcon 3s ease-in-out infinite;
}

@keyframes pulseIcon {
    0%, 100% { box-shadow: 0 0 0 0 rgba(34,211,238,.2); }
    50% { box-shadow: 0 0 0 20px rgba(34,211,238,0); }
}

.otp-illus-icon i {
    font-size: 3rem;
    color: #22d3ee;
}

.otp-illus-content h3 {
    color: #fff;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 12px;
    line-height: 1.5;
}

.otp-illus-content p {
    color: rgba(255,255,255,.65);
    font-size: .9rem;
    line-height: 1.8;
}

/* Floating particles */
.otp-particle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
    z-index: 1;
    animation: particleDrift linear infinite;
}
.otp-particle:nth-child(1) { width: 60px; height: 60px; top: 15%; left: 15%; animation-duration: 14s; }
.otp-particle:nth-child(2) { width: 35px; height: 35px; top: 70%; left: 75%; animation-duration: 9s; animation-delay: 3s; }
.otp-particle:nth-child(3) { width: 50px; height: 50px; top: 85%; left: 20%; animation-duration: 12s; animation-delay: 1s; }

@keyframes particleDrift {
    0%, 100% { transform: translateY(0) rotate(0deg); opacity: .2; }
    50% { transform: translateY(-30px) rotate(180deg); opacity: .5; }
}

/* ─── Form Panel ─── */
.otp-form-panel {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    background: #f8fafc;
    position: relative;
}

.otp-form-container {
    width: 100%;
    max-width: 480px;
    text-align: center;
    animation: otpSlideIn .5s ease;
}

@keyframes otpSlideIn {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

.otp-brand-mobile {
    display: none;
    text-align: center;
    margin-bottom: 20px;
}

.otp-brand-mobile img {
    height: 45px;
    object-fit: contain;
}

/* ─── OTP Header ─── */
.otp-header-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(14,116,144,.1), rgba(34,211,238,.1));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
}

.otp-header-icon i {
    font-size: 2rem;
    color: #0e7490;
}

.otp-form-container h2 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 8px;
}

.otp-form-container .otp-desc {
    color: #64748b;
    font-size: .9rem;
    margin-bottom: 6px;
    line-height: 1.7;
}

.otp-email-display {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f1f5f9;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: .88rem;
    color: #0e7490;
    font-weight: 600;
    margin-bottom: 32px;
    direction: ltr;
}

.otp-email-display i {
    font-size: .75rem;
}

/* ─── OTP Input Boxes ─── */
.otp-code-inputs {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-bottom: 28px;
    direction: ltr;
}

.otp-code-inputs input {
    width: 54px;
    height: 64px;
    text-align: center;
    font-size: 1.6rem;
    font-weight: 800;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    outline: none;
    transition: all .25s ease;
    background: #fff;
    color: #0f172a;
    caret-color: #0e7490;
}

.otp-code-inputs input:focus {
    border-color: #0e7490;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(14,116,144,.1);
    transform: translateY(-2px);
}

.otp-code-inputs input.filled {
    border-color: #0e7490;
    background: rgba(14,116,144,.04);
}

.otp-code-inputs input.error-shake {
    border-color: #ef4444;
    animation: shakeInput .4s ease;
}

@keyframes shakeInput {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-6px); }
    75% { transform: translateX(6px); }
}

/* ─── Submit Button ─── */
.btn-verify-otp {
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
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    position: relative;
    overflow: hidden;
}

.btn-verify-otp::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.15), transparent);
    transition: left .5s;
}

.btn-verify-otp:hover {
    background: linear-gradient(135deg, #0c6478 0%, #0780a0 100%);
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(14,116,144,.3);
}

.btn-verify-otp:hover::before {
    left: 100%;
}

.btn-verify-otp:disabled {
    opacity: .6;
    cursor: not-allowed;
    transform: none;
}

/* ─── Timer & Resend ─── */
.otp-timer-section {
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.otp-timer-text {
    font-size: .88rem;
    color: #64748b;
    margin-bottom: 8px;
}

.otp-timer-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f1f5f9;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: .88rem;
    color: #334155;
    font-weight: 600;
    font-variant-numeric: tabular-nums;
}

.otp-timer-badge i {
    color: #0e7490;
    font-size: .8rem;
}

.btn-resend-otp {
    background: none;
    border: 1.5px solid #0e7490;
    color: #0e7490;
    font-weight: 600;
    padding: 8px 24px;
    border-radius: 10px;
    cursor: pointer;
    font-size: .88rem;
    transition: all .3s;
    display: none;
    margin: 0 auto;
}

.btn-resend-otp:hover {
    background: #0e7490;
    color: #fff;
}

.btn-resend-otp.visible {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* ─── Change Email ─── */
.otp-change-email {
    margin-top: 16px;
}

.otp-change-email a {
    font-size: .85rem;
    color: #94a3b8;
    text-decoration: none;
    transition: color .2s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.otp-change-email a:hover {
    color: #e8532e;
}

/* ─── Alerts ─── */
.otp-alert {
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 20px;
    font-size: .85rem;
    display: flex;
    align-items: center;
    gap: 10px;
    text-align: start;
    animation: alertSlide .3s ease;
}

@keyframes alertSlide {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}

.otp-alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.otp-alert-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.otp-alert-info {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    color: #075985;
}

/* ─── Progress Steps ─── */
.otp-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: 32px;
}

.otp-step {
    display: flex;
    align-items: center;
    gap: 0;
}

.otp-step-dot {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    font-weight: 700;
    transition: all .3s;
}

.otp-step-dot.completed {
    background: #0e7490;
    color: #fff;
}

.otp-step-dot.active {
    background: rgba(14,116,144,.15);
    color: #0e7490;
    border: 2px solid #0e7490;
}

.otp-step-dot.pending {
    background: #f1f5f9;
    color: #94a3b8;
    border: 2px solid #e2e8f0;
}

.otp-step-line {
    width: 60px;
    height: 2px;
    background: #e2e8f0;
    margin: 0 4px;
}

.otp-step-line.completed {
    background: #0e7490;
}

/* ─── Responsive ─── */
@media (max-width: 992px) {
    .otp-illustration-panel {
        display: none;
    }

    .otp-brand-mobile {
        display: block;
    }

    .otp-form-panel {
        padding: 24px 20px;
    }
}

@media (max-width: 480px) {
    .otp-code-inputs input {
        width: 46px;
        height: 56px;
        font-size: 1.3rem;
    }

    .otp-code-inputs {
        gap: 6px;
    }
}
</style>
@endpush

@section('content')
<div class="otp-split-wrapper">

    {{-- Left Illustration Panel --}}
    <div class="otp-illustration-panel">
        <div class="otp-particle"></div>
        <div class="otp-particle"></div>
        <div class="otp-particle"></div>

        <div class="otp-illus-content">
            <div class="otp-illus-icon">
                <i class="fas fa-shield-alt"></i>
            </div>

            <h3>{{ __('تأمين حسابك') }}</h3>
            <p>{{ __('نحن نهتم بأمان حسابك. أدخل الكود المرسل إلى بريدك الإلكتروني لتأكيد هويتك وتفعيل حسابك.') }}</p>
        </div>
    </div>

    {{-- Right Form Panel --}}
    <div class="otp-form-panel">
        <div class="otp-form-container">

            {{-- Mobile Logo --}}
            <div class="otp-brand-mobile">
                <a href="{{ url('/') }}">
                    <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="{{ config('app.name') }}">
                </a>
            </div>

            {{-- Progress Steps --}}
            <div class="otp-progress">
                <div class="otp-step">
                    <div class="otp-step-dot completed"><i class="fas fa-check" style="font-size: .7rem;"></i></div>
                </div>
                <div class="otp-step-line completed"></div>
                <div class="otp-step">
                    <div class="otp-step-dot active">2</div>
                </div>
                <div class="otp-step-line"></div>
                <div class="otp-step">
                    <div class="otp-step-dot pending">3</div>
                </div>
            </div>

            {{-- Icon --}}
            <div class="otp-header-icon">
                <i class="fas fa-envelope-open-text"></i>
            </div>

            <h2>{{ __('أدخل كود التحقق') }}</h2>
            <p class="otp-desc">{{ __('لقد أرسلنا كود تحقق مكون من 6 أرقام إلى بريدك الإلكتروني') }}</p>

            <div class="otp-email-display">
                <i class="fas fa-envelope"></i>
                {{ $email }}
            </div>

            {{-- Alerts --}}
            @if ($errors->any())
                <div class="otp-alert otp-alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="otp-alert otp-alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('info'))
                <div class="otp-alert otp-alert-info">
                    <i class="fas fa-info-circle"></i>
                    {{ session('info') }}
                </div>
            @endif

            {{-- OTP Form --}}
            <form method="POST" action="{{ route('auth.verify-otp') }}" id="otpForm">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="otp-code-inputs" id="otpInputs">
                    <input type="text" name="otp[]" maxlength="1" pattern="\d*" inputmode="numeric" required autofocus autocomplete="one-time-code">
                    <input type="text" name="otp[]" maxlength="1" pattern="\d*" inputmode="numeric" required>
                    <input type="text" name="otp[]" maxlength="1" pattern="\d*" inputmode="numeric" required>
                    <input type="text" name="otp[]" maxlength="1" pattern="\d*" inputmode="numeric" required>
                    <input type="text" name="otp[]" maxlength="1" pattern="\d*" inputmode="numeric" required>
                    <input type="text" name="otp[]" maxlength="1" pattern="\d*" inputmode="numeric" required>
                </div>

                <button type="submit" class="btn-verify-otp" id="verifyBtn">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ __('تأكيد الرمز') }}</span>
                </button>
            </form>

            {{-- Timer & Resend --}}
            <div class="otp-timer-section">
                <div id="timerContainer">
                    <p class="otp-timer-text">{{ __('لم يصلك الكود؟ يمكنك إعادة الإرسال بعد') }}</p>
                    <div class="otp-timer-badge">
                        <i class="fas fa-clock"></i>
                        <span id="timer">02:00</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('auth.resend-otp') }}" id="resendForm" style="display: inline;">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" class="btn-resend-otp" id="resendBtn">
                        <i class="fas fa-redo"></i>
                        {{ __('إعادة إرسال الكود') }}
                    </button>
                </form>
            </div>

            {{-- Change email --}}
            <div class="otp-change-email">
                <a href="{{ route('register') }}">
                    <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'right' : 'left' }}"></i>
                    {{ __('تغيير البريد الإلكتروني') }}
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('#otpInputs input');
    const form = document.getElementById('otpForm');
    const verifyBtn = document.getElementById('verifyBtn');

    // OTP input navigation
    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            const val = e.target.value.replace(/\D/g, '');
            e.target.value = val.slice(0, 1);

            if (val.length === 1) {
                input.classList.add('filled');
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            } else {
                input.classList.remove('filled');
            }

            // Auto-submit when all filled
            const allFilled = Array.from(inputs).every(i => i.value.length === 1);
            if (allFilled) {
                verifyBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> <span>{{ __("جاري التحقق...") }}</span>';
                verifyBtn.disabled = true;
                setTimeout(() => form.submit(), 400);
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace') {
                if (!e.target.value && index > 0) {
                    inputs[index - 1].focus();
                    inputs[index - 1].value = '';
                    inputs[index - 1].classList.remove('filled');
                }
                input.classList.remove('filled');
            }
            // Arrow keys
            if (e.key === 'ArrowLeft' && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            if (e.key === 'ArrowRight' && index > 0) {
                inputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6).split('');
            pasteData.forEach((char, i) => {
                if (inputs[i]) {
                    inputs[i].value = char;
                    inputs[i].classList.add('filled');
                }
            });
            const nextIndex = Math.min(pasteData.length, inputs.length - 1);
            inputs[nextIndex].focus();

            if (pasteData.length === 6) {
                verifyBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> <span>{{ __("جاري التحقق...") }}</span>';
                verifyBtn.disabled = true;
                setTimeout(() => form.submit(), 400);
            }
        });

        // Focus styling
        input.addEventListener('focus', () => {
            input.select();
        });
    });

    // Timer Logic
    let timeLeft = 120;
    const timerSpan = document.getElementById('timer');
    const timerContainer = document.getElementById('timerContainer');
    const resendBtn = document.getElementById('resendBtn');

    const countdown = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(countdown);
            timerContainer.style.display = 'none';
            resendBtn.classList.add('visible');
        } else {
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            timerSpan.textContent =
                (minutes < 10 ? "0" : "") + minutes + ":" +
                (seconds < 10 ? "0" : "") + seconds;
            timeLeft--;
        }
    }, 1000);

    // Form submit loading
    form.addEventListener('submit', function() {
        verifyBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> <span>{{ __("جاري التحقق...") }}</span>';
        verifyBtn.disabled = true;
    });
});
</script>
@endpush
@endsection
