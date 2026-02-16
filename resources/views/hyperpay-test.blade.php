<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>اختبار HyperPay</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#0f172a; --card:#1e293b; --border:rgba(255,255,255,0.08); --purple:#7c3aed; --green:#10b981; --blue:#3b82f6; --text:#e2e8f0; --muted:#94a3b8; --danger:#ef4444; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Cairo',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; display:flex; flex-direction:column; align-items:center; padding:40px 20px; }
        .container { max-width:700px; width:100%; }
        .header { text-align:center; margin-bottom:32px; }
        .badge { display:inline-flex; gap:6px; background:rgba(124,58,237,0.15); border:1px solid rgba(124,58,237,0.3); padding:5px 16px; border-radius:50px; font-size:0.8rem; color:var(--purple); margin-bottom:16px; }
        .header h1 { font-size:2rem; font-weight:800; background:linear-gradient(135deg,#fff,var(--purple)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
        .header p { color:var(--muted); margin-top:8px; }
        .card { background:var(--card); border:1px solid var(--border); border-radius:16px; padding:28px; margin-bottom:20px; }
        .card h3 { font-size:1.1rem; font-weight:700; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; font-size:0.85rem; font-weight:600; color:var(--muted); margin-bottom:6px; }
        .form-group input,.form-group select { width:100%; padding:12px 16px; background:var(--bg); border:1px solid var(--border); border-radius:10px; color:var(--text); font-family:'Cairo',sans-serif; font-size:0.95rem; }
        .form-group input:focus,.form-group select:focus { outline:none; border-color:var(--purple); }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .btn { width:100%; padding:14px; border:none; border-radius:12px; font-family:'Cairo',sans-serif; font-size:1rem; font-weight:700; cursor:pointer; transition:all 0.2s; }
        .btn-primary { background:linear-gradient(135deg,var(--purple),#a855f7); color:white; }
        .btn-primary:hover { opacity:0.9; }
        .btn-primary:disabled { opacity:0.5; cursor:not-allowed; }
        .status-box { padding:16px 20px; border-radius:12px; margin-top:16px; display:none; gap:10px; align-items:center; }
        .status-box.show { display:flex; }
        .status-success { background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); }
        .status-error { background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); }
        .status-loading { background:rgba(59,130,246,0.1); border:1px solid rgba(59,130,246,0.3); }
        .wpwl-form { direction:ltr; text-align:left; }
        .test-cards { background:rgba(59,130,246,0.06); border:1px solid rgba(59,130,246,0.15); border-radius:12px; padding:20px; margin-top:20px; }
        .test-cards h4 { font-size:0.9rem; color:var(--blue); margin-bottom:12px; }
        .test-cards table { width:100%; border-collapse:collapse; font-size:0.82rem; }
        .test-cards th,.test-cards td { padding:8px 10px; text-align:right; border-bottom:1px solid var(--border); }
        .test-cards th { color:var(--blue); font-weight:600; }
        .test-cards td { color:var(--muted); }
        .test-cards code { background:rgba(255,255,255,0.05); padding:2px 6px; border-radius:4px; font-family:monospace; }
        .result-card { text-align:center; padding:40px; }
        .result-icon { font-size:4rem; margin-bottom:16px; }
        .result-details { background:var(--bg); border-radius:12px; padding:20px; margin-top:20px; text-align:left; direction:ltr; }
        .result-details pre { font-family:monospace; font-size:0.8rem; color:var(--text); white-space:pre-wrap; word-break:break-all; }
        .spinner { border:3px solid var(--border); border-top:3px solid var(--purple); border-radius:50%; width:22px; height:22px; animation:spin .8s linear infinite; display:inline-block; }
        @keyframes spin { to { transform:rotate(360deg); } }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="badge">🧪 بيئة الاختبار</div>
        <h1>💜 HyperPay Payment Test</h1>
        <p>اختبار بوابة الدفع HyperPay مع بيانات الاختبار الرسمية</p>
    </div>

    @if(isset($result))
        {{-- ===== RESULT PAGE ===== --}}
        <div class="card result-card">
            @if(!empty($result['success']))
                <div class="result-icon">✅</div>
                <h2 style="color:var(--green);">تم الدفع بنجاح!</h2>
            @else
                <div class="result-icon">❌</div>
                <h2 style="color:var(--danger);">فشلت عملية الدفع</h2>
                <p>{{ $result['message'] ?? 'حدث خطأ' }}</p>
            @endif
            <div class="result-details">
                <pre>{{ json_encode($result['data'] ?? $result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            <a href="{{ route('hyperpay.test') }}" class="btn btn-primary" style="display:inline-block;margin-top:20px;text-decoration:none;">🔄 اختبار جديد</a>
        </div>
    @else
        {{-- ===== CHECKOUT FORM ===== --}}
        <div class="card" id="form-card">
            <h3>💳 بيانات عملية الدفع</h3>
            <form id="checkout-form">
                <div class="form-group">
                    <label>المبلغ (ريال)</label>
                    <input type="number" id="amount" value="100" min="1" step="1" required>
                </div>
                <div class="form-group">
                    <label>وسيلة الدفع</label>
                    <select id="payment_type">
                        <option value="visa_master">Visa / MasterCard</option>
                        <option value="mada">مدى (Mada)</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>الاسم الأول</label>
                        <input type="text" id="first_name" value="Test" required>
                    </div>
                    <div class="form-group">
                        <label>الاسم الأخير</label>
                        <input type="text" id="last_name" value="User" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>البريد الإلكتروني</label>
                    <input type="email" id="email" value="test@example.com" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>المدينة</label>
                        <input type="text" id="city" value="Riyadh">
                    </div>
                    <div class="form-group">
                        <label>الشارع</label>
                        <input type="text" id="street" value="Test Street">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" id="btn-checkout">🚀 إنشاء Checkout</button>
            </form>
            <div class="status-box" id="status-box">
                <span id="status-icon"></span>
                <span id="status-text"></span>
            </div>
        </div>

        {{-- Payment Widget --}}
        <div id="payment-widget-container" class="card" style="display:none;">
            <h3>💳 أدخل بيانات البطاقة</h3>
            <form action="{{ route('hyperpay.result') }}" class="paymentWidgets" data-brands="VISA MASTER MADA"></form>
        </div>

        {{-- Test Cards --}}
        <div class="test-cards">
            <h4>🧪 بطاقات الاختبار</h4>
            <table>
                <thead><tr><th>النوع</th><th>رقم البطاقة</th><th>انتهاء</th><th>CVV</th></tr></thead>
                <tbody>
                    <tr><td>Visa</td><td><code>4440000009900010</code></td><td><code>01/39</code></td><td><code>100</code></td></tr>
                    <tr><td>MasterCard</td><td><code>5123450000000008</code></td><td><code>01/39</code></td><td><code>100</code></td></tr>
                    <tr><td>MADA</td><td><code>4464040000000007</code></td><td><code>12/25</code></td><td><code>100</code></td></tr>
                </tbody>
            </table>
            <p style="color:var(--muted);font-size:0.8rem;margin-top:10px;">⚠️ المبالغ يجب أن تكون بدون كسور (مثل 100.00) في بيئة الاختبار</p>
        </div>
    @endif
</div>

@if(!isset($result))
<script>
    function showStatus(type, text) {
        const box = document.getElementById('status-box');
        box.className = 'status-box show status-' + type;
        document.getElementById('status-icon').innerHTML = type === 'loading' ? '<div class="spinner"></div>' : (type === 'success' ? '✅' : '❌');
        document.getElementById('status-text').textContent = text;
    }

    document.getElementById('checkout-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const btn = document.getElementById('btn-checkout');
        btn.disabled = true;
        btn.textContent = '⏳ جارِ إنشاء Checkout...';
        showStatus('loading', 'جارِ التواصل مع HyperPay عبر الخادم...');

        try {
            const response = await fetch('{{ route("hyperpay.checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    amount: document.getElementById('amount').value,
                    payment_type: document.getElementById('payment_type').value,
                    first_name: document.getElementById('first_name').value,
                    last_name: document.getElementById('last_name').value,
                    email: document.getElementById('email').value,
                    city: document.getElementById('city').value,
                    street: document.getElementById('street').value,
                })
            });

            const data = await response.json();
            console.log('Checkout Response:', data);

            if (data.id) {
                showStatus('success', 'تم إنشاء Checkout بنجاح! ID: ' + data.id);

                // Load HyperPay payment widget
                const wpwlScript = document.createElement('script');
                wpwlScript.textContent = 'var wpwlOptions = { paymentTarget: "_top", locale: "ar" };';
                document.head.appendChild(wpwlScript);

                const script = document.createElement('script');
                script.src = 'https://eu-test.oppwa.com/v1/paymentWidgets.js?checkoutId=' + data.id;
                script.onload = function() {
                    document.getElementById('payment-widget-container').style.display = 'block';
                    document.getElementById('form-card').style.display = 'none';
                };
                document.body.appendChild(script);
            } else {
                showStatus('error', 'فشل: ' + (data.result?.description || data.error || JSON.stringify(data)));
                btn.disabled = false;
                btn.textContent = '🚀 إنشاء Checkout';
            }
        } catch (err) {
            showStatus('error', 'خطأ في الاتصال: ' + err.message);
            btn.disabled = false;
            btn.textContent = '🚀 إنشاء Checkout';
        }
    });
</script>
@endif
</body>
</html>
