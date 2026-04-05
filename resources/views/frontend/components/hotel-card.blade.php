{{-- Hotel Card Component - Premium Design --}}
<div class="hotel-card group" style="
    background: var(--color-surface, #fff);
    border-radius: var(--radius-2xl, 20px);
    overflow: hidden;
    border: 1px solid var(--color-border, #f0f0f0);
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
    transition: all .4s cubic-bezier(.4,0,.2,1);
    height: 100%;
    display: flex;
    flex-direction: column;
">
    {{-- Hotel Image --}}
    <div style="position: relative; aspect-ratio: 16/10; overflow: hidden;">
        @if(isset($hotel['HotelPicture']) && $hotel['HotelPicture'])
            <img src="{{ $hotel['HotelPicture'] }}" 
                 alt="{{ $hotel['HotelName'] }}" 
                 loading="lazy"
                 style="width: 100%; height: 100%; object-fit: cover; transition: transform .6s cubic-bezier(.4,0,.2,1);"
                 onmouseover="this.style.transform='scale(1.08)'" 
                 onmouseout="this.style.transform='scale(1)'">
        @else
            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); display: flex; align-items: center; justify-content: center;">
                <svg style="width: 48px; height: 48px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        @endif

        {{-- Gradient Overlay --}}
        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 60%; background: linear-gradient(to top, rgba(0,0,0,.45), transparent); pointer-events: none;"></div>

        {{-- Star Rating Badge --}}
        @if(isset($hotel['StarRating']) && $hotel['StarRating'] > 0)
            <div style="position: absolute; top: 12px; inset-inline-end: 12px; background: rgba(255,255,255,.92); backdrop-filter: blur(8px); padding: 4px 10px; border-radius: 10px; display: flex; align-items: center; gap: 4px; box-shadow: 0 2px 8px rgba(0,0,0,.1);">
                <span style="color: #f59e0b; font-weight: 800; font-size: 13px;">{{ $hotel['StarRating'] }}</span>
                <svg style="width: 12px; height: 12px; color: #f59e0b; fill: currentColor;" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
            </div>
        @endif

        {{-- Hotel Name Overlay on Image --}}
        <div style="position: absolute; bottom: 12px; inset-inline-start: 14px; inset-inline-end: 14px; z-index: 2;">
            <h3 style="font-size: 1.1rem; font-weight: 800; color: #fff; margin: 0; line-height: 1.35; text-shadow: 0 1px 4px rgba(0,0,0,.3); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                <a href="{{ route('hotels.show', array_merge(['hotelCode' => $hotel['HotelCode'], 'session_id' => $sessionId], request()->only(['check_in', 'check_out']))) }}" style="color: inherit; text-decoration: none;">
                    {{ $hotel['HotelName'] }}
                </a>
            </h3>
        </div>
    </div>

    {{-- Card Body --}}
    <div style="padding: 16px 18px 18px; flex-grow: 1; display: flex; flex-direction: column;">
        {{-- Location --}}
        <div style="display: flex; align-items: center; gap: 5px; color: var(--color-text-muted, #64748b); font-size: 13px; margin-bottom: 12px;">
            <svg style="width: 14px; height: 14px; flex-shrink: 0; color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $hotel['HotelAddress'] ?? __('Address unavailable') }}</span>
        </div>

        {{-- Price & CTA --}}
        <div style="margin-top: auto; display: flex; align-items: flex-end; justify-content: space-between; padding-top: 14px; border-top: 1px solid var(--color-border, #f1f5f9);">
            <div>
                <p style="font-size: 11px; color: var(--color-text-muted, #94a3b8); margin: 0 0 2px;">{{ __('Starting from') }}</p>
                <p style="font-size: 1.35rem; font-weight: 900; color: var(--color-primary); margin: 0; line-height: 1.2;">
                    {{ number_format($hotel['LowestRate'] ?? 0, 2) }}
                    <span style="font-size: 12px; font-weight: 500; color: var(--color-text-muted, #94a3b8);">{{ $hotel['Currency'] ?? 'SAR' }}</span>
                </p>
                <p style="font-size: 11px; color: var(--color-text-muted, #94a3b8); margin: 2px 0 0; font-weight: 500;">{{ __('per night') }}</p>
            </div>

            <a href="{{ route('hotels.show', array_merge(['hotelCode' => $hotel['HotelCode'], 'session_id' => $sessionId], request()->only(['check_in', 'check_out']))) }}" 
               class="btn btn-primary" 
               style="padding: 10px 20px; border-radius: var(--radius-xl, 14px); font-size: 13px; font-weight: 700; white-space: nowrap; box-shadow: 0 4px 14px rgba(var(--color-primary-rgb, 232,83,46), .25); transition: all .3s ease;">
                {{ __('View Details') }}
            </a>
        </div>

        @if(isset($hotel['Availability']) && $hotel['Availability'] < 5)
            <p style="font-size: 10px; color: #ef4444; font-weight: 700; margin: 8px 0 0; text-transform: uppercase; letter-spacing: .08em; animation: pulse 2s ease-in-out infinite;">
                🔥 {{ __('Limited availability left!') }}
            </p>
        @endif
    </div>
</div>

<style>
    .hotel-card:hover {
        box-shadow: 0 20px 50px rgba(0,0,0,.1) !important;
        transform: translateY(-4px);
        border-color: transparent !important;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
</style>
