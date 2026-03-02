@extends('frontend.agent.layouts.agent-layout')

@section('title', __('My Trips'))
@section('page-title', __('My Trips'))

@section('content')
@push('styles')
<style>
    :root {
        --accent-color: #e8532e;
        --accent-soft: rgba(232, 83, 46, 0.08);
        --accent-hover: #d14424;
    }

    .dash-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 20px;
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
        border: 1px solid #f1f5f9;
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

    .stat-value { font-size: 1.6rem; font-weight: 800; color: #0f172a; line-height: 1; }
    .stat-label { font-size: .8rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; }

    /* Filters Section */
    .filters-section {
        background: #fff;
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 30px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        color: #1e293b;
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
        color: #64748b;
        border: none;
        border-radius: 12px;
        padding: 0 20px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-reset:hover { background: #e2e8f0; color: #1e293b; }

    /* Trip Cards */
    .trip-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        margin-bottom: 20px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        box-shadow: 0 4px 15px rgba(0,0,0,.02);
    }
    .trip-card:hover {
        transform: translateY(-4px) scale(1.002);
        box-shadow: 0 12px 30px rgba(0,0,0,.08);
    }

    .trip-card-image {
        width: 240px;
        min-height: 180px;
        position: relative;
        flex-shrink: 0;
    }
    .trip-card-image img { width: 100%; height: 100%; object-fit: cover; }
    .image-placeholder {
        width: 100%; height: 100%; background: #f8fafc;
        display: flex; align-items: center; justify-content: center;
        color: #cbd5e1; font-size: 3rem;
    }

    .status-badge-overlay {
        position: absolute;
        top: 15px;
        inset-inline-start: 15px;
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 800;
        backdrop-filter: blur(8px);
    }
    .status-active-overlay { background: rgba(16, 185, 129, 0.9); color: #fff; }
    .status-inactive-overlay { background: rgba(245, 158, 11, 0.9); color: #fff; }

    .trip-card-content {
        flex: 1;
        padding: 25px 30px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-width: 0;
    }

    .trip-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .trip-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 15px;
    }
    .trip-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
        color: #64748b;
        font-weight: 600;
    }
    .trip-meta-item i { color: var(--accent-color); font-size: 0.95rem; }

    .trip-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
        margin-top: auto;
    }

    .trip-price-box .price-label { font-size: 0.75rem; color: #94a3b8; font-weight: 700; display: block; }
    .trip-price-box .price-value { font-size: 1.4rem; font-weight: 900; color: var(--accent-color); }

    .trip-actions { display: flex; gap: 10px; }
    .btn-action {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        border: 1.5px solid #f1f5f9;
        color: #64748b;
        background: #fff;
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-edit:hover { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }
    .btn-delete:hover { border-color: #ef4444; color: #ef4444; background: #fef2f2; }

    /* Premium Pagination */
    .pagination-wrapper { margin-top: 40px; }
    .pagination {
        display: flex; justify-content: center; gap: 8px; list-style: none; padding: 0;
    }
    .page-item .page-link {
        width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;
        border-radius: 12px; background: #fff; border: 1.5px solid #f1f5f9;
        color: #64748b; font-weight: 700; text-decoration: none; transition: all 0.2s;
    }
    .page-item.active .page-link { background: var(--accent-color); color: #fff; border-color: var(--accent-color); box-shadow: 0 5px 15px rgba(232, 83, 46, 0.3); }
    .page-item:not(.active) .page-link:hover { border-color: var(--accent-color); color: var(--accent-color); }

    @media (max-width: 768px) {
        .trip-card { flex-direction: column; }
        .trip-card-image { width: 100%; height: 200px; }
        .trip-card-content { padding: 20px; }
        .trip-footer { flex-direction: column; gap: 15px; align-items: flex-start; }
        .trip-actions { width: 100%; justify-content: flex-end; }
    }
</style>
@endpush

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-layer-group"></i></div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Total Trips') }}</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;color:#16a34a;"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Active') }}</div>
            <div class="stat-value">{{ $stats['active'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff7ed;color:#ea580c;"><i class="fas fa-eye-slash"></i></div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Inactive') }}</div>
            <div class="stat-value">{{ $stats['inactive'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef2f2;color:#dc2626;"><i class="fas fa-exclamation-circle"></i></div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Expired') }}</div>
            <div class="stat-value">{{ $stats['expired'] }}</div>
        </div>
    </div>
</div>

<div class="dash-header-row">
    <h4 style="margin:0;font-weight:900;color:#1e293b;font-size:1.4rem;">{{ __('Listing Trips') }}</h4>
    <a href="{{ route('agent.trips.create') }}" class="btn-filter" style="box-shadow: 0 8px 20px rgba(232, 83, 46, 0.25);">
        <i class="fa fa-plus"></i> {{ __('Create New Trip') }}
    </a>
</div>

{{-- Filters --}}
<div class="filters-section">
    <form action="{{ route('agent.trips.index') }}" method="GET" class="filter-grid" id="filterForm">
        <div class="filter-item" style="grid-column: span 2;">
            <label>{{ __('Search Trip') }}</label>
            <input type="text" name="search" class="filter-input" placeholder="{{ __('Enter trip title...') }}" value="{{ request('search') }}">
        </div>
        <div class="filter-item">
            <label>{{ __('Destination') }}</label>
            <select name="country_id" class="filter-input">
                <option value="">{{ __('All Countries') }}</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-item">
            <label>{{ __('Status') }}</label>
            <select name="status" class="filter-input">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>
        </div>
        <div class="filter-item" style="display: flex; gap: 10px;">
            <button type="submit" class="btn-filter" style="flex: 1;">
                <i class="fas fa-filter"></i> {{ __('Filter') }}
            </button>
            <a href="{{ route('agent.trips.index') }}" class="btn-reset">
                <i class="fas fa-undo"></i>
            </a>
        </div>
    </form>
</div>

{{-- Trips List --}}
@forelse($trips as $trip)
    @php $image = $trip->images?->first(); @endphp
    <div class="trip-card">
        <div class="trip-card-image">
            @if($image)
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $trip->title }}">
            @else
                <div class="image-placeholder"><i class="fas fa-image"></i></div>
            @endif
            <div class="status-badge-overlay {{ $trip->active ? 'status-active-overlay' : 'status-inactive-overlay' }}">
                <i class="fas {{ $trip->active ? 'fa-check-circle' : 'fa-clock' }}"></i>
                {{ $trip->active ? __('Active') : __('Inactive') }}
            </div>
        </div>

        <div class="trip-card-content">
            <div>
                <h3 class="trip-title">{{ $trip->title }}</h3>
                <div class="trip-meta">
                    <div class="trip-meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $trip->toCountry->name ?? '-' }} @if($trip->toCity) • {{ $trip->toCity->name }} @endif
                    </div>
                    <div class="trip-meta-item">
                        <i class="fas fa-clock"></i>
                        {{ $trip->duration ?? '-' }}
                    </div>
                    <div class="trip-meta-item">
                        <i class="fas fa-users"></i>
                        {{ $trip->personnel_capacity ?? 1 }} {{ __('Personnel') }}
                    </div>
                    <div class="trip-meta-item">
                        <i class="fas fa-ticket-alt"></i>
                        {{ $trip->bookings_count ?? $trip->bookings()->count() }} {{ __('Bookings') }}
                    </div>
                </div>
            </div>

            <div class="trip-footer">
                <div class="trip-price-box">
                    <span class="price-label">{{ __('Starting Price') }}</span>
                    @if($trip->price_before_discount && $trip->price_before_discount > $trip->price)
                        <span class="original-price" style="text-decoration: line-through; color: #94a3b8; font-size: 0.85rem; margin-inline-end: 8px;">{{ number_format($trip->price_before_discount, 0) }}</span>
                    @endif
                    <span class="price-value">{{ number_format($trip->price, 0) }} <small style="font-size: 0.6em;">{{ __('SAR') }}</small></span>
                </div>
                <div class="trip-actions">
                    <a href="{{ route('agent.trips.show', $trip->id) }}" class="btn-action" title="{{ __('View Details') }}" style="color: #0ea5e9;">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('agent.trips.edit', $trip->id) }}" class="btn-action btn-edit" title="{{ __('Edit Trip') }}">
                        <i class="fas fa-pen-nib"></i>
                    </a>
                    <form action="{{ route('agent.trips.destroy', $trip->id) }}" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-action btn-delete" title="{{ __('Delete Trip') }}" onclick="confirmDelete(this)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="empty-state" style="background:#fff; border-radius:30px; padding:100px 30px; text-align:center; border:1px dashed #e2e8f0;">
        <div style="width:100px; height:100px; background:#f1f5f9; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 24px; color:#cbd5e1; font-size:3rem;">
            <i class="fas fa-search"></i>
        </div>
        <h3 style="font-weight:900; color:#1e293b; margin-bottom:10px;">{{ __('No Results Found') }}</h3>
        <p style="color:#64748b; margin-bottom:0;">{{ __('Try adjusting your filters or search keywords.') }}</p>
    </div>
@endforelse

{{-- Premium Pagination --}}
@if($trips->hasPages())
    <div class="pagination-wrapper">
        <ul class="pagination">
            {{-- Simple implementation for demo/override --}}
            @if (!$trips->onFirstPage())
                <li class="page-item"><a class="page-link" href="{{ $trips->previousPageUrl() }}"><i class="fas fa-chevron-right"></i></a></li>
            @endif

            @foreach ($trips->getUrlRange(1, $trips->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $trips->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
            @endforeach

            @if ($trips->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $trips->nextPageUrl() }}"><i class="fas fa-chevron-left"></i></a></li>
            @endif
        </ul>
    </div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(button) {
        Swal.fire({
            title: '{{ __('Delete Trip?') }}',
            text: '{{ __('This action cannot be undone and will be blocked if bookings exist.') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '{{ __('Yes, Delete') }}',
            cancelButtonText: '{{ __('Cancel') }}',
            borderRadius: '20px'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        });
    }
</script>
@endpush
@endsection
