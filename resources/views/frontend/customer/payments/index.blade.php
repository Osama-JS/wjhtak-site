@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Payments & Invoices'))
@section('page-title', __('Payments & Invoices'))

@push('styles')
<style>
.payment-list-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    overflow: hidden;
}

.payment-list-header {
    padding: 18px 22px;
    border-bottom: 1px solid #f3f4f6;
    font-weight: 700;
    font-size: .95rem;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
}

.payment-list-header i { color: var(--accent-color, #e8532e); }

.payment-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 22px;
    border-bottom: 1px solid #f9fafb;
    transition: background .15s;
}

.payment-row:last-child { border-bottom: none; }
.payment-row:hover { background: #fafafa; }

.payment-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #f0fdf4;
    color: #16a34a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.payment-info { flex: 1; min-width: 0; }
.payment-trip-name { font-weight: 700; font-size: .9rem; color: #111827; }
.payment-meta { font-size: .77rem; color: #9ca3af; margin-top: 2px; }

.payment-amount {
    font-weight: 700;
    font-size: 1rem;
    color: #111827;
    text-align: end;
}

.payment-amount .currency { font-size: .78rem; color: #9ca3af; font-weight: 400; }

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: .73rem;
    font-weight: 600;
}

.badge-success { background: #f0fdf4; color: #15803d; }
.badge-pending  { background: #fff7ed; color: #c2410c; }
.badge-failed   { background: #fef2f2; color: #b91c1c; }

.dl-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    background: #f1f5f9;
    border-radius: 8px;
    text-decoration: none;
    color: #374151;
    font-size: .78rem;
    font-weight: 600;
    transition: all .2s;
    white-space: nowrap;
}

.dl-btn:hover { background: var(--accent-color, #e8532e); color: #fff; }

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}

.empty-state i { font-size: 3rem; display: block; margin-bottom: 12px; }
.empty-state p { font-size: .9rem; }
</style>
@endpush

@section('content')
<div class="payment-list-card">
    <div class="payment-list-header">
        <i class="fas fa-credit-card"></i>
        {{ __('Payment Records') }}
    </div>

    @forelse($payments as $payment)
        <div class="payment-row">
            <div class="payment-icon">
                <i class="fas fa-receipt"></i>
            </div>

            <div class="payment-info">
                <div class="payment-trip-name">
                    {{ $payment->booking?->trip?->title ?? __('Trip') }}
                </div>
                <div class="payment-meta">
                    {{ $payment->created_at->format('d/m/Y H:i') }}
                    · {{ strtoupper($payment->payment_gateway) }}
                    @if($payment->payment_method)
                        · {{ strtoupper($payment->payment_method) }}
                    @endif
                </div>
            </div>

            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
                <div class="payment-amount">
                    {{ number_format($payment->amount, 2) }}
                    <span class="currency">{{ __('SAR') }}</span>
                </div>
                <span class="status-badge badge-{{ $payment->status === 'paid' ? 'success' : ($payment->status === 'pending' ? 'pending' : 'failed') }}">
                    @if($payment->status === 'paid') <i class="fas fa-check-circle"></i> {{ __('Paid') }}
                    @elseif($payment->status === 'pending') <i class="fas fa-clock"></i> {{ __('Pending') }}
                    @else <i class="fas fa-times-circle"></i> {{ __('Failed') }}
                    @endif
                </span>
            </div>

            @if($payment->status === 'paid' && $payment->booking)
                <a href="{{ route('customer.bookings.invoice', $payment->booking->id) }}" class="dl-btn">
                    <i class="fas fa-file-pdf"></i> {{ __('Invoice') }}
                </a>
            @endif
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-receipt"></i>
            <p>{{ __('No payments yet.') }}</p>
        </div>
    @endforelse
</div>

@if($payments->hasPages())
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $payments->links() }}
    </div>
@endif
@endsection
