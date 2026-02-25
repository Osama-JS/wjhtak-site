@extends('layouts.app')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.bank-transfers.index') }}">{{ __('Bank Transfers Review') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Transfer Details') }} #{{ $transfer->id }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Receipt Information') }}</h4>
                <span class="badge badge-{{ $transfer->status === 'pending' ? 'warning' : ($transfer->status === 'approved' ? 'success' : 'danger') }}">
                    {{ strtoupper(__($transfer->status)) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-12 text-center">
                        <h5 class="mb-3">{{ __('Uploaded Receipt') }}</h5>
                        <div class="receipt-preview" style="border: 2px dashed #eee; padding: 10px; border-radius: 10px;">
                            @if(Str::endsWith($transfer->receipt_image, '.pdf'))
                                <embed src="{{ asset('storage/' . $transfer->receipt_image) }}" type="application/pdf" width="100%" height="600px" />
                            @else
                                <a href="{{ asset('storage/' . $transfer->receipt_image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $transfer->receipt_image) }}" class="img-fluid rounded" style="max-height: 800px;" alt="Receipt">
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <h6><strong>{{ __('Sender Name') }}:</strong></h6>
                        <p>{{ $transfer->sender_name }}</p>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <h6><strong>{{ __('Receipt / Ref Number') }}:</strong></h6>
                        <p>{{ $transfer->receipt_number ?? '—' }}</p>
                    </div>
                    <div class="col-sm-12">
                        <h6><strong>{{ __('User Notes') }}:</strong></h6>
                        <p class="text-muted">{{ $transfer->notes ?? __('No notes provided.') }}</p>
                    </div>
                </div>

                @if($transfer->status === 'rejected')
                    <div class="alert alert-danger mt-3">
                        <strong>{{ __('Rejection Reason') }}:</strong><br>
                        {{ $transfer->rejection_reason }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Booking Summary') }}</h4>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>{{ __('Booking ID') }}</span>
                        <strong>#{{ $transfer->trip_booking_id }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>{{ __('Customer') }}</span>
                        <strong>{{ $transfer->user->full_name }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>{{ __('Trip') }}</span>
                        <strong class="text-end">{{ $transfer->booking->trip->title ?? '—' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>{{ __('Total Amount') }}</span>
                        <strong class="text-success">{{ number_format($transfer->booking->total_price, 2) }} {{ __('SAR') }}</strong>
                    </li>
                </ul>

                @if($transfer->status === 'pending')
                    <div class="mt-4">
                        <form id="approveTransferForm"
                              action="{{ route('admin.bank-transfers.approve', $transfer->id) }}"
                              method="POST"
                              class="mb-2"
                              onsubmit="return handleApproveSubmit(event, this)">
                            @csrf
                            <button type="submit" id="approveBtn" class="btn btn-success btn-block no-loading">
                                <i class="fas fa-check-circle me-1"></i> {{ __('Approve Transfer') }}
                            </button>
                        </form>

                        <button type="button" class="btn btn-danger btn-block" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times-circle me-1"></i> {{ __('Reject Transfer') }}
                        </button>
                    </div>
                @endif

                <a href="{{ route('admin.trip-bookings.show', $transfer->trip_booking_id) }}" class="btn btn-outline-primary btn-block mt-3">
                    <i class="fas fa-calendar-check me-1"></i> {{ __('View Booking Details') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Reject Bank Transfer') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.bank-transfers.reject', $transfer->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">{{ __('Reason for rejection') }}</label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="{{ __('Explain why the transfer was rejected...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Reject Now') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Reject modal submit loading state
    $('#rejectModal form').on('submit', function() {
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> {{ __('Processing...') }}');
    });
});

function handleApproveSubmit(e, form) {
    // Show confirmation first
    if (!confirm('{{ __('Are you sure you want to approve this bank transfer? This will confirm the booking.') }}')) {
        return false; // Stop form submit
    }
    // Confirmed — show loading state on the button
    var btn = form.querySelector('button[type="submit"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> {{ __('Processing...') }}';
    }
    return true; // Allow form to submit
}
</script>
@endsection
