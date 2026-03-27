@extends('frontend.layouts.app')

@section('title', __('Hotels'))

@section('meta_description', __('Find and book the best hotels around the world at the best prices.'))

@php
    $headerBg = \App\Models\Setting::get('page_header_bg');
@endphp

@section('content')
    {{-- Page Header --}}
    <section class="page-header" style="position: relative; padding-top: calc(var(--space-24) + 60px); padding-bottom: var(--space-12); background: var(--color-primary); overflow: hidden;">
        @if($headerBg)
            <div style="position: absolute; inset: 0; z-index: 0;">
                <img src="{{ asset($headerBg) }}" alt="" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.35;">
                <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,.1), var(--color-primary));"></div>
            </div>
        @else
            <div style="position: absolute; inset: 0; background: var(--gradient-primary, linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark, #c0441f) 100%)); z-index: 0;"></div>
        @endif

        {{-- Decorative Elements --}}
        <div style="position: absolute; inset: 0; z-index: 1; overflow: hidden; pointer-events: none;">
            <div style="position: absolute; top: -50px; inset-inline-end: -50px; width: 200px; height: 200px; background: rgba(255,255,255,.04); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -30px; inset-inline-start: 10%; width: 120px; height: 120px; background: rgba(255,255,255,.03); border-radius: 50%;"></div>
        </div>

        <div class="container" style="position: relative; z-index: 2;">
            <div style="max-width: 700px; margin: 0 auto; text-align: center; color: #fff;">
                <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,.12); backdrop-filter: blur(8px); padding: 6px 16px; border-radius: 30px; font-size: 12px; font-weight: 600; margin-bottom: 16px; letter-spacing: .03em;">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    {{ __('Hotels') }}
                </div>
                <h1 style="font-size: clamp(1.75rem, 5vw, 2.75rem); font-weight: 900; margin: 0 0 12px; line-height: 1.2; text-shadow: 0 2px 8px rgba(0,0,0,.15);">
                    {{ __('Find the Best Hotels Around the World') }}
                </h1>
                <p style="font-size: clamp(.9rem, 2vw, 1.1rem); opacity: .9; max-width: 550px; margin: 0 auto; line-height: 1.6;">
                    {{ __('Discover a wide range of hotels suitable for your budget, and compare easily to choose the best for you.') }}
                </p>
            </div>
        </div>
    </section>

    {{-- Search Bar --}}
    <div class="container" style="margin-top: -36px; position: relative; z-index: 20; padding-left: var(--space-4); padding-right: var(--space-4);">
        <div style="background: var(--color-surface, #fff); padding: 24px; border-radius: var(--radius-2xl, 20px); box-shadow: 0 10px 40px rgba(0,0,0,.08); border: 1px solid var(--color-border, #f0f0f0);">
            <form action="{{ route('hotels.index') }}" method="GET" class="hotel-search-form">
                <div class="hotel-search-grid">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 700; color: var(--color-text, #1e293b); margin-bottom: 8px; text-transform: uppercase; letter-spacing: .04em;">{{ __('Destination') }}</label>
                        <select name="city_code" style="width: 100%; background: var(--color-bg, #f8fafc); border: 1px solid var(--color-border, #e2e8f0); border-radius: var(--radius-xl, 14px); padding: 12px 16px; font-size: 14px; color: var(--color-text, #1e293b); transition: all .2s; appearance: auto;">
                            <option value="">{{ __('Select City') }}</option>
                            @foreach($cities ?? [] as $city)
                                <option value="{{ $city['CityCode'] }}" {{ request('city_code') == $city['CityCode'] ? 'selected' : '' }}>
                                    {{ $city['CityName'] }}, {{ $city['CountryName'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 700; color: var(--color-text, #1e293b); margin-bottom: 8px; text-transform: uppercase; letter-spacing: .04em;">{{ __('Check-in') }}</label>
                        <input type="date" name="check_in" value="{{ request('check_in', \Carbon\Carbon::now()->addDays(7)->toDateString()) }}" style="width: 100%; background: var(--color-bg, #f8fafc); border: 1px solid var(--color-border, #e2e8f0); border-radius: var(--radius-xl, 14px); padding: 12px 16px; font-size: 14px; color: var(--color-text, #1e293b); transition: all .2s;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 700; color: var(--color-text, #1e293b); margin-bottom: 8px; text-transform: uppercase; letter-spacing: .04em;">{{ __('Check-out') }}</label>
                        <input type="date" name="check_out" value="{{ request('check_out', \Carbon\Carbon::now()->addDays(10)->toDateString()) }}" style="width: 100%; background: var(--color-bg, #f8fafc); border: 1px solid var(--color-border, #e2e8f0); border-radius: var(--radius-xl, 14px); padding: 12px 16px; font-size: 14px; color: var(--color-text, #1e293b); transition: all .2s;">
                    </div>
                    <div style="display: flex; align-items: flex-end;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 13px; border-radius: var(--radius-xl, 14px); font-weight: 700; font-size: 15px; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 16px rgba(var(--color-primary-rgb, 232,83,46), .3);">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            {{ __('Search') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Content --}}
    <section style="padding: var(--space-12) 0; background: var(--color-bg, #fafbfc); min-height: 60vh;">
        <div class="container">
            <div class="hotels-layout">
                
                {{-- Mobile Filters Toggle --}}
                <div class="hotels-mobile-filter-toggle">
                    <button id="mobileFiltersBtn" style="width: 100%; background: var(--color-surface, #fff); padding: 12px 16px; border-radius: var(--radius-xl, 14px); border: 1px solid var(--color-border, #e2e8f0); font-size: 14px; font-weight: 700; display: flex; align-items: center; justify-content: space-between; color: var(--color-text, #1e293b); cursor: pointer;">
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <svg style="width: 18px; height: 18px; color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            {{ __('Filters') }}
                        </span>
                        <svg style="width: 16px; height: 16px; transition: transform .3s;" id="mobileFiltersArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </div>

                {{-- Sidebar Filters --}}
                <aside id="sidebarFilters" class="hotels-sidebar">
                    <div style="background: var(--color-surface, #fff); padding: 24px; border-radius: var(--radius-2xl, 20px); border: 1px solid var(--color-border, #f0f0f0); box-shadow: 0 1px 3px rgba(0,0,0,.03); position: sticky; top: 90px;">
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--color-text, #1e293b); margin: 0 0 20px; display: flex; align-items: center; gap: 8px;">
                            <svg style="width: 20px; height: 20px; color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            {{ __('Advanced Filters') }}
                        </h3>

                        <form action="{{ route('hotels.index') }}" method="GET" style="display: flex; flex-direction: column; gap: 20px;">
                            @foreach(request()->except(['price_min', 'price_max', 'rating', 'category']) as $key => $value)
                                @if(is_array($value))
                                    @foreach($value as $v)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach

                            {{-- Price Range --}}
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--color-text, #1e293b); margin-bottom: 10px;">{{ __('Price Range') }}</label>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                    <input type="number" name="price_min" placeholder="{{ __('Min') }}" value="{{ request('price_min') }}" style="width: 100%; background: var(--color-bg, #f8fafc); border: 1px solid var(--color-border, #e2e8f0); border-radius: var(--radius-lg, 12px); padding: 10px 12px; font-size: 13px;">
                                    <input type="number" name="price_max" placeholder="{{ __('Max') }}" value="{{ request('price_max') }}" style="width: 100%; background: var(--color-bg, #f8fafc); border: 1px solid var(--color-border, #e2e8f0); border-radius: var(--radius-lg, 12px); padding: 10px 12px; font-size: 13px;">
                                </div>
                            </div>

                            {{-- Star Rating --}}
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--color-text, #1e293b); margin-bottom: 10px;">{{ __('Hotel Stars') }}</label>
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    @foreach([5, 4, 3, 2, 1] as $star)
                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 6px 8px; border-radius: var(--radius-lg, 12px); transition: background .2s;" onmouseover="this.style.background='var(--color-bg, #f8fafc)'" onmouseout="this.style.background='transparent'">
                                            <input type="checkbox" name="stars[]" value="{{ $star }}" {{ in_array($star, (array) request('stars')) ? 'checked' : '' }} style="accent-color: var(--color-primary); width: 16px; height: 16px; border-radius: 4px;">
                                            <span style="font-size: 13px; color: var(--color-text-muted, #64748b); display: flex; align-items: center; gap: 3px;">
                                                @for($i = 0; $i < $star; $i++)
                                                    <svg style="width: 13px; height: 13px; color: #f59e0b; fill: currentColor;" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                @endfor
                                                @if($star < 5)
                                                    <span style="font-size: 10px; color: var(--color-text-muted, #94a3b8); margin-inline-start: 2px;">& {{ __('Up') }}</span>
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Amenities --}}
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--color-text, #1e293b); margin-bottom: 10px;">{{ __('Amenities') }}</label>
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    @foreach(['WiFi' => __('WiFi'), 'Pool' => __('Pool'), 'Breakfast' => __('Breakfast'), 'Parking' => __('Parking'), 'Gym' => __('Gym'), 'Spa' => __('Spa')] as $key => $label)
                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 6px 8px; border-radius: var(--radius-lg, 12px); transition: background .2s;" onmouseover="this.style.background='var(--color-bg, #f8fafc)'" onmouseout="this.style.background='transparent'">
                                            <input type="checkbox" name="amenities[]" value="{{ $key }}" {{ in_array($key, (array) request('amenities')) ? 'checked' : '' }} style="accent-color: var(--color-primary); width: 16px; height: 16px; border-radius: 4px;">
                                            <span style="font-size: 13px; color: var(--color-text-muted, #64748b);">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; border-radius: var(--radius-xl, 14px); font-weight: 700; box-shadow: 0 4px 14px rgba(var(--color-primary-rgb, 232,83,46), .2);">
                                {{ __('Apply Filters') }}
                            </button>
                            
                            <a href="{{ route('hotels.index', request()->only(['city_code', 'check_in', 'check_out'])) }}" style="display: block; text-align: center; font-size: 12px; color: var(--color-text-muted, #94a3b8); text-decoration: underline; transition: color .2s;" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--color-text-muted, #94a3b8)'">
                                {{ __('Clear all filters') }}
                            </a>
                        </form>
                    </div>
                </aside>

                {{-- Results --}}
                <div class="hotels-results">
                    @if(request()->filled('city_code'))
                        {{-- Results Header --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
                            <h2 style="font-size: 1.1rem; font-weight: 800; color: var(--color-text, #1e293b); margin: 0;">
                                {{ __('Found') }} <span style="color: var(--color-primary);">{{ count($hotels ?? []) }}</span> {{ __('hotels in') }} 
                                @php $selectedCity = collect($cities)->firstWhere('CityCode', request('city_code')); @endphp
                                {{ $selectedCity['CityName'] ?? __('Selected City') }}
                            </h2>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 13px; color: var(--color-text-muted, #94a3b8);">{{ __('Sort by:') }}</span>
                                <select style="font-size: 13px; border: none; background: transparent; font-weight: 700; color: var(--color-text, #1e293b); cursor: pointer; padding: 4px;">
                                    <option>{{ __('Popularity') }}</option>
                                    <option>{{ __('Price: Low to High') }}</option>
                                    <option>{{ __('Price: High to Low') }}</option>
                                    <option>{{ __('Rating') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Hotels Grid --}}
                        <div class="hotels-grid">
                            @forelse($hotels as $hotel)
                                <div class="scroll-animate">
                                    @include('frontend.components.hotel-card', [
                                        'hotel' => $hotel, 
                                        'sessionId' => $sessionId,
                                        'check_in' => request('check_in'),
                                        'check_out' => request('check_out')
                                    ])
                                </div>
                            @empty
                                <div style="grid-column: 1 / -1; padding: 60px 20px; text-align: center;">
                                    <div style="background: var(--color-surface, #fff); padding: 48px 32px; border-radius: var(--radius-2xl, 20px); border: 2px dashed var(--color-border, #e2e8f0); max-width: 420px; margin: 0 auto;">
                                        <svg style="width: 56px; height: 56px; color: #cbd5e1; margin: 0 auto 16px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--color-text, #1e293b); margin: 0 0 8px;">{{ __('No hotels found') }}</h3>
                                        <p style="font-size: 14px; color: var(--color-text-muted, #94a3b8); margin: 0 0 20px;">{{ __('Try adjusting your search criteria or filters.') }}</p>
                                        <a href="{{ route('hotels.index') }}" class="btn btn-ghost" style="color: var(--color-primary); font-weight: 700;">{{ __('Reset All') }}</a>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    @else
                        {{-- Welcome / Start Search --}}
                        <div style="padding: 60px 20px; text-align: center; background: var(--color-surface, #fff); border-radius: var(--radius-2xl, 20px); border: 1px solid var(--color-border, #f0f0f0); box-shadow: 0 1px 3px rgba(0,0,0,.03);">
                            <div style="max-width: 450px; margin: 0 auto;">
                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 72px; height: 72px; background: rgba(var(--color-primary-rgb, 232,83,46), .08); border-radius: 50%; margin-bottom: 20px;">
                                    <svg style="width: 36px; height: 36px; color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <h3 style="font-size: 1.5rem; font-weight: 900; color: var(--color-text, #1e293b); margin: 0 0 10px;">{{ __('Start your search') }}</h3>
                                <p style="font-size: 14px; color: var(--color-text-muted, #94a3b8); margin: 0 0 28px; line-height: 1.6;">{{ __('Enter a destination and dates above to discover amazing hotels at the best rates.') }}</p>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                    <div style="padding: 16px; background: var(--color-bg, #f8fafc); border-radius: var(--radius-xl, 14px); text-align: center; cursor: pointer; border: 1px solid transparent; transition: all .25s;" onmouseover="this.style.background='rgba(var(--color-primary-rgb,232,83,46),.05)';this.style.borderColor='rgba(var(--color-primary-rgb,232,83,46),.15)'" onmouseout="this.style.background='var(--color-bg, #f8fafc)';this.style.borderColor='transparent'">
                                        <span style="display: block; color: var(--color-primary); font-weight: 800; font-size: 1.1rem; margin-bottom: 4px;">Riyadh</span>
                                        <span style="font-size: 10px; color: var(--color-text-muted, #94a3b8); text-transform: uppercase; letter-spacing: .1em;">Popular</span>
                                    </div>
                                    <div style="padding: 16px; background: var(--color-bg, #f8fafc); border-radius: var(--radius-xl, 14px); text-align: center; cursor: pointer; border: 1px solid transparent; transition: all .25s;" onmouseover="this.style.background='rgba(var(--color-primary-rgb,232,83,46),.05)';this.style.borderColor='rgba(var(--color-primary-rgb,232,83,46),.15)'" onmouseout="this.style.background='var(--color-bg, #f8fafc)';this.style.borderColor='transparent'">
                                        <span style="display: block; color: var(--color-primary); font-weight: 800; font-size: 1.1rem; margin-bottom: 4px;">Dubai</span>
                                        <span style="font-size: 10px; color: var(--color-text-muted, #94a3b8); text-transform: uppercase; letter-spacing: .1em;">Trending</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Error Toast --}}
    @if(isset($error))
        <div id="errorToast" style="position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); z-index: 50; animation: slideUp .5s cubic-bezier(.18,.89,.32,1.28);">
            <div style="background: #ef4444; color: #fff; padding: 12px 24px; border-radius: 50px; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 40px rgba(239,68,68,.3); font-size: 14px; font-weight: 600;">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ $error }}</span>
                <button onclick="document.getElementById('errorToast').remove()" style="background: none; border: none; color: #fff; cursor: pointer; padding: 0; opacity: .8; transition: opacity .2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='.8'">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    @endif
@endsection

@push('styles')
<style>
    /* Search Form Grid */
    .hotel-search-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }
    @media (min-width: 768px) {
        .hotel-search-grid {
            grid-template-columns: 1.5fr 1fr 1fr auto;
            gap: 16px;
        }
    }

    /* Search inputs focus */
    .hotel-search-grid input:focus,
    .hotel-search-grid select:focus {
        outline: none;
        border-color: var(--color-primary) !important;
        box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb, 232,83,46), .1);
    }

    /* Main Layout */
    .hotels-layout {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    @media (min-width: 1024px) {
        .hotels-layout {
            flex-direction: row;
            gap: 32px;
        }
    }

    /* Mobile filter toggle */
    .hotels-mobile-filter-toggle {
        display: block;
    }
    @media (min-width: 1024px) {
        .hotels-mobile-filter-toggle {
            display: none;
        }
    }

    /* Sidebar */
    .hotels-sidebar {
        display: none;
        width: 100%;
    }
    @media (min-width: 1024px) {
        .hotels-sidebar {
            display: block !important;
            width: 280px;
            flex-shrink: 0;
        }
    }

    /* Results */
    .hotels-results {
        flex-grow: 1;
        min-width: 0;
    }

    /* Hotels Grid */
    .hotels-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }
    @media (min-width: 640px) {
        .hotels-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (min-width: 1280px) {
        .hotels-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Animations */
    @keyframes slideUp {
        0% { transform: translate(-50%, 100%); opacity: 0; }
        100% { transform: translate(-50%, 0); opacity: 1; }
    }

    /* Scroll animations */
    .scroll-animate {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp .5s ease forwards;
    }
    .scroll-animate:nth-child(2) { animation-delay: .08s; }
    .scroll-animate:nth-child(3) { animation-delay: .16s; }
    .scroll-animate:nth-child(4) { animation-delay: .24s; }
    .scroll-animate:nth-child(5) { animation-delay: .32s; }
    .scroll-animate:nth-child(6) { animation-delay: .4s; }

    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileFiltersBtn = document.getElementById('mobileFiltersBtn');
        const sidebarFilters = document.getElementById('sidebarFilters');
        const mobileFiltersArrow = document.getElementById('mobileFiltersArrow');

        if (mobileFiltersBtn) {
            mobileFiltersBtn.addEventListener('click', function() {
                const isHidden = sidebarFilters.style.display === 'none' || !sidebarFilters.style.display;
                sidebarFilters.style.display = isHidden ? 'block' : 'none';
                mobileFiltersArrow.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0)';
            });
        }
    });
</script>
@endpush
