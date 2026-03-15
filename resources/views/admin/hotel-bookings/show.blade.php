@extends('layouts.app')

@section('title', 'تفاصيل حجز الفندق #' . $booking->id)

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">القائمة الرئيسية</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.hotel-bookings.index') }}">حجوزات الفنادق</a></li>
        <li class="breadcrumb-item active">حجز #{{ $booking->id }}</li>
    </ol>
</div>
@endsection

@push('styles')
<style>
.info-label { font-size: 0.8rem; color: #6b7280; margin-bottom: 2px; }
.info-value  { font-weight: 600; }
.badge-status { font-size: 0.88rem; padding: 6px 14px; border-radius: 20px; font-weight: 500; }
.badge-status.confirmed  { background:#d1fae5; color:#065f46; }
.badge-status.pending    { background:#fef3c7; color:#92400e; }
.badge-status.draft      { background:#e0e7ff; color:#3730a3; }
.badge-status.cancelled  { background:#fee2e2; color:#991b1b; }
.badge-status.failed     { background:#f3f4f6; color:#6b7280; }
.timeline-item { position: relative; padding-right: 30px; padding-bottom: 20px; }
.timeline-item:before { content:''; position:absolute; right:9px; top:25px; width:2px; bottom:0; background:#e5e7eb; }
.timeline-item:last-child:before { display:none; }
.timeline-dot { position:absolute; right:0; top:4px; width:18px; height:18px; border-radius:50%; background:#6366f1; border:3px solid #fff; box-shadow:0 0 0 2px #6366f1; }
</style>
@endpush

@section('content')

<div class="row">
    {{-- Left Column --}}
    <div class="col-xl-8">

        {{-- Hotel Info --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">{{ $booking->hotel_name }}</h4>
                        @if($booking->hotel_name_ar)
                            <div class="text-muted">{{ $booking->hotel_name_ar }}</div>
                        @endif
                        <div class="text-muted mt-1">
                            <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                            {{ $booking->city_name }}, {{ $booking->country_code }}
                        </div>
                    </div>
                    <span class="badge-status {{ $booking->status }}">
                        {{ match($booking->status) {
                            'confirmed' => '✅ مؤكد',
                            'pending'   => '⏳ قيد الانتظار',
                            'draft'     => '📝 مسودة',
                            'cancelled' => '❌ ملغى',
                            'failed'    => '⚠️ فشل',
                            default     => $booking->status,
                        } }}
                    </span>
                </div>

                <hr>

                <div class="row g-3">
                    <div class="col-sm-6 col-md-3">
                        <div class="info-label">رقم الحجز TBO</div>
                        <div class="info-value">{{ $booking->tbo_booking_id ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="info-label">تسجيل الوصول</div>
                        <div class="info-value">{{ $booking->check_in_date?->format('Y-m-d') }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="info-label">تسجيل المغادرة</div>
                        <div class="info-value">{{ $booking->check_out_date?->format('Y-m-d') }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="info-label">عدد الليالي</div>
                        <div class="info-value">{{ $booking->nights_count }} ليلة</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="info-label">نوع الغرفة</div>
                        <div class="info-value">{{ $booking->room_type_name }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="info-label">عدد الغرف</div>
                        <div class="info-value">{{ $booking->rooms_count }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="info-label">البالغين / الأطفال</div>
                        <div class="info-value">{{ $booking->adults }} / {{ $booking->children }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="info-label">الإجمالي</div>
                        <div class="info-value text-success fs-5">{{ number_format($booking->total_price, 2) }} {{ $booking->currency }}</div>
                    </div>
                </div>

                @if($booking->cancellation_reason)
                <div class="alert alert-warning mt-3 mb-0">
                    <strong>سبب الإلغاء:</strong> {{ $booking->cancellation_reason }}
                </div>
                @endif
            </div>
        </div>

        {{-- Guests --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i>النزلاء ({{ $booking->guests->count() }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>الاسم الكامل</th>
                                <th>النوع</th>
                                <th>الجنسية</th>
                                <th>رقم الجواز</th>
                                <th>تاريخ الانتهاء</th>
                                <th>تاريخ الميلاد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->guests as $guest)
                            <tr>
                                <td>
                                    <strong>{{ $guest->title }} {{ $guest->first_name }} {{ $guest->last_name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $guest->type === 'adult' ? 'primary' : 'info' }}">
                                        {{ $guest->type === 'adult' ? 'بالغ' : 'طفل' }}
                                    </span>
                                </td>
                                <td>{{ $guest->nationality ?? '—' }}</td>
                                <td>{{ $guest->passport_number ?? '—' }}</td>
                                <td>{{ $guest->passport_expiry?->format('Y-m-d') ?? '—' }}</td>
                                <td>{{ $guest->dob?->format('Y-m-d') ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-muted text-center py-3">لا توجد بيانات نزلاء</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Raw TBO Response --}}
        @if($booking->tbo_raw_booking)
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-code me-2"></i>استجابة TBO الأصلية
                </h5>
            </div>
            <div class="card-body p-0">
                <pre class="m-0 p-3" style="max-height:300px; overflow:auto; font-size:0.78rem; background:#1e293b; color:#e2e8f0; border-radius:0 0 8px 8px;">{{ json_encode($booking->tbo_raw_booking, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
        @endif

    </div>

    {{-- Right Column --}}
    <div class="col-xl-4">

        {{-- Customer Info --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-user me-2"></i>معلومات العميل</h5>
            </div>
            <div class="card-body">
                <div class="info-label">الاسم</div>
                <div class="info-value mb-2">{{ $booking->user?->full_name ?? '—' }}</div>
                <div class="info-label">البريد الإلكتروني</div>
                <div class="info-value mb-2">{{ $booking->user?->email ?? '—' }}</div>
                <div class="info-label">رقم الهاتف</div>
                <div class="info-value">{{ $booking->user?->phone ?? '—' }}</div>
            </div>
        </div>

        {{-- Payment Info --}}
        @if($booking->payment)
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-credit-card me-2"></i>معلومات الدفع</h5>
            </div>
            <div class="card-body">
                <div class="info-label">بوابة الدفع</div>
                <div class="info-value mb-2">{{ strtoupper($booking->payment->payment_gateway) }}</div>
                <div class="info-label">طريقة الدفع</div>
                <div class="info-value mb-2">{{ $booking->payment->payment_method }}</div>
                <div class="info-label">Transaction ID</div>
                <div class="info-value mb-2" style="word-break:break-all;">
                    <code>{{ $booking->payment->transaction_id }}</code>
                </div>
                <div class="info-label">المبلغ</div>
                <div class="info-value text-success mb-2">
                    {{ number_format($booking->payment->amount, 2) }} {{ $booking->payment->currency }}
                </div>
                @if($booking->payment->invoice_path)
                <a href="{{ asset('storage/' . $booking->payment->invoice_path) }}" target="_blank"
                   class="btn btn-sm btn-outline-primary w-100 mt-2">
                    <i class="fas fa-file-pdf me-1"></i>تحميل الفاتورة
                </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Actions --}}
        @if($booking->status !== 'cancelled')
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <button class="btn btn-danger w-100" onclick="confirmCancel({{ $booking->id }})">
                    <i class="fas fa-times me-1"></i> إلغاء الحجز
                </button>
            </div>
        </div>
        @endif

        {{-- History Timeline --}}
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>سجل التغييرات</h5>
            </div>
            <div class="card-body">
                @forelse($booking->histories->sortByDesc('created_at') as $history)
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="fw-semibold" style="font-size:0.9rem;">{{ $history->action }}</div>
                    <div class="text-muted" style="font-size:0.82rem;">{{ $history->description }}</div>
                    @if($history->previous_state && $history->new_state)
                    <div class="mt-1" style="font-size:0.78rem;">
                        <span class="badge bg-secondary">{{ $history->previous_state }}</span>
                        <i class="fas fa-arrow-left mx-1 text-muted" style="font-size:0.7rem;"></i>
                        <span class="badge bg-primary">{{ $history->new_state }}</span>
                    </div>
                    @endif
                    <div class="text-muted mt-1" style="font-size:0.78rem;">
                        {{ $history->created_at?->diffForHumans() }}
                        @if($history->user) — {{ $history->user->full_name }} @endif
                    </div>
                </div>
                @empty
                <div class="text-muted text-center py-3">لا يوجد سجل</div>
                @endforelse
            </div>
        </div>
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
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        سيتم إرسال طلب إلغاء إلى TBO. تأكد من سياسة الإلغاء قبل المتابعة.
                    </div>
                    <label class="form-label">سبب الإلغاء (اختياري)</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="أدخل سبب الإلغاء..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                </div>
            </div>
        </form>
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
</script>
@endpush
