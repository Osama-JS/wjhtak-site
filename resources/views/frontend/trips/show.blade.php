@extends('frontend.layouts.app')

@section('title', $trip->title ?? __('Trip Details'))

@section('meta_description', Str::limit(strip_tags($trip->description ?? ''), 160))

@section('og_title', $trip->title ?? __('Trip Details'))
@section('og_description', Str::limit(strip_tags($trip->description ?? ''), 160))

@section('content')
    {{-- Trip Header/Gallery --}}
    @php
        $headerBg = \App\Models\Setting::get('page_header_bg');
    @endphp
    <section style="padding-top: calc(60px + var(--space-4)); background: {{ $headerBg ? 'url('.asset($headerBg).') center/cover no-repeat' : 'var(--color-bg-alt)' }}; position: relative;">
        @if($headerBg)
            <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); z-index: 0;"></div>
        @endif
        <div class="container" style="position: relative; z-index: 1;">
            {{-- Breadcrumb --}}
            <nav class="breadcrumb" style="padding: var(--space-4) 0;" aria-label="Breadcrumb">
                <span class="breadcrumb-item">
                    <a href="{{ route('home') }}">{{ __('Home') }}</a>
                </span>
                <span class="breadcrumb-separator">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </span>
                <span class="breadcrumb-item">
                    <a href="{{ route('trips.index') }}">{{ __('Trips') }}</a>
                </span>
                <span class="breadcrumb-separator">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </span>
                <span class="breadcrumb-item active">{{ Str::limit($trip->title ?? '', 30) }}</span>
            </nav>

            {{-- Gallery --}}
            <div class="trip-gallery" style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-3); border-radius: var(--radius-2xl); overflow: hidden; margin-bottom: var(--space-8);">
                {{-- Main Image --}}
                <div style="aspect-ratio: 16/10; overflow: hidden;">
                    @if($trip->images && count($trip->images) > 0)
                        <img
                            src="{{ asset('storage/' . $trip->images[0]->image_path) }}"
                            alt="{{ $trip->title }}"
                            style="width: 100%; height: 100%; object-fit: cover;"
                        >
                    @else
                        <img
                            src="{{ asset('images/demo/trip-placeholder.jpg') }}"
                            alt="{{ $trip->title }}"
                            style="width: 100%; height: 100%; object-fit: cover;"
                        >
                    @endif
                </div>

                {{-- Side Images --}}
                <div style="display: grid; grid-template-rows: 1fr 1fr; gap: var(--space-3);">
                    @if($trip->images && count($trip->images) > 1)
                        @foreach($trip->images->slice(1, 2) as $image)
                            <div style="overflow: hidden; position: relative;">
                                <img
                                    src="{{ asset('storage/' . $image->image_path) }}"
                                    alt="{{ $trip->title }}"
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                >
                            </div>
                        @endforeach
                    @else
                        <div style="overflow: hidden; background: var(--color-surface);"></div>
                        <div style="overflow: hidden; background: var(--color-surface);"></div>
                    @endif

                    {{-- View All Photos Button --}}
                    @if($trip->images && count($trip->images) > 3)
                        <button class="btn btn-ghost" style="position: absolute; bottom: var(--space-3); right: var(--space-3); background: white;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="7" height="7" x="3" y="3" rx="1"></rect>
                                <rect width="7" height="7" x="14" y="3" rx="1"></rect>
                                <rect width="7" height="7" x="14" y="14" rx="1"></rect>
                                <rect width="7" height="7" x="3" y="14" rx="1"></rect>
                            </svg>
                            {{ __('View All') }} ({{ count($trip->images) }})
                        </button>
                    @endif
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

                            {{-- Book Button --}}
                            <button type="submit" class="btn btn-accent btn-lg w-full">
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
@endsection

@push('styles')
<style>
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
</style>
@endpush

