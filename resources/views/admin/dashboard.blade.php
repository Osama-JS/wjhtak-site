@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<!-- Removed redundant container-fluid as it is in the layout -->
<div class="row">
    <!-- Welcome Header -->
    <div class="col-12 mb-4">
        <div class="card welcome-card border-0 shadow-sm overflow-hidden" style="border-radius: 15px; background: linear-gradient(135deg, #135846 0%, #1a7a62 100%);">
            <div class="card-body p-4 text-white">
                <div class="row align-items-center">
                    <div class="col-md-9">
                        <h3 class="fw-bold mb-1">{{ $greeting }}, {{ $adminName }}! 👋</h3>
                        <p class="mb-0 opacity-80 small">{{ __('Welcome to your travel hub. Here is an overview for today.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top KPIs -->
    <div class="col-xl-3 col-sm-6 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-body py-3 px-4">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-box bg-success-light text-success p-2 rounded-circle me-3">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h6 class="text-muted small mb-0">{{ __('Revenue') }}</h6>
                </div>
                <h4 class="fw-bold mb-0 text-success">{{ number_format($stats['revenue_total'], 2) }} SAR</h4>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-body py-3 px-4">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-box bg-primary-light text-primary p-2 rounded-circle me-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h6 class="text-muted small mb-0">{{ __('Bookings') }}</h6>
                </div>
                <h4 class="fw-bold mb-0 text-primary">{{ $stats['bookings_total'] }} <span class="badge bg-warning-light text-warning fs-12 ms-2">{{ $stats['bookings_pending'] }} {{ __('Pending') }}</span></h4>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-body py-3 px-4">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-box bg-info-light text-info p-2 rounded-circle me-3">
                        <i class="fas fa-plane"></i>
                    </div>
                    <h6 class="text-muted small mb-0">{{ __('Active Trips') }}</h6>
                </div>
                <h4 class="fw-bold mb-0 text-info">{{ $stats['trips_active'] }} <span class="badge bg-danger-light text-danger fs-12 ms-2">{{ $stats['trips_expired'] }} {{ __('Expired') }}</span></h4>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-body py-3 px-4">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-box bg-secondary-light text-secondary p-2 rounded-circle me-3" style="background-color:rgba(108,117,125,0.1);">
                        <i class="fas fa-users"></i>
                    </div>
                    <h6 class="text-muted small mb-0">{{ __('Total Users') }}</h6>
                </div>
                <h4 class="fw-bold mb-0 text-dark">{{ $stats['users_total'] }} <span class="text-success small ms-2">+{{ $stats['users_new_today'] }} {{ __('Today') }}</span></h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart Column -->
    <div class="col-xl-7 col-lg-12 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">{{ __('User Growth Analysis') }}</h5>
            </div>
            <div class="card-body pt-0 px-4" style="height: 300px;">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Activities Table Column -->
    <div class="col-xl-5 col-lg-12 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">{{ __('Recent Bookings') }}</h5>
                <a href="{{ route('admin.trip-bookings.index') }}" class="text-primary small fw-semibold">{{ __('See All') }}</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <tbody>
                            @forelse($recentBookings as $booking)
                                <tr class="border-0">
                                    <td class="ps-4 border-0 py-3">
                                        <div class="avatar-xs bg-light rounded text-primary text-center p-2 mb-0" style="width:32px; height:32px; line-height:16px;">
                                            <i class="fas fa-ticket-alt small"></i>
                                        </div>
                                    </td>
                                    <td class="border-0 py-3">
                                        <h6 class="mb-0 fw-bold small text-dark">{{ $booking->user->name ?? __('Guest') }}</h6>
                                        <p class="text-muted small mb-0" style="font-size: 11px;">{{ \Illuminate\Support\Str::limit($booking->trip->title ?? '', 30) }}</p>
                                    </td>
                                    <td class="text-end pe-4 border-0 py-3">
                                        <span class="text-dark fw-bold small">{{ number_format($booking->total_price, 2) }}</span><br>
                                        <span class="text-muted small" style="font-size: 10px;">{{ $booking->created_at->diffForHumans(null, true) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted small">{{ __('No recent bookings.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- User List Column -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">{{ __('New Members') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <tbody>
                            @foreach($latestUsers as $user)
                            <tr class="border-0">
                                <td class="ps-4 border-0 py-2">
                                    <div class="bg-primary-light text-primary rounded-circle text-center p-2 mb-0" style="width:32px; height:32px; line-height:16px;">
                                        <i class="fas fa-user small"></i>
                                    </div>
                                </td>
                                <td class="border-0 py-2">
                                    <h6 class="mb-0 fw-semibold small text-dark">{{ $user->name }}</h6>
                                    <p class="text-muted mb-0 small" style="font-size: 10px;">{{ \Illuminate\Support\Str::limit($user->email, 25) }}</p>
                                </td>
                                <td class="text-end pe-4 border-0 py-2">
                                  {{--<span class="badge badge-xs badge-outline-dark">{{ $user->created_at->format('d/m/Y') }}</span>--}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Companies Promo Column -->
    <div class="col-lg-6 mb-4">
        <div class="card text-white border-0 shadow-none h-100" style="border-radius: 15px; background: rgba(19, 88, 70, 0.04); border: 1px dashed #135846 !important;">
            <div class="card-body p-4 d-flex flex-column justify-content-center text-center">
                <i class="fas fa-hands-helping fa-3x text-primary mb-3 opacity-50"></i>
                <h5 class="fw-bold text-dark mb-2">{{ __('Companies Overview') }}</h5>
                <h2 class="fw-bold text-primary mb-1">{{ $stats['companies_count'] }}</h2>
                <p class="text-muted small mb-3">{{ __('Registered travel partners contributing to our catalog.') }}</p>
                <div class="d-flex justify-content-center">
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-primary btn-sm px-4 shadow-sm" style="background:#135846; border:none; border-radius: 8px;">
                        {{ __('Manage Partners') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-primary-light { background-color: rgba(19, 88, 70, 0.1); }
    .bg-success-light { background-color: rgba(40, 167, 69, 0.1); }
    .bg-warning-light { background-color: rgba(255, 193, 7, 0.1); }
    .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
    .bg-info-light { background-color: rgba(23, 162, 184, 0.1); }
    .text-success { color: #28a745 !important; }
    .text-primary { color: #135846 !important; }
    .badge-outline-dark { border: 1px solid #dee2e6; color: #6c757d; font-weight: 500; }
    .welcome-card { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .fs-12 { font-size: 12px; }
</style>
@endpush

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: '{{ __("New Users") }}',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#135846',
                    backgroundColor: 'rgba(19, 88, 70, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#135846',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#135846' }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f8f9fa' } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endsection
