@extends('layouts.app')

@section('title', __('Flight Booking Requests'))
@section('page-title', __('Flight Booking Requests'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Booking Requests List') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="flightRequestsTable" class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>{{ __('Booking ID') }}</strong></th>
                                <th><strong>{{ __('Passenger') }}</strong></th>
                                <th><strong>{{ __('Flight') }}</strong></th>
                                <th><strong>{{ __('Route') }}</strong></th>
                                <th><strong>{{ __('Total Price') }}</strong></th>
                                <th><strong>{{ __('Status') }}</strong></th>
                                <th><strong>{{ __('Action') }}</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $requests = [
                                    ['id' => '#BK-9042', 'name' => 'John Wick', 'flight' => 'EK-201', 'route' => 'DXB -> LHR', 'price' => '$950', 'status' => 'Pending', 'status_class' => 'warning'],
                                    ['id' => '#BK-8850', 'name' => 'Tony Stark', 'flight' => 'EY-202', 'route' => 'AUH -> JFK', 'price' => '$1,200', 'status' => 'Confirmed', 'status_class' => 'success'],
                                    ['id' => '#BK-7741', 'name' => 'Bruce Wayne', 'flight' => 'QR-105', 'route' => 'DOH -> LHR', 'price' => '$800', 'status' => 'Cancelled', 'status_class' => 'danger'],
                                    ['id' => '#BK-6632', 'name' => 'Wanda Maximoff', 'flight' => 'SV-300', 'route' => 'JED -> RUH', 'price' => '$250', 'status' => 'Confirmed', 'status_class' => 'success'],
                                ];
                            @endphp

                            @foreach($requests as $req)
                            <tr>
                                <td><strong>{{ $req['id'] }}</strong></td>
                                <td>{{ $req['name'] }}</td>
                                <td>{{ $req['flight'] }}</td>
                                <td>{{ $req['route'] }}</td>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#flightRequestsTable').DataTable({
            language: {
                url: '{{ app()->getLocale() == 'ar' ? "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" : "" }}'
            }
        });
    });
</script>
@endpush
