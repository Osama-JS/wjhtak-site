{{-- Destination Card Component --}}
@props(['destination', 'tripCount' => 0])

@php
    $destId = $destination->id ?? 0;
    $destName = $destination->nicename ?? $destination->name ?? __('Unknown');
    $destIso = $destination->iso ?? '';
    $destFlag = $destination->flag ?? null;
@endphp

<a href="{{ route('trips.index', ['country' => $destId]) }}" class="destination-card scroll-animate">
    {{-- Background Image --}}
    <div class="destination-card-image">
        <img
            src="{{ $destination->landmark_image_url ?? asset('images/demo/destination-placeholder.jpg') }}"
            alt="{{ $destName }}"
            loading="lazy"
            onerror="this.src='{{ asset('images/demo/destination-placeholder.jpg') }}'"
        >
    </div>

    {{-- Overlay --}}
    <div class="destination-card-overlay"></div>

    {{-- Content --}}
    <div class="destination-card-content">
        {{-- Flag --}}
        @if($destFlag)
            <img
                src="{{ asset('storage/' . $destFlag) }}"
                alt=""
                class="destination-card-flag"
                onerror="this.style.display='none'"
            >
        @endif

        {{-- Title --}}
        <h3 class="destination-card-title">{{ $destName }}</h3>

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

