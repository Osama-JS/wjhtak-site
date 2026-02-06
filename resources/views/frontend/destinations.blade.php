@extends('frontend.layouts.app')

@section('title', __('Destinations'))

@section('meta_description', __('Explore amazing travel destinations around the world'))

@section('content')
    {{-- Page Header --}}
    <section class="section" style="padding-top: calc(var(--space-24) + 60px); padding-bottom: var(--space-10); background: var(--gradient-primary);">
        <div class="container">
            <div class="text-center" style="color: white;">
                <h1 style="font-size: var(--text-4xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                    {{ __('Explore Destinations') }}
                </h1>
                <p style="font-size: var(--text-lg); opacity: 0.9; max-width: 600px; margin: 0 auto;">
                    {{ __('Discover breathtaking locations around the world and plan your next adventure.') }}
                </p>
            </div>

            {{-- Breadcrumb --}}
            <nav class="breadcrumb" style="justify-content: center; margin-top: var(--space-6);" aria-label="Breadcrumb">
                <span class="breadcrumb-item">
                    <a href="{{ route('home') }}" style="color: rgba(255,255,255,0.7);">{{ __('Home') }}</a>
                </span>
                <span class="breadcrumb-separator" style="color: rgba(255,255,255,0.5);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </span>
                <span class="breadcrumb-item active" style="color: white;">{{ __('Destinations') }}</span>
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
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(2, 250px); gap: var(--space-4);">
                @forelse($featuredCountries ?? [] as $index => $country)
                    @php
                        $gridStyles = [
                            0 => 'grid-row: span 2;',
                            1 => '',
                            2 => '',
                            3 => 'grid-column: span 2;',
                        ];
                    @endphp
                    <a
                        href="{{ route('trips.index', ['country' => $country->id]) }}"
                        class="destination-card scroll-animate"
                        style="{{ $gridStyles[$index] ?? '' }}"
                    >
                        <div class="destination-card-image">
                            <img
                                src="{{ asset('images/destinations/' . strtolower($country->iso ?? 'default') . '.jpg') }}"
                                alt="{{ $country->nicename ?? $country->name }}"
                                loading="lazy"
                                onerror="this.src='{{ asset('images/demo/destination-placeholder.jpg') }}'"
                            >
                        </div>
                        <div class="destination-card-overlay"></div>
                        <div class="destination-card-content">
                            @if($country->flag)
                                <img
                                    src="{{ asset('storage/' . $country->flag) }}"
                                    alt=""
                                    class="destination-card-flag"
                                    onerror="this.style.display='none'"
                                >
                            @endif
                            <h3 class="destination-card-title">{{ $country->nicename ?? $country->name }}</h3>
                            <p class="destination-card-count">
                                {{ $country->trips_count ?? 0 }} {{ __('Trips Available') }}
                            </p>
                        </div>
                        <div class="destination-card-arrow">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                @empty
                    {{-- Demo Featured Destinations --}}
                    @foreach(['Dubai', 'Paris', 'Maldives', 'Istanbul'] as $index => $name)
                        @php
                            $gridStyles = [
                                0 => 'grid-row: span 2;',
                                1 => '',
                                2 => '',
                                3 => 'grid-column: span 2;',
                            ];
                        @endphp
                        <a href="#" class="destination-card scroll-animate delay-{{ ($index + 1) * 100 }}" style="{{ $gridStyles[$index] }}">
                            <div class="destination-card-image">
                                <img src="{{ asset('images/demo/destination-' . ($index + 1) . '.jpg') }}" alt="{{ $name }}" loading="lazy">
                            </div>
                            <div class="destination-card-overlay"></div>
                            <div class="destination-card-content">
                                <h3 class="destination-card-title">{{ $name }}</h3>
                                <p class="destination-card-count">{{ rand(15, 50) }} {{ __('Trips Available') }}</p>
                            </div>
                            <div class="destination-card-arrow">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
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
