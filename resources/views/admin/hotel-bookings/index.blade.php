@extends('layouts.app')

@section('title', 'حجوزات الفنادق - TBO')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">القائمة الرئيسية</a></li>
        <li class="breadcrumb-item active">حجوزات الفنادق</li>
    </ol>
</div>
@endsection

@push('styles')
<style>
.badge-status { font-size: 0.78rem; padding: 5px 12px; border-radius: 20px; font-weight: 500; }
.badge-status.confirmed  { background:#d1fae5; color:#065f46; }
.badge-status.pending    { background:#fef3c7; color:#92400e; }
.badge-status.draft      { background:#e0e7ff; color:#3730a3; }
.badge-status.cancelled  { background:#fee2e2; color:#991b1b; }
.badge-status.failed     { background:#f3f4f6; color:#6b7280; }
.stat-card { border: none; border-radius: 14px; transition: transform .2s; }
.stat-card:hover { transform: translateY(-3px); }
.stat-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
</style>
@endpush

@section('content')

{{-- Stats --}}
<div class="row mb-4">
    <div class="col-6 col-xl-3 mb-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-hotel"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['total']) }}</h3>
                    <small class="text-muted">إجمالي الحجوزات</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 mb-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-check-circle"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['confirmed']) }}</h3>
                    <small class="text-muted">مؤكدة</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 mb-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-clock"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['pending']) }}</h3>
                    <small class="text-muted">قيد الانتظار</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 mb-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-dollar-sign"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['revenue'], 0) }}</h3>
                    <small class="text-muted">إجمالي الإيرادات (ر.س)</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.hotel-bookings.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">بحث</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                       placeholder="اسم الفندق، رقم TBO، اسم العميل...">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">الكل</option>
                    <option value="draft"     {{ request('status')=='draft'     ? 'selected':'' }}>مسودة</option>
                    <option value="pending"   {{ request('status')=='pending'   ? 'selected':'' }}>قيد الانتظار</option>
                    <option value="confirmed" {{ request('status')=='confirmed' ? 'selected':'' }}>مؤكد</option>
                    <option value="cancelled" {{ request('status')=='cancelled' ? 'selected':'' }}>ملغى</option>
                    <option value="failed"    {{ request('status')=='failed'    ? 'selected':'' }}>فشل</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-search me-1"></i>بحث</button>
                <a href="{{ route('admin.hotel-bookings.index') }}" class="btn btn-outline-secondary"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0"><i class="fas fa-hotel me-2"></i>حجوزات الفنادق</h4>
        <a href="{{ route('admin.hotel-bookings.index', array_merge(request()->query(), ['export'=>'csv'])) }}"
           class="btn btn-sm btn-outline-success">
            <i class="fas fa-file-csv me-1"></i>تصدير CSV
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>رقم TBO</th>
                        <th>العميل</th>
                        <th>الفندق</th>
                        <th>تواريخ الإقامة</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td>{{ $booking->id }}</td>
                        <td>
                            @if($booking->tbo_booking_id)
                                <code>{{ $booking->tbo_booking_id }}</code>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $booking->user?->full_name ?? '—' }}</div>
                            <small class="text-muted">{{ $booking->user?->phone }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $booking->hotel_name }}</div>
                            <small class="text-muted">{{ $booking->city_name }}, {{ $booking->country_code }}</small>
                        </td>
                        <td>
                            <div>{{ $booking->check_in_date?->format('Y-m-d') }}</div>
                            <small class="text-muted">{{ $booking->nights_count }} ليلة</small>
                        </td>
                        <td class="fw-bold text-success">
                            {{ number_format($booking->total_price, 0) }}
                            <small>{{ $booking->currency }}</small>
                        </td>
                        <td>
                            <span class="badge-status {{ $booking->status }}">
                                {{ match($booking->status) {
                                    'confirmed' => 'مؤكد',
                                    'pending'   => 'قيد الانتظار',
                                    'draft'     => 'مسودة',
                                    'cancelled' => 'ملغى',
                                    'failed'    => 'فشل',
                                    default     => $booking->status,
                                } }}
                            </span>
                        </td>
                        <td>
                            <div>{{ $booking->created_at?->format('Y-m-d') }}</div>
                            <small class="text-muted">{{ $booking->created_at?->diffForHumans() }}</small>
                        </td>
                        <td>
                            <a href="{{ route('admin.hotel-bookings.show', $booking->id) }}"
                               class="btn btn-sm btn-outline-primary me-1" title="عرض">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($booking->status !== 'cancelled')
                            <button class="btn btn-sm btn-outline-danger me-1"
                                    onclick="confirmCancel({{ $booking->id }})" title="إلغاء">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif

                            {{-- Delete Button --}}
                            <button class="btn btn-sm btn-outline-danger open-delete-modal"
                                    data-id="{{ $booking->id }}"
                                    data-url="{{ route('admin.hotel-bookings.destroy', $booking->id) }}"
                                    title="حذف">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="fas fa-hotel fa-2x mb-3 d-block opacity-30"></i>
                            لا توجد حجوزات فنادق
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())
        <div class="p-3">
            {{ $bookings->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Cancel Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="cancelForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إلغاء الحجز</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">سبب الإلغاء (اختياري)</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="أدخل سبب الإلغاء..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                </div>
            </div>
        </form>
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
                            <li><i class="fas fa-check-circle me-1"></i> {{ __('Permanent loss of all guest details and stay information.') }}</li>
                            <li><i class="fas fa-check-circle me-1"></i> {{ __('Complete deletion of payment audit trails and history.') }}</li>
                            <li><i class="fas fa-check-circle me-1"></i> {{ __('This action cannot be undone under any circumstances.') }}</li>
                        </ul>
                    </div>

                    <div class="card bg-light border-0 mb-0">
                        <div class="card-body p-3">
                            <h6 class="fw-bold small mb-2"><i class="fas fa-info-circle"></i> {{ __('Safety Restrictions') }}:</h6>
                            <p class="small text-muted mb-0">
                                {{ __('The system will block deletion if the booking is confirmed or has successful/active payments.') }}
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

@push('scripts')
<script>
function confirmCancel(bookingId) {
    const form = document.getElementById('cancelForm');
    form.action = `/admin/hotel-bookings/${bookingId}/cancel`;
    new bootstrap.Modal(document.getElementById('cancelModal')).show();
}

$(document).on('click', '.open-delete-modal', function() {
    const id = $(this).data('id');
    const url = $(this).data('url');

    $('#displayBookingId').text(id);
    $('#deleteBookingForm').attr('action', url);
    $('#deleteBookingModal').modal('show');
});
</script>
@endpush
