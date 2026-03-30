@extends('layouts.app')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.hotel-bookings.index') }}">{{ __('Hotel Bookings') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Booking Details') }} #{{ $booking->id }}</a></li>
    </ol>
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-9 col-lg-8">
        <div class="card">
            <div class="card-header border-bottom-0 pb-0 d-flex justify-content-between align-items-start">
                <div>
                     <h4 class="card-title">{{ __('Hotel Booking Information') }}</h4>
                     <p class="mb-0 text-muted">{{ __('Booked on') }}: {{ $booking->created_at->format('Y-m-d H:i') }}</p>

                     @if($booking->status == 'cancelled' && $booking->cancellation_reason)
                     <div class="alert alert-danger mt-3 mb-0">
                         <strong><i class="fas fa-exclamation-circle me-1"></i> {{ __('Cancellation Reason') }}:</strong>
                         {{ $booking->cancellation_reason }}
                     </div>
                     @endif
                </div>
                <div class="d-flex align-items-center mt-3 mt-sm-0">
                    @php
                        $stateClasses = [
                            'awaiting_payment' => 'warning',
                            'preparing' => 'info',
                            'confirmed' => 'success',
                            'cancelled' => 'danger',
                        ];
                        $stateClass = $stateClasses[$booking->booking_state] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $stateClass }} me-2">{{ __($booking->booking_state) }}</span>

                    @if($booking->status !== 'cancelled')
                        <div class="dropdown ms-2">
                            <button class="btn btn-primary light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                {{ __('Actions') }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <form action="{{ route('admin.hotel-bookings.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to cancel this booking?') }}')">
                                    @csrf
                                    <input type="hidden" name="reason" value="إلغاء إداري من صفحة التفاصيل">
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-times me-2"></i> {{ __('Cancel Booking') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Hotel Info -->
                <div class="row mb-5">
                    <div class="col-md-12">
                        <h5 class="text-primary mb-3"><i class="fas fa-hotel me-2"></i> {{ __('Hotel Details') }}</h5>
                        <div class="d-flex align-items-start border p-3 rounded">
                            @if($booking->hotel_image)
                                <img src="{{ $booking->hotel_image }}" alt="Hotel Image" class="rounded me-3" style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                    <i class="fas fa-hotel fa-3x text-muted"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h4 class="mb-1">{{ $booking->hotel_name }} @if($booking->star_rating) <span class="text-warning small" style="font-size: 0.8rem;">{{ str_repeat('★', $booking->star_rating) }}</span> @endif</h4>
                                <p class="mb-2 text-muted"><i class="fas fa-map-marker-alt me-1"></i> {{ $booking->hotel_address ?? $booking->city_name }}</p>
                                
                                <div class="row g-2 mt-2">
                                    <div class="col-sm-6">
                                        <div class="p-2 border rounded bg-light">
                                            <small class="d-block text-muted mb-1">{{ __('Check-in') }}</small>
                                            <strong class="text-success">{{ $booking->check_in_date?->format('D, d M Y') }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="p-2 border rounded bg-light">
                                            <small class="d-block text-muted mb-1">{{ __('Check-out') }}</small>
                                            <strong class="text-danger">{{ $booking->check_out_date?->format('D, d M Y') }}</strong>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <span class="badge badge-light text-dark border me-1"><i class="fas fa-door-open me-1"></i> {{ $booking->room_type_name }}</span>
                                    <span class="badge badge-light text-dark border me-1"><i class="fas fa-moon me-1"></i> {{ $booking->nights_count }} {{ __('Nights') }}</span>
                                    <span class="badge badge-light text-dark border"><i class="fas fa-users me-1"></i> {{ $booking->adults }} {{ __('Adults') }}, {{ $booking->children }} {{ __('Children') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guests -->
                <div class="row">
                    <div class="col-12">
                         <h5 class="text-primary mb-3"><i class="fas fa-users me-2"></i> {{ __('Guests List') }} <span class="badge badge-primary light badge-sm">{{ $booking->guests->count() }}</span></h5>
                         <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Full Name') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Passport / ID') }}</th>
                                        <th>{{ __('Nationality') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($booking->guests as $index => $guest)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $guest->title }}</td>
                                        <td>
                                            {{ $guest->first_name }} {{ $guest->last_name }}
                                            @if($guest->is_lead) <span class="badge badge-xs badge-secondary ms-1">{{ __('Lead') }}</span> @endif
                                        </td>
                                        <td>{{ ucfirst($guest->type) }}</td>
                                        <td>{{ $guest->passport_number ?? '---' }}</td>
                                        <td>{{ $guest->nationality ?? '---' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('No guest details found.') }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                         </div>
                    </div>
                </div>

                <!-- Cancellation Policy JSON -->
                @if($booking->cancellation_policy)
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="text-danger mb-2"><i class="fas fa-info-circle me-1"></i> {{ __('TBO Cancellation Policy') }}</h5>
                        <div class="alert alert-light border small">
                            @if(is_array($booking->cancellation_policy))
                                <ul class="mb-0">
                                    @foreach($booking->cancellation_policy as $policy)
                                        @if(is_array($policy))
                                            <li>{{ $policy['ChargeType'] ?? '' }}: {{ $policy['Charge'] ?? '0' }} {{ $booking->currency }} ({{ __('From') }}: {{ $policy['FromDate'] ?? '' }})</li>
                                        @else
                                            <li>{{ $policy }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @else
                                {{ $booking->cancellation_policy }}
                            @endif
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-xl-3 col-lg-4">
        <!-- Customer Info -->
        <div class="card h-auto mb-4">
            <div class="card-header border-bottom">
                 <h5 class="card-title mb-0">{{ __('Customer') }}</h5>
            </div>
            <div class="card-body">
                @if($booking->user)
                    <div class="text-center mb-4">
                        <img src="{{ $booking->user->profile_photo_url }}" class="rounded-circle mb-2" width="80" height="80" alt="User">
                        <h5 class="mb-0">{{ $booking->user->full_name }}</h5>
                        <p class="text-muted small">{{ $booking->user->email }}</p>
                    </div>
                    <div class="mt-3 text-center">
                         <a href="{{ route('admin.users.show', $booking->user->id) }}" class="btn btn-outline-primary btn-sm btn-block w-100">{{ __('View Profile') }}</a>
                    </div>
                @else
                    <div class="alert alert-warning mb-0">{{ __('User account deleted.') }}</div>
                @endif
            </div>
        </div>

        <!-- Payment Info -->
        <div class="card h-auto mb-4">
            <div class="card-header border-bottom">
                 <h5 class="card-title mb-0">{{ __('Financial Summary') }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">{{ __('Total Price') }}</span>
                    <span class="h4 text-primary mb-0">{{ number_format($booking->total_price, 2) }} <small>{{ $booking->currency }}</small></span>
                </div>
                
                @if($booking->payment)
                    <hr>
                    <div class="alert alert-secondary py-2 px-3 mb-0" style="font-size: 0.85rem">
                        <div class="d-flex justify-content-between mb-1">
                            <span><strong>{{ __('Gateway') }}:</strong></span>
                            <span>{{ strtoupper($booking->payment->payment_gateway) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span><strong>{{ __('Method') }}:</strong></span>
                            <span>{{ __($booking->payment->payment_method) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><strong>{{ __('Status') }}:</strong></span>
                            <span class="badge badge-xs badge-success">{{ __($booking->payment->status) }}</span>
                        </div>
                    </div>
                @endif

                <div class="mt-4">
                    <div class="d-grid gap-2">
                        @if($booking->status == 'confirmed')
                             <button class="btn btn-success light" disabled><i class="fas fa-check-circle me-2"></i> {{ __('Confirmed') }}</button>
                        @elseif($booking->status == 'cancelled')
                             <button class="btn btn-danger light" disabled><i class="fas fa-times-circle me-2"></i> {{ __('Cancelled') }}</button>
                        @else
                             <button class="btn btn-warning light" disabled><i class="fas fa-clock me-2"></i> {{ $booking->status }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Debug Info (TBO Refs) -->
        <div class="card h-auto">
            <div class="card-header border-bottom">
                 <h5 class="card-title mb-0">{{ __('Technical Info') }}</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">{{ __('TBO Booking ID') }}</span>
                        <strong>{{ $booking->tbo_booking_id ?? '---' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">{{ __('Hotel Code') }}</span>
                        <strong>{{ $booking->hotel_code }}</strong>
                    </li>
                    <li class="list-group-item">
                        <span class="text-muted d-block mb-1">{{ __('Result Token') }}</span>
                        <code class="text-break">{{ Str::limit($booking->tbo_result_token, 50) }}</code>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <!-- History Logs -->
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title"><i class="fas fa-history me-2"></i>{{ __('Booking History Log') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Action') }}</th>
                                <th>{{ __('Description') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->histories()->with('user')->latest()->get() as $log)
                            <tr>
                                <td style="white-space:nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $log->user ? $log->user->full_name : __('System') }}</td>
                                <td><span class="badge badge-light text-dark">{{ $log->action }}</span></td>
                                <td>{{ $log->description }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">{{ __('No history logs available.') }}</td>
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
