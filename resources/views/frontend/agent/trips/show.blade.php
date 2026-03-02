@extends('frontend.agent.layouts.agent-layout')

@section('title', __('Trip Details') . ': ' . $trip->title)
@section('page-title', __('Trip Details'))

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
<style>
    :root { --accent: #e8532e; --accent-soft: rgba(232,83,46,.08); }

    /* ── Section Cards ── */
    .agent-section {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(0,0,0,.03);
        margin-bottom: 28px;
        overflow: hidden;
    }
    .agent-section-header {
        padding: 20px 28px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .agent-section-header h5 {
        margin: 0;
        font-weight: 800;
        color: #1e293b;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .agent-section-header h5 .icon-wrap {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: var(--accent-soft);
        display: flex; align-items: center; justify-content: center;
        color: var(--accent); font-size: .9rem;
    }
    .agent-section-body { padding: 24px 28px; }

    /* ── Trip Info Grid ── */
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; }
    .info-item { background: #f8fafc; border-radius: 14px; padding: 16px 18px; border: 1px solid #f1f5f9; }
    .info-item .info-label { font-size: .73rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }
    .info-item .info-value { font-size: 1rem; font-weight: 700; color: #1e293b; }

    /* ── Route Banner ── */
    .route-banner {
        background: linear-gradient(135deg, #f8fafc, #fff);
        border-radius: 16px;
        padding: 22px 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 24px;
        border: 1px solid #f1f5f9;
        margin-bottom: 20px;
    }
    .route-city h6 { font-weight: 800; color: #1e293b; margin: 0; font-size: 1.2rem; }
    .route-city small { color: #94a3b8; font-size: .8rem; font-weight: 600; }
    .route-line {
        flex: 1;
        height: 2px;
        background: linear-gradient(90deg, var(--accent), #fb923c);
        position: relative;
        max-width: 200px;
    }
    .route-line::after { content: '✈'; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); background: #fff; padding: 0 8px; color: var(--accent); font-size: 1rem; }

    /* ── Image Gallery ── */
    .dz-zone { border: 2px dashed #e2e8f0; border-radius: 16px; background: #f8fafc; padding: 30px; text-align: center; cursor: pointer; transition: all .2s; }
    .dz-zone:hover { border-color: var(--accent); background: var(--accent-soft); }
    .dz-zone .dz-message { font-size: .95rem; color: #64748b; font-weight: 600; }
    .dz-zone .dz-message i { font-size: 2.5rem; color: var(--accent); display: block; margin-bottom: 12px; }

    .images-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 14px; margin-top: 20px; }
    .img-thumb-wrap { position: relative; border-radius: 12px; overflow: hidden; aspect-ratio: 1; }
    .img-thumb-wrap img { width: 100%; height: 100%; object-fit: cover; }
    .img-thumb-wrap .del-btn {
        position: absolute; top: 6px; right: 6px;
        width: 28px; height: 28px;
        background: rgba(239,68,68,.9); color: #fff;
        border: none; border-radius: 8px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: .75rem; opacity: 0; transition: opacity .2s;
    }
    .img-thumb-wrap:hover .del-btn { opacity: 1; }

    /* ── Itinerary ── */
    .sortable-ghost { opacity: .4; background: #f8fafc !important; border: 2px dashed var(--accent) !important; }
    .itinerary-card {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 20px;
        border-radius: 14px;
        border: 1px solid #f1f5f9;
        background: #fff;
        margin-bottom: 12px;
        transition: box-shadow .2s;
    }
    .itinerary-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.06); }
    .drag-handle { cursor: grab; color: #cbd5e1; font-size: 1.1rem; padding-top: 2px; flex-shrink: 0; }
    .day-badge { background: var(--accent-soft); color: var(--accent); border-radius: 8px; padding: 2px 10px; font-size: .75rem; font-weight: 800; }
    .btn-itin-action { width: 32px; height: 32px; border-radius: 8px; border: 1px solid #f1f5f9; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; background: #fff; transition: all .2s; font-size: .8rem; }
    .btn-itin-edit:hover { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }
    .btn-itin-delete:hover { border-color: #ef4444; color: #ef4444; background: #fef2f2; }

    /* ── Bookings Table ── */
    .bk-table { width: 100%; border-collapse: collapse; }
    .bk-table th { padding: 10px 14px; font-size: .73rem; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; font-weight: 700; background: #f8fafc; }
    .bk-table td { padding: 12px 14px; border-top: 1px solid #f1f5f9; font-size: .88rem; color: #475569; }
    .bk-table tr:hover td { background: #fafafa; }
    .badge-status { padding: 4px 12px; border-radius: 20px; font-size: .75rem; font-weight: 700; }
    .badge-confirmed { background: #f0fdf4; color: #16a34a; }
    .badge-pending   { background: #fff7ed; color: #ea580c; }
    .badge-cancelled { background: #fef2f2; color: #dc2626; }

    @media (max-width: 600px) { .info-grid { grid-template-columns: 1fr 1fr; } }

    /* ── Itinerary Two-Col Layout ── */
    .itin-layout { display: grid; grid-template-columns: 1fr 2fr; gap: 24px; align-items: start; }

    /* ── Add Day Form Box ── */
    .itin-add-box {
        background: #fff;
        border-radius: 18px;
        border: 1.5px solid #f1f5f9;
        box-shadow: 0 4px 24px rgba(0,0,0,.04);
        overflow: hidden;
    }
    .itin-add-box-header {
        padding: 16px 20px;
        background: linear-gradient(135deg, var(--accent-soft), rgba(232,83,46,.04));
        border-bottom: 1.5px solid rgba(232,83,46,.12);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .itin-add-box-header .hdr-icon {
        width: 34px; height: 34px;
        border-radius: 10px;
        background: var(--accent);
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: .8rem;
        flex-shrink: 0;
    }
    .itin-add-box-header h6 {
        margin: 0;
        font-weight: 800;
        font-size: .92rem;
        color: #1e293b;
    }
    .itin-add-box-body { padding: 20px; }

    /* Premium Input Fields */
    .pf { margin-bottom: 16px; }
    .pf label {
        font-size: .78rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .05em;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 7px;
    }
    .pf label i { color: var(--accent); font-size: .75rem; }
    .pf .pf-input {
        width: 100%;
        padding: 11px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 11px;
        font-size: .9rem;
        color: #1e293b;
        background: #fafafa;
        transition: all .2s;
        outline: none;
        font-family: inherit;
    }
    .pf .pf-input:focus {
        border-color: var(--accent);
        background: #fff;
        box-shadow: 0 0 0 3.5px rgba(232,83,46,.1);
    }
    .pf .pf-input::placeholder { color: #c0cada; font-size: .88rem; }
    textarea.pf-input { resize: vertical; min-height: 90px; line-height: 1.6; }

    .btn-add-day {
        width: 100%;
        padding: 13px;
        background: linear-gradient(135deg, var(--accent), #f05a32);
        color: #fff;
        border: none;
        border-radius: 11px;
        font-weight: 800;
        font-size: .9rem;
        cursor: pointer;
        transition: all .2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        letter-spacing: .02em;
        box-shadow: 0 4px 14px rgba(232,83,46,.3);
        margin-top: 4px;
    }
    .btn-add-day:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(232,83,46,.4); }
    .btn-add-day:active { transform: translateY(0); }

    @media (max-width: 768px) {
        .itin-layout { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">

    {{-- ── Breadcrumb ── --}}
    <div style="margin-bottom: 20px;">
        <a href="{{ route('agent.trips.index') }}" style="color:#64748b; text-decoration:none; font-size:.88rem;">
            <i class="fas fa-angle-right" style="margin-inline-end:6px;"></i>{{ __('My Trips') }}
        </a>
        <span style="margin: 0 8px; color:#cbd5e1;">/</span>
        <span style="font-size:.88rem; color:#1e293b; font-weight:700;">{{ $trip->title }}</span>
    </div>

    {{-- ──────────────────────── SECTION 1: TRIP INFO ──────────────────────── --}}
    <div class="agent-section">
        <div class="agent-section-header">
            <h5>
                <span class="icon-wrap"><i class="fas fa-plane-departure"></i></span>
                {{ $trip->title }}
            </h5>
            <div style="display:flex; gap:10px; flex-wrap: wrap;">
                <span class="{{ $trip->active ? 'badge-confirmed' : 'badge-pending' }} badge-status">
                    {{ $trip->active ? __('Active') : __('Inactive') }}
                </span>
                <a href="{{ route('agent.trips.edit', $trip->id) }}"
                   style="padding: 8px 18px; background: var(--accent); color:#fff; border-radius:10px; font-size:.85rem; font-weight:700; text-decoration:none;">
                    <i class="fas fa-pen me-1"></i> {{ __('Edit Trip') }}
                </a>
            </div>
        </div>
        <div class="agent-section-body">
            {{-- Route --}}
            <div class="route-banner">
                <div class="route-city text-start">
                    <h6>{{ $trip->fromCity->name ?? '-' }}</h6>
                    <small>{{ $trip->fromCountry->name ?? '' }}</small>
                </div>
                <div class="route-line"></div>
                <div class="route-city text-end">
                    <h6>{{ $trip->toCity->name ?? '-' }}</h6>
                    <small>{{ $trip->toCountry->name ?? '' }}</small>
                </div>
            </div>
            {{-- Info Grid --}}
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">{{ __('Current Price') }}</div>
                    <div class="info-value" style="color: var(--accent);">{{ number_format($trip->price, 0) }} {{ __('SAR') }}</div>
                </div>
                @if($trip->price_before_discount && $trip->price_before_discount > $trip->price)
                <div class="info-item">
                    <div class="info-label">{{ __('Old Price') }}</div>
                    <div class="info-value"><s style="color:#94a3b8;">{{ number_format($trip->price_before_discount, 0) }}</s></div>
                </div>
                @endif
                <div class="info-item">
                    <div class="info-label">{{ __('Duration') }}</div>
                    <div class="info-value">{{ $trip->duration ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">{{ __('Tickets') }}</div>
                    <div class="info-value">{{ $trip->tickets ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">{{ __('Max Capacity') }}</div>
                    <div class="info-value">{{ $trip->personnel_capacity ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">{{ __('Expiry Date') }}</div>
                    <div class="info-value" style="{{ $trip->expiry_date && $trip->expiry_date < now()->toDateString() ? 'color:#ef4444;' : '' }}">
                        {{ $trip->expiry_date ? \Carbon\Carbon::parse($trip->expiry_date)->format('d M Y') : '-' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">{{ __('Bookings') }}</div>
                    <div class="info-value">{{ $trip->bookings->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ──────────────────────── SECTION 2: IMAGES ──────────────────────── --}}
    <div class="agent-section">
        <div class="agent-section-header">
            <h5>
                <span class="icon-wrap"><i class="fas fa-images"></i></span>
                {{ __('Trip Photos') }}
            </h5>
            <small style="color:#94a3b8; font-size:.82rem;">{{ $trip->images->count() }} {{ __('Photos') }}</small>
        </div>
        <div class="agent-section-body">
            {{-- Dropzone --}}
            <form id="trip-images-upload" class="dz-zone dropzone" action="{{ route('agent.trips.images.store', $trip->id) }}">
                @csrf
                <div class="dz-message">
                    <i class="fas fa-cloud-upload-alt"></i>
                    {{ __('Drag and drop photos here to upload') }}
                    <br><small style="color:#94a3b8; font-weight:500; margin-top:4px; display:block;">JPG, PNG, GIF — {{ __('Max') }} 5MB</small>
                </div>
            </form>

            {{-- Existing Images --}}
            <div class="images-grid" id="images-grid">
                @foreach($trip->images as $image)
                <div class="img-thumb-wrap" id="img-{{ $image->id }}">
                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="">
                    <button class="del-btn" onclick="deleteImage({{ $image->id }}, this)" title="{{ __('Delete') }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ──────────────────────── SECTION 3: ITINERARY ──────────────────────── --}}
    <div class="agent-section">
        <div class="agent-section-header">
            <h5>
                <span class="icon-wrap"><i class="fas fa-list-ul"></i></span>
                {{ __('Trip Schedule') }}
            </h5>
            <span style="background:#f1f5f9; border-radius:8px; padding:4px 12px; font-size:.8rem; color:#64748b; font-weight:700;">
                {{ $trip->itineraries->count() }} {{ __('Days') }}
            </span>
        </div>
        <div class="agent-section-body">
            <div class="itin-layout">

                {{-- Add Form --}}
                <div class="itin-add-box">
                    <div class="itin-add-box-header">
                        <span class="hdr-icon"><i class="fas fa-calendar-plus"></i></span>
                        <h6>{{ __('Add Day Details') }}</h6>
                    </div>
                    <div class="itin-add-box-body">
                        <form action="{{ route('agent.trips.itinerary.store', $trip->id) }}" method="POST">
                            @csrf
                            <div class="pf">
                                <label><i class="fas fa-hashtag"></i>{{ __('Day Number') }}</label>
                                <input type="number" name="day_number" class="pf-input" value="{{ $trip->itineraries->count() + 1 }}" min="1" required>
                            </div>
                            <div class="pf">
                                <label><i class="fas fa-heading"></i>{{ __('Title') }}</label>
                                <input type="text" name="title" class="pf-input" placeholder="{{ __('e.g. Arrival in Cairo') }}" required>
                            </div>
                            <div class="pf">
                                <label><i class="fas fa-align-left"></i>{{ __('Description') }}</label>
                                <textarea name="description" class="pf-input" rows="4" placeholder="{{ __('Enter day details...') }}"></textarea>
                            </div>
                            <button type="submit" class="btn-add-day">
                                <i class="fas fa-plus"></i> {{ __('Add Day') }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Itinerary List --}}
                <div>
                    @if($trip->itineraries->isEmpty())
                        <div style="text-align:center; padding:60px; color:#94a3b8;">
                            <i class="fas fa-calendar-alt" style="font-size:3rem; margin-bottom:12px; display:block;"></i>
                            <p style="font-weight:600;">{{ __('No itinerary days added yet.') }}</p>
                        </div>
                    @else
                        <div id="itinerary-list">
                            @foreach($trip->itineraries as $itin)
                            <div class="itinerary-card" data-id="{{ $itin->id }}">
                                <i class="fas fa-grip-vertical drag-handle"></i>
                                <div style="flex:1; min-width:0;">
                                    <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:6px;">
                                        <span class="day-badge">{{ __('Day') }} {{ $itin->day_number }}</span>
                                        <h6 style="font-weight:800; color:#1e293b; margin:0; flex:1;">{{ $itin->title }}</h6>
                                        <div style="display:flex; gap:6px; flex-shrink:0;">
                                            <button class="btn-itin-action btn-itin-edit"
                                                    onclick="editItinerary({{ $itin->id }}, {{ $itin->day_number }}, '{{ addslashes($itin->title) }}', '{{ addslashes($itin->description) }}')"
                                                    title="{{ __('Edit') }}">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button class="btn-itin-action btn-itin-delete"
                                                    type="button"
                                                    onclick="deleteItinerary({{ $itin->id }})"
                                                    title="{{ __('Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form method="POST" id="del-itin-{{ $itin->id }}"
                                                  action="{{ route('agent.trips.itinerary.destroy', $itin->id) }}"
                                                  style="display:none;">
                                                @csrf @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                    @if($itin->description)
                                        <p style="color:#64748b; font-size:.88rem; margin:0;">{{ $itin->description }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ──────────────────────── SECTION 4: BOOKINGS ──────────────────────── --}}
    <div class="agent-section">
        <div class="agent-section-header">
            <h5>
                <span class="icon-wrap"><i class="fas fa-ticket-alt"></i></span>
                {{ __('Bookings') }}
            </h5>
            <span style="background:#f1f5f9; border-radius:8px; padding:4px 12px; font-size:.8rem; color:#64748b; font-weight:700;">
                {{ $trip->bookings->count() }} {{ __('Total') }}
            </span>
        </div>
        <div style="overflow-x:auto;">
            @if($trip->bookings->isEmpty())
                <div style="text-align:center; padding:60px; color:#94a3b8;">
                    <i class="fas fa-ticket-alt" style="font-size:3rem; margin-bottom:12px; display:block;"></i>
                    <p style="font-weight:600;">{{ __('No bookings yet for this trip.') }}</p>
                </div>
            @else
                <table class="bk-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Passengers') }}</th>
                            <th>{{ __('Total Price') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trip->bookings as $i => $booking)
                        <tr>
                            <td style="font-weight:700; color:#1e293b;">{{ $i + 1 }}</td>
                            <td>{{ $booking->user->full_name ?? '-' }}</td>
                            <td>{{ $booking->passengers_count ?? $booking->number_of_passengers ?? 1 }}</td>
                            <td style="font-weight:700; color: var(--accent);">{{ number_format($booking->total_price ?? 0, 0) }} {{ __('SAR') }}</td>
                            <td>
                                @php $st = $booking->status ?? 'pending'; @endphp
                                <span class="badge-status {{ $st === 'confirmed' ? 'badge-confirmed' : ($st === 'cancelled' ? 'badge-cancelled' : 'badge-pending') }}">
                                    {{ __($st) }}
                                </span>
                            </td>
                            <td>{{ $booking->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('agent.bookings.show', $booking->id) }}"
                                   style="padding:6px 14px; background:#f1f5f9; color:#475569; border-radius:8px; font-size:.8rem; font-weight:700; text-decoration:none; transition:all .2s;"
                                   onmouseover="this.style.background='var(--accent)'; this.style.color='#fff';" onmouseout="this.style.background='#f1f5f9'; this.style.color='#475569';">
                                    <i class="fas fa-eye me-1"></i> {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>

{{-- ── Edit Itinerary Modal ── --}}
<div id="editItinModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,.55); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div style="background:#fff; border-radius:20px; width:100%; max-width:500px; margin:20px; overflow:hidden; box-shadow:0 24px 60px rgba(0,0,0,.18);">

        {{-- Modal Header --}}
        <div style="padding:18px 22px; background:linear-gradient(135deg,var(--accent-soft),rgba(232,83,46,.04)); border-bottom:1.5px solid rgba(232,83,46,.12); display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:10px;">
                <span style="width:34px;height:34px;border-radius:10px;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.8rem; flex-shrink:0;">
                    <i class="fas fa-pen"></i>
                </span>
                <h5 style="margin:0; font-weight:800; color:#1e293b; font-size:.95rem;">{{ __('Edit Day Details') }}</h5>
            </div>
            <button onclick="closeEditModal()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:1.3rem;line-height:1;">&times;</button>
        </div>

        {{-- Modal Body --}}
        <div style="padding:22px;">
            <form id="editItinForm">
                @csrf
                <input type="hidden" id="edit_id">
                <div class="pf">
                    <label><i class="fas fa-hashtag"></i>{{ __('Day Number') }}</label>
                    <input type="number" id="edit_day_number" name="day_number" class="pf-input" required>
                </div>
                <div class="pf">
                    <label><i class="fas fa-heading"></i>{{ __('Title') }}</label>
                    <input type="text" id="edit_title" name="title" class="pf-input" required>
                </div>
                <div class="pf">
                    <label><i class="fas fa-align-left"></i>{{ __('Description') }}</label>
                    <textarea id="edit_description" name="description" class="pf-input" rows="4"></textarea>
                </div>
                <div style="display:flex; gap:10px; margin-top:4px;">
                    <button type="button" onclick="closeEditModal()" style="flex:1; padding:12px; border:1.5px solid #e2e8f0; border-radius:11px; background:#f8fafc; color:#64748b; font-weight:700; cursor:pointer; font-size:.88rem; transition:all .2s;" onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn-add-day" style="flex:2; margin-top:0;">
                        <i class="fas fa-check"></i> {{ __('Update Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Dropzone.autoDiscover = false;

const myDropzone = new Dropzone('#trip-images-upload', {
    url: "{{ route('agent.trips.images.store', $trip->id) }}",
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    paramName: 'file',
    maxFilesize: 5,
    acceptedFiles: 'image/*',
    addRemoveLinks: false,
    dictDefaultMessage: '',
    init: function () {
        this.on('success', function (file, response) {
            if (response.success) {
                appendImage(response.id, response.url);
            }
            this.removeFile(file);
        });
        this.on('error', function (file, response) {
            const msg = response.error || '{{ __("Error while uploading the image") }}';
            Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: msg, confirmButtonColor: 'var(--accent)' });
            this.removeFile(file);
        });
    }
});

function appendImage(id, url) {
    const grid = document.getElementById('images-grid');
    const wrap = document.createElement('div');
    wrap.className = 'img-thumb-wrap';
    wrap.id = 'img-' + id;
    wrap.innerHTML = `<img src="${url}" alt=""><button class="del-btn" onclick="deleteImage(${id}, this)" title="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>`;
    grid.append(wrap);
}

function deleteImage(id, btn) {
    Swal.fire({
        title: '{{ __("Delete Photo?") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '{{ __("Yes, Delete") }}',
        cancelButtonText: '{{ __("Cancel") }}',
    }).then(result => {
        if (!result.isConfirmed) return;
        const url = "{{ route('agent.trips.images.destroy', ':id') }}".replace(':id', id);
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('img-' + id)?.remove();
                Swal.fire({ icon: 'success', title: '{{ __("Deleted!") }}', timer: 1500, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', text: data.message });
            }
        });
    });
}

// ─── Sortable Itinerary ───────────────────────────────────────
const itinList = document.getElementById('itinerary-list');
if (itinList) {
    Sortable.create(itinList, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'sortable-ghost',
        onEnd: function () {
            const order = [...document.querySelectorAll('.itinerary-card')].map(el => el.dataset.id);
            fetch("{{ route('agent.trips.itinerary.reorder') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ order })
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) console.error('Reorder failed');
            });
        }
    });
}

// ─── Edit Itinerary Modal ─────────────────────────────────────
function editItinerary(id, day, title, description) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_day_number').value = day;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_description').value = description;
    const modal = document.getElementById('editItinModal');
    modal.style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editItinModal').style.display = 'none';
}

document.getElementById('editItinForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const id = document.getElementById('edit_id').value;
    const formData = new FormData(this);
    fetch("{{ route('agent.trips.itinerary.update', ':id') }}".replace(':id', id), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': formData.get('_token')
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { location.reload(); }
        else { Swal.fire({ icon: 'error', text: data.message || '{{ __("Error") }}' }); }
    })
    .catch(() => { Swal.fire({ icon: 'error', text: '{{ __("Connection error") }}' }); });
});

// Close modal when clicking outside
document.getElementById('editItinModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

function deleteItinerary(id) {
    Swal.fire({
        title: '{{ __("Delete this day?") }}',
        text: '{{ __("This action cannot be undone!") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-trash"></i> {{ __("Yes, Delete") }}',
        cancelButtonText: '{{ __("Cancel") }}',
        reverseButtons: true,
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('del-itin-' + id).submit();
        }
    });
}
</script>
@endpush
