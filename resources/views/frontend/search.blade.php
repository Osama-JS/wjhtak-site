@extends('frontend.layouts.app')

@section('title', __('Search Results'))


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
                    {{ __('Search Results') }}
                </h1>
                @if(request('q'))
                    <p style="font-size: var(--text-lg); opacity: 0.9; color: white !important;">
                        {{ __('Showing results for') }}: "{{ request('q') }}"
                    </p>
                @endif
            </div>
        </div>
    </section>

    {{-- Search Results --}}
    <section class="section">
        <div class="container">
            {{-- Search Box --}}
            <div style="max-width: 600px; margin: 0 auto var(--space-10);">
                <form action="{{ route('search') }}" method="GET">
                    <div style="position: relative;">
                        <input type="text" name="q" class="form-input" value="{{ request('q') }}" placeholder="{{ __('Search trips, destinations...') }}" style="padding-left: 48px; padding-right: 100px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%);">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                        <button type="submit" class="btn btn-primary" style="position: absolute; right: 4px; top: 50%; transform: translateY(-50%);">
                            {{ __('Search') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Results --}}
            @if(isset($trips) && count($trips) > 0)
                <p class="text-muted text-center" style="margin-bottom: var(--space-6);">
                    {{ __('Found') }} {{ $trips->total() }} {{ __('results') }}
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" style="gap: var(--space-6);">
                    @foreach($trips as $trip)
                        <div class="scroll-animate">
                            @include('frontend.components.trip-card', ['trip' => $trip])
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($trips->hasPages())
                    <div class="pagination" style="margin-top: var(--space-10);">
                        {{ $trips->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="text-center" style="padding: var(--space-16);">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="1" style="margin: 0 auto var(--space-6);">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                    <h3 style="font-size: var(--text-xl); margin-bottom: var(--space-2);">{{ __('No results found') }}</h3>
                    <p class="text-muted">{{ __('Try different keywords or browse our trips') }}</p>
                    <a href="{{ route('trips.index') }}" class="btn btn-primary" style="margin-top: var(--space-6);">
                        {{ __('Browse Trips') }}
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection
