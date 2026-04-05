@extends('layouts.app')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.hotel-bookings.index') }}">{{ __('Hotel Bookings') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('TBO Remote Lookup') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="card-title">{{ __('TBO Remote Booking Lookup') }}</h4>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.hotel-bookings.index') }}" class="btn btn-dark btn-sm shadow-sm px-3">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Local List') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- Date Filter Form --}}
                <div class="bg-light p-4 rounded mb-5 border">
                    <form action="{{ route('admin.hotel-bookings.remote') }}" method="GET">
                        <div class="row align-items-end g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">{{ __('From Date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="from_date" class="form-control" value="{{ $fromDate ?? '' }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">{{ __('To Date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="to_date" class="form-control" value="{{ $toDate ?? '' }}" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary d-block w-100 shadow-sm">
                                    <i class="fas fa-sync-alt me-1"></i> {{ __('Fetch from TBO') }}
                                </button>
                            </div>
                            <div class="col-md-3">
                                <p class="small text-muted mb-0">
                                    <i class="fas fa-info-circle me-1"></i>
                                    {{ __('Search for bookings made directly on TBO within this range.') }}
                                </p>
                            </div>
                        </div>
                    </form>
                </div>

                @if($error)
                    <div class="alert alert-danger shadow-sm">
                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> {{ __('API Error Output') }}</h5>
                        <p class="mb-0">{{ $error }}</p>
                    </div>
                @endif

                @if(request()->has('from_date') && !$error)
                    <div class="alert alert-info py-2 shadow-sm border-0 mb-4 d-flex align-items-center">
                        <i class="fas fa-search me-2 fs-5"></i>
                        <span>{{ __('Found') }} <strong>{{ count($remoteBookings) }}</strong> {{ __('bookings on TBO between') }} <strong>{{ $fromDate }}</strong> {{ __('and') }} <strong>{{ $toDate }}</strong></span>
                    </div>
                @endif

                {{-- Results Table --}}
                <div class="table-responsive">
                    <table class="table table-hover border">
                        <thead class="bg-light">
                            <tr>
                                <th><strong>{{ __('TBO Booking ID') }}</strong></th>
                                <th><strong>{{ __('Hotel Details') }}</strong></th>
                                <th><strong>{{ __('City') }}</strong></th>
                                <th><strong>{{ __('Dates') }}</strong></th>
                                <th class="text-end"><strong>{{ __('Total Price') }}</strong></th>
                                <th><strong>{{ __('TBO Status') }}</strong></th>
                                <th><strong>{{ __('Local Status') }}</strong></th>
                                <th class="text-center"><strong>{{ __('Actions') }}</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($remoteBookings as $b)
                                <tr>
                                    <td>
                                        <span class="badge badge-dark">#{{ $b['BookingId'] ?? '---' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $b['HotelName'] ?? '---' }}</div>
                                        <small class="text-muted">{{ $b['ConfirmationNo'] ?? '' }}</small>
                                    </td>
                                    <td>{{ $b['CityName'] ?? '---' }}</td>
                                    <td>
                                        <div class="badge badge-light text-dark fw-bold border">
                                            {{ $b['CheckInDate'] ?? '---' }} <i class="fas fa-arrow-right mx-1 small"></i> {{ $b['CheckOutDate'] ?? '---' }}
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-primary">
                                        {{ number_format($b['Price']['Total'] ?? 0, 2) }} {{ $b['Price']['Currency'] ?? 'SAR' }}
                                    </td>
                                    <td>
                                        @php
                                            $status = $b['Status'] ?? 'Unknown';
                                            $color = ($status == 'Confirmed') ? 'success' : (($status == 'Cancelled') ? 'danger' : 'warning');
                                        @endphp
                                        <span class="badge badge-{{ $color }} shadow-sm">{{ $status }}</span>
                                    </td>
                                    <td>
                                        @if(isset($localRefs[$b['BookingId']]))
                                            <span class="text-success small fw-bold">
                                                <i class="fas fa-check-circle me-1"></i> {{ __('Synced Locally') }}
                                            </span>
                                        @else
                                            <span class="text-warning small fw-bold">
                                                <i class="fas fa-times-circle me-1"></i> {{ __('Missing Locally') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            @if(isset($localRefs[$b['BookingId']]))
                                                <a href="{{ route('admin.hotel-bookings.show', $localRefs[$b['BookingId']]) }}" class="btn btn-primary shadow-sm btn-xs sharp" title="{{ __('View Local Details') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @else
                                                <button class="btn btn-warning shadow-sm btn-xs sharp disabled" title="{{ __('Import Not Supported Yet') }}">
                                                    <i class="fas fa-cloud-download-alt"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        @if(request()->has('from_date'))
                                            <div class="text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                                <p>{{ __('No remote bookings found for this range.') }}</p>
                                            </div>
                                        @else
                                            <div class="text-muted">
                                                <i class="fas fa-calendar-alt fa-3x mb-3 opacity-25"></i>
                                                <p>{{ __('Enter a date range and click "Fetch from TBO" to see all bookings.') }}</p>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
