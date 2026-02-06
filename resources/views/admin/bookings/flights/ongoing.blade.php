@extends('layouts.app')

@section('title', __('Tickets & Ongoing Flights'))
@section('page-title', __('Tickets & Ongoing Flights'))

@section('content')
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Active Flights')"
            :value="$stats['active_flights']"
            icon="fas fa-plane-departure"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('In Air')"
            :value="$stats['in_air']"
            icon="fas fa-cloud"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('On Ground')"
            :value="$stats['on_ground']"
            icon="fas fa-plane-arrival"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Delayed')"
            :value="$stats['delayed']"
            icon="fas fa-exclamation-circle"
        />
    </div>
</div>

<div class="row">
    <!-- Live Tracking Map -->
    <div class="col-xl-12">
        <div class="card overflow-hidden">
            <div class="card-header border-0 pb-0">
                <h4 class="fs-20 text-black">{{ __('Live Flight Tracking') }}</h4>
            </div>
            <div class="card-body p-0">
                <div id="liveFlightMap" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>

    <!-- Ongoing Flights List -->
    <div class="col-xl-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Issued Tickets & Active Flights') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ongoingFlightsTable" class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>{{ __('Ticket No.') }}</strong></th>
                                <th><strong>{{ __('Passenger') }}</strong></th>
                                <th><strong>{{ __('Flight') }}</strong></th>
                                <th><strong>{{ __('Airline') }}</strong></th>
                                <th><strong>{{ __('Progress') }}</strong></th>
                                <th><strong>{{ __('Estimated Arrival') }}</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ongoing = [
                                    ['ticket' => 'TKT-10023', 'name' => 'Alice Margret', 'flight' => 'EK-201', 'airline' => 'Emirates', 'progress' => '65', 'eta' => '12:45 PM'],
                                    ['ticket' => 'TKT-10884', 'name' => 'Oliver Queen', 'flight' => 'QR-105', 'airline' => 'Qatar Airways', 'progress' => '30', 'eta' => '03:30 PM'],
                                    ['ticket' => 'TKT-20331', 'name' => 'Lex Luthor', 'flight' => 'SV-300', 'airline' => 'Saudia', 'progress' => '90', 'eta' => '11:15 AM'],
                                ];
                            @endphp

                            @foreach($ongoing as $flight)
                            <tr>
                                <td><strong>{{ $flight['ticket'] }}</strong></td>
                                <td>{{ $flight['name'] }}</td>
                                <td>{{ $flight['flight'] }}</td>
                                <td>{{ $flight['airline'] }}</td>
                                <td>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-primary progress-animated" style="width: {{ $flight['progress'] }}%; height:10px;" role="progressbar">
                                            <span class="sr-only">{{ $flight['progress'] }}% Complete</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $flight['progress'] }}% {{ __('In Air') }}</small>
                                </td>
                                <td>{{ $flight['eta'] }}</td>
                            </tr>
                            @endforeach
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
    #liveFlightMap {
        background: #f0f2f5;
        z-index: 1;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    $(document).ready(function() {
        var map = L.map('liveFlightMap').setView([30, 0], 2);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
        }).addTo(map);

        // Map invalidate size fix for overflow/rendering issues
        setTimeout(function(){ map.invalidateSize();}, 500);

        var activeFlights = [
            { pos: [45.0, 15.0], flight: 'EK-201', angle: 45 },
            { pos: [20.0, 45.0], flight: 'QR-105', angle: 120 },
            { pos: [22.0, 30.0], flight: 'SV-300', angle: 90 }
        ];

        activeFlights.forEach(function(f) {
            var icon = L.divIcon({
                html: '<i class="fa fa-plane text-primary fs-20" style="transform: rotate(' + f.angle + 'deg);"></i>',
                className: 'plane-icon',
                iconSize: [20, 20]
            });

            L.marker(f.pos, {icon: icon}).addTo(map).bindPopup('<b>Flight: ' + f.flight + '</b><br>Altitude: 35,000ft');
        });
    });
</script>
@endpush
