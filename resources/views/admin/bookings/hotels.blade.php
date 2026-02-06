@extends('layouts.app')

@section('title', __('Hotel Bookings'))
@section('page-title', __('Hotel Bookings'))

@section('content')
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Total Hotels')"
            :value="850"
            icon="fas fa-hotel"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Active Bookings')"
            :value="124"
            icon="fas fa-bookmark"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Featured')"
            :value="45"
            icon="fas fa-star"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Cancelled')"
            :value="8"
            icon="fas fa-times-circle"
        />
    </div>
</div>

<div class="row">
    <div class="col-xl-4 col-lg-5">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Hotel Locations') }}</h4>
            </div>
            <div class="card-body p-0">
                <div id="hotelMap" style="height: 600px; border-radius: 0 0 15px 15px;"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-8 col-lg-7">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Featured Hotels') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <img src="{{ asset('images/big/img1.jpg') }}" class="card-img-top" alt="Hotel 1" style="height: 180px; object-fit: cover; border-radius: 15px 15px 0 0;">
                            <div class="card-body">
                                <h5 class="card-title">Burj Al Arab</h5>
                                <p class="card-text text-muted mb-1"><i class="fa fa-map-marker-alt text-danger me-2"></i>Dubai, UAE</p>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <span class="ms-2">5.0</span>
                                </div>
                                <h4 class="text-primary font-w600">$1,200 <small class="text-muted">/ {{ __('Night') }}</small></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <img src="{{ asset('images/big/img2.jpg') }}" class="card-img-top" alt="Hotel 2" style="height: 180px; object-fit: cover; border-radius: 15px 15px 0 0;">
                            <div class="card-body">
                                <h5 class="card-title">The Ritz-Carlton</h5>
                                <p class="card-text text-muted mb-1"><i class="fa fa-map-marker-alt text-danger me-2"></i>Riyadh, Saudi Arabia</p>
                                <div class="star-rating mb-2">
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <span class="ms-2">4.9</span>
                                </div>
                                <h4 class="text-primary font-w600">$850 <small class="text-muted">/ {{ __('Night') }}</small></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <h4 class="card-title mb-3">{{ __('Recent Reservations') }}</h4>
                    <table class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>{{ __('Guest') }}</strong></th>
                                <th><strong>{{ __('Hotel') }}</strong></th>
                                <th><strong>{{ __('Check In') }}</strong></th>
                                <th><strong>{{ __('Check Out') }}</strong></th>
                                <th><strong>{{ __('Status') }}</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Alice Johnson</td>
                                <td>Burj Al Arab</td>
                                <td>2026-01-15</td>
                                <td>2026-01-20</td>
                                <td><span class="badge light badge-success">{{ __('Booked') }}</span></td>
                            </tr>
                            <tr>
                                <td>Bob Smith</td>
                                <td>Ritz-Carlton</td>
                                <td>2026-01-22</td>
                                <td>2026-01-25</td>
                                <td><span class="badge light badge-info">{{ __('Stay In') }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJbhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    $(document).ready(function() {
        var map = L.map('hotelMap').setView([24.7136, 46.6753], 5);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
        }).addTo(map);

        var hotels = [
            { pos: [25.1412, 55.1852], name: 'Burj Al Arab', city: 'Dubai' },
            { pos: [24.6644, 46.6119], name: 'The Ritz-Carlton', city: 'Riyadh' },
            { pos: [21.4225, 39.8262], name: 'Abraj Al-Bait', city: 'Makkah' }
        ];

        hotels.forEach(function(h) {
            L.marker(h.pos).addTo(map).bindPopup('<b>' + h.name + '</b><br>' + h.city);
        });
    });
</script>
@endpush

@endsection

