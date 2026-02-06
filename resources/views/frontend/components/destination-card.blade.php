{{-- Destination Card Component --}}
@props(['destination', 'tripCount' => 0])

<a href="{{ route('trips.index', ['country' => $destination->id]) }}" class="destination-card scroll-animate">
    {{-- Background Image --}}
    <div class="destination-card-image">
        @if($destination->flag)
            {{-- Use a destination image if available, otherwise use flag as fallback --}}
            <img
                src="{{ asset('images/destinations/' . strtolower($destination->iso) . '.jpg') }}"
                alt="{{ $destination->nicename ?? $destination->name }}"
                loading="lazy"
                onerror="this.src='{{ asset('images/demo/destination-placeholder.jpg') }}'"
            >
        @else
            <img
                src="{{ asset('images/demo/destination-placeholder.jpg') }}"
                alt="{{ $destination->nicename ?? $destination->name }}"
                loading="lazy"
            >
        @endif
    </div>

    {{-- Overlay --}}
    <div class="destination-card-overlay"></div>

    {{-- Content --}}
    <div class="destination-card-content">
        {{-- Flag --}}
        @if($destination->flag)
            <img
                src="{{ asset('storage/' . $destination->flag) }}"
                alt=""
                class="destination-card-flag"
                onerror="this.style.display='none'"
            >
        @endif

        {{-- Title --}}
        <h3 class="destination-card-title">{{ $destination->nicename ?? $destination->name }}</h3>

        {{-- Trip Count --}}
        <p class="destination-card-count">
            {{ $tripCount }} {{ __('Trips Available') }}
        </p>
    </div>

    {{-- Arrow --}}
    <div class="destination-card-arrow">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14"/>
            <path d="m12 5 7 7-7 7"/>
        </svg>
    </div>
</a>
