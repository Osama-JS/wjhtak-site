@extends('frontend.agent.layouts.agent-layout')

@section('title', __('Bookings Management'))
@section('page-title', __('Bookings Management'))

@section('content')
@push('styles')
<style>
    :root {
        --accent-color: #e8532e;
        --accent-soft: rgba(232, 83, 46, 0.08);
        --accent-hover: #d14424;
        --border-light: #f1f5f9;
        --text-main: #1e293b;
        --text-muted: #64748b;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    @media (max-width: 992px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 520px) { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: #fff;
        border-radius: 18px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 18px;
        box-shadow: 0 4px 15px rgba(0,0,0,.03);
        border: 1px solid var(--border-light);
        transition: transform 0.3s ease;
    }
    .stat-card:hover { transform: translateY(-5px); }

    .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    .stat-value { font-size: 1.6rem; font-weight: 800; color: var(--text-main); line-height: 1; }
    .stat-label { font-size: .8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; margin-bottom: 6px; }

    /* Filters Section */
    .filters-section {
        background: #fff;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid var(--border-light);
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        align-items: flex-end;
    }

    .filter-item label {
        display: block;
        font-weight: 700;
        font-size: 0.85rem;
        color: #475569;
        margin-bottom: 8px;
    }

    .filter-input {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        font-size: 0.9rem;
        color: var(--text-main);
        transition: all 0.2s;
    }
    .filter-input:focus {
        outline: none;
        border-color: var(--accent-color);
        background: #fff;
        box-shadow: 0 0 0 4px var(--accent-soft);
    }

    .btn-filter {
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: var(--accent-color);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        padding: 0 24px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-filter:hover { background: var(--accent-hover); transform: scale(1.02); }

    .btn-reset {
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: var(--text-muted);
        border: none;
        border-radius: 12px;
        padding: 0 20px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-reset:hover { background: #e2e8f0; color: var(--text-main); }

    /* New Transactional Booking Cards @ Redesign */
    .booking-list-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .booking-transaction-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid var(--border-light);
        padding: 20px 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 10px rgba(0,0,0,.01);
        position: relative;
        overflow: hidden;
    }
    .booking-transaction-card:hover {
        transform: translateX(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,.05);
        border-color: var(--accent-soft);
    }
    .booking-transaction-card::before {
        content: '';
        position: absolute;
        inset-inline-start: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--accent-color);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .booking-transaction-card:hover::before { opacity: 1; }

    .id-column {
        min-width: 140px;
    }
    .booking-ref {
        display: block;
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .booking-number {
        font-size: 1.1rem;
        font-weight: 900;
        color: var(--text-main);
    }

    .customer-column {
        flex: 1;
        min-width: 200px;
    }
    .customer-name {
        display: block;
        font-weight: 800;
        color: var(--text-main);
        font-size: 1rem;
        margin-bottom: 2px;
    }
    .customer-email {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .trip-column {
        flex: 1.5;
        min-width: 250px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .mini-trip-thumb {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        object-fit: cover;
        background: #f8fafc;
    }
    .trip-compact-info .t-title {
        display: block;
        font-weight: 700;
        color: var(--text-main);
        font-size: 0.9rem;
        line-height: 1.3;
    }
    .trip-compact-info .t-meta {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .amount-column {
        text-align: end;
        min-width: 120px;
    }
    .amount-val {
        display: block;
        font-size: 1.1rem;
        font-weight: 900;
        color: var(--accent-color);
    }
    .amount-date {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .status-column {
        min-width: 140px;
        display: flex;
        justify-content: center;
    }

    .action-column {
        min-width: 100px;
        display: flex;
        justify-content: flex-end;
    }
    .btn-view-transaction {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: var(--accent-soft);
        color: var(--accent-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-view-transaction:hover {
        background: var(--accent-color);
        color: #fff;
        transform: scale(1.1);
    }

    .status-tag {
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 800;
        white-space: nowrap;
    }

    @media (max-width: 1200px) {
        .booking-transaction-card { flex-wrap: wrap; }
        .trip-column { order: 3; flex: 1 0 100%; border-top: 1px solid var(--border-light); padding-top: 15px; margin-top: 5px; }
        .amount-column { order: 2; flex: 1; }
        .status-column { order: 3; }
    }

    @media (max-width: 768px) {
        .booking-transaction-card { flex-direction: column; align-items: flex-start; gap: 12px; }
        .id-column, .customer-column, .trip-column, .amount-column, .status-column, .action-column {
            min-width: 0; width: 100%; text-align: start; flex: none; align-items: flex-start;
        }
        .amount-column { text-align: start; margin-top: 5px; }
        .status-column { justify-content: flex-start; }
        .action-column { justify-content: flex-end; position: absolute; top: 20px; inset-inline-end: 20px; }
        .trip-column { border: none; padding-top: 0; margin-top: 0; }
    }

    /* Statuses */
    .st-received { background: #eff6ff; color: #2563eb; }
    .st-preparing { background: #fff7ed; color: #ea580c; }
    .st-confirmed { background: #f0fdf4; color: #16a34a; }
    .st-tickets_sent { background: #f5f3ff; color: #7c3aed; }
    .st-cancelled { background: #fef2f2; color: #dc2626; }
</style>
@endpush

{{-- Stats Grid --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#f1f5f9;color:#475569;"><i class="fas fa-ticket-alt"></i></div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Total Bookings') }}</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-inbox"></i></div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Received') }}</div>
            <div class="stat-value">{{ $stats['received'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;color:#16a34a;"><i class="fas fa-check-double"></i></div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Confirmed') }}</div>
            <div class="stat-value">{{ $stats['confirmed'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef2f2;color:#dc2626;"><i class="fas fa-calendar-times"></i></div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Cancelled') }}</div>
            <div class="stat-value">{{ $stats['cancelled'] }}</div>
        </div>
    </div>
</div>

<div class="dash-header-row mb-4">
    <h4 style="margin:0;font-weight:900;color:var(--text-main);font-size:1.4rem;">{{ __('Listing Bookings') }}</h4>
</div>

{{-- Filters --}}
<div class="filters-section">
    <form action="{{ route('agent.bookings.index') }}" method="GET" class="filter-grid">
        <div class="filter-item" style="grid-column: span 2;">
            <label>{{ __('Search Customer') }}</label>
            <input type="text" name="search" class="filter-input" placeholder="{{ __('Name or Email...') }}" value="{{ request('search') }}">
        </div>
        <div class="filter-item">
            <label>{{ __('By Trip') }}</label>
            <select name="trip_id" class="filter-input">
                <option value="">{{ __('All Trips') }}</option>
                @foreach($trips as $trip)
                    <option value="{{ $trip->id }}" {{ request('trip_id') == $trip->id ? 'selected' : '' }}>{{ $trip->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-item">
            <label>{{ __('Status') }}</label>
            <select name="status" class="filter-input">
                <option value="">{{ __('All Statuses') }}</option>
                @foreach($states as $state)
                    <option value="{{ $state }}" {{ request('status') === $state ? 'selected' : '' }}>{{ __(ucfirst(str_replace('_', ' ', $state))) }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-item">
            <label>{{ __('Booking Date') }}</label>
            <input type="date" name="date" class="filter-input" value="{{ request('date') }}">
        </div>
        <div class="filter-item" style="display: flex; gap: 10px;">
            <button type="submit" class="btn-filter" style="flex: 1;">
                <i class="fas fa-search"></i>
            </button>
            <a href="{{ route('agent.bookings.index') }}" class="btn-reset">
                <i class="fas fa-undo"></i>
            </a>
        </div>
    </form>
</div>

{{-- Redesigned Bookings List @ Transactional --}}
<div class="booking-list-container">
    @forelse($bookings as $booking)
        @php
            $trip = $booking->trip;
            $image = $trip?->images?->first();
        @endphp
        <div class="booking-transaction-card">
            {{-- ID Column --}}
            <div class="id-column">
                <span class="booking-ref">{{ __('Ref Number') }}</span>
                <span class="booking-number">#{{ $booking->id }}</span>
            </div>

            {{-- Customer Column --}}
            <div class="customer-column">
                <span class="customer-name">{{ $booking->user->full_name }}</span>
                <span class="customer-email"><i class="far fa-envelope"></i> {{ $booking->user->email }}</span>
            </div>

            {{-- Trip Column --}}
            <div class="trip-column">
                @if($image)
                    <img src="{{ asset('storage/' . $image->image_path) }}" class="mini-trip-thumb" alt="">
                @else
                    <div class="mini-trip-thumb" style="display:flex; align-items:center; justify-content:center; color:#cbd5e1; font-size:1.2rem;">
                        <i class="fas fa-plane"></i>
                    </div>
                @endif
                <div class="trip-compact-info">
                    <span class="t-title">{{ $trip?->title ?? __('Trip Deleted') }}</span>
                    <span class="t-meta">{{ $booking->tickets_count }} {{ __('Passengers') }}</span>
                </div>
            </div>

            {{-- Status Column --}}
            <div class="status-column">
                <span class="status-tag st-{{ $booking->booking_state }}">
                    {{ __(ucfirst(str_replace('_', ' ', $booking->booking_state))) }}
                </span>
            </div>

            {{-- Amount Column --}}
            <div class="amount-column">
                <span class="amount-val">{{ number_format($booking->total_price, 0) }} <small style="font-size:0.6em;">{{ __('SAR') }}</small></span>
                <span class="amount-date">{{ $booking->created_at->format('d/m/Y') }}</span>
            </div>

            {{-- Action Column --}}
            <div class="action-column">
                <a href="{{ route('agent.bookings.show', $booking->id) }}" class="btn-view-transaction" title="{{ __('View Details') }}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </div>
        </div>
    @empty
        <div class="empty-state" style="background:#fff; border-radius:30px; padding:80px 30px; text-align:center; border:1px dashed #e2e8f0;">
            <div style="width:80px; height:80px; background:#f1f5f9; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; color:#cbd5e1; font-size:2.5rem;">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <h3 style="font-weight:900; color:#1e293b; margin-bottom:10px;">{{ __('No Bookings Found') }}</h3>
            <p style="color:#64748b; margin-bottom:0;">{{ __('Try adjusting your filters or search keywords.') }}</p>
        </div>
    @endforelse
</div>

{{-- Premium Pagination --}}
@if($bookings->hasPages())
    <div class="pagination-wrapper">
        <ul class="pagination">
            @if (!$bookings->onFirstPage())
                <li class="page-item"><a class="page-link" href="{{ $bookings->previousPageUrl() }}"><i class="fas fa-chevron-right"></i></a></li>
            @endif

            @foreach ($bookings->getUrlRange(1, $bookings->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $bookings->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
            @endforeach

            @if ($bookings->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $bookings->nextPageUrl() }}"><i class="fas fa-chevron-left"></i></a></li>
            @endif
        </ul>
    </div>
@endif

@endsection
