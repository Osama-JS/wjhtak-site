@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Booking Details') . ' #' . $booking->id)
@section('page-title', __('Booking Details') . ' #' . $booking->id)

@push('styles')
<style>
.booking-detail-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}

@media (max-width: 900px) { .booking-detail-grid { grid-template-columns: 1fr; } }

.detail-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    margin-bottom: 20px;
    overflow: hidden;
}

.detail-card-header {
    padding: 16px 22px;
    border-bottom: 1px solid #f3f4f6;
    font-weight: 700;
    font-size: .95rem;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
}

.detail-card-header i { color: var(--accent-color, #e8532e); }

.detail-card-body { padding: 20px 22px; }

/* Trip Hero */
.trip-hero {
    height: 200px;
    background: #f1f5f9;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 20px;
    position: relative;
}

.trip-hero img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.trip-hero-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: #94a3b8;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
}

/* Status timeline */
.timeline {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 20px;
}

.timeline-step {
    flex: 1;
    text-align: center;
    position: relative;
}

.timeline-step::after {
    content: '';
    position: absolute;
    top: 16px;
    width: 100%;
    height: 2px;
    background: #e5e7eb;
}

html[dir="ltr"] .timeline-step::after { left: 50%; }
html[dir="rtl"] .timeline-step::after { right: 50%; }

.timeline-step:last-child::after { display: none; }

.timeline-step.done::after { background: var(--accent-color, #e8532e); }

.step-dot {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid #e5e7eb;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 6px;
    font-size: .75rem;
    color: #94a3b8;
    position: relative;
    z-index: 1;
}

.timeline-step.active .step-dot {
    border-color: var(--accent-color, #e8532e);
    background: var(--accent-color, #e8532e);
    color: #fff;
}

.timeline-step.done .step-dot {
    border-color: var(--accent-color, #e8532e);
    background: var(--accent-color, #e8532e);
    color: #fff;
}

.step-label {
    font-size: .73rem;
    color: #6b7280;
    font-weight: 600;
}

.timeline-step.active .step-label,
.timeline-step.done .step-label {
    color: var(--accent-color, #e8532e);
}

/* Info row */
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 10px 0;
    border-bottom: 1px solid #f9fafb;
    font-size: .9rem;
}

.info-row:last-child { border-bottom: none; }

.info-row .info-label { color: #6b7280; font-weight: 500; }
.info-row .info-value { color: #111827; font-weight: 600; text-align: end; }

/* Passenger card */
.passenger-item {
    background: #f8fafc;
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.passenger-avatar {
    width: 38px;
    height: 38px;
    background: var(--accent-color, #e8532e);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: .9rem;
    flex-shrink: 0;
}

.passenger-info .p-name {
    font-weight: 700;
    font-size: .9rem;
    color: #111827;
}

.passenger-info .p-meta {
    font-size: .77rem;
    color: #6b7280;
    margin-top: 2px;
}

/* Sidebar summary */
.summary-total {
    background: linear-gradient(135deg, #1a2537, #2d3f5e);
    border-radius: 12px;
    padding: 20px;
    color: #fff;
    text-align: center;
    margin-bottom: 14px;
}

.summary-total .amount {
    font-size: 2rem;
    font-weight: 700;
}

.summary-total .amount-label {
    font-size: .85rem;
    opacity: .7;
    margin-top: 2px;
}

/* Action buttons */
.action-btn {
    display: block;
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    text-align: center;
    font-weight: 700;
    font-size: .9rem;
    text-decoration: none;
    border: none;
    cursor: pointer;
    margin-bottom: 10px;
    transition: all .2s;
}

.action-btn-primary {
    background: var(--accent-color, #e8532e);
    color: #fff;
}

.action-btn-primary:hover { background: #d04525; color: #fff; }

.action-btn-outline {
    border: 1.5px solid #e5e7eb;
    background: #fff;
    color: #374151;
}

.action-btn-outline:hover { border-color: var(--accent-color, #e8532e); color: var(--accent-color, #e8532e); }

.action-btn-danger {
    background: #fef2f2;
    color: #b91c1c;
    border: 1.5px solid #fca5a5;
}

.action-btn-danger:hover { background: #b91c1c; color: #fff; }

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: .8rem;
    font-weight: 700;
}

.status-pending   { background: #fff7ed; color: #c2410c; }
.status-confirmed { background: #f0fdf4; color: #15803d; }
.status-cancelled { background: #fef2f2; color: #b91c1c; }

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

<a href="{{ route('customer.bookings.index') }}" class="back-link">
    <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'right' : 'left' }}"></i>
    {{ __('Back to Bookings') }}
</a>

{{-- Status Timeline --}}
@php
    $latestTransfer = $booking->bankTransfers()->latest()->first();
@endphp

@if(request()->has('bank_transfer_submitted'))
    <div style="background:#f0fdf4; color:#15803d; padding:15px 20px; border-radius:10px; margin-bottom:20px; border:1px solid #bbf7d0;">
        <i class="fas fa-check-circle"></i> <strong>{{ __('Success!') }}</strong> {{ __('Your bank transfer receipt has been submitted and is under review. You will be notified once approved.') }}
    </div>
@endif

@if($latestTransfer)
    @if($latestTransfer->status === 'pending')
        <div style="background:#fffbeb; color:#b45309; padding:15px 20px; border-radius:10px; margin-bottom:20px; border:1px solid #fde68a;">
            <i class="fas fa-clock"></i> <strong>{{ __('Transfer Under Review') }}</strong>: {{ __('Your bank transfer submitted on :date is currently being reviewed by our team.', ['date' => $latestTransfer->created_at->format('d/m/Y H:i')]) }}
        </div>
    @elseif($latestTransfer->status === 'rejected')
        <div style="background:#fef2f2; color:#b91c1c; padding:15px 20px; border-radius:10px; margin-bottom:20px; border:1px solid #fecaca;">
            <i class="fas fa-exclamation-triangle"></i> <strong>{{ __('Transfer Rejected') }}</strong><br>
            {{ __('Your previous bank transfer was rejected for the following reason:') }}
            <div style="margin-top:5px; padding:10px; background:#fff; border-radius:6px; font-size:0.9rem;">
                {{ $latestTransfer->rejection_reason ?? __('No reason provided.') }}
            </div>
            <div style="margin-top:10px; font-size: 0.85rem;">
                {{ __('Please try paying again with a valid receipt or another payment method.') }}
            </div>
        </div>
    @endif
@endif

<div class="timeline" style="background:#fff;border-radius:14px;padding:20px 24px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:20px;">
    @php
        $steps = [
            'pending'   => __('Booked'),
            'payment'   => __('Awaiting Payment'),
            'confirmed' => __('Confirmed'),
        ];
        $currentStatus = $booking->status;
        $activeReached = false;
    @endphp

    <div class="timeline-step {{ in_array($currentStatus, ['pending','confirmed']) ? 'done' : '' }}">
        <div class="step-dot"><i class="fas fa-check"></i></div>
        <div class="step-label">{{ __('Booked') }}</div>
    </div>

    <div class="timeline-step {{ $currentStatus === 'pending' ? 'active' : ($currentStatus === 'confirmed' ? 'done' : '') }}">
        <div class="step-dot"><i class="fas fa-credit-card"></i></div>
        <div class="step-label">{{ __('Awaiting Payment') }}</div>
    </div>

    <div class="timeline-step {{ $currentStatus === 'confirmed' ? 'done active' : '' }}">
        <div class="step-dot"><i class="fas fa-check-circle"></i></div>
        <div class="step-label">{{ __('Confirmed') }}</div>
    </div>
</div>

<div class="booking-detail-grid">

    {{-- LEFT COLUMN --}}
    <div>
        {{-- Trip Info --}}
        <div class="detail-card">
            <div class="detail-card-header">
                <i class="fas fa-map-marked-alt"></i> {{ __('Trip Information') }}
            </div>
            <div class="detail-card-body">
                @php $trip = $booking->trip; $img = $trip?->images?->first(); @endphp
                <div class="trip-hero">
                    @if($img)
                        <img src="{{ asset('storage/' . $img->image_path) }}" alt="">
                    @else
                        <div class="trip-hero-placeholder"><i class="fas fa-map-marked-alt"></i></div>
                    @endif
                </div>
                <h3 style="font-size:1.1rem;font-weight:700;margin:0 0 14px;">{{ $trip?->title ?? __('Trip') }}</h3>

                <div class="info-row">
                    <span class="info-label">{{ __('Destination') }}</span>
                    <span class="info-value">{{ $trip?->toCountry?->name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Booking No') }}</span>
                    <span class="info-value">#{{ $booking->id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Booking Date') }}</span>
                    <span class="info-value">{{ $booking->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Passengers Count') }}</span>
                    <span class="info-value">{{ $booking->tickets_count }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Status') }}</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ $booking->status }}">
                            {{ $booking->status === 'pending' ? __('Pending') : ($booking->status === 'confirmed' ? __('Confirmed') : __('Cancelled')) }}
                        </span>
                    </span>
                </div>
                @if($booking->status === 'cancelled' && $booking->cancellation_reason)
                    <div class="info-row" style="background-color: #fef2f2; border-radius: 8px; padding: 12px; margin-top: 10px; border: 1px solid #fecaca;">
                        <span class="info-label" style="color: #b91c1c; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ __('Cancellation Reason') }}:</span>
                        <span class="info-value" style="color: #b91c1c; font-size: 0.9rem;">{{ $booking->cancellation_reason }}</span>
                    </div>
                @endif
                @if($booking->notes)
                    <div class="info-row">
                        <span class="info-label">{{ __('Notes') }}</span>
                        <span class="info-value">{{ $booking->notes }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Passengers --}}
        <div class="detail-card">
            <div class="detail-card-header">
                <i class="fas fa-users"></i> {{ __('Passengers') }} ({{ $booking->passengers->count() }})
            </div>
            <div class="detail-card-body">
                @foreach($booking->passengers as $i => $p)
                    <div class="passenger-item">
                        <div class="passenger-avatar">{{ $i + 1 }}</div>
                        <div class="passenger-info">
                            <div class="p-name">{{ $p->name }}</div>
                            <div class="p-meta">
                                @if($p->nationality) {{ $p->nationality }} · @endif
                                @if($p->passport_number) {{ __('Passport') }}: {{ $p->passport_number }} @endif
                                @if($p->phone) · {{ $p->phone }} @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Payment info (if exists) --}}
        @if($booking->payments->count() > 0)
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fas fa-receipt"></i> {{ __('Payment Information') }}
                </div>
                <div class="detail-card-body">
                    @foreach($booking->payments as $payment)
                        <div class="info-row">
                            <span class="info-label">{{ __('Payment Gateway') }}</span>
                            <span class="info-value">{{ strtoupper($payment->payment_gateway) }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">{{ __('Transaction No') }}</span>
                            <span class="info-value" style="font-size:.8rem;">{{ $payment->transaction_id ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">{{ __('Amount Paid') }}</span>
                            <span class="info-value" style="color:#16a34a;">{{ number_format($payment->amount, 2) }} {{ __('SAR') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">{{ __('Payment Date') }}</span>
                            <span class="info-value">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- RIGHT COLUMN (Sidebar) --}}
    <div>
        {{-- Price Summary --}}
        <div class="summary-total">
            <div class="amount">{{ number_format($booking->total_price, 0) }} <small style="font-size:1rem;">{{ __('SAR') }}</small></div>
            <div class="amount-label">{{ __('Total Price') }}</div>
        </div>

        {{-- Actions --}}
        @if($booking->status === 'pending')
            @if(!$latestTransfer || $latestTransfer->status === 'rejected')
                <a href="{{ route('customer.payments.checkout', $booking->id) }}" class="action-btn action-btn-primary">
                    <i class="fas fa-credit-card"></i> {{ __('Complete Payment Now') }}
                </a>
            @else
                <button class="action-btn" style="background:#e5e7eb; color:#6b7280; cursor:not-allowed;" disabled>
                    <i class="fas fa-clock"></i> {{ __('Payment Under Review') }}
                </button>
            @endif

            <form method="POST" action="{{ route('customer.bookings.cancel', $booking->id) }}"
                  onsubmit="return confirm('{{ __('Are you sure you want to cancel this booking?') }}')">
                @csrf
                <button class="action-btn action-btn-danger" type="submit">
                    <i class="fas fa-times"></i> {{ __('Cancel Booking') }}
                </button>
            </form>
        @elseif($booking->status === 'confirmed')
            @if($booking->ticket_url)
                <a href="{{ $booking->ticket_url }}" target="_blank" class="action-btn" style="background: #10b981; color: #fff;">
                    <i class="fas fa-ticket-alt"></i> {{ __('Download Tickets') }}
                </a>
            @endif
            <a href="{{ $booking->ticket_url ? '#' : route('customer.bookings.invoice', $booking->id) }}" class="action-btn action-btn-outline" {!! $booking->ticket_url ? 'style="display:none;"' : '' !!}>
                <i class="fas fa-file-pdf"></i> {{ __('Download Invoice') }}
            </a>
        @endif

        <a href="{{ route('customer.bookings.index') }}" class="action-btn action-btn-outline">
            <i class="fas fa-list"></i> {{ __('All Bookings') }}
        </a>

        @if($trip)
            <a href="{{ route('trips.show', $trip->id) }}" class="action-btn action-btn-outline">
                <i class="fas fa-info-circle"></i> {{ __('Trip Details') }}
            </a>
        @endif
    </div>

</div>

@endsection
