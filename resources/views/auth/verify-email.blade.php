@extends('frontend.layouts.app')

@section('title', __('تحقق من البريد الإلكتروني'))

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
    font-size: .95rem;
    margin-bottom: var(--space-6);
    line-height: 1.6;
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
    margin-bottom: var(--space-4);
}

.btn-auth-submit:hover { background: #d04525; }
.btn-auth-submit:active { transform: scale(.99); }
.btn-auth-submit:disabled { opacity: .7; cursor: not-allowed; }

.btn-auth-logout {
    background: none;
    border: none;
    color: var(--text-muted, #6b7280);
    font-size: .9rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: underline;
    transition: color .2s;
}

.btn-auth-logout:hover { color: var(--accent-color, #e8532e); }

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

        <h2 class="auth-title">{{ __('شكراً لتسجيلك!') }}</h2>
        <p class="auth-subtitle">
            {{ __('قبل البدء، هل يمكنك التحقق من عنوان بريدك الإلكتروني من خلال النقر على الرابط الذي أرسلناه إليك للتو؟ إذا لم تستلم البريد الإلكتروني، فسنرسل لك بريداً آخر بكل سرور.') }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert-auth alert-auth-success">
                <i class="fas fa-check-circle"></i>
                {{ __('تم إرسال رابط تحقق جديد إلى عنوان البريد الإلكتروني الذي قدمته أثناء التسجيل.') }}
            </div>
        @endif

        <div class="auth-actions">
            <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
                @csrf
                <button type="submit" class="btn-auth-submit" id="submitBtn">
                    <span>{{ __('إعادة إرسال بريد التحقق') }}</span>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>

            <div style="text-align: center;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-auth-logout">
                        {{ __('تسجيل الخروج') }}
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('resendForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> <span>{{ __("جاري الإرسال...") }}</span>';
});
</script>
@endpush
