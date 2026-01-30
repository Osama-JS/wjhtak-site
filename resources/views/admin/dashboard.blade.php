@extends('layouts.app')

@section('title', __('Dashboard'))
@section('page-title', __('Admin Dashboard'))

@section('content')
<div class="container-fluid">
    {{-- Statistics Cards --}}
    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-widget-one">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon d-inline-block me-3 bg-primary-light">
                            <i class="fas fa-users text-primary fs-24"></i>
                        </div>
                        <div class="stat-content d-inline-block">
                            <div class="stat-text text-muted">{{ __('Total Users') }}</div>
                            <div class="stat-digit fs-24 font-w600">{{ $stats['users'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-widget-one">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon d-inline-block me-3 bg-success-light">
                            <i class="fas fa-globe text-success fs-24"></i>
                        </div>
                        <div class="stat-content d-inline-block">
                            <div class="stat-text text-muted">{{ __('Countries') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-widget-one">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon d-inline-block me-3 bg-warning-light">
                            <i class="fas fa-city text-warning fs-24"></i>
                        </div>
                        <div class="stat-content d-inline-block">
                            <div class="stat-text text-muted">{{ __('Cities') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-widget-one">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon d-inline-block me-3 bg-info-light">
                            <i class="fas fa-images text-info fs-24"></i>
                        </div>
                        <div class="stat-content d-inline-block">
                            <div class="stat-text text-muted">{{ __('Active Banners') }}</div>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Users Registration Chart --}}
        <div class="col-xl-8 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Users Registration Trend') }} ({{ date('Y') }})</h4>
                </div>
                <div class="card-body">
                    <canvas id="usersChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Latest Users --}}
        <div class="col-xl-4 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Latest Registered Users') }}</h4>
                </div>
                <div class="card-body">
                    <div class="widget-media">
                        <ul class="timeline">
                            @foreach($latestUsers as $user)
                            <li>
                                <div class="timeline-panel">
                                    <div class="media me-2">
                                        <img alt="image" width="50" src="{{ $user->profile_photo_url }}" class="rounded-circle">
                                    </div>
                                    <div class="media-body">
                                        <h5 class="mb-1">{{ $user->name }}</h5>
                                        <small class="d-block text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if($user->status === 'active')
                                        <span class="badge badge-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Include Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('usersChart').getContext('2d');
        const usersChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: '{{ __("Registered Users") }}',
                    data: @json($chartData),
                    borderColor: 'rgba(58, 122, 254, 1)',
                    backgroundColor: 'rgba(58, 122, 254, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgba(58, 122, 254, 1)',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
