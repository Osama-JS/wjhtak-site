<div class="card shadow-sm border-0 mb-4 h-100">
    <div class="card-body p-4">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
                <div class="avatar avatar-lg bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="{{ $icon ?? 'fas fa-chart-line' }} fs-24"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <h6 class="text-muted mb-1 text-uppercase fw-semibold">{{ $label }}</h6>
                <div class="d-flex align-items-baseline">
                    <h3 class="mb-0 fw-bold">{{ $value }}</h3>
                    @if(isset($trend))
                        <span class="ms-2 badge {{ $trend > 0 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }} fs-12">
                            <i class="fas fa-arrow-{{ $trend > 0 ? 'up' : 'down' }} me-1"></i>{{ abs($trend) }}%
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
