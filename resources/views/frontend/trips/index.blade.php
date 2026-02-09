@extends('frontend.layouts.app')

@section('title', __('Trips'))

@section('meta_description', __('Browse our collection of amazing travel packages and book your next adventure'))

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
                    {{ __('Explore Our Trips') }}
                </h1>
                <p style="font-size: var(--text-lg); opacity: 0.9; max-width: 600px; margin: 0 auto;">
                    {{ __('Discover handpicked travel experiences designed to create unforgettable memories.') }}
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
                <span class="breadcrumb-item active" style="color: white;">{{ __('Trips') }}</span>
            </nav>
        </div>
    </section>

    {{-- Trips Content --}}
    <section class="section">
        <div class="container">
            <div class="trips-layout">

                {{-- Mobile Filters Toggle --}}
                <div class="md:hidden" style="margin-bottom: var(--space-4);">
                    <button id="filtersToggle" class="btn btn-outline w-full">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                        {{ __('Filters') }}
                    </button>
                </div>

                {{-- Layout Wrapper --}}
                <div class="trips-grid-wrapper">

                    {{-- Filters Sidebar --}}
                    <aside id="filtersSidebar" class="card" style="padding: var(--space-6); height: fit-content; display: none;">
                        <form action="{{ route('trips.index') }}" method="GET">
                            <h3 style="font-size: var(--text-lg); font-weight: var(--font-bold); margin-bottom: var(--space-5);">
                                {{ __('Filters') }}
                            </h3>

                            {{-- Destination Filter --}}
                            <div class="form-group">
                                <label class="form-label">{{ __('Destination') }}</label>
                                <select name="country" class="form-input form-select">
                                    <option value="">{{ __('All Destinations') }}</option>
                                    @foreach($countries ?? [] as $country)
                                        <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                                            {{ $country->nicename ?? $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Price Range --}}
                            <div class="form-group">
                                <label class="form-label">{{ __('Price Range') }}</label>
                                <div class="flex gap-2">
                                    <input
                                        type="number"
                                        name="min_price"
                                        class="form-input"
                                        placeholder="{{ __('Min') }}"
                                        value="{{ request('min_price') }}"
                                        style="width: 50%;"
                                    >
                                    <input
                                        type="number"
                                        name="max_price"
                                        class="form-input"
                                        placeholder="{{ __('Max') }}"
                                        value="{{ request('max_price') }}"
                                        style="width: 50%;"
                                    >
                                </div>
                            </div>

                            {{-- Duration Filter --}}
                            <div class="form-group">
                                <label class="form-label">{{ __('Duration') }}</label>
                                <select name="duration" class="form-input form-select">
                                    <option value="">{{ __('Any Duration') }}</option>
                                    <option value="1-3" {{ request('duration') == '1-3' ? 'selected' : '' }}>1-3 {{ __('Days') }}</option>
                                    <option value="4-7" {{ request('duration') == '4-7' ? 'selected' : '' }}>4-7 {{ __('Days') }}</option>
                                    <option value="8-14" {{ request('duration') == '8-14' ? 'selected' : '' }}>8-14 {{ __('Days') }}</option>
                                    <option value="15+" {{ request('duration') == '15+' ? 'selected' : '' }}>15+ {{ __('Days') }}</option>
                                </select>
                            </div>

                            {{-- Sort By --}}
                            <div class="form-group">
                                <label class="form-label">{{ __('Sort By') }}</label>
                                <select name="sort" class="form-input form-select">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('Latest') }}</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>{{ __('Top Rated') }}</option>
                                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>{{ __('Most Popular') }}</option>
                                </select>
                            </div>

                            {{-- Apply Filters Button --}}
                            <button type="submit" class="btn btn-primary w-full">
                                {{ __('Apply Filters') }}
                            </button>

                            {{-- Clear Filters --}}
                            @if(request()->hasAny(['country', 'min_price', 'max_price', 'duration', 'sort']))
                                <a href="{{ route('trips.index') }}" class="btn btn-ghost w-full" style="margin-top: var(--space-2);">
                                    {{ __('Clear Filters') }}
                                </a>
                            @endif
                        </form>
                    </aside>

                    {{-- Trips Grid --}}
                    <div>
                        {{-- Results Header --}}
                        <div class="flex items-center justify-between" style="margin-bottom: var(--space-6);">
                            <p class="text-muted">
                                {{ __('Showing') }} <strong>{{ $trips->count() ?? 0 }}</strong> {{ __('trips') }}
                            </p>

                            {{-- View Toggle --}}
                            <div class="flex gap-2">
                                <button class="btn btn-ghost active" id="gridViewBtn" aria-label="{{ __('Grid View') }}">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect width="7" height="7" x="3" y="3" rx="1"></rect>
                                        <rect width="7" height="7" x="14" y="3" rx="1"></rect>
                                        <rect width="7" height="7" x="14" y="14" rx="1"></rect>
                                        <rect width="7" height="7" x="3" y="14" rx="1"></rect>
                                    </svg>
                                </button>
                                <button class="btn btn-ghost" id="listViewBtn" aria-label="{{ __('List View') }}">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="8" x2="21" y1="6" y2="6"></line>
                                        <line x1="8" x2="21" y1="12" y2="12"></line>
                                        <line x1="8" x2="21" y1="18" y2="18"></line>
                                        <line x1="3" x2="3.01" y1="6" y2="6"></line>
                                        <line x1="3" x2="3.01" y1="12" y2="12"></line>
                                        <line x1="3" x2="3.01" y1="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Trips Grid --}}
                        <div id="tripsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" style="gap: var(--space-6);">
                            @forelse($trips ?? [] as $trip)
                                <div class="scroll-animate">
                                    @include('frontend.components.trip-card', ['trip' => $trip])
                                </div>
                            @empty
                                {{-- No Results --}}
                                <div style="grid-column: 1 / -1; text-align: center; padding: var(--space-16);">
                                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="1" style="margin: 0 auto var(--space-6);">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="m21 21-4.3-4.3"></path>
                                    </svg>
                                    <h3 style="font-size: var(--text-xl); margin-bottom: var(--space-2);">{{ __('No trips found') }}</h3>
                                    <p class="text-muted">{{ __('Try adjusting your filters or search criteria.') }}</p>
                                    <a href="{{ route('trips.index') }}" class="btn btn-primary" style="margin-top: var(--space-6);">
                                        {{ __('Clear Filters') }}
                                    </a>
                                </div>
                            @endforelse
                        </div>

                        {{-- Pagination --}}
                        @if(isset($trips) && $trips->hasPages())
                            <div class="pagination" style="margin-top: var(--space-10);">
                                {{-- Previous --}}
                                @if($trips->onFirstPage())
                                    <span class="pagination-item disabled">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="15 18 9 12 15 6"></polyline>
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $trips->previousPageUrl() }}" class="pagination-item">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="15 18 9 12 15 6"></polyline>
                                        </svg>
                                    </a>
                                @endif

                                {{-- Page Numbers --}}
                                @foreach($trips->getUrlRange(1, $trips->lastPage()) as $page => $url)
                                    @if($page == $trips->currentPage())
                                        <span class="pagination-item active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="pagination-item">{{ $page }}</a>
                                    @endif
                                @endforeach

                                {{-- Next --}}
                                @if($trips->hasMorePages())
                                    <a href="{{ $trips->nextPageUrl() }}" class="pagination-item">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </a>
                                @else
                                    <span class="pagination-item disabled">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    /* Trips Page Layout */
    .trips-layout {
        position: relative;
    }

    .trips-grid-wrapper {
        display: grid;
        grid-template-columns: 1fr;
        gap: var(--space-8);
    }

    @media (min-width: 1024px) {
        .trips-grid-wrapper {
            grid-template-columns: 280px 1fr;
        }

        #filtersSidebar {
            display: block !important;
        }
    }

    /* Filters Sidebar */
    #filtersSidebar {
        background: var(--color-surface);
        border-radius: var(--radius-xl);
        border: 1px solid var(--color-border);
        box-shadow: var(--shadow-sm);
        height: fit-content;
        position: sticky;
        top: 90px;
        transition: all 0.3s ease;
    }

    #filtersSidebar .form-label {
        font-weight: var(--font-bold);
        color: var(--color-text);
        font-size: var(--text-sm);
        margin-bottom: var(--space-2);
        display: block;
    }

    #filtersSidebar .form-input {
        width: 100%;
        padding: var(--space-3);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        font-size: var(--text-sm);
        background: var(--color-bg);
        transition: all 0.2s ease;
    }

    #filtersSidebar .form-input:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb), 0.1);
    }

    /* List View Styles */
    .list-view {
        display: flex !important;
        flex-direction: column;
        gap: var(--space-6);
    }

    .list-view .trip-card {
        display: grid;
        grid-template-columns: 300px 1fr;
        max-width: 100%;
        height: auto;
        aspect-ratio: auto;
    }

    .list-view .trip-card-image {
        position: relative;
        height: 100%;
        min-height: 220px;
    }

    .list-view .trip-card-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: var(--space-6);
    }

    .list-view .trip-card-title {
        font-size: var(--text-xl);
        margin-bottom: var(--space-2);
    }

    .list-view .trip-card-meta {
        margin-top: auto;
        padding-top: var(--space-4);
    }

    @media (max-width: 768px) {
        .list-view .trip-card {
            grid-template-columns: 1fr;
        }

        .list-view .trip-card-image {
            height: 200px;
            min-height: auto;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Mobile filters toggle
    const filtersToggle = document.getElementById('filtersToggle');
    const filtersSidebar = document.getElementById('filtersSidebar');

    if (filtersToggle && filtersSidebar) {
        filtersToggle.addEventListener('click', () => {
            filtersSidebar.style.display = filtersSidebar.style.display === 'none' ? 'block' : 'none';
        });
    }

    // View toggle (Grid/List)
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const tripsGrid = document.getElementById('tripsGrid');

    if (gridViewBtn && listViewBtn && tripsGrid) {
        gridViewBtn.addEventListener('click', () => {
            tripsGrid.classList.remove('list-view');
            gridViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');
        });

        listViewBtn.addEventListener('click', () => {
            tripsGrid.classList.add('list-view');
            listViewBtn.classList.add('active');
            gridViewBtn.classList.remove('active');
        });
    }
</script>
@endpush
