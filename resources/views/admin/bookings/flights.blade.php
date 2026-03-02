@extends('layouts.app')

@section('title', __('Flight Bookings'))
@section('page-title', __('Flight Bookings'))

@section('content')
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Total Flights')"
            :value="2540"
            icon="fas fa-plane"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Active Routes')"
            :value="185"
            icon="fas fa-route"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Airlines')"
            :value="42"
            icon="fas fa-building"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Cancelled')"
            :value="12"
            icon="fas fa-times-circle"
        />
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="fs-20 text-black">{{ __('Flight Routes & Live Tracking') }}</h4>
            </div>
            <div class="card-body">
                <div id="flightMap" style="height: 450px; border-radius: 15px;"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Recent Flight Bookings') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th style="width:80px;"><strong>#</strong></th>
                                <th><strong>{{ __('Passenger') }}</strong></th>
                                <th><strong>{{ __('Flight') }}</strong></th>
                                <th><strong>{{ __('Route') }}</strong></th>
                                <th><strong>{{ __('Date') }}</strong></th>
                                <th><strong>{{ __('Status') }}</strong></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>01</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('images/avatar/1.png') }}" class="rounded-lg me-2" width="24" alt=""/>
                                        <span class="w-space-no">John Doe</span>
                                    </div>
                                </td>
                                <td>EK-202</td>
                                <td>DXB -> LHR</td>
                                <td>15 Jan 2026</td>
                                <td><span class="badge light badge-success">{{ __('Confirmed') }}</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-success light sharp" data-bs-toggle="dropdown">
                                            <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-menu-item" href="#">{{ __('Edit') }}</a>
                                            <a class="dropdown-menu-item" href="#">{{ __('Delete') }}</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>02</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('images/avatar/1.png') }}" class="rounded-lg me-2" width="24" alt=""/>
                                        <span class="w-space-no">Ahmed Ali</span>
                                    </div>
                                </td>
                                <td>QR-105</td>
                                <td>DOH -> JFK</td>
                                <td>18 Jan 2026</td>
                                <td><span class="badge light badge-warning">{{ __('Pending') }}</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-warning light sharp" data-bs-toggle="dropdown">
                                            <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-menu-item" href="#">Edit</a>
                                            <a class="dropdown-menu-item" href="#">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>03</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('images/avatar/1.png') }}" class="rounded-lg me-2" width="24" alt=""/>
                                        <span class="w-space-no">Sarah Connor</span>
                                    </div>
                                </td>
                                <td>SV-300</td>
                                <td>JED -> RUH</td>
                                <td>20 Jan 2026</td>
                                <td><span class="badge light badge-danger">{{ __('Cancelled') }}</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-danger light sharp" data-bs-toggle="dropdown">
                                            <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-menu-item" href="#">Edit</a>
                                            <a class="dropdown-menu-item" href="#">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJbhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-container {
        background: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    $(document).ready(function() {
        var map = L.map('flightMap').setView([20, 0], 2);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
        }).addTo(map);

        // Sample Flight Paths
        var flights = [
            { from: [25.276987, 55.296249], to: [51.507351, -0.127758], color: '#ff5e5e', label: 'DXB -> LHR' },
            { from: [25.2854, 51.5310], to: [40.7128, -74.0060], color: '#3b4bd3', label: 'DOH -> JFK' },
            { from: [21.5433, 39.1728], to: [24.7136, 46.6753], color: '#27ae60', label: 'JED -> RUH' }
        ];

        flights.forEach(function(f) {
            var polyline = L.polyline([f.from, f.to], {
                color: f.color,
                weight: 3,
                opacity: 0.6,
                dashArray: '10, 10'
            }).addTo(map);

            L.marker(f.from).addTo(map).bindPopup('Origin: ' + f.label.split(' -> ')[0]);
            L.marker(f.to).addTo(map).bindPopup('Destination: ' + f.label.split(' -> ')[1]);
        });
    });
</script>
@endpush
