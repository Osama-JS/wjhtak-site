@extends('layouts.app')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Trip Bookings') }}</a></li>
    </ol>
</div>
@endsection

@section('content')

    <div class="row my-2">
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Total Bookings')"
                :value="$stats['total']"
                icon="fas fa-calendar-check"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Awaiting Payment')"
                :value="$stats['awaiting_payment']"
                icon="fas fa-clock"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Preparing')"
                :value="$stats['preparing']"
                icon="fas fa-cogs"
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
                    <h4 class="card-title">{{ __('Trip Bookings') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="bookings-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Trip') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Tickets') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Detailed Deletion Modal --}}
    <div class="modal fade" id="deleteBookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white"><i class="fas fa-exclamation-triangle me-2"></i> {{ __('Warning: Extreme Action') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteBookingForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-trash-alt fa-4x text-danger animate__animated animate__pulse animate__infinite"></i>
                        </div>
                        <h4 class="text-center fw-bold mb-3">{{ __('Are you sure you want to delete booking #') }}<span id="displayBookingId"></span>?</h4>

                        <div class="alert alert-danger shadow-sm border-0">
                            <h6 class="fw-bold"><i class="fas fa-shield-alt"></i> {{ __('Risks & Impact') }}:</h6>
                            <ul class="mb-0 small" style="list-style-type: none; padding-left: 0;">
                                <li><i class="fas fa-check-circle me-1"></i> {{ __('Permanent loss of all passenger details and documents.') }}</li>
                                <li><i class="fas fa-check-circle me-1"></i> {{ __('Complete deletion of payment audit trails and history.') }}</li>
                                <li><i class="fas fa-check-circle me-1"></i> {{ __('This action cannot be undone under any circumstances.') }}</li>
                            </ul>
                        </div>

                        <div class="card bg-light border-0 mb-0">
                            <div class="card-body p-3">
                                <h6 class="fw-bold small mb-2"><i class="fas fa-info-circle"></i> {{ __('Safety Restrictions') }}:</h6>
                                <p class="small text-muted mb-0">
                                    {{ __('The system will block deletion if the booking is confirmed, has successful payments, or pending bank transfers.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel & Keep') }}</button>
                        <button type="submit" class="btn btn-danger px-4 shadow-sm fw-bold">
                            <i class="fas fa-trash me-1"></i> {{ __('Delete Permanently') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var table = $('#bookings-table').DataTable({
            processing: true,
            serverSide: false, // Client-side processing for now as per controller
            ajax: "{{ route('admin.trip-bookings.data') }}",
            columns: [
                { data: 'id' },
                { data: 'user' },
                { data: 'trip' },
                { data: 'company' },
                { data: 'price' },
                { data: 'tickets' },
                { data: 'status' },
                { data: 'created_at' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            },
            order: [[0, 'desc']] // Order by ID desc
        });

        // Handle opening the detailed delete modal
        $(document).on('click', '.open-delete-modal', function() {
            const id = $(this).data('id');
            const url = $(this).data('url');

            $('#displayBookingId').text(id);
            $('#deleteBookingForm').attr('action', url);
            $('#deleteBookingModal').modal('show');
        });

        // Initialize tooltips
        $('body').tooltip({selector: '[data-toggle="tooltip"]'});
    });
</script>
@endsection
