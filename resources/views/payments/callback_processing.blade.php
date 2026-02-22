<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جاري التحقق - وجهتك</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #0f172a; color: #f8fafc; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .content { text-align: center; }
        .spinner { width: 50px; height: 50px; border: 5px solid rgba(255,255,255,0.1); border-top: 5px solid #7c3aed; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px; }
        @keyframes spin { to { transform: rotate(360deg); } }
        h2 { font-weight: 700; margin-bottom: 8px; }
        p { color: #94a3b8; font-size: 0.95rem; }
    </style>
</head>
<body>
    <div class="content">
        <div class="spinner"></div>
        <h2>جاري التحقق من عملية الدفع...</h2>
        <p>يرجى عدم إغلاق الصفحة أو العودة للخلف</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const paymentType = "{{ $payment_type }}";
            const paymentId = "{{ $payment_id }}";
            const checkoutId = "{{ $checkout_id }}";
            const status = "{{ $status }}";

            // If it's already a failure from the URL
            if (status === 'failure' || status === 'cancel') {
                window.location.href = "{{ route('payments.web.failure') }}?error=" + encodeURIComponent("فشلت عملية الدفع أو تم إلغاؤها.");
                return;
            }

            // Detection of ID from URL if not passed by Blade
            const urlParams = new URLSearchParams(window.location.search);
            const tapId = urlParams.get('tap_id') || urlParams.get('id');
            const tamaraOrderId = urlParams.get('orderId');
            const tabbyPaymentId = urlParams.get('payment_id');

            const finalId = paymentId || checkoutId || tapId || tamaraOrderId || tabbyPaymentId;

            // Call the verification API
            $.ajax({
                url: "/api/payment/verify",
                method: "POST",
                headers: {
                    'Accept': 'application/json'
                },
                data: {
                    payment_type: paymentType,
                    id: finalId,
                    payment_id: (paymentType === 'tabby' || paymentType === 'tamara' || paymentType === 'tap') ? finalId : null,
                    checkout_id: (paymentType !== 'tabby' && paymentType !== 'tamara' && paymentType !== 'tap') ? finalId : null
                },
                success: function(response) {
                    if (response.success || response.error === false) {
                        window.location.href = "{{ route('payments.web.success') }}?booking_id=" + (response.booking_id || "");
                    } else {
                        window.location.href = "{{ route('payments.web.failure') }}?error=" + encodeURIComponent(response.message || "فشل التحقق من الدفع");
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || "حدث خطأ أثناء التواصل مع الخادم";
                    window.location.href = "{{ route('payments.web.failure') }}?error=" + encodeURIComponent(msg);
                }
            });
        });
    </script>
</body>
</html>
