<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إتمام عملية الدفع - وجهتك</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #7c3aed;
            --primary-light: #a855f7;
            --bg: #0f172a;
            --card: #1e293b;
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(255,255,255,0.08);
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }
        * { margin:0; padding:0; box-sizing:border-box; -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Cairo', sans-serif; background-color: var(--bg); color: var(--text); line-height: 1.6; overflow-x: hidden; }

        .checkout-container { max-width: 500px; margin: 0 auto; padding: 20px; animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Header */
        .page-header { text-align: center; margin-bottom: 24px; }
        .logo { font-size: 1.8rem; font-weight: 800; background: linear-gradient(135deg, #fff, var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 4px; }
        .step-indicator { display: flex; justify-content: center; gap: 8px; margin-top: 10px; }
        .step { width: 30px; height: 4px; border-radius: 2px; background: var(--border); }
        .step.active { background: var(--primary); }

        /* Summary Card */
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; padding: 20px; margin-bottom: 20px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3); }
        .summary-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px dashed var(--border); }
        .summary-title { font-weight: 700; font-size: 1.1rem; }
        .trip-price { font-size: 1.25rem; font-weight: 800; color: var(--primary-light); }

        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; }
        .info-label { color: var(--text-muted); }
        .info-value { font-weight: 600; }

        /* Payment Logic */
        .payment-section-title { font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .payment-section-title i { color: var(--primary); }

        /* HyperPay Widget Override */
        .wpwl-container { direction: ltr !important; }
        .wpwl-form { background: transparent !important; border: none !important; padding: 0 !important; width: 100% !important; }
        .wpwl-label { color: var(--text-muted) !important; font-family: 'Cairo' !important; font-size: 0.85rem !important; margin-bottom: 6px !important; }
        .wpwl-control { background: #0f172a !important; border: 1px solid var(--border) !important; color: white !important; border-radius: 12px !important; padding: 12px !important; height: auto !important; }
        .wpwl-button-pay { background: linear-gradient(135deg, var(--primary), var(--primary-light)) !important; border: none !important; border-radius: 14px !important; padding: 14px !important; font-family: 'Cairo' !important; font-weight: 700 !important; font-size: 1rem !important; cursor: pointer !important; width: 100% !important; margin-top: 20px !important; box-shadow: 0 8px 20px rgba(124,58,237,0.3) !important; transition: transform 0.2s !important; }
        .wpwl-button-pay:active { transform: scale(0.98) !important; }

        /* Redirect Button (Tabby/Tamara) */
        .btn-redirect { display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 16px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; border: none; border-radius: 16px; font-weight: 700; font-size: 1rem; cursor: pointer; text-decoration: none; box-shadow: 0 8px 20px rgba(124,58,237,0.3); transition: all 0.3s; }
        .btn-redirect:active { transform: scale(0.98); opacity: 0.9; }

        .method-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: rgba(124,58,237,0.1); border: 1px solid rgba(124,58,237,0.2); border-radius: 50px; font-size: 0.75rem; color: var(--primary-light); font-weight: 600; }

        .loader-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: var(--bg); display: none; flex-direction: column; align-items: center; justify-content: center; z-index: 1000; }
        .spinner { width: 40px; height: 40px; border: 4px solid var(--border); border-top: 4px solid var(--primary); border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 16px; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .secure-badge { text-align: center; margin-top: 20px; font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; justify-content: center; gap: 6px; }
    </style>
</head>
<body>

<div class="checkout-container">
    <div class="page-header">
        <div class="logo">WJHTAK</div>
        <p>إكمال حجز رحلتك</p>
        <div class="step-indicator">
            <div class="step active"></div>
            <div class="step active"></div>
            <div class="step"></div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card">
        <div class="summary-header">
            <span class="summary-title">ملخص الحجز</span>
            <span class="trip-price">{{ number_format($amount, 2) }} ر.س</span>
        </div>
        <div class="info-row">
            <span class="info-label">الرحلة:</span>
            <span class="info-value">{{ $trip->title_ar ?? $trip->title }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">المسافر:</span>
            <span class="info-value">{{ $user->full_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">رقم الحجز:</span>
            <span class="info-value">#{{ $booking->id }}</span>
        </div>
    </div>

    <!-- Payment Section -->
    <div class="card">
        <div class="payment-section-title">
            <i>💳</i>
            <span>دفع عبر</span>
            <span class="method-badge">{{ strtoupper($method) }}</span>
        </div>

        @if(in_array($method, ['mada', 'visa_master', 'apple_pay']))
            @if(isset($checkout_id))
                <form action="{{ route('payments.web.callback', ['payment_type' => $method]) }}" class="paymentWidgets" data-brands="{{ $method === 'mada' ? 'MADA' : 'VISA MASTER' }}"></form>
            @else
                <div style="text-align:center; padding:20px;">
                    <p style="color:var(--danger)">فشل تحميل طلب الدفع. يرجى المحاولة لاحقاً.</p>
                </div>
            @endif
        @else
            <!-- Tamara / Tabby Logic -->
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">
                سيتم توجيهك الآن إلى بوابة {{ ucfirst($method) }} لإكمال عملية الدفع بالتقسيط.
            </p>
            <button id="btn-redirect" class="btn-redirect">
                استمرار عبر {{ ucfirst($method) }} 🚀
            </button>
        @endif
    </div>

    <div class="secure-badge">
        <span>🔒 دفع آمن ومشفر 256-بت</span>
    </div>
</div>

<div class="loader-overlay" id="loader">
    <div class="spinner"></div>
    <p>جاري تحضير صفحة الدفع...</p>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@if(in_array($method, ['mada', 'visa_master', 'apple_pay']) && isset($checkout_id))
    <script>
        var wpwlOptions = {
            paymentTarget: "_top",
            locale: "ar",
            style: "plain",
            labels: {
                cvv: "رمز الأمان (CVV)",
                cardHolder: "اسم صاحب البطاقة",
                cardNumber: "رقم البطاقة",
                expiryDate: "تاريخ الانتهاء"
            }
        };
    </script>
    <script src="https://{{ config('services.hyperpay.mode') === 'production' ? 'oppwa.com' : 'test.oppwa.com' }}/v1/paymentWidgets.js?checkoutId={{ $checkout_id }}"></script>
@endif

<script>
    $(document).ready(function() {
        $('#btn-redirect').on('click', function() {
            const $btn = $(this);
            $('#loader').css('display', 'flex');
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('payments.web.initiate-redirect') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    booking_id: "{{ $booking->id }}",
                    method: "{{ $method }}"
                },
                success: function(response) {
                    if (response.checkout_url) {
                        window.location.href = response.checkout_url;
                    } else {
                        alert('خطأ في استلام رابط الدفع: ' + (response.message || 'حدث خطأ غير متوقع'));
                        $('#loader').hide();
                        $btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    alert('فشل الاتصال بالخادم: ' + (xhr.responseJSON?.message || 'خطأ غير معروف'));
                    $('#loader').hide();
                    $btn.prop('disabled', false);
                }
            });
        });
    });
</script>

</body>
</html>
