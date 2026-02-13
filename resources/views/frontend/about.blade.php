@extends('frontend.layouts.app')

@section('title', __('About Us'))

@section('meta_description', __('Learn about Wjhtak - Your trusted partner for premium travel experiences'))

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
            <div class="text-center" style="color: white;">
                <h1 style="font-size: var(--text-4xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                    {{ __('About Wjhtak') }}
                </h1>
                <p style="font-size: var(--text-lg); opacity: 0.9; max-width: 600px; margin: 0 auto;">
                    {{ __('Crafting unforgettable journeys since 2015') }}
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
                <span class="breadcrumb-item active" style="color: white;">{{ __('About Us') }}</span>
            </nav>
        </div>
    </section>

    {{-- Story Section --}}
    <section class="section">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-12); align-items: center;">
                {{-- Image --}}
                <div class="scroll-animate" style="position: relative;">
                    <img
                        src="{{ asset('images/about/team.jpg') }}"
                        alt="{{ __('Our Team') }}"
                        style="width: 100%; border-radius: var(--radius-2xl); box-shadow: var(--shadow-xl);"
                        
                    >
                    {{-- Decoration --}}
                    <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: var(--gradient-accent); border-radius: var(--radius-2xl); z-index: -1;"></div>
                    <div style="position: absolute; bottom: -20px; left: -20px; width: 150px; height: 150px; background: var(--gradient-primary); border-radius: var(--radius-2xl); z-index: -1; opacity: 0.3;"></div>
                </div>
                @php
                    $story = \App\Models\Setting::get('story_' . app()->getLocale(), config('app.name'));
                    $mission = \App\Models\Setting::get('mission_' . app()->getLocale(), config('app.name'));
                    $vision = \App\Models\Setting::get('vision_' . app()->getLocale(), config('app.name'));
                @endphp

                {{-- Content --}}
                <div class="scroll-animate delay-100">
                    <span class="section-subtitle">{{ __('Our Story') }}</span>
                    <h2 style="font-size: var(--text-3xl); font-weight: var(--font-bold); margin-bottom: var(--space-5);">
                        {{ __('Turning Travel Dreams Into Reality') }}
                    </h2>
                    <p style="color: var(--color-text-secondary); line-height: var(--leading-relaxed); margin-bottom: var(--space-4);">
                        {{ $story }}
                    </p>
                    
                    {{-- Stats --}}
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-4);">
                        <div>
                            <div style="font-size: var(--text-3xl); font-weight: var(--font-bold); color: var(--color-primary);" data-counter="50000" data-suffix="+">0</div>
                            <div class="text-muted">{{ __('Happy Travelers') }}</div>
                        </div>
                        <div>
                            <div style="font-size: var(--text-3xl); font-weight: var(--font-bold); color: var(--color-primary);" data-counter="200" data-suffix="+">0</div>
                            <div class="text-muted">{{ __('Partners') }}</div>
                        </div>
                        <div>
                            <div style="font-size: var(--text-3xl); font-weight: var(--font-bold); color: var(--color-primary);" data-counter="50" data-suffix="+">0</div>
                            <div class="text-muted">{{ __('Destinations') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Mission & Vision --}}
    <section class="section bg-surface">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-8);">
                {{-- Mission --}}
                <div class="card scroll-animate" style="padding: var(--space-8);">
                    <div style="width: 70px; height: 70px; background: var(--gradient-accent); border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; margin-bottom: var(--space-5);">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary-dark)" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="m16 10-5.5 5.5L8 13"/>
                        </svg>
                    </div>
                    <h3 style="font-size: var(--text-2xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                        {{ __('Our Mission') }}
                    </h3>
                    <p style="color: var(--color-text-secondary); line-height: var(--leading-relaxed);">
                        {{ $mission }}
                    </p>
                </div>

                {{-- Vision --}}
                <div class="card scroll-animate delay-100" style="padding: var(--space-8);">
                    <div style="width: 70px; height: 70px; background: var(--gradient-accent); border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; margin-bottom: var(--space-5);">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary-dark)" stroke-width="2">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <h3 style="font-size: var(--text-2xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                        {{ __('Our Vision') }}
                    </h3>
                    <p style="color: var(--color-text-secondary); line-height: var(--leading-relaxed);">
                        {{ $vision }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Values Section --}}
    <section class="section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">{{ __('Our Values') }}</span>
                <h2 class="section-title">{{ __('What We Stand For') }}</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4" style="gap: var(--space-6);">
                {{-- Value 1 --}}
                <div class="card text-center scroll-animate" style="padding: var(--space-8);">
                    <div style="width: 60px; height: 60px; background: var(--color-surface-hover); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2">
                            <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <h4 style="font-size: var(--text-lg); font-weight: var(--font-bold); margin-bottom: var(--space-2);">{{ __('Excellence') }}</h4>
                    <p class="text-muted" style="font-size: var(--text-sm);">
                        {{ __('We strive for excellence in every aspect of our service.') }}
                    </p>
                </div>

                {{-- Value 2 --}}
                <div class="card text-center scroll-animate delay-100" style="padding: var(--space-8);">
                    <div style="width: 60px; height: 60px; background: var(--color-surface-hover); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <h4 style="font-size: var(--text-lg); font-weight: var(--font-bold); margin-bottom: var(--space-2);">{{ __('Customer First') }}</h4>
                    <p class="text-muted" style="font-size: var(--text-sm);">
                        {{ __('Your satisfaction and safety are our top priorities.') }}
                    </p>
                </div>

                {{-- Value 3 --}}
                <div class="card text-center scroll-animate delay-200" style="padding: var(--space-8);">
                    <div style="width: 60px; height: 60px; background: var(--color-surface-hover); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2">
                            <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                    </div>
                    <h4 style="font-size: var(--text-lg); font-weight: var(--font-bold); margin-bottom: var(--space-2);">{{ __('Integrity') }}</h4>
                    <p class="text-muted" style="font-size: var(--text-sm);">
                        {{ __('Honesty and transparency in everything we do.') }}
                    </p>
                </div>

                {{-- Value 4 --}}
                <div class="card text-center scroll-animate delay-300" style="padding: var(--space-8);">
                    <div style="width: 60px; height: 60px; background: var(--color-surface-hover); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2">
                            <path d="m21.64 3.64-1.28-1.28a1.21 1.21 0 0 0-1.72 0L2.36 18.64a1.21 1.21 0 0 0 0 1.72l1.28 1.28a1.2 1.2 0 0 0 1.72 0L21.64 5.36a1.2 1.2 0 0 0 0-1.72Z"/>
                            <path d="m14 7 3 3"/>
                            <path d="M5 6v4"/>
                            <path d="M19 14v4"/>
                            <path d="M10 2v2"/>
                            <path d="M7 8H3"/>
                            <path d="M21 16h-4"/>
                            <path d="M11 3H9"/>
                        </svg>
                    </div>
                    <h4 style="font-size: var(--text-lg); font-weight: var(--font-bold); margin-bottom: var(--space-2);">{{ __('Innovation') }}</h4>
                    <p class="text-muted" style="font-size: var(--text-sm);">
                        {{ __('Constantly improving to serve you better.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="section bg-surface">
        <div class="container">
            <div class="newsletter scroll-animate">
                <h2 class="newsletter-title">{{ __('Ready to Start Your Journey?') }}</h2>
                <p class="newsletter-desc">
                    {{ __('Browse our curated collection of trips and find your perfect adventure.') }}
                </p>
                <div class="flex justify-center gap-4" style="margin-top: var(--space-6);">
                    <a href="{{ route('trips.index') }}" class="btn btn-accent btn-lg">
                        {{ __('Explore Trips') }}
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline btn-lg" style="background: white;">
                        {{ __('Contact Us') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    @media (max-width: 768px) {
        .story-grid {
            grid-template-columns: 1fr !important;
        }

        .mission-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush
