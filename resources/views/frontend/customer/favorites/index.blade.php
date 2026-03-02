@extends('frontend.customer.layouts.customer-layout')

@section('title', __('My Favorites'))
@section('page-title', __('My Favorites'))

@section('content')
@push('styles')
<style>
    :root {
        --accent-color: #e8532e;
        --accent-soft: rgba(232, 83, 46, 0.08);
        --accent-hover: #d14424;
    }

    .favorites-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
    }

    .favorite-card {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #f1f5f9;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .favorite-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        border-color: var(--accent-soft);
    }

    .favorite-image {
        height: 200px;
        position: relative;
        overflow: hidden;
    }

    .favorite-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .favorite-card:hover .favorite-image img {
        transform: scale(1.1);
    }

    .remove-fav-btn {
        position: absolute;
        top: 15px;
        inset-inline-end: 15px;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(255,255,255,0.9);
        color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: all 0.2s;
        z-index: 10;
        backdrop-filter: blur(4px);
    }

    .remove-fav-btn:hover {
        background: #ef4444;
        color: #fff;
        transform: scale(1.1);
    }

    .favorite-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .fav-trip-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 12px;
        line-height: 1.4;
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .fav-trip-title:hover {
        color: var(--accent-color);
    }

    .fav-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .fav-meta-item {
        font-size: 0.82rem;
        color: #64748b;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .fav-meta-item i {
        color: var(--accent-color);
    }

    .fav-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid #f1f5f9;
    }

    .fav-price-box .p-label {
        font-size: 0.7rem;
        color: #94a3b8;
        font-weight: 700;
        display: block;
        text-transform: uppercase;
    }

    .fav-price-box .p-value {
        font-size: 1.2rem;
        font-weight: 900;
        color: #1e293b;
    }

    .btn-view-trip {
        padding: 8px 18px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        color: #475569;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-view-trip:hover {
        background: var(--accent-color);
        color: #fff;
        border-color: var(--accent-color);
    }

    /* Empty State */
    .fav-empty {
        background: #fff;
        border-radius: 30px;
        padding: 80px 30px;
        text-align: center;
        border: 1px dashed #e2e8f0;
        grid-column: 1 / -1;
    }

    .fav-empty-icon {
        width: 90px;
        height: 90px;
        background: #fef2f2;
        color: #fca5a5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        font-size: 2.8rem;
    }

    .btn-accent-main {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        background: var(--accent-color);
        color: #fff;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.2s;
    }
    .btn-accent-main:hover {
        background: var(--accent-hover);
        transform: scale(1.05);
        color: #fff;
    }
</style>
@endpush

<div class="dash-header-row mb-4">
    <h4 style="margin:0;font-weight:900;color:#1e293b;font-size:1.4rem;">{{ __('My Favorite Trips') }}</h4>
    <p class="text-muted" style="font-size: 0.9rem;">{{ __('Trips you have saved to visit later.') }}</p>
</div>

@if($favorites->count() > 0)
    <div class="favorites-grid" id="favoritesGrid">
        @foreach($favorites as $favorite)
            @php
                $trip = $favorite->trip;
                $image = $trip?->images?->first();
            @endphp
            @if($trip)
                <div class="favorite-card" id="fav-card-{{ $trip->id }}">
                    <div class="favorite-image">
                        @if($image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $trip->title }}">
                        @else
                            <div style="width:100%; height:100%; background:#f8fafc; display:flex; align-items:center; justify-content:center; color:#cbd5e1; font-size:3rem;">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                        @endif

                        <button class="remove-fav-btn" onclick="removeFavorite({{ $trip->id }})" title="{{ __('Remove from favorites') }}">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>

                    <div class="favorite-body">
                        <a href="{{ route('trips.show', $trip->id) }}" class="fav-trip-title">
                            {{ $trip->title }}
                        </a>

                        <div class="fav-meta">
                            @if($trip->toCountry)
                                <div class="fav-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $trip->toCountry->name }}
                                </div>
                            @endif
                            @if($trip->duration)
                                <div class="fav-meta-item">
                                    <i class="fas fa-clock"></i>
                                    {{ $trip->duration }}
                                </div>
                            @endif
                            @if($trip->company)
                                <div class="fav-meta-item">
                                    <i class="fas fa-building"></i>
                                    {{ $trip->company->name }}
                                </div>
                            @endif
                        </div>

                        <div class="fav-footer">
                            <div class="fav-price-box">
                                <span class="p-label">{{ __('Price') }}</span>
                                <span class="p-value">{{ number_format($trip->price, 0) }} <small style="font-size:0.6em;">{{ __('SAR') }}</small></span>
                            </div>
                            <a href="{{ route('trips.show', $trip->id) }}" class="btn-view-trip">
                                {{ __('View Details') }} <i class="fas fa-chevron-{{ app()->isLocale('ar') ? 'left' : 'right' }} ms-1" style="font-size:0.75rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @if($favorites->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $favorites->links() }}
        </div>
    @endif
@else
    <div class="fav-empty">
        <div class="fav-empty-icon">
            <i class="far fa-heart"></i>
        </div>
        <h3 style="font-weight:900; color:#1e293b; margin-bottom:10px;">{{ __('Your wishlist is empty') }}</h3>
        <p style="color:#64748b; margin-bottom:30px; max-width:400px; margin-inline:auto;">{{ __("You haven't added any trips to your favorites yet. Start exploring and save the ones you love!") }}</p>
        <a href="{{ route('trips.index') }}" class="btn-accent-main">
            {{ __('Explore Trips') }}
        </a>
    </div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function removeFavorite(tripId) {
    const url = '{{ route("customer.favorites.toggle", ":id") }}'.replace(':id', tripId);

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.error && !data.is_favorite) {
            // Smoothly remove from UI
            const card = document.getElementById(`fav-card-${tripId}`);
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.9)';

            setTimeout(() => {
                card.remove();
                if (document.querySelectorAll('.favorite-card').length === 0) {
                    location.reload(); // Show empty state
                }
            }, 300);

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: data.message
            });
        }
    });
}
</script>
@endpush

@endsection
