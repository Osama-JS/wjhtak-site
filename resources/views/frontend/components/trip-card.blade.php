{{-- Trip Card Component --}}
@props(['trip', 'featured' => false])

<article class="trip-card {{ $featured ? 'trip-card-featured' : '' }}">
    {{-- Image --}}
    <div class="trip-card-image">
        @if($trip->images && count($trip->images) > 0)
            <img
                src="{{ asset('storage/' . $trip->images[0]->image_path) }}"
                alt="{{ $trip->title }}"
                loading="lazy"
            >
        @else
            <img
                src="{{ asset('images/demo/trip-placeholder.jpg') }}"
                alt="{{ $trip->title }}"
                loading="lazy"
            >
        @endif

        {{-- Discount Badge --}}
        @if($trip->price_before_discount && $trip->price_before_discount > $trip->price)
            @php
                $discount = round((($trip->price_before_discount - $trip->price) / $trip->price_before_discount) * 100);
            @endphp
            <span class="trip-card-badge">{{ $discount }}% {{ __('Off') }}</span>
        @elseif($trip->is_ad)
            <span class="trip-card-badge" style="background: var(--color-primary); color: white;">{{ __('Featured') }}</span>
        @endif

        {{-- Favorite Button --}}
        <button
            class="trip-card-favorite {{ auth()->check() && $trip->isFavorited ? 'active' : '' }}"
            data-trip-id="{{ $trip->id }}"
            aria-label="{{ __('Add to favorites') }}"
        >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
            </svg>
        </button>

        {{-- Rating Overlay --}}
        @if($trip->average_rating)
            <div class="trip-card-overlay">
                <div class="trip-card-rating">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <span>{{ number_format($trip->average_rating, 1) }}</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="trip-card-content">
        {{-- Location --}}
        <div class="trip-card-location">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <span>
                {{ $trip->fromCountry->nicename ?? $trip->fromCountry->name }}
                →
                {{ $trip->toCountry->nicename ?? $trip->toCountry->name }}
            </span>
        </div>

        {{-- Title --}}
        <h3 class="trip-card-title">
            <a href="{{ route('trips.show', $trip->id) }}">{{ $trip->title }}</a>
        </h3>

        {{-- Meta --}}
        <div class="trip-card-meta">
            {{-- Duration --}}
            @if($trip->duration)
                <div class="trip-card-meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span>{{ $trip->duration }}</span>
                </div>
            @endif

            {{-- Capacity --}}
            @if($trip->personnel_capacity)
                <div class="trip-card-meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>{{ $trip->personnel_capacity }}</span>
                </div>
            @endif

            {{-- Price --}}
            <div class="trip-card-price">
                @if($trip->price_before_discount && $trip->price_before_discount > $trip->price)
                    <span class="trip-card-price-old">${{ number_format($trip->price_before_discount) }}</span>
                @endif
                <span class="trip-card-price-current">
                    ${{ number_format($trip->price) }}
                    <span class="trip-card-price-unit">/ {{ __('person') }}</span>
                </span>
            </div>
        </div>
    </div>
</article>
