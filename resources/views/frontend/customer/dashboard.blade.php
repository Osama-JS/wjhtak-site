@extends('frontend.customer.layouts.customer-layout')

@section('title', __('لوحة التحكم'))
@section('page-title', __('لوحة التحكم'))

@push('styles')
<style>
/* ─── Stats Cards ─── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 28px;
}

@media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 520px)  { .stats-grid { grid-template-columns: 1fr; } }

.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 22px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    transition: transform .2s, box-shadow .2s;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,.1);
}

.stat-card .stat-icon {
    width: 54px;
    height: 54px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}

.stat-icon-blue   { background: #eff6ff; color: #2563eb; }
.stat-icon-green  { background: #f0fdf4; color: #16a34a; }
.stat-icon-orange { background: #fff7ed; color: #ea580c; }
.stat-icon-red    { background: #fef2f2; color: #dc2626; }
.stat-icon-purple { background: #faf5ff; color: #9333ea; }

.stat-card .stat-info .stat-label {
    font-size: .78rem;
    color: #6b7280;
    margin-bottom: 2px;
}

.stat-card .stat-info .stat-value {
    font-size: 1.65rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}

/* ─── Section Cards ─── */
.dash-section {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    margin-bottom: 24px;
}

.dash-section-header {
    padding: 18px 22px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dash-section-header h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.dash-section-header h3 i {
    color: var(--accent-color, #e8532e);
}

.dash-section-link {
    font-size: .83rem;
    color: var(--accent-color, #e8532e);
    text-decoration: none;
    font-weight: 600;
}

.dash-section-body {
    padding: 16px 22px;
}

/* ─── Booking Row ─── */
.booking-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid #f9fafb;
}

.booking-row:last-child { border-bottom: none; }

.booking-thumb {
    width: 56px;
    height: 56px;
    border-radius: 10px;
    object-fit: cover;
    flex-shrink: 0;
}

.booking-thumb-placeholder {
    width: 56px;
    height: 56px;
    border-radius: 10px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 1.3rem;
    flex-shrink: 0;
}

.booking-info { flex: 1; min-width: 0; }

.booking-title {
    font-weight: 600;
    font-size: .9rem;
    color: #111827;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.booking-meta {
    font-size: .78rem;
    color: #6b7280;
    margin-top: 2px;
}

.booking-price {
    font-weight: 700;
    font-size: .95rem;
    color: #111827;
    white-space: nowrap;
}

/* ─── Status Badge ─── */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: .73rem;
    font-weight: 600;
    white-space: nowrap;
}

.status-pending   { background: #fff7ed; color: #c2410c; }
.status-confirmed { background: #f0fdf4; color: #15803d; }
.status-cancelled { background: #fef2f2; color: #b91c1c; }

/* ─── Empty State ─── */
.empty-state {
    text-align: center;
    padding: 32px 20px;
    color: #9ca3af;
}

.empty-state i { font-size: 2.5rem; margin-bottom: 10px; display: block; }
.empty-state p { font-size: .9rem; margin-bottom: 16px; }

/* ─── Welcome Banner ─── */
.welcome-banner {
    background: #ffffff;
    border-radius: 16px;
    padding: 32px 36px;
    margin-bottom: 28px;
    color: #1a2537;
    position: relative;
    overflow: hidden;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
}

.welcome-banner::after {
    content: '';
    position: absolute;
    top: -20px;
    inset-inline-end: -20px;
    width: 120px;
    height: 120px;
    background: #f8fafc;
    border-radius: 50%;
    z-index: 0;
}

.welcome-banner h2, .welcome-banner p {
    position: relative;
    z-index: 1;
}

.welcome-banner h2 {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0 0 8px;
    color: #0f172a;
}

.welcome-banner p {
    font-size: .95rem;
    color: #64748b;
    margin: 0;
    font-weight: 500;
}

/* ─── 2-column grid ─── */
.dash-two-cols {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 900px) { .dash-two-cols { grid-template-columns: 1fr; } }

/* ─── Payment Row ─── */
.payment-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f9fafb;
    gap: 10px;
}

.payment-row:last-child { border-bottom: none; }

.payment-row .pay-info .pay-trip {
    font-weight: 600;
    font-size: .88rem;
    color: #111827;
}

.payment-row .pay-info .pay-date {
    font-size: .75rem;
    color: #9ca3af;
}

.payment-row .pay-amount {
    font-weight: 700;
    font-size: .95rem;
    color: #16a34a;
    white-space: nowrap;
}
</style>
@endpush

@section('content')

{{-- Welcome Banner --}}
<div class="welcome-banner">
    <h2>{{ __('Welcome') }}, {{ auth()->user()->first_name }}! 👋</h2>
    <p>{{ __('Manage your bookings, favorites, and payments easily.') }}</p>
</div>

{{-- Stats Cards --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">
            <i class="fas fa-ticket-alt"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Total Bookings') }}</div>
            <div class="stat-value">{{ $totalBookings }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-orange">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Pending') }}</div>
            <div class="stat-value">{{ $pendingBookings }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Confirmed') }}</div>
            <div class="stat-value">{{ $confirmedBookings }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-purple">
            <i class="fas fa-heart"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Favorites') }}</div>
            <div class="stat-value">{{ $favoritesCount }}</div>
        </div>
    </div>
</div>

{{-- Two columns --}}
<div class="dash-two-cols">

    {{-- Upcoming Bookings --}}
    <div class="dash-section">
        <div class="dash-section-header">
            <h3><i class="fas fa-calendar-alt"></i> {{ __('Latest Bookings') }}</h3>
            <a href="{{ route('customer.bookings.index') }}" class="dash-section-link">{{ __('View All') }}</a>
        </div>
        <div class="dash-section-body">
            @forelse($upcomingBookings as $booking)
                <div class="booking-row">
                    @if($booking->trip && $booking->trip->image_url)
                        <img src="{{ $booking->trip->image_url }}" alt="" class="booking-thumb">
                    @else
                        <div class="booking-thumb-placeholder"><i class="fas fa-map-marked-alt"></i></div>
                    @endif

                    <div class="booking-info">
                        <div class="booking-title">{{ $booking->trip->title ?? __('Trip') }}</div>
                        <div class="booking-meta">
                            <span class="status-badge status-{{ $booking->status }}">
                                {{ $booking->status === 'pending' ? __('Pending') : ($booking->status === 'confirmed' ? __('Confirmed') : __('Cancelled')) }}
                            </span>
                            · {{ $booking->tickets_count }} {{ __('Passenger') }}
                        </div>
                    </div>
                    <div class="booking-price">{{ number_format($booking->total_price, 0) }} {{ __('SAR') }}</div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-ticket-alt"></i>
                    <p>{{ __('No bookings yet') }}</p>
                    <a href="{{ route('trips.index') }}" class="btn btn-accent btn-sm">{{ __('Explore Trips') }}</a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Payments --}}
    <div class="dash-section">
        <div class="dash-section-header">
            <h3><i class="fas fa-credit-card"></i> {{ __('Latest Payments') }}</h3>
            <a href="{{ route('customer.payments.index') }}" class="dash-section-link">{{ __('View All') }}</a>
        </div>
        <div class="dash-section-body">
            @forelse($recentPayments as $payment)
                <div class="payment-row">
                    <div class="pay-info">
                        <div class="pay-trip">{{ $payment->booking->trip->title ?? __('Trip') }}</div>
                        <div class="pay-date">{{ $payment->created_at->format('d/m/Y') }} · {{ strtoupper($payment->payment_gateway) }}</div>
                    </div>
                    <div class="pay-amount">+{{ number_format($payment->amount, 0) }} {{ __('SAR') }}</div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-receipt"></i>
                    <p>{{ __('No payments yet') }}</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- Quick Links --}}
<div class="dash-section">
    <div class="dash-section-header">
        <h3><i class="fas fa-bolt"></i> {{ __('Quick Access') }}</h3>
    </div>
    <div class="dash-section-body" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; padding: 18px 22px;">
        <a href="{{ route('trips.index') }}" style="display:flex;align-items:center;gap:10px;padding:14px 16px;background:#f8fafc;border-radius:10px;text-decoration:none;color:#374151;font-weight:600;font-size:.88rem;transition:all .2s;" onmouseover="this.style.background='var(--accent-color,#e8532e)';this.style.color='#fff'" onmouseout="this.style.background='#f8fafc';this.style.color='#374151'">
            <i class="fas fa-search"></i> {{ __('Explore Trips') }}
        </a>
        <a href="{{ route('customer.bookings.index') }}" style="display:flex;align-items:center;gap:10px;padding:14px 16px;background:#f8fafc;border-radius:10px;text-decoration:none;color:#374151;font-weight:600;font-size:.88rem;transition:all .2s;" onmouseover="this.style.background='var(--accent-color,#e8532e)';this.style.color='#fff'" onmouseout="this.style.background='#f8fafc';this.style.color='#374151'">
            <i class="fas fa-ticket-alt"></i> {{ __('My Bookings') }}
        </a>
        <a href="{{ route('customer.favorites.index') }}" style="display:flex;align-items:center;gap:10px;padding:14px 16px;background:#f8fafc;border-radius:10px;text-decoration:none;color:#374151;font-weight:600;font-size:.88rem;transition:all .2s;" onmouseover="this.style.background='var(--accent-color,#e8532e)';this.style.color='#fff'" onmouseout="this.style.background='#f8fafc';this.style.color='#374151'">
            <i class="fas fa-heart"></i> {{ __('Favorites') }}
        </a>
        <a href="{{ route('customer.profile') }}" style="display:flex;align-items:center;gap:10px;padding:14px 16px;background:#f8fafc;border-radius:10px;text-decoration:none;color:#374151;font-weight:600;font-size:.88rem;transition:all .2s;" onmouseover="this.style.background='var(--accent-color,#e8532e)';this.style.color='#fff'" onmouseout="this.style.background='#f8fafc';this.style.color='#374151'">
            <i class="fas fa-user-edit"></i> {{ __('Profile') }}
        </a>
    </div>
</div>

@endsection
