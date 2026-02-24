@extends('frontend.layouts.app')

@section('title', __('تحقق من رمز إعادة التعيين'))

@push('styles')
<style>
.auth-page-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 80px var(--space-4);
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    font-family: 'Tajawal', sans-serif !important;
}

.auth-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,.12);
    width: 100%;
    max-width: 500px;
    padding: var(--space-10) var(--space-8);
    text-align: center;
    animation: fadeInUp .4s ease;
}

.otp-inputs {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin: var(--space-8) 0;
    direction: ltr;
}

.otp-inputs input {
    width: 50px;
    height: 60px;
    text-align: center;
    font-size: 1.5rem;
    font-weight: 700;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    outline: none;
    transition: all .2s;
    background: #fafafa;
}

.otp-inputs input:focus {
    border-color: var(--accent-color, #e8532e);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(232,83,46,.1);
}

.resend-timer {
    font-size: .9rem;
    color: var(--text-muted, #6b7280);
    margin-top: var(--space-4);
}

.btn-resend {
    background: none;
    border: none;
    color: var(--accent-color, #e8532e);
    font-weight: 600;
    cursor: pointer;
    text-decoration: underline;
    display: none;
}

.btn-resend:disabled {
    color: #9ca3af;
    cursor: not-allowed;
    text-decoration: none;
}

.email-highlight {
    color: var(--color-primary);
    font-weight: 600;
}
</style>
@endpush

@section('content')
<div class="auth-page-wrapper">
    <div class="auth-card">
        <div class="auth-logo mb-4">
            <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="Logo" style="height: 50px;">
        </div>

        <h2 class="auth-title mb-2">{{ __('إعادة تعيين كلمة المرور') }}</h2>
        <p class="auth-subtitle">
            {{ __('لقد أرسلنا كود التحقق المكون من 6 أرقام إلى بريدك الإلكتروني:') }}<br>
            <span class="email-highlight">{{ $email }}</span>
        </p>

        @if ($errors->any())
            <div class="alert-auth alert-auth-error mb-4" style="color: #ef4444; font-size: .9rem; background: #fef2f2; padding: 10px; border-radius: 10px;">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success mb-4" style="border-radius: 10px; font-size: .9rem; background: #f0fdf4; color: #166534; padding: 10px;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.otp.submit') }}" id="otpForm">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="otp-inputs">
                <input type="text" name="otp[]" maxlength="1" pattern="\d*" inputmode="numeric" required autofocus>
                <input type="text" name="otp[]" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <input type="text" name="otp[]" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <input type="text" name="otp[]" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <input type="text" name="otp[]" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <input type="text" name="otp[]" maxlength="1" pattern="\d*" inputmode="numeric" required>
            </div>

            <button type="submit" class="btn-auth-submit" style="width: 100%; padding: 13px; background: var(--accent-color, #e8532e); color: #fff; border: none; border-radius: 10px; font-size: 1rem; font-weight: 700; cursor: pointer;">
                {{ __('تأكيد الرمز') }}
            </button>
        </form>

        <div class="resend-timer" id="timerContainer">
            {{ __('إعادة إرسال الكود خلال') }} <span id="timer">02:00</span>
        </div>

        <form method="POST" action="{{ route('password.otp.resend') }}" id="resendForm" style="display: inline;">
            @csrf
            <button type="submit" class="btn-resend" id="resendBtn">
                {{ __('إعادة إرسال الكود الآن') }}
            </button>
        </form>

        <div class="mt-4">
            <a href="{{ route('password.request') }}" style="font-size: .9rem; color: #6b7280; text-decoration: none;">
                <i class="fas fa-arrow-right"></i> {{ __('تغيير البريد الإلكتروني') }}
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.otp-inputs input');
    const form = document.getElementById('otpForm');

    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            if (e.target.value.length > 1) {
                e.target.value = e.target.value.slice(0, 1);
            }
            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').slice(0, 6).split('');
            pasteData.forEach((char, i) => {
                if (inputs[index + i]) {
                    inputs[index + i].value = char;
                }
            });
            if (inputs[index + pasteData.length]) {
                inputs[index + pasteData.length].focus();
            } else {
                inputs[inputs.length - 1].focus();
            }
        });
    });

    let timeLeft = 120;
    const timerSpan = document.getElementById('timer');
    const timerContainer = document.getElementById('timerContainer');
    const resendBtn = document.getElementById('resendBtn');

    const countdown = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(countdown);
            timerContainer.style.display = 'none';
            resendBtn.style.display = 'inline-block';
        } else {
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            timerSpan.textContent =
                (minutes < 10 ? "0" : "") + minutes + ":" +
                (seconds < 10 ? "0" : "") + seconds;
            timeLeft--;
        }
    }, 1000);
});
</script>
@endpush
@endsection
