@extends('layouts.app')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.trip-bookings.index') }}">{{ __('Trip Bookings') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Booking Details') }} #{{ $booking->id }}</a></li>
    </ol>
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-9 col-lg-8">
        <div class="card">
            <div class="card-header border-bottom-0 pb-0">
                <div>
                     <h4 class="card-title">{{ __('Booking Information') }}</h4>
                     <p class="mb-0 text-muted">{{ __('Booking Date') }}: {{ $booking->booking_date->format('Y-m-d') }}</p>

                     @if($booking->status == 'cancelled' && $booking->cancellation_reason)
                     <div class="alert alert-danger mt-3 mb-0">
                         <strong><i class="fas fa-exclamation-circle me-1"></i> {{ __('Cancellation Reason') }}:</strong>
                         {{ $booking->cancellation_reason }}
                     </div>
                     @endif
                </div>
                <div class="d-flex align-items-center mt-3 mt-sm-0">
                    @if($booking->status == 'pending')
                        <span class="badge badge-warning me-2">{{ __('Pending') }}</span>
                    @elseif($booking->status == 'confirmed')
                        <span class="badge badge-success me-2">{{ __('Confirmed') }}</span>
                    @elseif($booking->status == 'cancelled')
                        <span class="badge badge-danger me-2">{{ __('Cancelled') }}</span>
                    @endif

                    <div class="dropdown ms-2">
                        <button class="btn btn-primary light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            {{ __('Actions') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            @if($booking->status != 'confirmed')
                            <form action="{{ route('admin.trip-bookings.update-status', $booking->id) }}" method="POST" class="d-inline confirm-action" data-confirm-message="{{ __('Confirm this booking?') }}">
                                @csrf
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="dropdown-item text-success"><i class="fas fa-check me-2"></i> {{ __('Confirm') }}</button>
                            </form>
                            @endif

                            @if($booking->status != 'cancelled')
                                <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-times me-2"></i> {{ __('Cancel') }}
                                </button>
                            @endif

                            @if($booking->status != 'confirmed')
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('admin.trip-bookings.destroy', $booking->id) }}" method="POST" class="d-inline confirm-action" data-confirm-message="{{ __('Delete this booking?') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i> {{ __('Delete') }}</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Trip Info -->
                <div class="row mb-5">
                    <div class="col-md-12">
                        <h5 class="text-primary mb-3"><i class="fas fa-plane me-2"></i> {{ __('Trip Details') }}</h5>
                        <div class="d-flex align-items-start border p-3 rounded">
                            @if($booking->trip && $booking->trip->image_url)
                                <img src="{{ $booking->trip->image_url }}" alt="Trip Image" class="rounded me-3" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                </div>
                            @endif
                            <div>
                                @if($booking->trip)
                                    <h4 class="mb-1"><a href="{{ route('trips.show', $booking->trip->id) }}" target="_blank">{{ $booking->trip->title }}</a></h4>
                                    <p class="mb-1 text-muted">{{ Str::limit($booking->trip->description, 150) }}</p>
                                    <div class="mt-2">
                                        <span class="badge badge-light text-dark border me-1"><i class="fas fa-map-marker-alt me-1"></i> {{ $booking->trip->toCity->name ?? '' }}, {{ $booking->trip->toCountry->name ?? '' }}</span>
                                        <span class="badge badge-light text-dark border"><i class="fas fa-clock me-1"></i> {{ $booking->trip->duration ?? '' }} {{ __('Days') }}</span>
                                    </div>
                                @else
                                    <h4 class="text-danger">{{ __('Trip Deleted') }}</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Passengers -->
                <div class="row">
                    <div class="col-12">
                         <h5 class="text-primary mb-3"><i class="fas fa-users me-2"></i> {{ __('Passengers List') }} <span class="badge badge-primary light badge-sm">{{ $booking->passengers->count() }}</span></h5>
                         <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Passport Number') }}</th>
                                        <th>{{ __('Passport Expiry') }}</th>
                                        <th>{{ __('Nationality') }}</th>
                                        <th>{{ __('Phone') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($booking->passengers as $index => $passenger)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $passenger->name }}</td>
                                        <td>{{ $passenger->passport_number ?? '---' }}</td>
                                        <td>{{ $passenger->passport_expiry ? $passenger->passport_expiry->format('Y-m-d') : '---' }}</td>
                                        <td>{{ $passenger->nationality ?? '---' }}</td>
                                        <td>{{ $passenger->phone ?? '---' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('No passenger details found.') }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                         </div>
                    </div>
                </div>

                <!-- Admin Notes -->
                @if($booking->notes)
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="text-muted mb-2">{{ __('Notes') }}</h5>
                        <div class="alert alert-light border">
                            {{ $booking->notes }}
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

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
                        <p class="text-muted small">{{ $booking->user->role_name ?? 'User' }}</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted"><i class="fas fa-envelope me-2"></i> {{ __('Email') }}</span>
                            <span class="text-end text-break">{{ $booking->user->email }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted"><i class="fas fa-phone me-2"></i> {{ __('Phone') }}</span>
                            <span>{{ $booking->user->phone ?? '---' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted"><i class="fas fa-globe me-2"></i> {{ __('Country') }}</span>
                            <span>{{ $booking->user->country ?? '---' }}</span>
                        </li>
                    </ul>
                    <div class="mt-3 text-center">
                         <a href="{{ route('admin.users.show', $booking->user->id) }}" class="btn btn-outline-primary btn-sm btn-block">{{ __('View Profile') }}</a>
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        {{ __('User account deleted.') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Info -->
        <div class="card h-auto">
            <div class="card-header border-bottom">
                 <h5 class="card-title mb-0">{{ __('Payment Summary') }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">{{ __('Tickets Count') }}</span>
                    <span class="fw-bold">{{ $booking->tickets_count }}</span>
                </div>
                 <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">{{ __('Price per Ticket') }}</span>
                    <!-- Approximated since we only store total -->
                    @if($booking->tickets_count > 0)
                        <span class="fw-bold">{{ number_format($booking->total_price / $booking->tickets_count, 2) }} {{ __('SAR') }}</span>
                    @else
                        <span>-</span>
                    @endif
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="h5 mb-0">{{ __('Total') }}</span>
                    <span class="h4 text-primary mb-0">{{ number_format($booking->total_price, 2) }} <small>{{ __('SAR') }}</small></span>
                </div>

                @if($booking->payment)
                    <div class="alert alert-secondary py-2 px-3 mb-0" style="font-size: 0.85rem">
                        <div class="d-flex justify-content-between mb-1">
                            <span><strong>{{ __('Method') }}:</strong></span>
                            <span>{{ strtoupper(str_replace('_', ' ', $booking->payment->payment_gateway)) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><strong>{{ __('Txn ID') }}:</strong></span>
                            <span class="text-break">{{ $booking->payment->transaction_id ?? 'N/A' }}</span>
                        </div>
                    </div>
                @endif

                <div class="mt-4">
                    <div class="d-grid gap-2">
                        @if($booking->status == 'confirmed')
                             <button class="btn btn-success light" disabled><i class="fas fa-check-circle me-2"></i> {{ __('Paid') }}</button>
                        @elseif($booking->status == 'cancelled')
                             <button class="btn btn-danger light" disabled><i class="fas fa-times-circle me-2"></i> {{ __('Cancelled') }}</button>
                        @else
                             <button class="btn btn-warning light" disabled><i class="fas fa-clock me-2"></i> {{ __('Unpaid') }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Bank Transfer Card --}}
        @if($latestBankTransfer)
            <div class="card h-auto mb-4 border-{{ $latestBankTransfer->status === 'approved' ? 'success' : ($latestBankTransfer->status === 'rejected' ? 'danger' : 'warning') }}">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-university me-2"></i>{{ __('Bank Transfer Details') }}
                    </h5>
                    @php
                        $badgeClass = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'][$latestBankTransfer->status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $badgeClass }}">{{ strtoupper(__($latestBankTransfer->status)) }}</span>
                </div>
                <div class="card-body">
                    {{-- Receipt Image --}}
                    @if($latestBankTransfer->receipt_image)
                        <div class="text-center mb-3">
                            <a href="{{ asset('storage/' . $latestBankTransfer->receipt_image) }}" target="_blank">
                                <img src="{{ asset('storage/' . $latestBankTransfer->receipt_image) }}"
                                     class="img-fluid rounded border"
                                     style="max-height: 200px; object-fit: contain;"
                                     alt="{{ __('Receipt') }}">
                            </a>
                            <p class="text-muted small mt-1">{{ __('Click to view full image') }}</p>
                        </div>
                    @endif

                    {{-- Transfer Details --}}
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">{{ __('Sender Name') }}</span>
                            <strong>{{ $latestBankTransfer->sender_name }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">{{ __('Receipt / Ref No.') }}</span>
                            <strong>{{ $latestBankTransfer->receipt_number ?? '—' }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">{{ __('Submitted On') }}</span>
                            <strong>{{ $latestBankTransfer->created_at->format('Y-m-d H:i') }}</strong>
                        </li>
                        @if($latestBankTransfer->reviewed_at)
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">{{ __('Reviewed On') }}</span>
                            <strong>{{ $latestBankTransfer->reviewed_at->format('Y-m-d H:i') }}</strong>
                        </li>
                        @endif
                        @if($latestBankTransfer->notes)
                        <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">{{ __('Customer Notes') }}</span>
                            <em>{{ $latestBankTransfer->notes }}</em>
                        </li>
                        @endif
                        @if($latestBankTransfer->status === 'rejected' && $latestBankTransfer->rejection_reason)
                        <li class="list-group-item px-0">
                            <span class="text-danger d-block mb-1"><i class="fas fa-times-circle me-1"></i>{{ __('Rejection Reason') }}</span>
                            <em>{{ $latestBankTransfer->rejection_reason }}</em>
                        </li>
                        @endif
                    </ul>

                    {{-- Action Button --}}
                    <div class="mt-3">
                        <a href="{{ route('admin.bank-transfers.show', $latestBankTransfer->id) }}"
                           class="btn btn-sm btn-outline-primary btn-block">
                            <i class="fas fa-external-link-alt me-1"></i>
                            {{ $latestBankTransfer->status === 'pending' ? __('Review Transfer') : __('View Transfer Details') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Ticket Upload Section for Confirmed/Completed Bookings --}}

        @if(in_array($booking->status, ['confirmed', 'completed']))
            <div class="card h-auto mb-4 border-primary">
                <div class="card-header border-bottom bg-primary-light">
                     <h5 class="card-title mb-0 text-primary"><i class="fas fa-ticket-alt me-2"></i>{{ __('Booking Tickets') }}</h5>
                </div>
                <div class="card-body">
                    @if($booking->ticket_url)
                        <div class="alert alert-success d-flex flex-column align-items-center mb-4">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <strong>{{ __('Ticket Uploaded') }}</strong>
                            <div class="d-flex gap-2 justify-content-center mt-2">
                                <a href="{{ $booking->ticket_url }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-eye me-1"></i> {{ __('View Ticket') }}
                                </a>
                                <form action="{{ route('admin.trip-bookings.send-ticket', $booking->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-paper-plane me-1"></i> {{ __('Send to Customer') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.trip-bookings.upload-ticket', $booking->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label font-w600">{{ __('Upload New Ticket') }} <small class="text-muted">(PDF, JPG, PNG)</small></label>
                            <input type="file" name="ticket_file" class="form-control" accept=".pdf, .jpg, .jpeg, .png" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="send_email" value="1" id="sendEmailCheck" checked>
                            <label class="form-check-label text-muted" for="sendEmailCheck">
                                {{ __('Notify customer and send ticket via email') }}
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-upload me-1"></i> {{ __('Upload & Save') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Cancel Modal --}}
@if($booking->status != 'cancelled')
<div class="modal fade" id="cancelModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Cancel Booking') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.trip-bookings.update-status', $booking->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="cancelled">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ __('Are you sure you want to cancel this booking?') }}
                    </div>
                    <div class="form-group">
                        <label class="form-label mb-2">{{ __('Reason for cancellation') }} <span class="text-danger">*</span></label>
                        <textarea name="cancellation_reason" class="form-control" rows="4" required placeholder="{{ __('Explain why the booking was cancelled...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Cancel Booking') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle Action Confirmations
        $(document).on('submit', '.confirm-action', function(e) {
            e.preventDefault();
            var form = this;
            var message = $(this).data('confirm-message') || "{{ __('Are you sure?') }}";

            WJHTAKAdmin.confirm(message, function() {
                form.submit();
            });
        });
    });
</script>
@endsection
