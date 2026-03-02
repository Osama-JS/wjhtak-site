@extends('frontend.agent.layouts.agent-layout')

@section('title', __('Agent Dashboard'))
@section('page-title', __('Agent Dashboard'))

@push('styles')
<style>
/* ─── Welcome Banner ─── */
.welcome-banner {
    background: #ffffff;
    border-radius: 16px;
    padding: 32px 36px;
    margin-bottom: 28px;
    color: #1a2537;
    position: relative;
    overflow: hidden;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
}

.welcome-banner::after {
    content: '';
    position: absolute;
    top: -20px;
    inset-inline-end: -20px;
    width: 120px;
    height: 120px;
    background: #f8fafc;
    border-radius: 50%;
    z-index: 0;
}

.welcome-banner h2, .welcome-banner p {
    position: relative;
    z-index: 1;
}

.welcome-banner h2 {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0 0 8px;
    color: #0f172a;
}

.welcome-banner p {
    font-size: .95rem;
    color: #64748b;
    margin: 0;
    font-weight: 500;
}

/* ─── Stats Cards ─── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 28px;
}

@media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 520px)  { .stats-grid { grid-template-columns: 1fr; } }

.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 22px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    transition: transform .2s, box-shadow .2s;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,.1);
}

.stat-card .stat-icon {
    width: 54px;
    height: 54px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}

.stat-icon-blue   { background: #eff6ff; color: #2563eb; }
.stat-icon-green  { background: #f0fdf4; color: #16a34a; }
.stat-icon-orange { background: #fff7ed; color: #ea580c; }
.stat-icon-red    { background: #fef2f2; color: #dc2626; }
.stat-icon-purple { background: #f5f3ff; color: #7c3aed; }
.stat-icon-cyan   { background: #ecfeff; color: #0891b2; }
.stat-icon-indigo { background: #eef2ff; color: #4f46e5; }
.stat-icon-amber  { background: #fffbeb; color: #d97706; }

.stat-card .stat-info .stat-label {
    font-size: .78rem;
    color: #6b7280;
    margin-bottom: 2px;
}

.stat-card .stat-info .stat-value {
    font-size: 1.65rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}

/* ─── Section Cards ─── */
.dash-section {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    margin-bottom: 24px;
}

