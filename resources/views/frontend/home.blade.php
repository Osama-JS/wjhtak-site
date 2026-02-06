@extends('frontend.layouts.app')

@section('title', __('Home'))

@section('content')
    {{-- Hero Section --}}
    <section class="hero">
        {{-- Background --}}
        <div class="hero-bg">
            <img src="{{ asset('images/hero-bg.jpg') }}" alt="" loading="eager">
            <div class="hero-overlay"></div>
        </div>

        <div class="container">
            <div class="hero-content">
                {{-- Badge --}}
                <div class="hero-badge scroll-animate">
                    <svg class="hero-badge-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <span class="hero-badge-text">{{ __('Premium Tourism Experience') }}</span>
                </div>

                {{-- Title --}}
                <h1 class="hero-title scroll-animate delay-100">
                    {{ __('Discover Your') }}
                    <br>
                    <span class="hero-title-accent">{{ __('Dream Destination') }}</span>
                </h1>

                {{-- Description --}}
                <p class="hero-desc scroll-animate delay-200">
                    {{ __('Explore the world with our curated travel experiences. From exotic beaches to mountain adventures, we make your travel dreams come true.') }}
                </p>

                {{-- CTA Buttons --}}
                <div class="hero-cta scroll-animate delay-300">
                    <a href="{{ route('trips.index') }}" class="btn btn-accent btn-lg">
                        {{ __('Explore Trips') }}
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14"/>
                            <path d="m12 5 7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="{{ route('about') }}" class="btn btn-outline btn-lg" style="border-color: rgba(255,255,255,0.3); color: white;">
                        {{ __('Learn More') }}
                    </a>
                </div>

                {{-- Search Box --}}
                <div class="hero-search scroll-animate delay-400">
                    @include('frontend.components.search-box', ['countries' => $countries ?? []])
                </div>

                {{-- Stats --}}
                <div class="hero-stats scroll-animate delay-500">
                    <div class="hero-stat">
                        <div class="hero-stat-value" data-counter="{{ $stats['trips'] ?? 500 }}" data-suffix="+">0</div>
                        <div class="hero-stat-label">{{ __('Trips') }}</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value" data-counter="{{ $stats['destinations'] ?? 50 }}" data-suffix="+">0</div>
                        <div class="hero-stat-label">{{ __('Destinations') }}</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value" data-counter="{{ $stats['customers'] ?? 10000 }}" data-suffix="+">0</div>
                        <div class="hero-stat-label">{{ __('Happy Travelers') }}</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value" data-counter="{{ $stats['rating'] ?? 4.9 }}" data-prefix="">0</div>
                        <div class="hero-stat-label">{{ __('Rating') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scroll Indicator --}}
        <div class="hero-scroll">
            <div class="hero-scroll-icon"></div>
        </div>
    </section>

    {{-- Featured Destinations Section --}}
    <section class="section bg-surface">
        <div class="container">
            {{-- Section Header --}}
            <div class="section-header">
                <span class="section-subtitle">{{ __('Explore') }}</span>
                <h2 class="section-title">{{ __('Popular Destinations') }}</h2>
                <p class="section-desc">
                    {{ __('Discover our most loved destinations, handpicked by thousands of travelers around the world.') }}
                </p>
            </div>

            {{-- Destinations Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4" style="gap: var(--space-5);">
                @forelse($destinations ?? [] as $index => $destination)
                    @include('frontend.components.destination-card', [
                        'destination' => $destination,
                        'tripCount' => $destination->trips_count ?? rand(10, 50)
                    ])
                @empty
                    {{-- Demo cards if no data --}}
                    @for($i = 0; $i < 4; $i++)
                        <div class="destination-card scroll-animate delay-{{ ($i + 1) * 100 }}">
                            <div class="destination-card-image">
                                <img src="{{ asset('images/demo/destination-' . ($i + 1) . '.jpg') }}" alt="Destination" loading="lazy">
                            </div>
                            <div class="destination-card-overlay"></div>
                            <div class="destination-card-content">
                                <h3 class="destination-card-title">{{ ['Dubai', 'Paris', 'Maldives', 'Tokyo'][$i] }}</h3>
                                <p class="destination-card-count">{{ rand(15, 45) }} {{ __('Trips Available') }}</p>
                            </div>
                            <div class="destination-card-arrow">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>

            {{-- View All Button --}}
            <div class="text-center" style="margin-top: var(--space-10);">
                <a href="{{ route('destinations') }}" class="btn btn-outline">
                    {{ __('View All Destinations') }}
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Featured Trips Section --}}
    <section class="section">
        <div class="container">
            {{-- Section Header --}}
            <div class="section-header">
                <span class="section-subtitle">{{ __('Top Picks') }}</span>
                <h2 class="section-title">{{ __('Featured Trips') }}</h2>
                <p class="section-desc">
                    {{ __('Handpicked travel experiences for unforgettable adventures. Book now and explore the world.') }}
                </p>
            </div>

            {{-- Trips Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" style="gap: var(--space-6);">
                @forelse($featuredTrips ?? [] as $trip)
                    <div class="scroll-animate">
                        @include('frontend.components.trip-card', ['trip' => $trip, 'featured' => true])
                    </div>
                @empty
                    {{-- Demo cards if no data --}}
                    @for($i = 0; $i < 6; $i++)
                        <article class="trip-card scroll-animate delay-{{ (($i % 3) + 1) * 100 }}">
                            <div class="trip-card-image">
                                <img src="{{ asset('images/demo/trip-' . (($i % 3) + 1) . '.jpg') }}" alt="Trip" loading="lazy">
                                <span class="trip-card-badge">{{ rand(10, 30) }}% {{ __('Off') }}</span>
                                <button class="trip-card-favorite"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg></button>
                                <div class="trip-card-overlay">
                                    <div class="trip-card-rating"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><span>{{ number_format(rand(40, 50) / 10, 1) }}</span></div>
                                </div>
                            </div>
                            <div class="trip-card-content">
                                <div class="trip-card-location"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span>{{ ['Dubai → Maldives', 'Cairo → Luxor', 'Riyadh → Istanbul'][$i % 3] }}</span></div>
                                <h3 class="trip-card-title"><a href="#">{{ ['Luxury Beach Resort', 'Ancient Wonders Tour', 'Cultural Heritage Trip'][$i % 3] }}</a></h3>
                                <div class="trip-card-meta">
                                    <div class="trip-card-meta-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>{{ rand(3, 10) }} {{ __('Days') }}</span></div>
                                    <div class="trip-card-price"><span class="trip-card-price-old">${{ rand(800, 1200) }}</span><span class="trip-card-price-current">${{ rand(500, 799) }}<span class="trip-card-price-unit">/ {{ __('person') }}</span></span></div>
                                </div>
                            </div>
                        </article>
                    @endfor
                @endforelse
            </div>

            {{-- View All Button --}}
            <div class="text-center" style="margin-top: var(--space-10);">
                <a href="{{ route('trips.index') }}" class="btn btn-primary btn-lg">
                    {{ __('View All Trips') }}
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Banner Slider Section --}}
    @if(isset($banners) && count($banners) > 0)
    <section class="section" style="padding: 0;">
        <div data-slider="banner" class="slider">
            @foreach($banners as $banner)
                <div class="slider-slide">
                    <div style="position: relative; height: 500px; overflow: hidden;">
                        <img
                            src="{{ asset('storage/' . $banner->image_path) }}"
                            alt="{{ $banner->title }}"
                            style="width: 100%; height: 100%; object-fit: cover;"
                        >
                        <div style="position: absolute; inset: 0; background: linear-gradient(90deg, rgba(0,0,0,0.7) 0%, transparent 100%);"></div>
                        <div class="container" style="position: absolute; inset: 0; display: flex; align-items: center;">
                            <div style="max-width: 500px; color: white;">
                                <h2 style="font-size: var(--text-4xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                                    {{ $banner->title }}
                                </h2>
                                <p style="font-size: var(--text-lg); opacity: 0.9; margin-bottom: var(--space-6);">
                                    {{ $banner->desc }}
                                </p>
                                @if($banner->trip_id)
                                    <a href="{{ route('trips.show', $banner->trip_id) }}" class="btn btn-accent">
                                        {{ __('View Details') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Why Choose Us Section --}}
    <section class="section bg-surface">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">{{ __('Why Us') }}</span>
                <h2 class="section-title">{{ __('Why Choose Wjhtak') }}</h2>
                <p class="section-desc">
                    {{ __('We provide exceptional travel experiences with unmatched service quality.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4" style="gap: var(--space-6);">
                {{-- Feature 1 --}}
                <div class="card text-center scroll-animate" style="padding: var(--space-8);">
                    <div style="width: 70px; height: 70px; background: var(--gradient-accent); border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-5);">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary-dark)" stroke-width="2">
                            <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <h4 style="font-size: var(--text-xl); margin-bottom: var(--space-3);">{{ __('Best Destinations') }}</h4>
                    <p class="text-muted" style="font-size: var(--text-sm);">
                        {{ __('Handpicked locations around the world for unforgettable experiences.') }}
                    </p>
                </div>

                {{-- Feature 2 --}}
                <div class="card text-center scroll-animate delay-100" style="padding: var(--space-8);">
                    <div style="width: 70px; height: 70px; background: var(--gradient-accent); border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-5);">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary-dark)" stroke-width="2">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                    <h4 style="font-size: var(--text-xl); margin-bottom: var(--space-3);">{{ __('Best Prices') }}</h4>
                    <p class="text-muted" style="font-size: var(--text-sm);">
                        {{ __('Competitive pricing with exclusive deals and discounts.') }}
                    </p>
                </div>

                {{-- Feature 3 --}}
                <div class="card text-center scroll-animate delay-200" style="padding: var(--space-8);">
                    <div style="width: 70px; height: 70px; background: var(--gradient-accent); border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-5);">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary-dark)" stroke-width="2">
                            <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                    </div>
                    <h4 style="font-size: var(--text-xl); margin-bottom: var(--space-3);">{{ __('Trusted Service') }}</h4>
                    <p class="text-muted" style="font-size: var(--text-sm);">
                        {{ __('Licensed and certified with 24/7 customer support.') }}
                    </p>
                </div>

                {{-- Feature 4 --}}
                <div class="card text-center scroll-animate delay-300" style="padding: var(--space-8);">
                    <div style="width: 70px; height: 70px; background: var(--gradient-accent); border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-5);">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary-dark)" stroke-width="2">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <h4 style="font-size: var(--text-xl); margin-bottom: var(--space-3);">{{ __('Secure Booking') }}</h4>
                    <p class="text-muted" style="font-size: var(--text-sm);">
                        {{ __('Safe and encrypted payment processing for peace of mind.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Newsletter Section --}}
    <section class="section">
        <div class="container">
            <div class="newsletter scroll-animate">
                <h2 class="newsletter-title">{{ __('Subscribe to Our Newsletter') }}</h2>
                <p class="newsletter-desc">
                    {{ __('Get exclusive deals, travel tips, and destination guides delivered to your inbox.') }}
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
