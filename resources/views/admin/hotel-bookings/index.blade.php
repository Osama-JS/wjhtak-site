@extends('layouts.app')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Hotel Bookings') }}</a></li>
    </ol>
</div>
@endsection

@section('content')

    <div class="row my-2">
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Total Bookings')"
                :value="$stats['total']"
                icon="fas fa-hotel"
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
                :label="__('Pending / Draft')"
                :value="$stats['pending']"
                icon="fas fa-clock"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-success-light border-0">
                <div class="card-body text-center p-3">
                    <p class="mb-1 small fw-bold text-success">{{ __('Total Revenue') }}</p>
                    <h3 class="mb-0 fw-bold text-success">{{ number_format($stats['revenue'], 2) }} <span style="font-size:0.8rem;">SAR</span></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('Hotel Bookings (TBO)') }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.hotel-bookings.remote') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-search"></i> {{ __('Lookup in TBO') }}
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-file-csv"></i> {{ __('Export CSV') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Filters --}}
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Search hotel, TBO ID, or user...') }}" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" title="{{ __('Date From') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" title="{{ __('Date To') }}">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-secondary w-100">{{ __('Filter') }}</button>
                            <a href="{{ route('admin.hotel-bookings.index') }}" class="btn btn-light w-100">{{ __('Reset') }}</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Hotel') }}</th>
                                    <th>{{ __('Dates') }}</th>
                                    <th>{{ __('Total Price') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Internal Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td><strong>#{{ $booking->id }}</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="ms-2">
                                                    <h5 class="mb-0 fs-14">{{ $booking->user?->full_name ?? '---' }}</h5>
                                                    <span class="text-muted fs-12">{{ $booking->user?->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fs-14 fw-bold">{{ $booking->hotel_name }}</div>
                                            <div class="fs-12 text-muted">{{ $booking->city_name }}</div>
                                        </td>
                                        <td>
                                            <div class="fs-12">
                                                <span class="text-success"><i class="fas fa-sign-in-alt"></i> {{ $booking->check_in_date?->format('d/m/Y') }}</span><br>
                                                <span class="text-danger"><i class="fas fa-sign-out-alt"></i> {{ $booking->check_out_date?->format('d/m/Y') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ number_format($booking->total_price, 2) }} {{ $booking->currency }}</div>
                                        </td>
                                        <td>
                                            @php
                                                $stateColors = [
                                                    'awaiting_payment' => 'warning',
                                                    'preparing' => 'info',
                                                    'confirmed' => 'success',
                                                    'cancelled' => 'danger',
                                                ];
                                                $color = $stateColors[$booking->booking_state] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $color }}">{{ __($booking->booking_state) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge light badge-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('admin.hotel-bookings.show', $booking->id) }}" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-eye"></i></a>
                                                
                                                @if($booking->status !== 'cancelled')
                                                    <form action="{{ route('admin.hotel-bookings.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to cancel this booking?') }}')">
                                                        @csrf
                                                        <input type="hidden" name="reason" value="إلغاء إداري من لوحة التحكم">
                                                        <button type="submit" class="btn btn-danger shadow btn-xs sharp"><i class="fas fa-times"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center p-5">
                                            <i class="fas fa-hotel fa-3x text-light mb-3"></i>
                                            <p class="text-muted">{{ __('No hotel bookings found matching your criteria.') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
