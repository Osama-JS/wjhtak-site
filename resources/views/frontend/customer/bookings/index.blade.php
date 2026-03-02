@extends('frontend.customer.layouts.customer-layout')

@section('title', __('My Bookings'))
@section('page-title', __('My Bookings'))

@push('styles')
<style>
.filter-bar {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 22px;
}

.filter-btn {
    padding: 8px 18px;
    border-radius: 30px;
    border: 1.5px solid #e5e7eb;
    background: #fff;
    color: #6b7280;
    font-size: .85rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s;
}

.filter-btn:hover,
.filter-btn.active {
    background: var(--accent-color, #e8532e);
    border-color: var(--accent-color, #e8532e);
    color: #fff;
}

.booking-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    margin-bottom: 16px;
    overflow: hidden;
    transition: box-shadow .2s;
}

.booking-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.1); }

.booking-card-body {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 18px 20px;
}

.booking-img {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    object-fit: cover;
    flex-shrink: 0;
}

.booking-img-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: #94a3b8;
    flex-shrink: 0;
}

.booking-details { flex: 1; min-width: 0; }

.booking-trip-name {
    font-weight: 700;
    font-size: 1rem;
    color: #111827;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.booking-meta-row {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    font-size: .8rem;
    color: #6b7280;
}

.booking-meta-row span { display: flex; align-items: center; gap: 4px; }

.booking-right {
    text-align: end;
    flex-shrink: 0;
}

.booking-price {
    font-size: 1.2rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 6px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: .75rem;
    font-weight: 600;
}

.status-pending   { background: #fff7ed; color: #c2410c; }
.status-confirmed { background: #f0fdf4; color: #15803d; }
.status-cancelled { background: #fef2f2; color: #b91c1c; }

.booking-card-footer {
    border-top: 1px solid #f3f4f6;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
}

.booking-card-footer .booking-date-info {
    font-size: .8rem;
    color: #9ca3af;
}

.booking-actions { display: flex; gap: 8px; }

.btn-sm {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: .8rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all .2s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-outline {
    border: 1.5px solid #e5e7eb;
    background: #fff;
    color: #374151;
}

.btn-outline:hover { border-color: var(--accent-color, #e8532e); color: var(--accent-color, #e8532e); }

.btn-accent {
    background: var(--accent-color, #e8532e);
    color: #fff;
}

.btn-accent:hover { background: #d04525; color: #fff; }

.btn-danger { background: #fef2f2; color: #b91c1c; border: 1.5px solid #fca5a5; }
.btn-danger:hover { background: #b91c1c; color: #fff; }

/* Empty state */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
}

.empty-state .empty-icon {
    font-size: 4rem;
    color: #e2e8f0;
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: 8px;
}

.empty-state p {
    color: #9ca3af;
    font-size: .9rem;
    margin-bottom: 20px;
}
</style>
@endpush

@section('content')

{{-- Filter Bar --}}
<div class="filter-bar">
    <a href="{{ route('customer.bookings.index') }}" class="filter-btn {{ !request('status') ? 'active' : '' }}">
        {{ __('All') }}
    </a>
    <a href="{{ route('customer.bookings.index', ['status' => 'pending']) }}" class="filter-btn {{ request('status') === 'pending' ? 'active' : '' }}">
        <i class="fas fa-clock"></i> {{ __('Pending') }}
    </a>
    <a href="{{ route('customer.bookings.index', ['status' => 'confirmed']) }}" class="filter-btn {{ request('status') === 'confirmed' ? 'active' : '' }}">
        <i class="fas fa-check-circle"></i> {{ __('Confirmed') }}
    </a>
    <a href="{{ route('customer.bookings.index', ['status' => 'cancelled']) }}" class="filter-btn {{ request('status') === 'cancelled' ? 'active' : '' }}">
        <i class="fas fa-times-circle"></i> {{ __('Cancelled') }}
    </a>
</div>

{{-- Bookings --}}
@forelse($bookings as $booking)
    @php
        $trip = $booking->trip;
        $image = $trip?->images?->first();
    @endphp
    <div class="booking-card">
        <div class="booking-card-body">
            {{-- Image --}}
            @if($image)
                <img src="{{ asset('storage/' . $image->image_path) }}" class="booking-img" alt="">
            @else
                <div class="booking-img-placeholder"><i class="fas fa-map-marked-alt"></i></div>
            @endif

            {{-- Details --}}
            <div class="booking-details">
                <div class="booking-trip-name">{{ $trip?->title ?? __('Trip') }}</div>
                <div class="booking-meta-row">
                    @if($trip?->toCountry)
                        <span><i class="fas fa-globe"></i> {{ $trip->toCountry->name }}</span>
                    @endif
                    <span><i class="fas fa-users"></i> {{ $booking->tickets_count }} {{ $booking->tickets_count > 1 ? __('Passengers') : __('Passenger') }}</span>
                    <span><i class="fas fa-calendar"></i> {{ $booking->booking_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}</span>
                </div>
            </div>

            {{-- Right side --}}
            <div class="booking-right">
                <div class="booking-price">{{ number_format($booking->total_price, 0) }} {{ __('SAR') }}</div>
                <span class="status-badge status-{{ $booking->status }}">
                    @if($booking->status === 'pending')
                        <i class="fas fa-clock"></i> {{ __('Pending') }}
                    @elseif($booking->status === 'confirmed')
                        <i class="fas fa-check-circle"></i> {{ __('Confirmed') }}
                    @else
                        <i class="fas fa-times-circle"></i> {{ __('Cancelled') }}
                    @endif
                </span>
            </div>
        </div>

        <div class="booking-card-footer">
            <div class="booking-date-info">
                {{ __('Booking No') }}: #{{ $booking->id }} · {{ $booking->created_at->format('d/m/Y H:i') }}
            </div>
            <div class="booking-actions">
                <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn-sm btn-outline">
                    <i class="fas fa-eye"></i> {{ __('Details') }}
                </a>
                @if($booking->status === 'pending')
                    <a href="{{ route('customer.payments.checkout', $booking->id) }}" class="btn-sm btn-accent">
                        <i class="fas fa-credit-card"></i> {{ __('Complete Payment') }}
                    </a>
                @elseif($booking->status === 'confirmed')
                    <a href="{{ route('customer.bookings.invoice', $booking->id) }}" class="btn-sm btn-outline">
                        <i class="fas fa-file-pdf"></i> {{ __('Invoice') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-ticket-alt"></i></div>
        <h3>{{ __('No bookings found') }}</h3>
        <p>{{ __('You have not made any bookings yet. Start exploring available trips!') }}</p>
        <a href="{{ route('trips.index') }}" class="btn-sm btn-accent" style="display:inline-flex;">
            <i class="fas fa-search"></i> {{ __('Explore Trips') }}
        </a>
    </div>
@endforelse

{{-- Pagination --}}
@if($bookings->hasPages())
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $bookings->links() }}
    </div>
@endif

@endsection
