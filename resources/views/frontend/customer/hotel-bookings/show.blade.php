@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Hotel Booking Details') . ' #' . $booking->id)
@section('page-title', __('Hotel Booking Details') . ' #' . $booking->id)

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

/* Hotel Hero */
.hotel-hero {
    height: 200px;
    background: #f1f5f9;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 20px;
    position: relative;
}

.hotel-hero img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hotel-hero-placeholder {
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

/* Guest item */
.guest-item {
    background: #f8fafc;
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.guest-avatar {
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

.guest-info .g-name {
    font-weight: 700;
    font-size: .9rem;
    color: #111827;
}

.guest-info .g-meta {
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

<a href="{{ route('customer.hotel-bookings.index') }}" class="back-link">
    <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'right' : 'left' }}"></i>
    {{ __('Back to Hotel Bookings') }}
</a>

{{-- Status Timeline --}}
<div class="timeline" style="background:#fff;border-radius:14px;padding:20px 24px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:20px;">
    @php
        $currentState = $booking->booking_state;
        $states = [
            \App\Models\HotelBooking::STATE_AWAITING_PAYMENT => ['icon' => 'fa-clock', 'label' => __('Awaiting Payment')],
            \App\Models\HotelBooking::STATE_PREPARING        => ['icon' => 'fa-tasks', 'label' => __('Preparing')],
            \App\Models\HotelBooking::STATE_CONFIRMED        => ['icon' => 'fa-check-circle', 'label' => __('Confirmed')],
        ];
        
        $stateKeys = array_keys($states);
        $currentIndex = array_search($currentState, $stateKeys);
        if ($currentIndex === false) $currentIndex = -1;
    @endphp

    @if($currentState === \App\Models\HotelBooking::STATE_CANCELLED)
        <div class="timeline-step done active" style="flex: 1;">
            <div class="step-dot" style="border-color:#b91c1c; background:#b91c1c; color:#fff;">
                <i class="fas fa-times"></i>
            </div>
            <div class="step-label" style="color:#b91c1c;">{{ __('Cancelled') }}</div>
        </div>
    @else
        @foreach($states as $key => $data)
            @php
                $stepIndex = array_search($key, $stateKeys);
                $isDone = $stepIndex <= $currentIndex;
                $isActive = $stepIndex === $currentIndex;
            @endphp
            <div class="timeline-step {{ $isDone ? 'done' : '' }} {{ $isActive ? 'active' : '' }}">
                <div class="step-dot"><i class="fas {{ $data['icon'] }}"></i></div>
                <div class="step-label">{{ $data['label'] }}</div>
            </div>
        @endforeach
    @endif
</div>

<div class="booking-detail-grid">

    {{-- LEFT COLUMN --}}
    <div>
        {{-- Hotel Info --}}
        <div class="detail-card">
            <div class="detail-card-header">
                <i class="fas fa-hotel"></i> {{ __('Hotel Information') }}
            </div>
            <div class="detail-card-body">
                <div class="hotel-hero">
                    @if($booking->hotel_image)
                        <img src="{{ $booking->hotel_image }}" alt="">
                    @else
                        <div class="hotel-hero-placeholder"><i class="fas fa-hotel"></i></div>
                    @endif
                </div>
                <h3 style="font-size:1.1rem;font-weight:700;margin:0 0 14px;">{{ $booking->hotel_name }}</h3>

                <div class="info-row">
                    <span class="info-label">{{ __('Address') }}</span>
                    <span class="info-value">{{ $booking->hotel_address ?? $booking->city_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('City') }}</span>
                    <span class="info-value">{{ $booking->city_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Check-in') }}</span>
                    <span class="info-value text-success">{{ $booking->check_in_date->format('d M, Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Check-out') }}</span>
                    <span class="info-value text-danger">{{ $booking->check_out_date->format('d M, Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Staying') }}</span>
                    <span class="info-value">{{ $booking->nights_count }} {{ __('Nights') }} / {{ $booking->rooms_count }} {{ __('Rooms') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Room Type') }}</span>
                    <span class="info-value">{{ $booking->room_type_name }}</span>
                </div>
            </div>
        </div>

        {{-- Guests --}}
        <div class="detail-card">
            <div class="detail-card-header">
                <i class="fas fa-users"></i> {{ __('Guests Details') }}
            </div>
            <div class="detail-card-body">
                @foreach($booking->guests as $guest)
                    <div class="guest-item">
                        <div class="guest-avatar">{{ strtoupper(substr($guest->first_name, 0, 1)) }}</div>
                        <div class="guest-info">
                            <div class="g-name">{{ $guest->title }}. {{ $guest->first_name }} {{ $guest->last_name }} @if($guest->is_lead) <span class="badge bg-secondary" style="font-size:0.6rem;">{{ __('Lead Guest') }}</span> @endif</div>
                            <div class="g-meta">
                                {{ ucfirst($guest->type) }} · 
                                @if($guest->passport_number) {{ __('Passport') }}: {{ $guest->passport_number }} · @endif
                                {{ $guest->nationality }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- History --}}
        @if($booking->histories->count() > 0)
        <div class="detail-card">
            <div class="detail-card-header">
                <i class="fas fa-history"></i> {{ __('Booking Timeline') }}
            </div>
            <div class="detail-card-body">
                <div class="booking-history">
                    @foreach($booking->histories as $history)
                        <div class="info-row">
                            <span class="info-label">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                            <span class="info-value">{{ $history->description }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- RIGHT COLUMN (Sidebar) --}}
    <div>
        {{-- Price Summary --}}
        <div class="summary-total">
            <div class="amount">{{ number_format($booking->total_price, 0) }} <span class="currency-label" style="font-size:1rem;">{{ $booking->currency }}</span></div>
            <div class="amount-label">{{ __('Total Amount') }}</div>
        </div>

        @if($booking->booking_state === \App\Models\HotelBooking::STATE_AWAITING_PAYMENT)
            <a href="{{ route('customer.payments.checkout', $booking->id) }}" class="action-btn action-btn-primary">
                <i class="fas fa-credit-card"></i> {{ __('Pay Now') }}
            </a>
        @endif

        @if($booking->status !== \App\Models\HotelBooking::STATUS_CANCELLED)
            <form action="{{ route('customer.hotel-bookings.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to cancel this booking?') }}')">
                @csrf
                <button type="submit" class="action-btn action-btn-outline" style="color: #b91c1c;">
                    <i class="fas fa-times-circle"></i> {{ __('Cancel Booking') }}
                </button>
            </form>
        @endif

        <div class="detail-card">
            <div class="detail-card-header">
                <i class="fas fa-info-circle"></i> {{ __('Booking Meta') }}
            </div>
            <div class="detail-card-body">
                <div class="info-row">
                    <span class="info-label">{{ __('Booking ID') }}</span>
                    <span class="info-value">#{{ $booking->id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('TBO Ref') }}</span>
                    <span class="info-value">{{ $booking->tbo_booking_id ?? '---' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Booked on') }}</span>
                    <span class="info-value">{{ $booking->created_at->format('d M, Y') }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
