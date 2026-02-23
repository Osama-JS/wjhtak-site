<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فشل الدفع - وجهتك</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #7c3aed; --bg: #0f172a; --card: #1e293b; --danger: #ef4444; }
        body { font-family: 'Cairo', sans-serif; background-color: var(--bg); color: #f8fafc; margin: 0; display: flex; align-items: center; justify-content: center; height: 100vh; padding: 20px; }
        .card { background: var(--card); border-radius: 24px; padding: 40px 20px; width: 100%; max-width: 400px; text-align: center; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .icon { width: 80px; height: 80px; background: rgba(239,68,68,0.1); color: var(--danger); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 24px; }
        h1 { font-weight: 800; margin-bottom: 12px; font-size: 1.5rem; }
        .error-msg { color: #94a3b8; margin-bottom: 30px; font-size: 1rem; }
        .btn { display: inline-block; width: 100%; padding: 14px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 14px; text-decoration: none; font-weight: 700; transition: all 0.2s; }
        .btn:active { background: rgba(255,255,255,0.1); }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✕</div>
        <h1>عذراً، فشلت العملية</h1>
        <p class="error-msg">{{ $error ?? 'حدث خطأ غير متوقع أثناء عملية الدفع.' }}</p>
        @if(request()->query('source') !== 'api')
            <a href="{{ route('home') }}" class="btn">العودة للرئيسية</a>
        @endif
    </div>

    <script>
       function sendResultToWjhtakApp() {
            // تجهيز البيانات المطلوبة فقط
            var simpleResult = {
                success: false,
                message: "لم تنجح عملية الدفع"
            };

            // تحويل الكائن إلى نص JSON
            var resultJson = JSON.stringify(simpleResult);

            // الإرسال عبر الجسر البرمجي الخاص بتطبيق وجهتك
            if (window.FlutterBridge) {
                window.FlutterBridge.postMessage(resultJson);
            } else {
                console.log("FlutterBridge not found");
            }
        }
    </script>
</body>
</html>
