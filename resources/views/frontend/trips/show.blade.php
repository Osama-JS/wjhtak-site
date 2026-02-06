@extends('frontend.layouts.app')

@section('title', $trip->title ?? __('Trip Details'))

@section('meta_description', Str::limit(strip_tags($trip->description ?? ''), 160))

@section('og_title', $trip->title ?? __('Trip Details'))
@section('og_description', Str::limit(strip_tags($trip->description ?? ''), 160))

@section('content')
    {{-- Trip Header/Gallery --}}
    <section style="padding-top: calc(60px + var(--space-4)); background: var(--color-bg-alt);">
        <div class="container">
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
            <div style="display: grid; grid-template-columns: 1fr; gap: var(--space-8);">
                @media (min-width: 1024px) {
                    style="grid-template-columns: 1fr 380px;"
                }

                {{-- Main Content --}}
                <div>
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
                    <div class="card" style="padding: var(--space-6); position: sticky; top: calc(60px + var(--space-4));">
                        {{-- Price --}}
                        <div style="margin-bottom: var(--space-5);">
                            @if($trip->price_before_discount && $trip->price_before_discount > $trip->price)
                                <span style="font-size: var(--text-lg); color: var(--color-text-muted); text-decoration: line-through;">
                                    ${{ number_format($trip->price_before_discount) }}
                                </span>
                                <span class="badge badge-accent" style="margin-left: var(--space-2);">
                                    {{ round((($trip->price_before_discount - $trip->price) / $trip->price_before_discount) * 100) }}% {{ __('Off') }}
                                </span>
                            @endif
                            <div style="font-size: var(--text-3xl); font-weight: var(--font-bold); color: var(--color-primary);">
                                ${{ number_format($trip->price) }}
                                <span style="font-size: var(--text-base); font-weight: var(--font-normal); color: var(--color-text-muted);">
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
    @media (min-width: 1024px) {
        .trip-details-layout {
            grid-template-columns: 1fr 380px;
        }
    }

    @media (max-width: 768px) {
        .trip-gallery {
            grid-template-columns: 1fr !important;
        }

        .trip-gallery > div:last-child {
            display: none;
        }
    }
</style>
@endpush
