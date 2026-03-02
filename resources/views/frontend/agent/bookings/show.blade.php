@extends('frontend.agent.layouts.agent-layout')

@section('title', __('Booking Details') . ' #' . $booking->id)
@section('page-title', __('Booking Details'))

@section('content')
@push('styles')
<style>
    :root {
        --accent-color: #e8532e;
        --accent-soft: rgba(232, 83, 46, 0.08);
        --accent-hover: #d14424;
        --border-color: #f1f5f9;
        --title-color: #1e293b;
        --text-muted: #64748b;
    }

    .booking-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Header Section */
    .booking-top-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.9rem;
    }
    .btn-back:hover { color: var(--accent-color); }

    /* Main Grid */
    .booking-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
    }
    @media (max-width: 992px) { .booking-grid { grid-template-columns: 1fr; } }

    /* Card Styling */
    .detail-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 15px rgba(0,0,0,.02);
        margin-bottom: 25px;
        overflow: hidden;
    }

    .card-header-premium {
        padding: 20px 25px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fff;
    }
    .card-header-premium i { color: var(--accent-color); font-size: 1.2rem; }
    .card-header-premium h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--title-color); }

    .card-body-premium { padding: 25px; }

    /* Trip Hero Section */
    .trip-summary-box {
        display: flex;
        gap: 20px;
        align-items: center;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 25px;
    }
    .trip-thumb {
        width: 100px;
        height: 100px;
        border-radius: 15px;
        object-fit: cover;
        box-shadow: 0 4px 10px rgba(0,0,0,.05);
    }
    .trip-info h2 { font-size: 1.3rem; font-weight: 900; color: var(--title-color); margin-bottom: 8px; }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 16px;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-received { background: #eff6ff; color: #2563eb; }
    .status-preparing { background: #fff7ed; color: #ea580c; }
    .status-confirmed { background: #f0fdf4; color: #16a34a; }
    .status-tickets_sent { background: #f5f3ff; color: #7c3aed; }
    .status-cancelled { background: #fef2f2; color: #dc2626; }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    }
    @media (max-width: 576px) { .info-grid { grid-template-columns: 1fr; } }

    .info-block label {
        display: block;
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .info-block .val { font-size: 1rem; font-weight: 700; color: var(--title-color); }
    .info-block .val i { margin-inline-end: 5px; color: var(--accent-color); }

    /* Passenger Section */
    .passenger-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid var(--border-color);
    }
    .passenger-row:last-child { border-bottom: none; }
    .passenger-meta { display: flex; align-items: center; gap: 15px; }
    .passenger-icon {
        width: 44px; height: 44px; border-radius: 12px;
        background: var(--accent-soft); color: var(--accent-color);
        display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
    }
    .p-name { font-weight: 800; color: var(--title-color); display: block; }
    .p-phone { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; }
    .p-doc { font-size: 0.8rem; color: var(--text-muted); font-weight: 700; background: #f8fafc; padding: 4px 10px; border-radius: 6px; }

    /* Pricing Section */
    .pricing-footer {
        margin-top: 25px;
        padding: 20px;
        background: #f8fafc;
        border-radius: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .total-label { font-weight: 800; color: var(--text-muted); }
    .total-val { font-size: 1.5rem; font-weight: 900; color: var(--accent-color); }

    /* Upload Box */
    .upload-box {
        background: #fff;
        border: 2px dashed #e2e8f0;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        transition: all 0.2s;
    }
    .upload-box:hover { border-color: var(--accent-color); background: var(--accent-soft); }
    .file-input-wrapper { position: relative; margin-top: 15px; }
    .btn-premium-upload {
        width: 100%;
        padding: 12px;
        background: var(--accent-color);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
    }
    .btn-premium-upload:hover { background: var(--accent-hover); box-shadow: 0 5px 15px rgba(232, 83, 46, 0.3); }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-body-premium { padding: 20px; }
        .trip-summary-box { flex-direction: column; text-align: center; }
        .passenger-row { flex-direction: column; align-items: flex-start; gap: 10px; }
        .passenger-meta { width: 100%; }
        .pricing-footer { flex-direction: column; gap: 10px; }
    }
</style>
@endpush

<div class="booking-container">
    <div class="booking-top-nav">
        <a href="{{ route('agent.bookings.index') }}" class="btn-back">
            <i class="fas fa-arrow-right"></i> {{ __('Back to Bookings List') }}
        </a>
        <div style="font-weight: 800; color: var(--text-muted);">
            {{ __('Booking ID') }}: <span style="color: var(--title-color);">#{{ $booking->id }}</span>
        </div>
    </div>

    <div class="booking-grid">
        <div class="booking-left">
            {{-- Main Info --}}
            <div class="detail-card">
                <div class="card-header-premium">
                    <i class="fas fa-info-circle"></i>
                    <h3>{{ __('Booking Overview') }}</h3>
                </div>
                <div class="card-body-premium">
                    <div class="trip-summary-box">
                        @php $image = $booking->trip->images->first(); @endphp
                        @if($image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="trip-thumb" alt="">
                        @else
                            <div class="trip-thumb" style="background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#cbd5e1; font-size:2rem;">
                                <i class="fas fa-plane"></i>
                            </div>
                        @endif
                        <div class="trip-info">
                            <h2>{{ $booking->trip->title }}</h2>
                            <div class="status-pill status-{{ $booking->booking_state }}">
                                <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                {{ __(ucfirst(str_replace('_', ' ', $booking->booking_state))) }}
                            </div>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div class="info-block">
                            <label>{{ __('Primary Customer') }}</label>
                            <div class="val"><i class="fas fa-user-circle"></i> {{ $booking->user->full_name }}</div>
                        </div>
                        <div class="info-block">
                            <label>{{ __('Email Address') }}</label>
                            <div class="val"><i class="fas fa-envelope"></i> {{ $booking->user->email }}</div>
                        </div>
                        <div class="info-block">
                            <label>{{ __('Destination') }}</label>
                            <div class="val"><i class="fas fa-map-marker-alt"></i> {{ $booking->trip->toCountry->name ?? '-' }} ({{ $booking->trip->toCity->name ?? '-' }})</div>
                        </div>
                        <div class="info-block">
                            <label>{{ __('Booking Date') }}</label>
                            <div class="val"><i class="fas fa-calendar-alt"></i> {{ $booking->created_at->format('d M, Y • H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Passengers Info --}}
            <div class="detail-card">
                <div class="card-header-premium">
                    <i class="fas fa-users"></i>
                    <h3>{{ __('Passenger Details') }} ({{ $booking->tickets_count }})</h3>
                </div>
                <div class="card-body-premium">
                    @forelse($booking->passengers as $passenger)
                        <div class="passenger-row">
                            <div class="passenger-meta">
                                <div class="passenger-icon"><i class="fas fa-user"></i></div>
                                <div>
                                    <span class="p-name">{{ $passenger->name }}</span>
                                    <span class="p-phone">{{ $passenger->phone ?? __('No phone') }}</span>
                                </div>
                            </div>
                            <div style="text-align: end;">
                                <div class="p-doc">{{ $passenger->nationality ?? __('Global') }}</div>
                                <div style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted); margin-top: 4px;">
                                    <i class="fas fa-passport"></i> {{ $passenger->passport_number ?? '-' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; color: var(--text-muted); padding: 40px 0;">
                            <i class="fas fa-user-slash" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                            {{ __('No detailed passenger information provided.') }}
                        </div>
                    @endforelse

                    <div class="pricing-footer">
                        <span class="total-label">{{ __('Total Booking Amount') }}</span>
                        <span class="total-val">{{ number_format($booking->total_price, 0) }} <small style="font-size: 0.6em;">{{ __('SAR') }}</small></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="booking-right">
            {{-- Logistics & Tickets --}}
            <div class="detail-card">
                <div class="card-header-premium">
                    <i class="fas fa-ticket-alt"></i>
                    <h3>{{ __('Tickets & Logistics') }}</h3>
                </div>
                <div class="card-body-premium">
                    <div class="info-block mb-4">
                        <label>{{ __('Payment Status') }}</label>
                        <div class="val" style="color: #16a34a;"><i class="fas fa-check-circle"></i> {{ __('Paid in Full') }}</div>
                    </div>

                    <div style="background: #f8fafc; border-radius: 15px; padding: 20px; border: 1.5px solid #f1f5f9;">
                        <h6 style="font-weight: 900; color: var(--title-color); margin-bottom: 15px; font-size: 0.9rem;">{{ __('Ticket Management') }}</h6>

                        @if($booking->tickets)
                            <div style="background: #f0fdf4; border: 1px solid #bbfcce; border-radius: 12px; padding: 15px; margin-bottom: 15px;">
                                <div style="display: flex; align-items: center; gap: 10px; color: #166534; font-size: 0.85rem; font-weight: 700;">
                                    <i class="fas fa-check-circle"></i> {{ __('Ticket is available') }}
                                </div>
                                <a href="{{ asset('storage/' . $booking->tickets) }}" target="_blank" class="btn btn-sm w-100 mt-3 fw-bold" style="background:#fff; border: 1px solid #ddd; border-radius: 8px;">
                                    <i class="fas fa-external-link-alt"></i> {{ __('View / Download') }}
                                </a>
                            </div>
                        @else
                            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 15px; margin-bottom: 15px;">
                                <div style="display: flex; align-items: center; gap: 10px; color: #991b1b; font-size: 0.85rem; font-weight: 700;">
                                    <i class="fas fa-exclamation-triangle"></i> {{ __('Needs Ticket Upload') }}
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('agent.bookings.tickets', $booking->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="upload-box">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 1.5rem; color: var(--accent-color); margin-bottom: 8px;"></i>
                                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">
                                    {{ __('Click or drag to upload ticket file') }}
                                </div>
                                <input type="file" name="tickets_file" style="opacity: 0; position: absolute; inset: 0; cursor: pointer;" required onchange="this.nextElementSibling.innerText = this.files[0].name">
                                <div id="fileName" style="font-size: 0.7rem; margin-top: 5px; color: #2563eb;"></div>
                            </div>
                            <button type="submit" class="btn-premium-upload mt-4">
                                <i class="fas fa-check-circle"></i> {{ __('Upload Ticket') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <button class="btn btn-light w-100 fw-bold border" style="border-radius: 15px; padding: 12px; color: var(--text-muted);" onclick="window.print()">
                    <i class="fas fa-print"></i> {{ __('Print Booking Details') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
