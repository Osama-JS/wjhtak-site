@extends('frontend.layouts.app')

@section('title', __('Destinations'))

@section('meta_description', __('Explore amazing travel destinations around the world'))

@php
    $headerBg = \App\Models\Setting::get('page_header_bg');
@endphp

@section('content')
    {{-- Page Header --}}
    <section class="page-header" style="position: relative; padding-top: calc(var(--space-24) + 60px); padding-bottom: var(--space-10); background: var(--color-primary); overflow: hidden;">
        @if($headerBg)
            <div style="position: absolute; inset: 0; z-index: 0;">
                <img src="{{ asset($headerBg) }}" alt="" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.4;">
                <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, transparent, var(--color-primary));"></div>
            </div>
        @else
            <div style="position: absolute; inset: 0; background: var(--gradient-primary); z-index: 0;"></div>
        @endif

        <div class="container" style="position: relative; z-index: 1;">
            <div class="text-center" style="color: white !important;">
                <h1 style="font-size: var(--text-4xl); font-weight: var(--font-bold); margin-bottom: var(--space-4); color: white !important;">
                    {{ __('Explore Destinations') }}
                </h1>
                <p style="font-size: var(--text-lg); opacity: 0.9; max-width: 600px; margin: 0 auto; color: white !important;">
                    {{ __('Discover breathtaking locations around the world and plan your next adventure.') }}
                </p>
            </div>

            {{-- Breadcrumb --}}
            <nav class="breadcrumb" style="justify-content: center; margin-top: var(--space-6);" aria-label="Breadcrumb">
                <span class="breadcrumb-item">
                    <a href="{{ route('home') }}" style="color: rgba(255,255,255,0.7) !important;">{{ __('Home') }}</a>
                </span>
                <span class="breadcrumb-separator" style="color: rgba(255,255,255,0.5) !important;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </span>
                <span class="breadcrumb-item active" style="color: white !important;">{{ __('Destinations') }}</span>
            </nav>
        </div>
    </section>

    {{-- Featured Destinations --}}
    <section class="section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">{{ __('Top Picks') }}</span>
                <h2 class="section-title">{{ __('Featured Destinations') }}</h2>
            </div>

            {{-- Featured Grid (Large Cards) --}}
            <div class="home-destinations-grid scroll-animate">
                @forelse($featuredCountries ?? [] as $index => $country)
                    @php
                        $gridStyles = [
                            0 => '--desktop-grid-row: span 2;',
                            1 => '',
                            2 => '',
                            3 => '--desktop-grid-column: span 2;',
                        ];
                    @endphp
                    <div class="home-destination-item" style="{{ $gridStyles[$index % 4] ?? '' }}">
                        @include('frontend.components.destination-card', [
                            'destination' => $country,
                            'tripCount' => $country->trips_count ?? 0
                        ])
                    </div>
                @empty
                    {{-- Demo Featured Destinations --}}
                    @php
                        $demoDestinations = [
                            ['nicename' => 'Dubai', 'iso' => 'ae', 'trips_count' => 45],
                            ['nicename' => 'Paris', 'iso' => 'fr', 'trips_count' => 32],
                            ['nicename' => 'Maldives', 'iso' => 'mv', 'trips_count' => 28],
                            ['nicename' => 'Istanbul', 'iso' => 'tr', 'trips_count' => 50],
                        ];
                    @endphp
                    @foreach($demoDestinations as $index => $demo)
                        @php
                            $gridStyles = [
                                0 => '--desktop-grid-row: span 2;',
                                1 => '',
                                2 => '',
                                3 => '--desktop-grid-column: span 2;',
                            ];
                            $destObj = (object)$demo;
                        @endphp
                        <div class="home-destination-item" style="{{ $gridStyles[$index % 4] ?? '' }}">
                            @include('frontend.components.destination-card', [
                                'destination' => $destObj,
                                'tripCount' => $demo['trips_count']
                            ])
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    {{-- All Destinations --}}
    <section class="section bg-surface">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">{{ __('Browse') }}</span>
                <h2 class="section-title">{{ __('All Destinations') }}</h2>
            </div>

            {{-- Search --}}
            <div style="max-width: 400px; margin: 0 auto var(--space-8);">
                <div style="position: relative;">
                    <input
                        type="text"
                        id="destinationSearch"
                        class="form-input"
                        placeholder="{{ __('Search destinations...') }}"
                        style="padding-left: 48px;"
                    >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%);">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                </div>
            </div>

            {{-- Countries Grid --}}
            <div id="destinationsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5" style="gap: var(--space-4);">
                @forelse($countries ?? [] as $country)
                    <a
                        href="{{ route('trips.index', ['country' => $country->id]) }}"
                        class="destination-item scroll-animate"
                        data-name="{{ strtolower($country->nicename ?? $country->name) }}"
                    >
                        <div class="destination-item-flag">
                            @if($country->flag)
                                <img
                                    src="{{ asset('storage/' . $country->flag) }}"
                                    alt="{{ $country->nicename ?? $country->name }}"
                                    onerror="this.innerHTML='🌍'"
                                >
                            @else
                                <span style="font-size: 32px;">🌍</span>
                            @endif
                        </div>
                        <div class="destination-item-info">
                            <h4 class="destination-item-name">{{ $country->nicename ?? $country->name }}</h4>
                            <span class="destination-item-count">{{ $country->trips_count ?? 0 }} {{ __('trips') }}</span>
                        </div>
                    </a>
                @empty
                    {{-- Demo Countries --}}
                    @foreach(['Saudi Arabia', 'UAE', 'Egypt', 'Turkey', 'Indonesia', 'Thailand', 'Malaysia', 'Japan', 'France', 'Italy'] as $name)
                        <a href="#" class="destination-item scroll-animate" data-name="{{ strtolower($name) }}">
                            <div class="destination-item-flag">
                                <span style="font-size: 32px;">🌍</span>
                            </div>
                            <div class="destination-item-info">
                                <h4 class="destination-item-name">{{ $name }}</h4>
                                <span class="destination-item-count">{{ rand(5, 30) }} {{ __('trips') }}</span>
                            </div>
                        </a>
                    @endforeach
                @endforelse
            </div>

            {{-- No Results Message --}}
            <div id="noResults" style="display: none; text-align: center; padding: var(--space-10);">
                <p class="text-muted">{{ __('No destinations found matching your search.') }}</p>
            </div>
        </div>
    </section>

    {{-- Newsletter Section --}}
    <section class="section">
        <div class="container">
            <div class="newsletter scroll-animate">
                <h2 class="newsletter-title">{{ __('Get Travel Inspiration') }}</h2>
                <p class="newsletter-desc">
                    {{ __('Subscribe to receive destination guides and exclusive travel offers.') }}
                </p>
                <form class="newsletter-form" action="#" method="POST">
                    @csrf
                    <input
                        type="email"
                        name="email"
                        class="newsletter-input"
                        placeholder="{{ __('Enter your email address') }}"
                        required
                    >
                    <button type="submit" class="btn btn-accent btn-lg">
                        {{ __('Subscribe') }}
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .destination-item {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        padding: var(--space-4);
        background: var(--color-bg);
        border-radius: var(--radius-lg);
        border: 1px solid var(--color-border);
        transition: all var(--transition-fast);
        text-decoration: none;
    }

    .destination-item:hover {
        border-color: var(--color-primary);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .destination-item-flag {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        overflow: hidden;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--color-surface-hover);
    }

    .destination-item-flag img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .destination-item-info {
        flex: 1;
        min-width: 0;
    }

    .destination-item-name {
        font-weight: var(--font-semibold);
        margin-bottom: var(--space-1);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .destination-item-count {
        font-size: var(--text-sm);
        color: var(--color-text-muted);
    }

    @media (max-width: 768px) {
        .featured-destinations-grid {
            grid-template-columns: 1fr !important;
            grid-template-rows: auto !important;
        }

        .featured-destinations-grid > * {
            grid-row: auto !important;
            grid-column: auto !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Destination search filter
    const searchInput = document.getElementById('destinationSearch');
    const destinationsGrid = document.getElementById('destinationsGrid');
    const noResults = document.getElementById('noResults');

    if (searchInput && destinationsGrid) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const items = destinationsGrid.querySelectorAll('.destination-item');
            let visibleCount = 0;

            items.forEach(item => {
                const name = item.dataset.name || '';
                const isMatch = name.includes(searchTerm);
                item.style.display = isMatch ? '' : 'none';
                if (isMatch) visibleCount++;
            });

            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        });
    }
</script>
@endpush
