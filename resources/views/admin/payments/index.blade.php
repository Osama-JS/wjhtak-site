@extends('layouts.app')

@section('title', __('Payment Transactions'))

@section('content')
<div class="container-fluid">
    <div class="row page-titles mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Payments') }}</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h4 class="card-title fw-bold">{{ __('Payment Transactions') }}</h4>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Search by Trans ID or Booking #') }}" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="gateway" class="form-select">
                                <option value="">{{ __('All Gateways') }}</option>
                                <option value="hyperpay" {{ request('gateway') == 'hyperpay' ? 'selected' : '' }}>HyperPay</option>
                                <option value="tap" {{ request('gateway') == 'tap' ? 'selected' : '' }}>Tap</option>
                                <option value="tabby" {{ request('gateway') == 'tabby' ? 'selected' : '' }}>Tabby</option>
                                <option value="tamara" {{ request('gateway') == 'tamara' ? 'selected' : '' }}>Tamara</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('Booking #') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Trip') }}</th>
                                    <th>{{ __('Gateway') }}</th>
                                    <th>{{ __('Method') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr>
                                    <td><strong>#{{ $payment->trip_booking_id }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="ms-0">
                                                <p class="mb-0 fw-bold">{{ $payment->booking->user->full_name ?? __('Guest') }}</p>
                                                <small class="text-muted">{{ $payment->booking->user->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 150px;">
                                            {{ $payment->booking->trip->title_ar ?? ($payment->booking->trip->title ?? '---') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-outline-dark">{{ ucfirst($payment->payment_gateway) }}</span>
                                    </td>
                                    <td>{{ strtoupper($payment->payment_method) }}</td>
                                    <td><strong>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</strong></td>
                                    <td>
                                        @if($payment->status == 'paid')
                                            <span class="badge bg-success text-white">{{ __('Paid') }}</span>
                                        @elseif($payment->status == 'pending')
                                            <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                        @else
                                            <span class="badge bg-danger text-white">{{ __('Failed') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white view-json" data-json="{{ json_encode($payment->raw_response) }}">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        @if($payment->invoice_path)
                                            <a href="{{ asset('storage/' . $payment->invoice_path) }}" target="_blank" class="btn btn-sm btn-secondary">
                                                <i class="fa fa-file-invoice"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">{{ __('No transactions found.') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Raw Response -->
<div class="modal fade" id="jsonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Gateway Response Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="jsonContent" class="bg-light p-3 rounded" style="max-height: 500px; overflow-y: auto;"></pre>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.view-json').on('click', function() {
            const json = $(this).data('json');
            $('#jsonContent').text(JSON.stringify(json, null, 2));
            const modal = new bootstrap.Modal(document.getElementById('jsonModal'));
            modal.show();
        });
    });
</script>
@endpush
@endsection
