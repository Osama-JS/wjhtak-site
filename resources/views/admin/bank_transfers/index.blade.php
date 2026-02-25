@extends('layouts.app')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Bank Transfers Review') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Bank Transfers Review') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="bank-transfers-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Trip') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Sender') }}</th>
                                    <th>{{ __('Receipt No') }}</th>
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
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var table = $('#bank-transfers-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.bank-transfers.data') }}",
            columns: [
                { data: 'id' },
                { data: 'user.full_name', name: 'user.first_name' },
                { data: 'booking.trip.title', name: 'booking.trip.title', defaultContent: '—' },
                { data: 'amount', searchable: false },
                { data: 'sender_name' },
                { data: 'receipt_number' },
                { data: 'status' },
                { data: 'created_at' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            },
            order: [[0, 'desc']]
        });
    });
</script>
@endsection
