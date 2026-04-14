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

            {{-- Featured Grid (Bento Layout) --}}
            @php
                $gradients = [
                    'linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%)',
                    'linear-gradient(135deg, #2b1055 0%, #d53369 100%)',
                    'linear-gradient(135deg, #0093E9 0%, #80D0C7 100%)',
                    'linear-gradient(135deg, #c33764 0%, #1d2671 100%)',
                ];
                $emojis = ['🌍', '✈️', '🏝️', '🕌'];

                // Build the display items — use real data + fill with demos to always show 4
                $displayItems = [];

                if (!empty($featuredCountries) && $featuredCountries->count() > 0) {
                    foreach ($featuredCountries as $country) {
                        $displayItems[] = [
                            'name' => $country->nicename ?? $country->name ?? __('Destination'),
                            'trips_count' => $country->trips_count ?? 0,
                            'url' => route('trips.index', ['country' => $country->id]),
                            'image' => $country->landmark_image_url ?? null,
                            'flag' => $country->flag ? asset('storage/' . $country->flag) : null,
                        ];
                    }
                }

                // Fill remaining slots with demo data
                $demoDefaults = [
                    ['name' => 'Dubai',    'trips_count' => 45, 'emoji' => '🏙️'],
                    ['name' => 'Paris',    'trips_count' => 32, 'emoji' => '🗼'],
                    ['name' => 'Maldives', 'trips_count' => 28, 'emoji' => '🏝️'],
                    ['name' => 'Istanbul', 'trips_count' => 50, 'emoji' => '🕌'],
                ];

                $needed = 4 - count($displayItems);
                for ($i = 0; $i < $needed; $i++) {
                    $demo = $demoDefaults[$i % count($demoDefaults)];
                    $displayItems[] = [
                        'name' => $demo['name'],
                        'trips_count' => $demo['trips_count'],
                        'url' => '#',
                        'image' => null,
                        'flag' => null,
                        'emoji' => $demo['emoji'],
                    ];
                }
            @endphp

            <div class="featured-grid">
                @foreach(array_slice($displayItems, 0, 4) as $index => $item)
                    <div class="featured-grid-item featured-grid-item--{{ $index + 1 }}">
                        <a href="{{ $item['url'] }}" class="featured-card" style="background: {{ $gradients[$index % 4] }};">
                            {{-- Background image if available --}}
                            @if(!empty($item['image']))
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="featured-card__bg-image" loading="lazy"
                                     onerror="this.style.display='none'">
                            @endif

                            {{-- Emoji fallback --}}
                            <div class="featured-card__emoji">{{ $item['emoji'] ?? $emojis[$index % 4] }}</div>

                            {{-- Content --}}
                            <div class="featured-card__content">
                                @if(!empty($item['flag']))
                                    <img src="{{ $item['flag'] }}" alt="" class="featured-card__flag" onerror="this.style.display='none'">
                                @endif
                                <h3 class="featured-card__title">{{ $item['name'] }}</h3>
                                <p class="featured-card__count">
                                    <span class="featured-card__dot"></span>
                                    {{ $item['trips_count'] }} {{ __('Trips Available') }}
                                </p>
                            </div>

                            {{-- Arrow --}}
                            <div class="featured-card__arrow">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    </div>
                @endforeach
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
                    @foreach([__('Saudi Arabia'), __('UAE'), __('Egypt'), __('Turkey'), __('Indonesia'), __('Thailand'), __('Malaysia'), __('Japan'), __('France'), __('Italy')] as $name)
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
    /* ============================================
       Featured Bento Grid — Fully Responsive
       ============================================ */
    .featured-grid {
        display: grid;
        gap: var(--space-4);
        grid-template-columns: 1fr;
    }

    /* Mobile: single column, uniform height */
    .featured-grid-item {
        min-height: 240px;
        border-radius: var(--radius-2xl);
        overflow: hidden;
    }

    /* Tablet: 2-column layout with hero item spanning full height */
    @media (min-width: 640px) {
        .featured-grid {
            grid-template-columns: repeat(2, 1fr);
            grid-auto-rows: 200px;
        }
        .featured-grid-item {
            min-height: unset;
        }
        .featured-grid-item--1 {
            grid-row: span 2;
        }
    }

    /* Desktop: 12-column bento layout */
    @media (min-width: 1024px) {
        .featured-grid {
            grid-template-columns: repeat(12, 1fr);
            grid-template-rows: 240px 240px;
            gap: var(--space-5);
        }
        /* Item 1 — large hero, left column */
        .featured-grid-item--1 {
            grid-column: 1 / 6;
            grid-row: 1 / 3;
        }
        /* Item 2 — top right */
        .featured-grid-item--2 {
            grid-column: 6 / 9;
            grid-row: 1 / 2;
        }
        /* Item 3 — top far right */
        .featured-grid-item--3 {
            grid-column: 9 / 13;
            grid-row: 1 / 2;
        }
        /* Item 4 — wide bottom right */
        .featured-grid-item--4 {
            grid-column: 6 / 13;
            grid-row: 2 / 3;
        }
    }

    .featured-grid-item .destination-card,
    .featured-grid-item .featured-card {
        width: 100%;
        height: 100%;
        aspect-ratio: unset;
    }

    /* ============================================
       Featured Card — Background Image Overlay
       ============================================ */
    .featured-card__bg-image {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
        filter: brightness(0.6);
        transition: transform 0.8s cubic-bezier(0.23, 1, 0.32, 1), filter 0.4s ease;
    }

    .featured-card:hover .featured-card__bg-image {
        transform: scale(1.1);
        filter: brightness(0.5);
    }

    .featured-card__flag {
        width: 32px;
        height: 32px;
        border-radius: var(--radius-full);
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.7);
        margin-bottom: var(--space-2);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    /* ============================================
       Demo Featured Card (Gradient + Emoji)
       ============================================ */
    .featured-card {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        border-radius: var(--radius-2xl);
        overflow: hidden;
        text-decoration: none;
        color: white;
        padding: var(--space-6);
        transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15), 0 8px 40px rgba(0, 0, 0, 0.08);
    }

    .featured-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25), 0 30px 60px rgba(0, 0, 0, 0.12);
    }

    .featured-card__emoji {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -55%);
        font-size: 5rem;
        opacity: 0.2;
        transition: all 0.5s ease;
        filter: grayscale(0.3);
        pointer-events: none;
    }

    .featured-card:hover .featured-card__emoji {
        opacity: 0.35;
        transform: translate(-50%, -60%) scale(1.15);
        filter: grayscale(0);
    }

    .featured-card__content {
        position: relative;
        z-index: 2;
    }

    .featured-card__title {
        font-size: var(--text-2xl);
        font-weight: var(--font-bold);
        color: white;
        margin-bottom: var(--space-2);
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.4);
    }

    .featured-card__count {
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        font-size: var(--text-sm);
        font-weight: var(--font-medium);
        color: rgba(255, 255, 255, 0.9);
        padding: var(--space-1) var(--space-3);
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: var(--radius-full);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .featured-card__dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        background: var(--color-accent, #FFC107);
        border-radius: var(--radius-full);
        animation: pulse-dot 2s ease-in-out infinite;
    }

    .featured-card__arrow {
        position: absolute;
        top: var(--space-5);
        right: var(--space-5);
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: var(--radius-full);
        color: white;
        opacity: 0;
        transform: translateX(-12px) rotate(-45deg);
        transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    [dir="rtl"] .featured-card__arrow {
        right: auto;
        left: var(--space-5);
        transform: translateX(12px) rotate(45deg);
    }

    .featured-card:hover .featured-card__arrow {
        opacity: 1;
        transform: translateX(0) rotate(0);
        background: rgba(255, 255, 255, 0.3);
    }

    /* Shine effect on hover */
    .featured-card::after {
        content: "";
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            45deg,
            transparent 40%,
            rgba(255, 255, 255, 0.08) 45%,
            rgba(255, 255, 255, 0.15) 50%,
            rgba(255, 255, 255, 0.08) 55%,
            transparent 60%
        );
        transform: translateX(-100%);
        transition: transform 0.8s;
        pointer-events: none;
        z-index: 3;
    }

    .featured-card:hover::after {
        transform: translateX(100%);
    }

    /* ============================================
       All Destinations List Items
       ============================================ */
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