.dash-section-header {
    padding: 18px 22px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dash-section-header h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.dash-section-header h3 i {
    color: var(--accent-color, #e8532e);
}

.dash-section-link {
    font-size: .83rem;
    color: var(--accent-color, #e8532e);
    text-decoration: none;
    font-weight: 600;
}

.dash-section-body {
    padding: 16px 22px;
}

/* ─── Booking Row ─── */
.booking-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid #f9fafb;
}

.booking-row:last-child { border-bottom: none; }

.booking-thumb {
    width: 56px;
    height: 56px;
    border-radius: 10px;
    object-fit: cover;
    flex-shrink: 0;
}

.booking-thumb-placeholder {
    width: 56px;
    height: 56px;
    border-radius: 10px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 1.3rem;
    flex-shrink: 0;
}

.booking-info { flex: 1; min-width: 0; }

.booking-title {
    font-weight: 600;
    font-size: .9rem;
    color: #111827;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.booking-meta {
    font-size: .78rem;
    color: #6b7280;
    margin-top: 2px;
}

.booking-price {
    font-weight: 700;
    font-size: .95rem;
    color: #111827;
    white-space: nowrap;
}

/* ─── Status Badge ─── */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: .73rem;
    font-weight: 600;
    white-space: nowrap;
}

.status-pending   { background: #fff7ed; color: #c2410c; }
.status-confirmed { background: #f0fdf4; color: #15803d; }
.status-cancelled { background: #fef2f2; color: #b91c1c; }

/* ─── Two-column layout ─── */
.dash-two-cols {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 24px;
}

@media (max-width: 992px) { .dash-two-cols { grid-template-columns: 1fr; } }

/* ─── Quick Access Links ─── */
.quick-link-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    text-decoration: none;
    color: #334155;
    font-weight: 600;
    font-size: .88rem;
    transition: all .2s;
    margin-bottom: 12px;
}

.quick-link-item:hover {
    background: var(--accent-color, #e8532e);
    color: #fff;
    border-color: var(--accent-color, #e8532e);
    transform: translateX(5px);
}

.quick-link-item i {
    width: 24px;
    text-align: center;
    font-size: 1.1rem;
    opacity: .7;
}

.quick-link-item:hover i { opacity: 1; }

/* ─── Charts Container ─── */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    margin-bottom: 28px;
}

@media (max-width: 992px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
}

.chart-container {
    background: #fff;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    border: 1px solid #f1f5f9;
}

.chart-header {
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chart-header h4 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.chart-canvas-wrapper {
    position: relative;
    height: 300px;
}
</style>
@endpush

@section('content')

{{-- Welcome Banner --}}
<div class="welcome-banner">
    <h2>{{ __('Welcome Back') }}, {{ auth()->user()->first_name }}! 👋</h2>
    <p>{{ __('Manage your company trips and view your latest bookings easily.') }}</p>
</div>

{{-- Stats Section --}}
<div class="dash-section-title" style="margin-bottom: 12px; font-weight: 700; color: #475569; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
    <i class="fas fa-chart-pie"></i> {{ __('Business Overview') }}
</div>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-purple">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Total Earnings') }}</div>
            <div class="stat-value">{{ number_format($totalEarnings, 0) }} <small style="font-size: 0.7em;">{{ __('SAR') }}</small></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-cyan">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Total Passengers') }}</div>
            <div class="stat-value">{{ number_format($totalPassengers) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-indigo">
            <i class="fas fa-plane-departure"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Active Trips') }}</div>
            <div class="stat-value">{{ $activeTrips }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-amber">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Completed Trips') }}</div>
            <div class="stat-value">{{ $completedTrips }}</div>
        </div>
    </div>
</div>

<div class="dash-section-title" style="margin-bottom: 12px; font-weight: 700; color: #475569; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
    <i class="fas fa-ticket-alt"></i> {{ __('Bookings Status') }}
</div>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">
            <i class="fas fa-list-ul"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Total Bookings') }}</div>
            <div class="stat-value">{{ $totalBookings }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Confirmed') }}</div>
            <div class="stat-value">{{ $confirmedBookings }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-orange">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Pending') }}</div>
            <div class="stat-value">{{ $pendingBookings }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-red">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Cancelled') }}</div>
            <div class="stat-value">{{ $cancelledBookings }}</div>
        </div>
    </div>
</div>

{{-- Charts Section --}}
<div class="dash-section-title" style="margin-bottom: 12px; margin-top: 28px; font-weight: 700; color: #475569; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
    <i class="fas fa-chart-line"></i> {{ __('Performance Analytics') }}
</div>

<div class="charts-grid">
    {{-- Revenue Growth (Line Chart) --}}
    <div class="chart-container" style="grid-column: span 2;">
        <div class="chart-header">
            <h4><i class="fas fa-chart-line"></i> {{ __('Revenue Growth (Last 6 Months)') }}</h4>
            <span style="font-size: 0.8rem; color: #64748b; font-weight: 500;">{{ __('SAR') }}</span>
        </div>
        <div class="chart-canvas-wrapper">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Booking Status (Doughnut) --}}
    <div class="chart-container">
        <div class="chart-header">
            <h4><i class="fas fa-chart-pie"></i> {{ __('Booking Status Distribution') }}</h4>
        </div>
        <div class="chart-canvas-wrapper">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    {{-- Top Trips (Bar) --}}
    <div class="chart-container">
        <div class="chart-header">
            <h4><i class="fas fa-fire"></i> {{ __('Top 5 Trips by Bookings') }}</h4>
        </div>
        <div class="chart-canvas-wrapper">
            <canvas id="topTripsChart"></canvas>
        </div>
    </div>
</div>

<div class="dash-two-cols">
    {{-- Latest Bookings --}}
    <div class="dash-section">
        <div class="dash-section-header">
            <h3><i class="fas fa-history"></i> {{ __('Latest Bookings') }}</h3>
            <a href="{{ route('agent.bookings.index') }}" class="dash-section-link">{{ __('View All') }}</a>
        </div>
        <div class="dash-section-body">
            @forelse($latestBookings as $booking)
                <div class="booking-row">
                    @php $image = $booking->trip->images->first(); @endphp
                    @if($image)
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="" class="booking-thumb">
                    @else
                        <div class="booking-thumb-placeholder"><i class="fas fa-map-marked-alt"></i></div>
                    @endif

                    <div class="booking-info">
                        <div class="booking-title">{{ $booking->trip->title }}</div>
                        <div class="booking-meta">
                            <span class="status-badge status-{{ $booking->status }}">
                                {{ $booking->status === 'pending' ? __('Pending') : ($booking->status === 'confirmed' ? __('Confirmed') : __('Cancelled')) }}
                            </span>
                            · {{ $booking->user->full_name }}
                        </div>
                    </div>
                    <div class="booking-price">{{ number_format($booking->total_price, 0) }} {{ __('SAR') }}</div>
                </div>
            @empty
                <div style="text-align:center;padding:32px 20px;color:#9ca3af;">
                    <i class="fas fa-ticket-alt" style="font-size:2.5rem;margin-bottom:10px;display:block;"></i>
                    <p>{{ __('No bookings yet') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Quick Access --}}
    <div class="dash-section">
        <div class="dash-section-header">
            <h3><i class="fas fa-bolt"></i> {{ __('Quick Access') }}</h3>
        </div>
        <div class="dash-section-body">
            <a href="{{ route('agent.trips.create') }}" class="quick-link-item">
                <i class="fas fa-plus-circle"></i> {{ __('Add New Trip') }}
            </a>
            <a href="{{ route('agent.trips.index') }}" class="quick-link-item">
                <i class="fas fa-map-marked-alt"></i> {{ __('Manage Trips') }}
            </a>
            <a href="{{ route('agent.bookings.index') }}" class="quick-link-item">
                <i class="fas fa-ticket-alt"></i> {{ __('Manage Bookings') }}
            </a>
            <a href="{{ url('/') }}" target="_blank" class="quick-link-item">
                <i class="fas fa-external-link-alt"></i> {{ __('View Site') }}
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 🎨 Global Chart.js Config
    Chart.defaults.font.family = "'Tajawal', sans-serif";
    Chart.defaults.color = '#64748b';

    // 📊 1. Revenue Growth Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueGradient = revenueCtx.createLinearGradient(0, 0, 0, 300);
    revenueGradient.addColorStop(0, 'rgba(124, 58, 237, 0.2)');
    revenueGradient.addColorStop(1, 'rgba(124, 58, 237, 0)');

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlyRevenue, 'label')) !!},
            datasets: [{
                label: "{{ __('Revenue') }}",
                data: {!! json_encode(array_column($monthlyRevenue, 'value')) !!},
                borderColor: '#7c3aed',
                backgroundColor: revenueGradient,
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#7c3aed',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
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
                    grid: { borderDash: [5, 5] },
                    ticks: { callback: value => value.toLocaleString() }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 🍩 2. Booking Status Distribution
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ["{{ __('Confirmed') }}", "{{ __('Pending') }}", "{{ __('Cancelled') }}"],
            datasets: [{
                data: [
                    {{ $statusDistribution['confirmed'] }},
                    {{ $statusDistribution['pending'] }},
                    {{ $statusDistribution['cancelled'] }}
                ],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                hoverOffset: 4,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            }
        }
    });

    // 📈 3. Top Trips Bar Chart
    const topTripsCtx = document.getElementById('topTripsChart').getContext('2d');
    new Chart(topTripsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($topTrips->toArray(), 'label')) !!},
            datasets: [{
                label: "{{ __('Bookings') }}",
                data: {!! json_encode(array_column($topTrips->toArray(), 'value')) !!},
                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                borderRadius: 8,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { display: false }
                },
                y: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endpush
