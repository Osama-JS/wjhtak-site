@extends('frontend.layouts.app')

@section('title', __('Home'))



@section('content')
    {{-- Hero Section --}}
    <section class="hero">
        {{-- Background --}}
        @php
            $heroBg = \App\Models\Setting::get('hero_bg');
        @endphp
        <div class="hero-bg">
            <img src="{{ $heroBg ? asset($heroBg) : asset('images/hero-bg.jpg') }}" alt="" loading="eager">
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

    {{-- Popular Destinations --}}
    <section class="section bg-surface">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">{{ __("Top Picks") }}</span>
                <h2 class="section-title">{{ __("Popular Destinations") }}</h2>
                <p class="section-desc">
                    {{ __("Discover our most loved destinations, handpicked by thousands of travelers around the world.") }}
                </p>
            </div>

           


            <div style="display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(2, 250px); gap: var(--space-4);" class="scroll-animate">
                @forelse($destinations ?? [] as $index => $destination)
                    @php
                        $gridStyles = [
                            0 => 'grid-row: span 2;',
                            1 => '',
                            2 => '',
                            3 => 'grid-column: span 2;',
                        ];
                    @endphp
                    <div style="{{ $gridStyles[$index % 4] ?? '' }}">
                        @include('frontend.components.destination-card', [
                            'destination' => $destination,
                            'tripCount' => $destination->trips_count ?? 0
                        ])
                    </div>
                @empty
                    {{-- Demo Destinations if no data --}}
                    @php
                        $demoDestinations = [
                            ['name' => __('Switzerland'), 'iso' => 'ch', 'trips_count' => 12],
                            ['name' => __('Turkey'), 'iso' => 'tr', 'trips_count' => 25],
                            ['name' => __('France'), 'iso' => 'fr', 'trips_count' => 18],
                            ['name' => __('Japan'), 'iso' => 'jp', 'trips_count' => 15],
                        ];
                    @endphp
                    @foreach($demoDestinations as $index => $demo)
                        @php
                            $gridStyles = [
                                0 => 'grid-row: span 2;',
                                1 => '',
                                2 => '',
                                3 => 'grid-column: span 2;',
                            ];
                            // Create a dummy object to satisfy component expectations if necessary,
                            // but component uses $destination->nicename etc.
                            $destObj = (object)$demo;
                        @endphp
                        <div style="{{ $gridStyles[$index % 4] ?? '' }}">
                            @include('frontend.components.destination-card', [
                                'destination' => $destObj,
                                'tripCount' => $demo['trips_count']
                            ])
                        </div>
                    @endforeach
                @endforelse
            </div>

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
            <div class="section-header">
                <span class="section-subtitle">{{ __("Best Deals") }}</span>
                <h2 class="section-title">{{ __("Featured Trips") }}</h2>
                <p class="section-desc">
                    {{ __("Explore our handpicked travel packages with exclusive offers and unforgettable experiences.") }}
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
                            alt="{{ $banner->title_ar }}"
                            style="width: 100%; height: 100%; object-fit: cover;"
                        >
                        <div style="position: absolute; inset: 0; background: linear-gradient(90deg, rgba(0,0,0,0.7) 0%, transparent 100%);"></div>
                        <div class="container" style="position: absolute; inset: 0; display: flex; align-items: center;">
                            <div style="max-width: 500px; color: white;">
                                <h2 style="font-size: var(--text-4xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                                    {{ app()->getLocale() == 'ar' ? $banner->title_ar : $banner->title_en }}
                                </h2>
                                <p style="font-size: var(--text-lg); opacity: 0.9; margin-bottom: var(--space-6);">
                                     {{ app()->getLocale() == 'ar' ? $banner->description_ar : $banner->description_en }}
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

    {{-- Why Choose Us --}}
    
    <section class="section bg-surface">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">{{ __("Why Us") }}</span>
                <h2 class="section-title">{{ __("Why Choose Wjhtak") }}</h2>
                <p class="section-desc">
                    {{ __("We provide exceptional travel experiences with unmatched service quality.") }}
                </p>
            </div>

           @php    
            // تعريف المسارات الكاملة لكل أيقونة (SVG Paths)
            $icons = [
                'M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z', // موقع
                'M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8', // عملة/مال
                'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z', // درع حماية
                'M7 11V7a5 5 0 0 1 10 0v4', // قفل
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4" style="gap: var(--space-6);">
            @forelse($questions as $index => $ques)
            <div class="card text-center scroll-animate" 
                style="padding: var(--space-8); transition: transform 0.3s ease; border: 1px solid var(--color-border);">
                
                <div style="width: 60px; height: 60px; background: var(--color-surface-hover); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $icons[$index % count($icons)] }}"/>
                        {{-- تمت إزالة الدائرة الثابتة هنا لضمان نظافة شكل الأيقونة --}}
                    </svg>
                </div>

                <h4 style="font-size: var(--text-lg); font-weight: var(--font-bold); margin-bottom: var(--space-2); color: var(--color-text-main);">
                    {{ $ques->question }}
                </h4>
                
                <p class="text-muted" style="font-size: var(--text-sm); line-height: 1.6;">
                    {{ $ques->answer }}
                </p>
            </div>
            @empty
            <div class="col-span-full text-center py-10">
                <p class="text-muted">{{ __('لا توجد نتائج لعرضها حالياً') }}</p>
            </div>
            @endforelse
        </div>
        </div>
    </section>

    {{-- Newsletter --}}
    <section class="section">
        <div class="container">
            <div class="newsletter scroll-animate">
                <h2 class="newsletter-title">{{ __("Subscribe to Our Newsletter") }}</h2>
                <p class="newsletter-desc">
                    {{ __("Get exclusive deals, travel tips, and destination guides delivered to your inbox.") }}
                </p>
                <form class="newsletter-form">
                    <input type="email" class="newsletter-input" placeholder="{{ __("Enter your email address") }}" required>
                    <button type="submit" class="btn btn-primary btn-lg">{{ __("Subscribe") }}</button>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('styles')
   <style>
        .destination-card-content{
          position: relative;  
        }
   </style>
@endpush


