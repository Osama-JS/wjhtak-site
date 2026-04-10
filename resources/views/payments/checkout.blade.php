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
            --primary: #4f46e5;
            --primary-light: #818cf8;
            --bg: #0f172a;
            --card: rgba(30, 41, 59, 0.7);
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(255,255,255,0.1);
            --success: #10b981;
            --danger: #ef4444;
            --accent: #f59e0b;
        }
        * { margin:0; padding:0; box-sizing:border-box; -webkit-tap-highlight-color: transparent; }
        body {
            font-family: 'Cairo', sans-serif;
            background: radial-gradient(circle at top right, #1e1b4b, #0f172a);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
        }

        .checkout-container { max-width: 500px; margin: 20px auto; padding: 20px; animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Header */
        .page-header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 2.2rem; font-weight: 800; letter-spacing: 2px; background: linear-gradient(to right, #fff, var(--primary-light)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 4px; }
        .page-header p { color: var(--text-muted); font-size: 0.95rem; }

        .step-indicator { display: flex; justify-content: center; gap: 10px; margin-top: 15px; }
        .step { width: 35px; height: 5px; border-radius: 10px; background: var(--border); transition: all 0.3s; }
        .step.active { background: var(--primary); box-shadow: 0 0 10px var(--primary); }

        /* Modern Glass Cards */
        .card {
            background: var(--card);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        }

        .summary-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border); }
        .summary-title { font-weight: 700; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }
        .trip-price { font-size: 1.4rem; font-weight: 800; color: #fff; }

        .info-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.95rem; }
        .info-label { color: var(--text-muted); }
        .info-value { font-weight: 600; color: #e2e8f0; }

        /* Payment Section */
        .payment-section-title { font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; gap: 12px; font-size: 1.1rem; }

        .method-display {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            padding: 10px 0;
        }
        .gateway-logo {
            height: 45px;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
            transition: transform 0.3s;
        }
        .gateway-logo:hover { transform: scale(1.05); }

        /* HyperPay Widget Customization */
        .wpwl-container { direction: ltr !important; }
        .wpwl-form { background: transparent !important; border: none !important; padding: 0 !important; width: 100% !important; }
        .wpwl-label {
            color: var(--text-muted) !important;
            font-family: 'Cairo' !important;
            font-size: 0.8rem !important;
            margin-bottom: 8px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }
        .wpwl-control {
            background: rgba(15, 23, 42, 0.6) !important;
            border: 1px solid var(--border) !important;
            color: white !important;
            border-radius: 16px !important;
            padding: 14px 16px !important;
            height: 54px !important;
            transition: all 0.3s !important;
        }
        .wpwl-control:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.2) !important;
            outline: none !important;
        }
        .wpwl-group { margin-bottom: 20px !important; }

        .wpwl-button-pay {
            background: linear-gradient(135deg, var(--primary), var(--primary-light)) !important;
            border: none !important;
            border-radius: 18px !important;
            padding: 18px !important;
            font-family: 'Cairo' !important;
            font-weight: 700 !important;
            font-size: 1.1rem !important;
            cursor: pointer !important;
            width: 100% !important;
            margin-top: 15px !important;
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.4) !important;
            transition: all 0.3s !important;
            color: white !important;
        }
        .wpwl-button-pay:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.5) !important;
        }
        .wpwl-button-pay:active { transform: translateY(0) !important; }

        /* Redirect Button (Tamara) */
        .btn-redirect {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 18px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.4);
            transition: all 0.3s;
        }
        .btn-redirect:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.5);
        }
        .btn-redirect:active { transform: translateY(0); }

        .secure-badge {
            text-align: center;
            margin-top: 30px;
            font-size: 0.85rem;
            color: var(--text-muted);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        .security-icons { display: flex; gap: 15px; opacity: 0.6; grayscale: 1; }
        .security-icons img { height: 20px; }

        .loader-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: var(--bg); display: none; flex-direction: column; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(10px); }
    </style>
</head>
<body>

