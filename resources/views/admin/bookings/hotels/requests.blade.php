@extends('layouts.app')

@section('title', __('Hotel Booking Requests'))
@section('page-title', __('Hotel Booking Requests'))

@section('content')
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Total Requests')"
            :value="$stats['total']"
            icon="fas fa-hotel"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Pending')"
            :value="$stats['pending']"
            icon="fas fa-clock"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Confirmed')"
            :value="$stats['confirmed']"
            icon="fas fa-check-circle"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Cancelled')"
            :value="$stats['cancelled']"
            icon="fas fa-times-circle"
        />
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Recent Hotel Reservations') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="hotelRequestsTable" class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>{{ __('Booking ID') }}</strong></th>
                                <th><strong>{{ __('Guest') }}</strong></th>
                                <th><strong>{{ __('Hotel') }}</strong></th>
                                <th><strong>{{ __('Room Type') }}</strong></th>
                                <th><strong>{{ __('Check In') }}</strong></th>
                                <th><strong>{{ __('Total Price') }}</strong></th>
                                <th><strong>{{ __('Status') }}</strong></th>
                                <th><strong>{{ __('Action') }}</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $hotel_reqs = [
                                    ['id' => '#HTR-1200', 'name' => 'John Doe', 'hotel' => 'Burj Al Arab', 'room' => 'Suite', 'checkin' => '2026-01-20', 'price' => '$2,400', 'status' => 'Confirmed', 'status_class' => 'success'],
                                    ['id' => '#HTR-1550', 'name' => 'Alice Smith', 'hotel' => 'Ritz-Carlton', 'room' => 'Deluxe', 'checkin' => '2026-01-22', 'price' => '$1,700', 'status' => 'Pending', 'status_class' => 'warning'],
                                    ['id' => '#HTR-1880', 'name' => 'Bob Brown', 'hotel' => 'Rosewood Jeddah', 'room' => 'Standard', 'checkin' => '2026-01-25', 'price' => '$500', 'status' => 'Cancelled', 'status_class' => 'danger'],
                                ];
                            @endphp

                            @foreach($hotel_reqs as $req)
                            <tr>
                                <td><strong>{{ $req['id'] }}</strong></td>
                                <td>{{ $req['name'] }}</td>
                                <td>{{ $req['hotel'] }}</td>
                                <td>{{ $req['room'] }}</td>
                                <td>{{ $req['checkin'] }}</td>
                                <td>{{ $req['price'] }}</td>
                                <td><span class="badge light badge-{{ $req['status_class'] }}">{{ __($req['status']) }}</span></td>
                                <td>
                                    <div class="d-flex">
                                        <button class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-success shadow btn-xs sharp me-1"><i class="fas fa-check"></i></button>
                                        <button class="btn btn-danger shadow btn-xs sharp"><i class="fas fa-times"></i></button>
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
@push('scripts')
<script>
    $(document).ready(function() {
        $('#hotelRequestsTable').DataTable({
            language: {
                url: '{{ app()->getLocale() == 'ar' ? "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" : "" }}'
                // url: "{{ app()->getLocale() == 'ar' ? asset('build/ar.json') : '' }}"
            }
        });
    });
</script>
@endpush
@endsection


