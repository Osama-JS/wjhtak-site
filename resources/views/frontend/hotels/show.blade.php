@extends('frontend.layouts.app')

@section('title', $hotel['HotelName'] ?? __('Hotel Details'))

@section('content')
    {{-- Hotel Header / Gallery --}}
    <section style="padding-top: calc(var(--space-24) + 60px); padding-bottom: var(--space-12); background: var(--color-bg, #fafbfc); border-bottom: 1px solid var(--color-border, #f0f0f0);">
        <div class="container">
            {{-- Breadcrumb --}}
            <nav style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text-muted, #94a3b8); margin-bottom: 24px; flex-wrap: wrap;" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" style="color: inherit; text-decoration: none; transition: color .2s;" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='inherit'">{{ __('Home') }}</a>
                <svg style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <a href="{{ route('hotels.index') }}" style="color: inherit; text-decoration: none; transition: color .2s;" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='inherit'">{{ __('Hotels') }}</a>
                <svg style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span style="color: var(--color-text, #1e293b); font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 200px;">{{ $hotel['HotelName'] }}</span>
            </nav>

            <div class="hotel-detail-top">
                {{-- Gallery Section --}}
                <div class="hotel-gallery-col">
                    <div style="position: relative; border-radius: var(--radius-2xl, 20px); overflow: hidden; aspect-ratio: 16/9; box-shadow: 0 8px 30px rgba(0,0,0,.1);">
                        @php
                            $images = $hotel['Images'] ?? [];
                            $firstImage = count($images) > 0 ? $images[0] : ($hotel['HotelPicture'] ?? null);
                        @endphp
                        @if($firstImage)
                            <img id="mainGalleryImage" src="{{ $firstImage }}" alt="{{ $hotel['HotelName'] }}" style="width: 100%; height: 100%; object-fit: cover; transition: opacity .4s ease;">
                        @else
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f1f5f9, #e2e8f0); display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 64px; height: 64px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        
                        {{-- Info Badge --}}
                        <div style="position: absolute; bottom: 16px; inset-inline-start: 16px; background: rgba(255,255,255,.92); backdrop-filter: blur(10px); padding: 8px 16px; border-radius: 16px; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 16px rgba(0,0,0,.1);">
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <span style="font-size: 1.1rem; font-weight: 900; color: var(--color-primary);">{{ $hotel['StarRating'] ?? 0 }}</span>
                                <svg style="width: 14px; height: 14px; color: #f59e0b; fill: currentColor;" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            </div>
                            <div style="width: 1px; height: 20px; background: #e2e8f0;"></div>
                            <div style="display: flex; align-items: center; gap: 4px; font-size: 13px; font-weight: 700; color: var(--color-text, #374151);">
                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                {{ $hotel['CityName'] ?? __('City') }}
                            </div>
                        </div>
                    </div>

                    {{-- Thumbnails --}}
                    @if(count($images) > 1)
                        <div class="gallery-thumbs" style="display: flex; gap: 10px; margin-top: 16px; overflow-x: auto; padding-bottom: 8px;">
                            @foreach(array_slice($images, 0, 8) as $index => $img)
                                <button onclick="changeMainImage('{{ $img }}', this)" class="gallery-thumb" style="width: 72px; height: 72px; flex-shrink: 0; border-radius: 14px; overflow: hidden; border: 2px solid transparent; cursor: pointer; transition: all .25s; padding: 0; background: none; box-shadow: 0 1px 4px rgba(0,0,0,.06);">
                                    <img src="{{ $img }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                </button>
                            @endforeach
                            @if(count($images) > 8)
                                <button style="width: 72px; height: 72px; flex-shrink: 0; border-radius: 14px; background: rgba(0,0,0,.04); display: flex; align-items: center; justify-content: center; color: var(--color-text-muted, #64748b); font-weight: 800; font-size: 13px; cursor: pointer; border: none; transition: all .25s;" onmouseover="this.style.background='rgba(var(--color-primary-rgb,232,83,46),.08)'" onmouseout="this.style.background='rgba(0,0,0,.04)'">
                                    +{{ count($images) - 8 }}
                                </button>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Hotel Info Side --}}
                <div class="hotel-info-col">
                    <div style="display: flex; flex-direction: column; height: 100%; justify-content: space-between;">
                        <div>
                            {{-- Top Badge --}}
                            <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(var(--color-primary-rgb, 232,83,46), .08); color: var(--color-primary); padding: 5px 14px; border-radius: 30px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 16px;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--color-primary); animation: pulse 2s ease infinite;"></span>
                                {{ __('Top Pick in') }} {{ $hotel['CityName'] ?? __('City') }}
                            </div>

                            <h1 style="font-size: clamp(1.5rem, 4vw, 2.25rem); font-weight: 900; color: var(--color-text, #1e293b); margin: 0 0 12px; line-height: 1.25;">
                                {{ $hotel['HotelName'] }}
                            </h1>

                            <p style="font-size: 14px; color: var(--color-text-muted, #64748b); line-height: 1.7; margin: 0 0 20px;">
                                {{ __('Enjoy a special stay at') }} {{ $hotel['HotelName'] }} {{ __('with the best services and facilities.') }}
                                {{ __('The hotel is strategically located near the most important tourist attractions and provides a comfortable and integrated stay experience.') }}
                            </p>

                            {{-- Facilities --}}
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 24px;">
                                @foreach(array_slice($hotel['HotelFacilities'] ?? [__('Free WiFi'), __('Pool'), __('Breakfast')], 0, 4) as $facility)
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text, #374151); background: var(--color-surface, #fff); padding: 10px 14px; border-radius: 14px; border: 1px solid var(--color-border, #f0f0f0); box-shadow: 0 1px 2px rgba(0,0,0,.02);">
                                        <svg style="width: 14px; height: 14px; color: var(--color-primary); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $facility }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Booking Summary Box --}}
                        <div style="background: var(--color-primary); padding: 24px; border-radius: var(--radius-2xl, 20px); color: #fff; box-shadow: 0 8px 30px rgba(var(--color-primary-rgb, 232,83,46), .25);">
                            <p style="font-size: 13px; opacity: .8; margin: 0 0 4px;">{{ __('Starting from') }}</p>
                            <div style="display: flex; align-items: flex-end; gap: 8px; margin-bottom: 20px;">
                                <span style="font-size: 2.25rem; font-weight: 900; line-height: 1;">{{ number_format($hotel['LowestRate'] ?? 0, 2) }}</span>
                                <span style="font-size: 13px; margin-bottom: 4px; opacity: .85;">{{ $hotel['Currency'] ?? 'SAR' }} / {{ __('Night') }}</span>
                            </div>
                            <button onclick="scrollToRooms()" style="width: 100%; background: #fff; color: var(--color-primary); padding: 14px; border-radius: 16px; font-weight: 900; font-size: 1rem; border: none; cursor: pointer; box-shadow: 0 4px 16px rgba(0,0,0,.1); transition: all .3s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.15)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)'">
                                {{ __('View Available Rooms') }}
                            </button>
                            <p style="text-align: center; font-size: 10px; text-transform: uppercase; font-weight: 700; letter-spacing: .1em; margin: 12px 0 0; opacity: .65;">
                                ✓ {{ __('Instant Confirmation') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Detailed Info & Rooms --}}
    <section style="padding: var(--space-16, 64px) 0; background: var(--color-surface, #fff);" id="roomsSection">
        <div class="container">
            <div class="hotel-detail-bottom">
                {{-- Main Content --}}
                <div class="hotel-main-col">
                    {{-- Description --}}
                    <div style="margin-bottom: 48px;">
                        <h2 style="font-size: 1.5rem; font-weight: 900; color: var(--color-text, #1e293b); margin: 0 0 20px; display: flex; align-items: center; gap: 14px;">
                            {{ __('Hotel Features') }}
                            <div style="flex-grow: 1; height: 3px; background: var(--color-border, #f0f0f0); border-radius: 10px;"></div>
                        </h2>
                        <div style="font-size: 14px; color: var(--color-text-muted, #64748b); line-height: 1.8;">
                            {!! $hotel['Description'] ?? __('No detailed description available.') !!}
                        </div>
                    </div>

                    {{-- Rooms --}}
                    <div>
                        <h2 style="font-size: 1.5rem; font-weight: 900; color: var(--color-text, #1e293b); margin: 0 0 24px; display: flex; align-items: center; gap: 14px;">
                            {{ __('Available Rooms') }}
                            <div style="flex-grow: 1; height: 3px; background: var(--color-border, #f0f0f0); border-radius: 10px;"></div>
                        </h2>
                        
                        @if(count($rooms) > 0)
                            <div style="display: flex; flex-direction: column; gap: 20px;">
                                @foreach($rooms as $room)
                                    @include('frontend.components.room-card', [
                                        'room' => $room,
                                        'hotelCode' => $hotelCode,
                                        'sessionId' => $sessionId,
                                        'hotelName' => $hotel['HotelName'] ?? 'Hotel'
                                    ])
                                @endforeach
                            </div>
                        @else
                            <div style="background: var(--color-bg, #f8fafc); padding: 48px 24px; border-radius: var(--radius-2xl, 20px); text-align: center; border: 2px dashed var(--color-border, #e2e8f0);">
                                <svg style="width: 48px; height: 48px; color: #cbd5e1; margin: 0 auto 12px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--color-text, #1e293b); margin: 0 0 8px;">{{ __('Search required to see live rates') }}</h3>
                                <p style="font-size: 13px; color: var(--color-text-muted, #94a3b8); margin: 0 0 20px;">{{ __('Please perform a search with your preferred dates from the listing page.') }}</p>
                                <a href="{{ route('hotels.index') }}" class="btn btn-primary" style="padding: 12px 32px; border-radius: 14px; font-weight: 700;">
                                    {{ __('Search Now') }}
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Reviews --}}
                    <div style="margin-top: 56px;">
                        <h2 style="font-size: 1.5rem; font-weight: 900; color: var(--color-text, #1e293b); margin: 0 0 24px; display: flex; align-items: center; gap: 14px;">
                            {{ __('Customer Reviews') }}
                            <div style="flex-grow: 1; height: 3px; background: var(--color-border, #f0f0f0); border-radius: 10px;"></div>
                        </h2>
                        <div style="background: var(--color-bg, #f8fafc); padding: 28px; border-radius: var(--radius-2xl, 20px);">
                            <div class="reviews-summary">
                                <div style="text-align: center; flex-shrink: 0;">
                                    <div style="font-size: 2.5rem; font-weight: 900; color: var(--color-text, #1e293b); line-height: 1.1;">4.8</div>
                                    <div style="display: flex; justify-content: center; gap: 2px; margin: 6px 0;">
                                        @for($i = 0; $i < 5; $i++) 
                                            <svg style="width: 14px; height: 14px; color: #f59e0b; fill: currentColor;" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        @endfor
                                    </div>
                                    <div style="font-size: 12px; color: var(--color-text-muted, #94a3b8);">124 {{ __('Reviews') }}</div>
                                </div>
                                <div style="flex-grow: 1; display: flex; flex-direction: column; gap: 6px;">
                                    @foreach([5 => 80, 4 => 30, 3 => 10, 2 => 4, 1 => 0] as $star => $pct)
                                        <div style="display: flex; align-items: center; gap: 10px; font-size: 12px;">
                                            <span style="width: 50px; font-weight: 700; color: var(--color-text-muted, #64748b);">{{ $star }} {{ __('stars') }}</span>
                                            <div style="flex-grow: 1; height: 6px; background: var(--color-border, #e2e8f0); border-radius: 10px; overflow: hidden;">
                                                <div style="height: 100%; background: var(--color-primary); border-radius: 10px; width: {{ $pct }}%; transition: width .6s ease;"></div>
                                            </div>
                                            <span style="width: 32px; text-align: end; color: var(--color-text-muted, #94a3b8);">{{ $pct }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <p style="text-align: center; color: var(--color-text-muted, #94a3b8); font-style: italic; font-size: 14px; margin: 20px 0 0;">"{{ __('Real feedback from guests who stayed at this hotel.') }}"</p>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="hotel-sidebar-col">
                    <div style="position: sticky; top: 90px; display: flex; flex-direction: column; gap: 24px;">
                        {{-- Booking Form --}}
                        <div style="background: var(--color-surface, #fff); padding: 28px; border-radius: var(--radius-2xl, 20px); border: 1px solid var(--color-border, #f0f0f0); box-shadow: 0 4px 20px rgba(0,0,0,.05);">
                            <h3 style="font-size: 1.25rem; font-weight: 900; color: var(--color-text, #1e293b); margin: 0 0 20px;">{{ __('Book Your Stay') }}</h3>
                            <form action="#" style="display: flex; flex-direction: column; gap: 14px;">
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--color-text-muted, #94a3b8); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px;">{{ __('Dates') }}</label>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                        <input type="date" style="background: var(--color-bg, #f8fafc); border: 1px solid var(--color-border, #e2e8f0); border-radius: 12px; padding: 10px 12px; font-size: 13px; width: 100%;">
                                        <input type="date" style="background: var(--color-bg, #f8fafc); border: 1px solid var(--color-border, #e2e8f0); border-radius: 12px; padding: 10px 12px; font-size: 13px; width: 100%;">
                                    </div>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--color-text-muted, #94a3b8); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px;">{{ __('Guests & Rooms') }}</label>
                                    <select style="width: 100%; background: var(--color-bg, #f8fafc); border: 1px solid var(--color-border, #e2e8f0); border-radius: 12px; padding: 10px 12px; font-size: 13px;">
                                        <option>2 {{ __('Adults') }}, 1 {{ __('Room') }}</option>
                                        <option>1 {{ __('Adult') }}, 1 {{ __('Room') }}</option>
                                        <option>2 {{ __('Adults') }}, 1 {{ __('Child') }}, 1 {{ __('Room') }}</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; border-radius: 16px; font-weight: 900; font-size: 15px; box-shadow: 0 4px 16px rgba(var(--color-primary-rgb, 232,83,46), .25); margin-top: 4px; transition: all .3s;">
                                    {{ __('Check Availability') }}
                                </button>
                                <p style="text-align: center; font-size: 11px; color: var(--color-text-muted, #94a3b8); font-style: italic; margin: 0;">{{ __("Don't miss the chance! Best price guarantee.") }}</p>
                            </form>
                        </div>

                        {{-- Map --}}
                        <div style="background: var(--color-surface, #fff); border-radius: var(--radius-2xl, 20px); border: 1px solid var(--color-border, #f0f0f0); overflow: hidden; height: 240px; position: relative; box-shadow: 0 1px 3px rgba(0,0,0,.03);">
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f1f5f9, #e2e8f0);"></div>
                            <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; padding: 20px; text-align: center;">
                                <div style="background: rgba(255,255,255,.92); backdrop-filter: blur(10px); padding: 20px 24px; border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.06);">
                                    <svg style="width: 28px; height: 28px; color: var(--color-primary); margin: 0 auto 8px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    <p style="font-size: 13px; font-weight: 700; color: var(--color-text, #1e293b); margin: 0 0 4px; line-height: 1.4;">{{ $hotel['HotelAddress'] ?? __('Address unavailable') }}</p>
                                    <a href="#" style="font-size: 12px; color: var(--color-primary); font-weight: 700; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">{{ __('Open in Google Maps') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    /* Top section: Gallery + Info */
    .hotel-detail-top {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }
    @media (min-width: 1024px) {
        .hotel-detail-top {
            flex-direction: row;
            gap: 36px;
        }
    }
    .hotel-gallery-col {
        width: 100%;
    }
    .hotel-info-col {
        width: 100%;
    }
    @media (min-width: 1024px) {
        .hotel-gallery-col { flex: 2; }
        .hotel-info-col { flex: 1; }
    }

    /* Bottom section: Main + Sidebar */
    .hotel-detail-bottom {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }
    @media (min-width: 1024px) {
        .hotel-detail-bottom {
            flex-direction: row;
            gap: 40px;
        }
    }
    .hotel-main-col {
        width: 100%;
    }
    .hotel-sidebar-col {
        width: 100%;
    }
    @media (min-width: 1024px) {
        .hotel-main-col { flex: 2; }
        .hotel-sidebar-col { flex: 1; }
    }

    /* Reviews summary */
    .reviews-summary {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    @media (min-width: 640px) {
        .reviews-summary {
            flex-direction: row;
            align-items: center;
            gap: 32px;
        }
    }

    /* Gallery thumbnails scrollbar */
    .gallery-thumbs::-webkit-scrollbar {
        height: 4px;
    }
    .gallery-thumbs::-webkit-scrollbar-track {
        background: transparent;
    }
    .gallery-thumbs::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .gallery-thumbs::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }

    /* Active thumbnail */
    .gallery-thumb.active {
        border-color: var(--color-primary) !important;
    }

    /* Pulse animation */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .4; }
    }
</style>
@endpush

@push('scripts')
<script>
    function changeMainImage(src, btn) {
        const mainImg = document.getElementById('mainGalleryImage');
        if (mainImg) {
            mainImg.style.opacity = '0';
            setTimeout(() => {
                mainImg.src = src;
                mainImg.style.opacity = '1';
            }, 250);
        }
        // Update active thumbnail
        document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
        if (btn) btn.classList.add('active');
    }

    function scrollToRooms() {
        const roomsSec = document.getElementById('roomsSection');
        if (roomsSec) {
            roomsSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Set first thumbnail as active
    document.addEventListener('DOMContentLoaded', function() {
        const firstThumb = document.querySelector('.gallery-thumb');
        if (firstThumb) firstThumb.classList.add('active');
    });
</script>
@endpush
