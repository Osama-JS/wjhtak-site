@extends('layouts.app')

@section('title', __('Hotels List & Map'))
@section('page-title', __('Hotels List & Map'))

@section('content')
<div class="row">
    <!-- Map Section -->
    <div class="col-xl-12 mb-4">
        <div class="card overflow-hidden">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Hotel Geographic Distribution') }}</h4>
            </div>
            <div class="card-body p-0">
                <div id="hotelMapBig" style="height: 400px; width: 100%;"></div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Registered Hotels') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="hotelsTable" class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>{{ __('ID') }}</strong></th>
                                <th><strong>{{ __('Hotel Name') }}</strong></th>
                                <th><strong>{{ __('Location') }}</strong></th>
                                <th><strong>{{ __('Rating') }}</strong></th>
                                <th><strong>{{ __('Price/Night') }}</strong></th>
                                <th><strong>{{ __('Action') }}</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $hotels = [
                                    ['id' => 'HTL-001', 'name' => 'Burj Al Arab', 'loc' => 'Dubai, UAE', 'lat' => 25.1412, 'lng' => 55.1852, 'rating' => 5, 'price' => '$1200'],
                                    ['id' => 'HTL-002', 'name' => 'The Ritz-Carlton', 'loc' => 'Riyadh, KSA', 'lat' => 24.6644, 'lng' => 46.6119, 'rating' => 4.9, 'price' => '$850'],
                                    ['id' => 'HTL-003', 'name' => 'Rosewood Jeddah', 'loc' => 'Jeddah, KSA', 'lat' => 21.5433, 'lng' => 39.1728, 'rating' => 4.8, 'price' => '$500'],
                                    ['id' => 'HTL-004', 'name' => 'The Savoy', 'loc' => 'London, UK', 'lat' => 51.5104, 'lng' => -0.1202, 'rating' => 5, 'price' => '$700'],
                                ];
                            @endphp

                            @foreach($hotels as $hotel)
                            <tr>
                                <td><strong>{{ $hotel['id'] }}</strong></td>
                                <td>{{ $hotel['name'] }}</td>
                                <td>{{ $hotel['loc'] }}</td>
                                <td>
                                    <div class="star-rating">
                                        @for($i=0; $i<5; $i++)
                                            <i class="fa fa-star {{ $i < floor($hotel['rating']) ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                        <span class="ms-1">{{ $hotel['rating'] }}</span>
                                    </div>
                                </td>
                                <td><span class="text-primary font-w600">{{ $hotel['price'] }}</span></td>
                                <td>
                                    <div class="d-flex">
                                        <button onclick="zoomToHotel({{ $hotel['lat'] }}, {{ $hotel['lng'] }})" class="btn btn-info shadow btn-xs sharp me-1"><i class="fas fa-map-marker-alt"></i></button>
                                        <button class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></button>
                                        <button class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJbhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"  crossorigin=""/> -->
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    var map;
    $(document).ready(function() {
        map = L.map('hotelMapBig').setView([24.7136, 46.6753], 4);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
        }).addTo(map);

        setTimeout(function(){ map.invalidateSize();}, 500);

        var hotels = @json($hotels);
        hotels.forEach(function(h) {
            L.marker([h.lat, h.lng]).addTo(map).bindPopup('<b>' + h.name + '</b><br>' + h.loc);
        });

        $('#hotelsTable').DataTable({
            language: {
                url: '{{ app()->getLocale() == 'ar' ? "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" : "" }}'
            }
        });
    });

    function zoomToHotel(lat, lng) {
        map.setView([lat, lng], 15);
        $('html, body').animate({
            scrollTop: $("#hotelMapBig").offset().top - 100
        }, 500);
    }
</script>
@endpush

@endsection

