@extends('frontend.layouts.app')

@section('title', $trip->title ?? __('Trip Details'))

@section('meta_description', Str::limit(strip_tags($trip->description ?? ''), 160))

@section('og_title', $trip->title ?? __('Trip Details'))
@section('og_description', Str::limit(strip_tags($trip->description ?? ''), 160))

@section('content')
    {{-- Trip Breadcrumb & Mini Info (Separated from Slider) --}}
    <section class="trip-top-bar" style="padding-top: calc(85px + var(--space-4)); background: var(--color-bg); position: relative; z-index: 10;">
        <div class="container">
            <nav class="breadcrumb" style="padding: var(--space-2) 0;" aria-label="Breadcrumb">
                <span class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></span>
                <span class="breadcrumb-separator">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </span>
                <span class="breadcrumb-item"><a href="{{ route('trips.index') }}">{{ __('Trips') }}</a></span>
                <span class="breadcrumb-separator">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </span>
                <span class="breadcrumb-item active">{{ Str::limit($trip->title ?? '', 40) }}</span>
            </nav>
        </div>
    </section>

    {{-- Premium Glass Gallery Section --}}
    <section class="premium-gallery-section" style="padding: var(--space-4) 0 var(--space-16) 0; background: var(--color-bg); overflow: visible;">
        <div class="container">
            <div class="gallery-layout-wrapper animate__animated animate__fadeIn">
                <div class="gallery-grid">
                    {{-- Main Large Slider (Left/Center) --}}
                    <div class="gallery-main-col">
                        <div class="swiper main-trip-slider">
                            <div class="swiper-wrapper">
                                @if($trip->images && count($trip->images) > 0)
                                    @foreach($trip->images as $image)
                                        <div class="swiper-slide">
                                            <div class="slide-inner">
                                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $trip->title }}">
                                                <div class="glass-overlay"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="swiper-slide">
                                        <div class="slide-inner">
                                            <img src="{{ asset('images/demo/trip-placeholder.jpg') }}" alt="Placeholder">
                                            <div class="glass-overlay"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Glass Navigation --}}
                            <div class="swiper-nav-glass swiper-button-next"></div>
                            <div class="swiper-nav-glass swiper-button-prev"></div>

                            {{-- Badge Info --}}
                            <div class="gallery-badge-info">
                                <span class="badge-glass">
                                    <i class="fas fa-camera me-1"></i> {{ count($trip->images) }} {{ __('Photos') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Vertical Thumbnails Slider (Right Side) --}}
                    <div class="gallery-thumbs-col">
                        <div class="swiper thumbnails-trip-slider">
                            <div class="swiper-wrapper">
                                @foreach($trip->images as $image)
                                    <div class="swiper-slide">
                                        <div class="thumb-inner">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thumbnail">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Trip Details --}}
    <section class="section" style="padding-top: var(--space-10);">
        <div class="container">
            <div class="trip-details-layout">

                {{-- Main Content --}}
                <div class="trip-main-content">
                    {{-- Header --}}
                    <div style="margin-bottom: var(--space-8);">
                        {{-- Location --}}
                        <div class="flex items-center gap-2" style="margin-bottom: var(--space-3); color: var(--color-text-muted);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>
                                {{ $trip->fromCountry->nicename ?? $trip->fromCountry->name ?? '' }}
                                →
                                {{ $trip->toCountry->nicename ?? $trip->toCountry->name ?? '' }}
                            </span>
                        </div>

                        {{-- Title --}}
                        <h1 style="font-size: var(--text-3xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                            {{ $trip->title }}
                        </h1>

                        {{-- Meta --}}
                        <div class="flex flex-wrap items-center gap-4" style="color: var(--color-text-muted);">
                            {{-- Rating --}}
                            @if($trip->average_rating)
                                <div class="flex items-center gap-1">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--color-accent)" stroke="var(--color-accent)" stroke-width="2">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                    <span style="font-weight: var(--font-semibold); color: var(--color-text);">
                                        {{ number_format($trip->average_rating, 1) }}
                                    </span>
                                    <span>({{ $trip->reviews_count ?? 0 }} {{ __('reviews') }})</span>
                                </div>
                            @endif

                            {{-- Duration --}}
                            @if($trip->duration)
                                <div class="flex items-center gap-2">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <span>{{ $trip->duration }}</span>
                                </div>
                            @endif

                            {{-- Capacity --}}
                            @if($trip->personnel_capacity)
                                <div class="flex items-center gap-2">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                    </svg>
                                    <span>{{ __('Up to') }} {{ $trip->personnel_capacity }} {{ __('travelers') }}</span>
                                </div>
                            @endif

                            {{-- Company --}}
                            @if($trip->company)
                                <div class="flex items-center gap-2">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 21h18"/>
                                        <path d="M5 21V7l8-4v18"/>
                                        <path d="M19 21V11l-6-4"/>
                                    </svg>
                                    <span>{{ $trip->company->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Description --}}
                    <div style="margin-bottom: var(--space-8);">
                        <h2 style="font-size: var(--text-xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                            {{ __('About This Trip') }}
                        </h2>
                        <div style="color: var(--color-text-secondary); line-height: var(--leading-relaxed);">
                            {!! nl2br(e($trip->description)) !!}
                        </div>
                    </div>

                    {{-- What's Included --}}
                    @if($trip->tickets)
                        <div style="margin-bottom: var(--space-8);">
                            <h2 style="font-size: var(--text-xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                                {{ __("What's Included") }}
                            </h2>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-3);">
                                @foreach(explode(',', $trip->tickets) as $item)
                                    <div class="flex items-center gap-3" style="padding: var(--space-3); background: var(--color-surface-hover); border-radius: var(--radius-lg);">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                            <polyline points="22 4 12 14.01 9 11.01"/>
                                        </svg>
                                        <span>{{ trim($item) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Trip Itinerary / Daily Schedule --}}
                    @if(isset($trip->itineraries) && count($trip->itineraries) > 0)
                        <div style="margin-bottom: var(--space-8);">
                            <h2 style="font-size: var(--text-xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-inline-end: var(--space-2);">
                                    <path d="M8 2v4"/><path d="M16 2v4"/>
                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                    <path d="M3 10h18"/>
                                </svg>
                                {{ __('Trip Itinerary') }}
                            </h2>

                            <div class="trip-itinerary" style="position: relative; padding-inline-start: var(--space-8);">
                                {{-- Timeline Line --}}
                                <div style="position: absolute; inset-inline-start: 12px; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, var(--color-primary), var(--color-accent));"></div>

                                @foreach($trip->itineraries as $itinerary)
                                    <div class="itinerary-item" style="position: relative; padding-bottom: var(--space-6); {{ $loop->last ? 'padding-bottom: 0;' : '' }}">
                                        {{-- Day Circle --}}
                                        <div style="position: absolute; inset-inline-start: calc(-1 * var(--space-8) + 4px); width: 18px; height: 18px; background: var(--gradient-primary); border-radius: 50%; border: 3px solid var(--color-bg);"></div>

                                        {{-- Content Card --}}
                                        <div class="card" style="padding: var(--space-4); border-inline-start: 3px solid var(--color-primary);">
                                            <div class="flex items-start gap-3">
                                                <span style="background: var(--gradient-primary); color: white; padding: var(--space-1) var(--space-3); border-radius: var(--radius-full); font-size: var(--text-sm); font-weight: var(--font-bold); white-space: nowrap;">
                                                    {{ __('Day') }} {{ $itinerary->day_number }}
                                                </span>
                                                <div style="flex: 1;">
                                                    <h4 style="font-weight: var(--font-semibold); margin-bottom: var(--space-2); color: var(--color-text);">
                                                        {{ $itinerary->title }}
                                                    </h4>
                                                    @if($itinerary->description)
                                                        <p style="color: var(--color-text-secondary); font-size: var(--text-sm); line-height: var(--leading-relaxed);">
                                                            {{ $itinerary->description }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Reviews Section --}}
                    @if(isset($trip->rates) && count($trip->rates) > 0)
                        <div style="margin-bottom: var(--space-8);">
                            <h2 style="font-size: var(--text-xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                                {{ __('Reviews') }} ({{ count($trip->rates) }})
                            </h2>

                            <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                                @foreach($trip->rates as $rate)
                                    <div class="card" style="padding: var(--space-5);">
                                        <div class="flex items-start gap-4">
                                            <div style="width: 48px; height: 48px; background: var(--gradient-primary); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; color: white; font-weight: var(--font-bold);">
                                                {{ substr($rate->user->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div style="flex: 1;">
                                                <div class="flex items-center justify-between" style="margin-bottom: var(--space-2);">
                                                    <strong>{{ $rate->user->name ?? __('Anonymous') }}</strong>
                                                    <div class="flex items-center gap-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $i <= $rate->rate ? 'var(--color-accent)' : 'var(--color-border)' }}" stroke="none">
                                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                                            </svg>
                                                        @endfor
                                                    </div>
                                                </div>
                                                <p style="color: var(--color-text-secondary); font-size: var(--text-sm);">
                                                    {{ $rate->review }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Booking Sidebar --}}
                <aside>
                    <div class="booking-card">
                        {{-- Price --}}
                        <div class="booking-price-wrapper">
                            @if($trip->price_before_discount && $trip->price_before_discount > $trip->price)
                                <span class="booking-price-old">
                                    ${{ number_format($trip->price_before_discount) }}
                                </span>
                                <span class="booking-price-badge">
                                    {{ round((($trip->price_before_discount - $trip->price) / $trip->price_before_discount) * 100) }}% {{ __('Off') }}
                                </span>
                            @endif
                            <div class="booking-price-current">
                                ${{ number_format($trip->price) }}
                                <span class="booking-price-unit">
                                    / {{ __('person') }}
                                </span>
                            </div>
                        </div>

                        {{-- Booking Form --}}
                        <form action="#" method="POST">
                            @csrf

                            {{-- Date --}}
                            <div class="form-group">
                                <label class="form-label">{{ __('Select Date') }}</label>
                                <input type="date" name="date" class="form-input" required>
                            </div>

                            {{-- Travelers --}}
                            <div class="form-group">
                                <label class="form-label">{{ __('Number of Travelers') }}</label>
                                <select name="travelers" class="form-input form-select">
                                    @for($i = 1; $i <= ($trip->personnel_capacity ?? 10); $i++)
                                        <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? __('Traveler') : __('Travelers') }}</option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Total --}}
                            <div style="padding: var(--space-4); background: var(--color-surface-hover); border-radius: var(--radius-lg); margin-bottom: var(--space-5);">
                                <div class="flex items-center justify-between">
                                    <span class="text-muted">{{ __('Total') }}</span>
                                    <span style="font-size: var(--text-xl); font-weight: var(--font-bold);">
                                        ${{ number_format($trip->price) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Book Button - Now triggers Modal --}}
                            <button type="button" class="btn btn-accent btn-lg w-full" onclick="showDownloadModal()">
                                {{ __('Book Now') }}
                            </button>
                        </form>

                        {{-- Contact --}}
                        <div style="margin-top: var(--space-5); padding-top: var(--space-5); border-top: 1px solid var(--color-border); text-align: center;">
                            <p class="text-muted" style="font-size: var(--text-sm); margin-bottom: var(--space-3);">
                                {{ __('Have questions?') }}
                            </p>
                            <a href="{{ route('contact') }}" class="btn btn-outline btn-sm">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                {{ __('Contact Us') }}
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- Related Trips --}}
    @if(isset($relatedTrips) && count($relatedTrips) > 0)
        <section class="section bg-surface">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">{{ __('You May Also Like') }}</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" style="gap: var(--space-6);">
                    @foreach($relatedTrips as $relatedTrip)
                        <div class="scroll-animate">
                            @include('frontend.components.trip-card', ['trip' => $relatedTrip])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    {{-- Premium Download App Modal --}}
    <div id="downloadAppModal" class="premium-download-modal">
        <div class="modal-backdrop" onclick="closeDownloadModal()"></div>
        <div class="modal-content-glass animate__animated animate__zoomIn">
            <button class="modal-close-btn" onclick="closeDownloadModal()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <div class="modal-body-content">
                <div class="modal-graphic">
                    <div class="phone-illustration">
                        <i class="fas fa-mobile-alt"></i>
                        <div class="phone-screen-circles">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                </div>
                <h2 class="modal-title-premium text-center">{{ __('Experience Wjhtak on Mobile') }}</h2>
                <p class="modal-desc-premium text-center">{{ __('For a faster booking experience, real-time updates and exclusive mobile-only offers, download our app now.') }}</p>

                <div class="store-buttons-container">
                    <a href="#" class="store-btn apple-store">
                        <div class="store-icon"><i class="fab fa-apple"></i></div>
                        <div class="store-text">
                            <span class="store-label">{{ __('Download on the') }}</span>
                            <span class="store-name">App Store</span>
                        </div>
                    </a>
                    <a href="#" class="store-btn google-play">
                        <div class="store-icon"><i class="fab fa-google-play"></i></div>
                        <div class="store-text">
                            <span class="store-label">{{ __('Get it on') }}</span>
                            <span class="store-name">Google Play</span>
                        </div>
                    </a>
                </div>

                <div class="modal-footer-hint text-center">
                    <p>{{ __('Already have the app?') }} <a href="#">{{ __('Open here') }}</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .gallery-grid {
        display: grid;
        grid-template-columns: 1fr 180px;
        gap: 15px;
        height: 560px;
        margin-bottom: var(--space-4);
    }

    .gallery-main-col {
        min-width: 0;
    }

    .gallery-thumbs-col {
        min-width: 0;
    }

    .main-trip-slider {
        width: 100%;
        height: 100%;
        border-radius: var(--radius-2xl);
        overflow: hidden;
        position: relative;
        box-shadow: 0 30px 60px rgba(0,0,0,0.15);
    }

    .main-trip-slider .swiper-slide {
        position: relative;
        overflow: hidden;
    }

    .main-trip-slider .slide-inner {
        width: 100%;
        height: 100%;
        transition: transform 0.8s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .main-trip-slider .swiper-slide-active .slide-inner {
        transform: scale(1.05);
    }

    .main-trip-slider img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .glass-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 150px;
        background: linear-gradient(to top, rgba(0,0,0,0.4), transparent);
        pointer-events: none;
    }

    /* Glass Navigation */
    .swiper-nav-glass {
        width: 50px !important;
        height: 50px !important;
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 50% !important;
        color: #fff !important;
        transition: all 0.3s ease !important;
    }

    .swiper-nav-glass:after {
        font-size: 18px !important;
        font-weight: bold;
    }

    .swiper-nav-glass:hover {
        background: rgba(255, 255, 255, 0.4) !important;
        transform: scale(1.1);
    }

    /* Thumbnails - Vertical */
    .thumbnails-trip-slider {
        height: 100%;
        width: 100%;
    }

    .thumbnails-trip-slider .swiper-slide {
        width: 100% !important;
        height: auto !important;
        aspect-ratio: 4/3;
        opacity: 0.6;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .thumbnails-trip-slider .swiper-slide-thumb-active {
        opacity: 1;
        transform: translateX(-10px);
    }

    [dir="rtl"] .thumbnails-trip-slider .swiper-slide-thumb-active {
        transform: translateX(10px);
    }

    .thumb-inner {
        width: 100%;
        height: 100%;
        border-radius: var(--radius-xl);
        overflow: hidden;
        border: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .swiper-slide-thumb-active .thumb-inner {
        border-color: var(--color-primary);
        box-shadow: 0 5px 15px rgba(var(--color-primary-rgb), 0.3);
    }

    .thumbnails-trip-slider img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Badge Info */
    .gallery-badge-info {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 10;
    }

    .badge-glass {
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
        color: white;
        padding: 8px 16px;
        border-radius: var(--radius-full);
        font-size: var(--text-sm);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    @media (max-width: 991px) {
        .gallery-grid {
            grid-template-columns: 1fr;
            height: auto;
        }
        .main-trip-slider {
            height: 400px;
        }
        .gallery-thumbs-col {
            padding-top: 10px;
        }
        .thumbnails-trip-slider {
            height: 80px;
        }
        .thumbnails-trip-slider .swiper-slide {
            width: 100px !important;
            aspect-ratio: 1/1;
        }
        .thumbnails-trip-slider .swiper-slide-thumb-active {
            transform: translateY(-5px);
        }
    }

    @media (max-width: 576px) {
        .main-trip-slider {
            height: 300px;
        }
    }
    /* Trip Details Layout */
    .trip-details-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: var(--space-8);
    }

    @media (min-width: 1024px) {
        .trip-details-layout {
            grid-template-columns: 1fr 400px;
        }
    }

    /* Trip Main Content */
    .trip-main-content {
        min-width: 0; /* Prevent overflow */
    }

    /* Gallery Styles */
    .trip-gallery {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: var(--space-3);
        border-radius: var(--radius-2xl);
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .trip-gallery img {
        transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .trip-gallery:hover img {
        transform: scale(1.05);
    }

    @media (max-width: 768px) {
        .trip-gallery {
            grid-template-columns: 1fr !important;
        }

        .trip-gallery > div:last-child {
            display: none;
        }
    }

    /* Breadcrumb Styling */
    .breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-2);
        font-size: var(--text-sm);
    }

    .breadcrumb-item a {
        color: var(--color-text-muted);
        transition: color 0.2s ease;
    }

    .breadcrumb-item a:hover {
        color: var(--color-primary);
    }

    .breadcrumb-item.active {
        color: var(--color-text);
        font-weight: var(--font-medium);
    }

    .breadcrumb-separator {
        color: var(--color-border);
    }

    /* Trip Header Section */
    .trip-header {
        margin-bottom: var(--space-8);
        padding-bottom: var(--space-6);
        border-bottom: 1px solid var(--color-border);
    }

    .trip-title {
        font-size: var(--text-3xl);
        font-weight: var(--font-extrabold);
        color: var(--color-text);
        margin-bottom: var(--space-4);
        line-height: var(--leading-tight);
    }

    @media (min-width: 768px) {
        .trip-title {
            font-size: var(--text-4xl);
        }
    }

    /* Content Sections */
    .trip-section {
        margin-bottom: var(--space-10);
    }

    .trip-section-title {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        font-size: var(--text-xl);
        font-weight: var(--font-bold);
        color: var(--color-text);
        margin-bottom: var(--space-5);
        padding-bottom: var(--space-3);
        border-bottom: 2px solid var(--color-border);
    }

    .trip-section-title svg {
        color: var(--color-primary);
    }

    /* Description */
    .trip-description {
        color: var(--color-text-secondary);
        line-height: 1.8;
        font-size: var(--text-base);
    }

    /* Included Items Grid */
    .included-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--space-3);
    }

    .included-item {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        padding: var(--space-4);
        background: linear-gradient(135deg, var(--color-surface) 0%, var(--color-surface-hover) 100%);
        border-radius: var(--radius-xl);
        border: 1px solid var(--color-border);
        transition: all 0.3s ease;
    }

    .included-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--color-primary);
    }

    .included-item svg {
        flex-shrink: 0;
    }

    /* Timeline / Itinerary */
    .trip-itinerary {
        position: relative;
        padding-inline-start: var(--space-10);
    }

    .itinerary-timeline {
        position: absolute;
        inset-inline-start: 16px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(to bottom, var(--color-primary), var(--color-accent));
        border-radius: var(--radius-full);
    }

    .itinerary-item {
        position: relative;
        padding-bottom: var(--space-6);
    }

    .itinerary-item:last-child {
        padding-bottom: 0;
    }

    .itinerary-dot {
        position: absolute;
        inset-inline-start: calc(-1 * var(--space-10) + 8px);
        width: 20px;
        height: 20px;
        background: var(--gradient-primary);
        border-radius: 50%;
        border: 4px solid var(--color-bg);
        box-shadow: 0 2px 10px rgba(var(--color-primary-rgb), 0.3);
    }

    .itinerary-card {
        background: var(--color-surface);
        border-radius: var(--radius-xl);
        padding: var(--space-5);
        border-inline-start: 4px solid var(--color-primary);
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }

    .itinerary-card:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow-lg);
    }

    [dir="rtl"] .itinerary-card:hover {
        transform: translateX(-4px);
    }

    .itinerary-day-badge {
        display: inline-flex;
        align-items: center;
        background: var(--gradient-primary);
        color: white;
        padding: var(--space-1) var(--space-4);
        border-radius: var(--radius-full);
        font-size: var(--text-sm);
        font-weight: var(--font-bold);
        margin-bottom: var(--space-3);
    }

    /* Reviews Section */
    .review-card {
        background: var(--color-surface);
        border-radius: var(--radius-xl);
        padding: var(--space-6);
        border: 1px solid var(--color-border);
        transition: all 0.3s ease;
    }

    .review-card:hover {
        box-shadow: var(--shadow-lg);
        border-color: transparent;
    }

    .review-avatar {
        width: 56px;
        height: 56px;
        background: var(--gradient-primary);
        border-radius: var(--radius-full);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: var(--text-xl);
        font-weight: var(--font-bold);
        flex-shrink: 0;
    }

    .review-stars {
        display: flex;
        gap: 2px;
    }

    /* Booking Sidebar */
    .booking-card {
        background: var(--color-surface);
        border-radius: var(--radius-2xl);
        padding: var(--space-8);
        box-shadow:
            0 4px 20px rgba(0, 0, 0, 0.08),
            0 8px 40px rgba(0, 0, 0, 0.04);
        border: 1px solid var(--color-border);
        position: sticky;
        top: calc(70px + var(--space-4));
    }

    .booking-price-wrapper {
        margin-bottom: var(--space-6);
        padding-bottom: var(--space-6);
        border-bottom: 1px solid var(--color-border);
    }

    .booking-price-old {
        font-size: var(--text-lg);
        color: var(--color-text-muted);
        text-decoration: line-through;
        margin-inline-end: var(--space-2);
    }

    .booking-price-badge {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, var(--color-accent) 0%, #fbbf24 100%);
        color: var(--color-gray-900);
        padding: var(--space-1) var(--space-3);
        border-radius: var(--radius-full);
        font-size: var(--text-xs);
        font-weight: var(--font-bold);
        text-transform: uppercase;
    }

    .booking-price-current {
        font-size: var(--text-4xl);
        font-weight: var(--font-extrabold);
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .booking-price-unit {
        font-size: var(--text-base);
        font-weight: var(--font-normal);
        color: var(--color-text-muted);
        -webkit-text-fill-color: var(--color-text-muted);
    }

    .booking-total {
        padding: var(--space-5);
        background: linear-gradient(135deg, var(--color-surface-hover) 0%, var(--color-surface) 100%);
        border-radius: var(--radius-xl);
        margin-bottom: var(--space-5);
        border: 1px solid var(--color-border);
    }

    .booking-contact {
        margin-top: var(--space-6);
        padding-top: var(--space-6);
        border-top: 1px solid var(--color-border);
        text-align: center;
    }

    /* Form Styling */
    .booking-card .form-group {
        margin-bottom: var(--space-4);
    }

    .booking-card .form-label {
        display: block;
        font-size: var(--text-sm);
        font-weight: var(--font-semibold);
        color: var(--color-text);
        margin-bottom: var(--space-2);
    }

    .booking-card .form-input {
        width: 100%;
        padding: var(--space-3) var(--space-4);
        border: 2px solid var(--color-border);
        border-radius: var(--radius-xl);
        font-size: var(--text-base);
        transition: all 0.2s ease;
        background: var(--color-bg);
    }

    .booking-card .form-input:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 4px rgba(var(--color-primary-rgb), 0.1);
    }

    /* Book Button */
    .book-btn {
        width: 100%;
        padding: var(--space-4) var(--space-6);
        font-size: var(--text-lg);
        font-weight: var(--font-bold);
        background: var(--gradient-primary);
        color: white;
        border: none;
        border-radius: var(--radius-xl);
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(var(--color-primary-rgb), 0.3);
    }

    .book-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(var(--color-primary-rgb), 0.4);
    }

    .book-btn:active {
        transform: translateY(0);
    }
    /* Premium Download Modal */
    .premium-download-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 10000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .premium-download-modal.active {
        display: flex;
    }

    .modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(10px);
        animation: fadeIn 0.4s ease;
    }

    .modal-content-glass {
        position: relative;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        width: 100%;
        max-width: 500px;
        border-radius: 30px;
        padding: 40px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.3);
        text-align: center;
        z-index: 1;
    }

    .modal-close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #f0f0f0;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #666;
        transition: all 0.2s ease;
    }

    .modal-close-btn:hover {
        background: #e0e0e0;
        color: #000;
        transform: rotate(90deg);
    }

    .modal-graphic {
        margin-bottom: 25px;
    }

    .phone-illustration {
        font-size: 80px;
        color: var(--color-primary);
        position: relative;
        display: inline-block;
    }

    .phone-screen-circles span {
        position: absolute;
        border-radius: 50%;
        background: var(--color-primary);
        opacity: 0.1;
        z-index: -1;
    }

    .phone-screen-circles span:nth-child(1) { width: 120px; height: 120px; top: -20px; left: -20px; animation: pulse 2s infinite; }
    .phone-screen-circles span:nth-child(2) { width: 160px; height: 160px; top: -40px; left: -40px; animation: pulse 3s infinite; }

    .modal-title-premium {
        font-size: 24px;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 12px;
    }

    .modal-desc-premium {
        color: #666;
        line-height: 1.6;
        margin-bottom: 30px;
    }

    .store-buttons-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 25px;
    }

    .store-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #000;
        color: white;
        padding: 10px 15px;
        border-radius: 12px;
        text-decoration: none;
        transition: transform 0.2s ease;
        text-align: left;
    }

    .store-btn:hover {
        transform: translateY(-3px);
        color: white;
    }

    .store-icon { font-size: 24px; }
    .store-label { display: block; font-size: 10px; opacity: 0.8; line-height: 1; }
    .store-name { display: block; font-size: 14px; font-weight: 700; line-height: 1.2; }

    .modal-footer-hint { font-size: 14px; color: #888; }
    .modal-footer-hint a { color: var(--color-primary); font-weight: 600; text-decoration: none; }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.1; }
        50% { transform: scale(1.1); opacity: 0.15; }
        100% { transform: scale(1); opacity: 0.1; }
    }

    @media (max-width: 480px) {
        .store-buttons-container { grid-template-columns: 1fr; }
        .modal-content-glass { padding: 30px 20px; }
    }
</style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
    const thumbsSwiper = new Swiper('.thumbnails-trip-slider', {
        direction: 'vertical',
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
        mousewheel: true,
        breakpoints: {
            0: {
                direction: 'horizontal',
                slidesPerView: 3,
            },
            992: {
                direction: 'vertical',
                slidesPerView: 4,
            }
        }
    });

    const mainSwiper = new Swiper('.main-trip-slider', {
        loop: true,
        spaceBetween: 10,
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        speed: 1000,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        thumbs: {
            swiper: thumbsSwiper,
        },
        mousewheel: {
            invert: false,
            forceToAxis: true,
        },
    });

    function showDownloadModal() {
        document.getElementById('downloadAppModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeDownloadModal() {
        document.getElementById('downloadAppModal').classList.remove('active');
        document.body.style.overflow = '';
    }
</script>
@endpush


