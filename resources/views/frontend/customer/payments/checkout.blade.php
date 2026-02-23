@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Complete Payment'))
@section('page-title', __('Complete Payment'))

@push('styles')
<style>
.checkout-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 22px;
    align-items: start;
}

@media (max-width: 960px) { .checkout-grid { grid-template-columns: 1fr; } }

.checkout-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    overflow: hidden;
    margin-bottom: 20px;
}

.checkout-card-header {
    padding: 16px 22px;
    border-bottom: 1px solid #f3f4f6;
    font-weight: 700;
    font-size: .95rem;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
}

.checkout-card-header i { color: var(--accent-color, #e8532e); }
.checkout-card-body { padding: 20px 22px; }

/* Payment method options */
.payment-methods {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 14px;
}

.payment-method-option {
    position: relative;
}

.payment-method-option input[type=radio] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.payment-method-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 16px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all .2s;
    text-align: center;
}

.payment-method-label:hover {
    border-color: var(--accent-color, #e8532e);
    background: #fff5f3;
}

.payment-method-option input:checked + .payment-method-label {
    border-color: var(--accent-color, #e8532e);
    background: #fff5f3;
    box-shadow: 0 0 0 3px rgba(232,83,46,.12);
}

.payment-method-label img {
    height: 30px;
    object-fit: contain;
}

.payment-method-label .method-name {
    font-size: .78rem;
    font-weight: 700;
    color: #374151;
}

.payment-method-label .method-desc {
    font-size: .7rem;
    color: #9ca3af;
}

/* Order summary */
.order-summary-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    position: sticky;
    top: 80px;
}

.order-summary-trip {
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
}

.order-trip-img {
    width: 100%;
    height: 140px;
    border-radius: 10px;
    object-fit: cover;
    margin-bottom: 12px;
}

.order-trip-img-placeholder {
    width: 100%;
    height: 140px;
    border-radius: 10px;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: #94a3b8;
    margin-bottom: 12px;
}

.order-trip-name { font-weight: 700; font-size: .95rem; color: #111827; }
.order-trip-meta { font-size: .78rem; color: #6b7280; margin-top: 4px; }

.order-price-rows {
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
}

.order-price-row {
    display: flex;
    justify-content: space-between;
    font-size: .88rem;
    margin-bottom: 8px;
}

.order-price-row:last-child { margin-bottom: 0; }
.order-price-row .label { color: #6b7280; }
.order-price-row .value { font-weight: 600; color: #111827; }

.order-total {
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8fafc;
}

.order-total .total-label { font-weight: 700; color: #111827; }
.order-total .total-amount {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--accent-color, #e8532e);
}

.btn-pay {
    display: block;
    width: calc(100% - 40px);
    margin: 16px 20px;
    padding: 14px;
    background: var(--accent-color, #e8532e);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    text-align: center;
    transition: background .2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-pay:hover { background: #d04525; }
.btn-pay:disabled { opacity: .6; cursor: not-allowed; }

.secure-note {
    text-align: center;
    padding: 0 20px 16px;
    font-size: .75rem;
    color: #9ca3af;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #6b7280;
    text-decoration: none;
    font-size: .88rem;
    margin-bottom: 20px;
    font-weight: 600;
    transition: color .2s;
}

.back-link:hover { color: var(--accent-color, #e8532e); }
</style>
@endpush

@section('content')

<a href="{{ route('customer.bookings.show', $booking->id) }}" class="back-link">
    <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'right' : 'left' }}"></i>
    {{ __('Back to Booking Details') }}
</a>

<div class="checkout-grid">

    {{-- LEFT: Payment Method Selection --}}
    <div>
        <div class="checkout-card">
            <div class="checkout-card-header">
                <i class="fas fa-credit-card"></i> {{ __('Select Payment Method') }}
            </div>
            <div class="checkout-card-body">
                <form id="paymentForm">
                    @csrf
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                    <input type="hidden" name="method" id="selectedMethod" value="">

                    <div class="payment-methods">

                        {{-- Mada --}}
                        <div class="payment-method-option">
                            <input type="radio" name="method" id="m_mada" value="mada" onchange="setMethod('mada')">
                            <label for="m_mada" class="payment-method-label">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fb/Mada_Logo.svg" alt="Mada">
                                <span class="method-name">Mada</span>
                                <span class="method-desc">{{ __('Mada Card') }}</span>
                            </label>
                        </div>

                        {{-- Visa / Mastercard --}}
                        <div class="payment-method-option">
                            <input type="radio" name="method" id="m_visa_master" value="visa_master" onchange="setMethod('visa_master')">
                            <label for="m_visa_master" class="payment-method-label">
                                <div style="display:flex; gap:8px;">
                                    <img src="https://t3.ftcdn.net/jpg/03/33/21/62/240_F_333216210_HjHUw1jjcYdGr3rRtYm3W1DIXAElEFJL.jpg" alt="Visa" style="height:20px;">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" style="height:20px;">
                                </div>
                                <span class="method-name">Visa / Master</span>
                                <span class="method-desc">{{ __('Instant Payment') }}</span>
                            </label>
                        </div>

                        {{-- Tabby --}}
                        <div class="payment-method-option">
                            <input type="radio" name="method" id="m_tabby" value="tabby" onchange="setMethod('tabby')">
                            <label for="m_tabby" class="payment-method-label">
                                <img src="https://uaelogos.ae/storage/1950/conversions/Tabby-thumb.png" alt="Tabby">
                                <span class="method-name">Tabby</span>
                                <span class="method-desc">{{ __('4 Installments') }}</span>
                            </label>
                        </div>

                        {{-- Tamara --}}
                        <div class="payment-method-option">
                            <input type="radio" name="method" id="m_tamara" value="tamara" onchange="setMethod('tamara')">
                            <label for="m_tamara" class="payment-method-label">
                                <img src="https://cdn.tamara.co/assets/svg/tamara-logo-badge-ar.svg" alt="Tamara">
                                <span class="method-name">Tamara</span>
                                <span class="method-desc">{{ __('3 Installments') }}</span>
                            </label>
                        </div>

                        {{-- Tap --}}
                        <div class="payment-method-option">
                            <input type="radio" name="method" id="m_tap" value="tap" onchange="setMethod('tap')">
                            <label for="m_tap" class="payment-method-label">
                                <img src="https://www.tap.company/content/images/2021/04/Tap-Logo-1.png" alt="Tap">
                                <span class="method-name">Tap</span>
                                <span class="method-desc">{{ __('Card Payment') }}</span>
                            </label>
                        </div>

                    </div>

                    <div id="methodError" style="display:none;margin-top:14px;padding:10px 14px;background:#fef2f2;border-radius:8px;color:#b91c1c;font-size:.85rem;">
                        <i class="fas fa-exclamation-circle"></i> {{ __('Please select a payment method first.') }}
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- RIGHT: Order Summary --}}
    <div>
        <div class="order-summary-card">
            <div class="order-summary-trip">
                @php $trip = $booking->trip; $img = $trip?->images?->first(); @endphp
                @if($img)
                    <img src="{{ asset('storage/' . $img->image_path) }}" class="order-trip-img" alt="">
                @else
                    <div class="order-trip-img-placeholder"><i class="fas fa-map-marked-alt"></i></div>
                @endif
                <div class="order-trip-name">{{ $trip?->title ?? __('Trip') }}</div>
                <div class="order-trip-meta">
                    <i class="fas fa-users"></i> {{ $booking->tickets_count }} {{ __('Passenger') }}
                    @if($trip?->toCountry)
                        &nbsp;·&nbsp;<i class="fas fa-globe"></i> {{ $trip->toCountry->name }}
                    @endif
                </div>
            </div>

            <div class="order-price-rows">
                <div class="order-price-row">
                    <span class="label">{{ __('Booking No') }}</span>
                    <span class="value">#{{ $booking->id }}</span>
                </div>
                <div class="order-price-row">
                    <span class="label">{{ __('Passengers Count') }}</span>
                    <span class="value">{{ $booking->tickets_count }}</span>
                </div>
            </div>

            <div class="order-total">
                <span class="total-label">{{ __('Total') }}</span>
                <span class="total-amount">{{ number_format($booking->total_price, 0) }} {{ __('SAR') }}</span>
            </div>

            <button class="btn-pay" id="payBtn" onclick="submitPayment()" type="button">
                <i class="fas fa-lock"></i> {{ __('Pay Now') }}
            </button>

            <div class="secure-note">
                <i class="fas fa-shield-alt"></i>
                {{ __('Secure and Encrypted Payment') }}
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
let selectedMethod = '';

function setMethod(method) {
    selectedMethod = method;
    document.getElementById('selectedMethod').value = method;
    document.getElementById('methodError').style.display = 'none';
}

function submitPayment() {
    if (!selectedMethod) {
        document.getElementById('methodError').style.display = 'block';
        return;
    }

    const btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> {{ __("Processing...") }}';

    const bookingId = {{ $booking->id }};

    // For redirect-based methods (Tabby, Tamara, Tap)
    if (['tabby', 'tamara', 'tap'].includes(selectedMethod)) {
        fetch('{{ route("payments.web.initiate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ booking_id: bookingId, method: selectedMethod })
        })
        .then(r => r.json())
        .then(data => {
            const redirectUrl = data.checkout_url || data.redirect_url || data.payment_url || data.url;
            if (redirectUrl) {
                window.location.href = redirectUrl;
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-lock"></i> {{ __("Pay Now") }}';
                alert(data.message || '{{ __("An error occurred, please try again.") }}');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-lock"></i> {{ __("Pay Now") }}';
        });
    } else {
        // HyperPay (Visa/Master/Mada) — redirect to existing web checkout
        window.location.href = `{{ url('payments/checkout') }}/${bookingId}/${selectedMethod}`;
    }
}
</script>
@endpush
