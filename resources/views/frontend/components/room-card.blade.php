{{-- Room Card Component - Premium Responsive Design --}}
<div class="room-card" style="
    background: var(--color-surface, #fff);
    border-radius: var(--radius-2xl, 20px);
    border: 1px solid var(--color-border, #f0f0f0);
    box-shadow: 0 1px 3px rgba(0,0,0,.03);
    overflow: hidden;
    transition: all .35s cubic-bezier(.4,0,.2,1);
    margin-bottom: 20px;
">
    <div style="display: flex; flex-direction: column;">
        {{-- Inner row on desktop --}}
        <div class="room-card-inner" style="display: flex; flex-direction: column;">
            {{-- Room Image --}}
            <div class="room-card-image" style="position: relative; overflow: hidden; background: #f8fafc;">
                @if(isset($room['RoomPicture']) && $room['RoomPicture'])
                    <img src="{{ $room['RoomPicture'] }}" alt="{{ $room['RoomTypeName'] }}" loading="lazy"
                         style="width: 100%; height: 100%; object-fit: cover; transition: transform .6s cubic-bezier(.4,0,.2,1);">
                @else
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 48px; height: 48px; color: #cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                @endif
                
                {{-- Dynamic Badge --}}
                @php
                    $isRefundable = true;
                    if (isset($room['Inclusions'])) {
                        foreach ($room['Inclusions'] as $inc) {
                            if (stripos($inc, 'Non-Refundable') !== false) $isRefundable = false;
                        }
                    }
                @endphp
                <div style="position: absolute; top: 12px; inset-inline-start: 12px; display: flex; flex-direction: column; gap: 6px;">
                    <span style="background: {{ $isRefundable ? '#16a34a' : '#ef4444' }}; color: #fff; font-size: 10px; font-weight: 800; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; box-shadow: 0 4px 12px rgba(0,0,0,.15);">
                        {{ $isRefundable ? __('Refundable') : __('Non-Refundable') }}
                    </span>
                    <span style="background: rgba(255,255,255,.9); backdrop-filter: blur(4px); color: var(--color-text); font-size: 10px; font-weight: 800; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; box-shadow: 0 4px 12px rgba(0,0,0,.1);">
                        {{ $room['RatePlanName'] ?? __('Standard Rate') }}
                    </span>
                </div>
            </div>

            {{-- Room Details --}}
            <div style="padding: 24px; flex-grow: 1; display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 12px;">
                    <h4 style="font-size: 1.25rem; font-weight: 900; color: var(--color-text, #1e293b); margin: 0; line-height: 1.2;">
                        {{ $room['RoomTypeName'] }}
                    </h4>
                    <div style="display: flex; align-items: center; gap: 4px; color: var(--color-text-muted); font-size: 13px; font-weight: 700; background: var(--color-bg); padding: 4px 8px; border-radius: 8px;">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        {{ $room['TotalPax'] ?? 2 }}
                    </div>
                </div>

                {{-- Inclusions with Icons --}}
                <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
                    @if(isset($room['Inclusions']) && is_array($room['Inclusions']))
                        @foreach($room['Inclusions'] as $inclusion)
                            @php
                                $icon = '<svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>';
                                $color = '#64748b';
                                $bg = '#f8fafc';
                                
                                if (stripos($inclusion, 'WiFi') !== false) {
                                    $icon = '<svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.151c5.857-5.857 15.355-5.857 21.213 0"></path></svg>';
                                    $color = '#2563eb'; $bg = '#eff6ff';
                                } elseif (stripos($inclusion, 'Breakfast') !== false) {
                                    $icon = '<svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>';
                                    $color = '#f59e0b'; $bg = '#fffbeb';
                                } elseif (stripos($inclusion, 'Cancel') !== false || stripos($inclusion, 'Refundable') !== false) {
                                    $icon = '<svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A3.323 3.323 0 0010.605 3.333a3.323 3.323 0 00-4.636 4.636 3.323 3.323 0 003.333 5.385l5.385 5.385a3.323 3.323 0 004.636-4.636l-5.385-5.385a3.323 3.323 0 00.285-.385z"></path></svg>';
                                    $color = '#16a34a'; $bg = '#f0fdf4';
                                }
                            @endphp
                            <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: {{ $color }}; background: {{ $bg }}; padding: 6px 12px; border-radius: 10px; border: 1px solid rgba(0,0,0,.03);">
                                {!! $icon !!}
                                {{ is_array($inclusion) ? implode(', ', $inclusion) : $inclusion }}
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Description --}}
                <p style="font-size: 14px; color: var(--color-text-muted, #64748b); line-height: 1.6; margin: 0 0 24px;">
                    {{ $room['RoomDescription'] ?? __('No additional description available for this room type.') }}
                </p>

                {{-- Pricing & Action --}}
                <div style="margin-top: auto; display: flex; align-items: center; justify-content: space-between; gap: 20px; padding-top: 20px; border-top: 1px solid var(--color-border, #f1f5f9);">
                    <div style="display: flex; flex-direction: column;">
                        @php
                            $nightsCount = $nights ?? 1;
                            $perNight = ($room['TotalFare'] ?? 0) / $nightsCount;
                        @endphp
                        <div style="display: flex; align-items: baseline; gap: 4px;">
                            <span style="font-size: 1.75rem; font-weight: 900; color: var(--color-text); letter-spacing: -0.02em;">{{ number_format($perNight, 2) }}</span>
                            <span style="font-size: 13px; font-weight: 700; color: var(--color-text-muted);">{{ $room['Currency'] ?? 'SAR' }} / {{ __('Night') }}</span>
                        </div>
                        @if($nightsCount > 1)
                            <div style="font-size: 12px; font-weight: 600; color: var(--color-primary); background: rgba(var(--color-primary-rgb, 232,83,46), .08); padding: 2px 8px; border-radius: 4px; margin-top: 4px; width: fit-content;">
                                {{ __('Total:') }} {{ number_format($room['TotalFare'], 2) }} {{ $room['Currency'] ?? 'SAR' }}
                            </div>
                        @endif
                    </div>
                    
                    <button class="btn btn-primary room-book-btn" 
                        data-room-index="{{ $room['RoomIndex'] }}"
                        data-rate-plan-code="{{ $room['RatePlanCode'] }}"
                        data-hotel-code="{{ $hotelCode ?? '' }}"
                        data-hotel-name="{{ $hotelName ?? '' }}"
                        data-session-id="{{ $sessionId ?? '' }}"
                        data-total-price="{{ $room['TotalFare'] }}"
                        data-room-type-name="{{ $room['RoomTypeName'] }}"
                        style="padding: 14px 32px; border-radius: 16px; font-weight: 900; font-size: 15px; box-shadow: 0 6px 20px rgba(var(--color-primary-rgb, 232,83,46), .25); transition: all .3s cubic-bezier(.4,0,.2,1); white-space: nowrap;">
                        {{ __('Book Now') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // We use delegation or just select all buttons. Since this might be included multiple times, 
        // we should ensure the script only runs once OR handle it carefully.
        // For simplicity in a Blade loop, we can use a class-based selector.
    });

    if (typeof bookRoom !== 'function') {
        window.bookRoom = function(btn) {
            const data = {
                room_index: btn.getAttribute('data-room-index'),
                rate_plan_code: btn.getAttribute('data-rate-plan-code'),
                hotel_code: btn.getAttribute('data-hotel-code'),
                hotel_name: btn.getAttribute('data-hotel-name'),
                session_id: btn.getAttribute('data-session-id'),
                total_price: btn.getAttribute('data-total-price'),
                room_type_name: btn.getAttribute('data-room-type-name'),
                _token: '{{ csrf_token() }}'
            };

            if (!data.session_id) {
                alert('{{ __("Session expired. Please search again.") }}');
                return;
            }

            // Show loading state
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Verifying...") }}';

            fetch('/api/hotels/pre-book', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(res => {
                if (!res.error && res.data && res.data.available) {
                    // Redirect to booking page with the result token
                    const params = new URLSearchParams({
                        session_id: data.session_id,
                        hotel_code: data.hotel_code,
                        hotel_name: data.hotel_name,
                        result_token: res.data.result_token,
                        total_price: res.data.total_price || data.total_price,
                        room_type_name: data.room_type_name,
                        // Pass search criteria from URL if possible
                        adults: new URLSearchParams(window.location.search).get('adults') || 2,
                        children: new URLSearchParams(window.location.search).get('children') || 0,
                        check_in: new URLSearchParams(window.location.search).get('check_in') || '',
                        check_out: new URLSearchParams(window.location.search).get('check_out') || '',
                    });
                    window.location.href = '/hotels/booking/create?' + params.toString();
                } else {
                    alert(res.message || '{{ __("Room is no longer available.") }}');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(err => {
                console.error(err);
                alert('{{ __("An error occurred. Please try again.") }}');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        };
    }

    // Attach click listener to all buttons
    document.querySelectorAll('.room-book-btn').forEach(btn => {
        btn.onclick = function() {
            bookRoom(this);
        };
    });
</script>

<style>
    .room-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0,0,0,.08) !important;
        border-color: var(--color-primary) !important;
    }
    .room-card:hover img {
        transform: scale(1.08);
    }
    .room-book-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(var(--color-primary-rgb, 232,83,46), .35) !important;
    }
    .room-book-btn:active {
        transform: translateY(0);
    }

    /* Mobile: stack layout */
    .room-card-image {
        width: 100%;
        aspect-ratio: 16 / 10;
    }

    /* Desktop: side-by-side layout */
    @media (min-width: 992px) {
        .room-card-inner {
            flex-direction: row !important;
        }
        .room-card-image {
            width: 320px;
            min-height: 280px;
            flex-shrink: 0;
            aspect-ratio: unset;
        }
    }
</style>