<div class="checkout-container">
    <div class="page-header">
        <div class="logo">WJHTAK</div>
        <p>بوابة الدفع الآمنة</p>
        <div class="step-indicator">
            <div class="step active"></div>
            <div class="step active"></div>
            <div class="step active"></div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card">
        <div class="summary-header">
            <span class="summary-title"><i>📝</i> تفاصيل الحجز</span>
            <span class="trip-price">{{ number_format($amount, 2) }} ر.س</span>
        </div>
        <div class="info-row">
            <span class="info-label">الرحلة</span>
            <span class="info-value">{{ $trip->title_ar ?? $trip->title }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">اسم العميل</span>
            <span class="info-value">{{ $user->full_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">رقم المرجع</span>
            <span class="info-value">#{{ $booking->id }}</span>
        </div>
    </div>

    <!-- Payment Section -->
    <div class="card">
        <div class="method-display">
            @php
                $logoUrl = match($method) {
                    'mada' => 'https://upload.wikimedia.org/wikipedia/commons/f/fb/Mada_Logo.svg',
                    'visa_master' => 'https://t3.ftcdn.net/jpg/03/33/21/62/240_F_333216210_HjHUw1jjcYdGr3rRtYm3W1DIXAElEFJL.jpg',
                    'apple_pay' => 'https://upload.wikimedia.org/wikipedia/commons/b/b0/Apple_Pay_logo.svg',
                    'tamara' => 'https://cdn.tamara.co/assets/svg/tamara-logo-badge-ar.svg',
                    default => null
                };
            @endphp

            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $method }}" class="gateway-logo">
            @endif
            <div class="payment-section-title">
                <span>إكمال الدفع عبر {{ strtoupper($method) }}</span>
            </div>
        </div>

        @if(in_array($method, ['mada', 'visa_master', 'apple_pay']))
            @if(isset($checkout_id))
                <form action="{{ route('payments.web.callback', ['payment_type' => $method, 'source' => $source]) }}" class="paymentWidgets" data-brands="{{ $method === 'mada' ? 'MADA' : ($method === 'apple_pay' ? 'APPLEPAY' : 'VISA MASTER') }}"></form>
            @else
                <div style="text-align:center; padding:20px;">
                    <p style="color:var(--danger)">فشل تحميل طلب الدفع. يرجى المحاولة لاحقاً.</p>
                </div>
            @endif
        @else
            <!-- Tamara Logic -->
            <p style="font-size: 0.9rem; color: var(--text-muted); text-align: center; margin-bottom: 24px;">
                سيتم توجيهك الآن إلى صفحة الدفع الرسمية لإكمال العملية بأمان.
            </p>
            <button id="btn-redirect" class="btn-redirect">
                متابعة الدفع الآن 💳
            </button>
        @endif
    </div>

    <div class="secure-badge">
        <p>🔒 جميع المدفوعات مشفرة وآمنة تماماً</p>
        <div class="security-icons">
            <img src="https://checkout.hyperpay.com/v1/paymentWidgets/img/pci-dss.png" alt="PCI DSS">
            <img src="https://checkout.hyperpay.com/v1/paymentWidgets/img/3d-secure.png" alt="3D Secure">
        </div>
    </div>
</div>

<div class="loader-overlay" id="loader">
    <div class="spinner"></div>
    <p>جاري تأمين الاتصال ومعالجة طلبك...</p>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@if(in_array($method, ['mada', 'visa_master', 'apple_pay']) && isset($checkout_id))
    @php
        $scriptUrl = rtrim(config('hyperpay.base_url'), '/');
        // Remove /v1 if it exists at the end because paymentWidgets is often at /v1/paymentWidgets.js
        if(str_ends_with($scriptUrl, '/v1')) {
            $scriptUrl = substr($scriptUrl, 0, -3);
        }
    @endphp
    <script src="{{ $scriptUrl }}/v1/paymentWidgets.js?checkoutId={{ $checkout_id }}"></script>
    <script type="text/javascript">
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
@endif

<script>
    $(document).ready(function() {
        $('#btn-redirect').on('click', function() {
            const $btn = $(this);
            $('#loader').css('display', 'flex');
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('payments.web.initiate') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    booking_id: "{{ $booking->id }}",
                    method: "{{ $method }}",
                    source: "{{ $source }}"
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
