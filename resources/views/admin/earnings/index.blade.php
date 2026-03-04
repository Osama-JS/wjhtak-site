@extends('layouts.app')

@section('title', __('Platform Earnings'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Platform Earnings') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Summary Cards --}}
    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Total Platform Earnings')"
                :value="number_format($totalEarnings, 2) . ' ' . __('SAR')"
                icon="fas fa-hand-holding-usd"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Target Month Earnings')"
                :value="number_format($currentMonthEarnings, 2) . ' ' . __('SAR')"
                icon="fas fa-calendar-check"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card h-100 shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-lg bg-{{ $growth >= 0 ? 'success' : 'danger' }}-light text-{{ $growth >= 0 ? 'success' : 'danger' }} rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-chart-line fs-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 text-uppercase fw-semibold">{{ __('Monthly Growth') }}</h6>
                            <h3 class="mb-0 fw-bold text-{{ $growth >= 0 ? 'success' : 'danger' }}">
                                {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Total Bookings')"
                :value="$totalBookingsCount"
                icon="fas fa-check-circle"
            />
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">{{ __('Earnings Trend') }}</h4>
                </div>
                <div class="card-body">
                    <canvas id="earningsTrendChart" height="280"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">{{ __('By Company') }}</h4>
                </div>
                <div class="card-body">
                    <canvas id="companyDistributionChart" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">{{ __('Filters') }}</h4>
        </div>
        <div class="card-body">
            <form id="filter-form" class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label">{{ __('Company') }}</label>
                    <select name="company_id" id="company_id" class="form-control select2">
                        <option value="">{{ __('All Companies') }}</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('From Date') }}</label>
                    <input type="date" name="date_from" id="date_from" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('To Date') }}</label>
                    <input type="date" name="date_to" id="date_to" class="form-control">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sync-alt"></i> {{ __('Apply Filters') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Earnings Table --}}
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('Earnings Details') }}</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="earnings-table" class="display" style="min-width: 845px">
                    <thead>
                        <tr>
                            <th>{{ __('Booking ID') }}</th>
                            <th>{{ __('Trip') }}</th>
                            <th>{{ __('Company') }}</th>
                            <th>{{ __('Total Price') }}</th>
                            <th>{{ __('Commission %') }}</th>
                            <th>{{ __('Platform Profit') }}</th>
                            <th>{{ __('Date') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // DataTable Initialization
        var table = $('#earnings-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('admin.earnings.data') }}",
                data: function(d) {
                    d.company_id = $('#company_id').val();
                    d.date_from = $('#date_from').val();
                    d.date_to = $('#date_to').val();
                }
            },
            columns: [
                { data: 'id' },
                { data: 'trip' },
                { data: 'company' },
                { data: 'total_price' },
                { data: 'commission_rate' },
                { data: 'profit' },
                { data: 'date' }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            },
            order: [[0, 'desc']]
        });

        // Filter Form Submit
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            table.ajax.reload();
        });

        // Earnings Trend Chart
        const trendCtx = document.getElementById('earningsTrendChart').getContext('2d');
        const monthlyData = @json($monthlyEarnings);

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: Object.keys(monthlyData),
                datasets: [{
                    label: '{{ __("Earnings") }}',
                    data: Object.values(monthlyData),
                    borderColor: '#4bc0c0',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4bc0c0'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return value + ' SAR'; }
                        }
                    }
                }
            }
        });

        // Company Distribution Doughnut Chart
        const distCtx = document.getElementById('companyDistributionChart').getContext('2d');
        const companyData = @json($companyStats);

        new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(companyData),
                datasets: [{
                    data: Object.values(companyData),
                    backgroundColor: [
                        '#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9f40'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 10 }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endsection
