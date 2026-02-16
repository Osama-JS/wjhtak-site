<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>اختبار تمارا - Tamara Test</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--bg:#0a0e1a;--card:#111827;--border:rgba(255,255,255,.07);--teal:#14b8a6;--teal-g:linear-gradient(135deg,#14b8a6,#06d6a0);--green:#10b981;--red:#ef4444;--orange:#f59e0b;--text:#e2e8f0;--muted:#94a3b8}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Cairo',sans-serif;background:var(--bg);color:var(--text);line-height:1.7;min-height:100vh}
.hero{background:linear-gradient(135deg,#0a1a2a,#0f172a);padding:40px 20px;text-align:center;border-bottom:1px solid var(--border)}
.hero h1{font-size:2rem;font-weight:800;background:var(--teal-g);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.hero p{color:var(--muted);font-size:.9rem}
.wrap{max-width:700px;margin:30px auto;padding:0 20px}
.card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:28px;margin-bottom:20px}
h3{color:var(--teal);font-size:1rem;margin-bottom:14px}
label{display:block;font-size:.85rem;color:var(--muted);margin-bottom:4px;margin-top:12px}
input,select{width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;background:#0d1117;color:#fff;font-family:inherit;font-size:.9rem;transition:border .2s}
input:focus,select:focus{outline:none;border-color:var(--teal)}
.row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.btn{width:100%;padding:14px;border:none;border-radius:12px;font-family:inherit;font-size:1rem;font-weight:700;cursor:pointer;margin-top:20px;transition:all .3s}
.btn-primary{background:var(--teal-g);color:#fff}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(20,184,166,.3)}
.btn-primary:disabled{opacity:.5;cursor:not-allowed;transform:none}
.status{padding:16px;border-radius:12px;margin-top:16px;display:none;font-size:.9rem}
.status.success{display:block;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:var(--green)}
.status.error{display:block;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:var(--red)}
.status.loading{display:block;background:rgba(20,184,166,.1);border:1px solid rgba(20,184,166,.3);color:var(--teal)}
.test-info{background:rgba(20,184,166,.05);border:1px solid rgba(20,184,166,.2);border-radius:12px;padding:18px;margin-top:16px}
.test-info h4{color:var(--teal);margin-bottom:8px;font-size:.9rem}
.test-info p{font-size:.8rem;color:var(--muted);margin:4px 0}
.test-info code{background:rgba(255,255,255,.06);padding:2px 6px;border-radius:4px;font-size:.8rem;color:var(--orange);direction:ltr;unicode-bidi:embed}
table{width:100%;border-collapse:collapse;font-size:.8rem;margin:10px 0}
th,td{padding:8px 10px;text-align:right;border-bottom:1px solid var(--border)}
th{color:var(--teal);font-weight:600}
td{color:var(--muted)}
pre{background:#0d1117;border:1px solid var(--border);border-radius:10px;padding:14px;margin:10px 0;overflow-x:auto;font-size:.75rem;direction:ltr;text-align:left;color:#c9d1d9;max-height:300px;overflow-y:auto}
.result-card{border-color:rgba(20,184,166,.3)}
.badge{display:inline-block;padding:4px 12px;border-radius:50px;font-size:.75rem;font-weight:600}
.badge.ok{background:rgba(16,185,129,.15);color:var(--green)}
.badge.fail{background:rgba(239,68,68,.15);color:var(--red)}
.badge.pending{background:rgba(245,158,11,.15);color:var(--orange)}
.flow{display:flex;align-items:center;justify-content:center;gap:10px;flex-wrap:wrap;margin:16px 0;font-size:.8rem;color:var(--muted)}
.flow .step{background:rgba(20,184,166,.1);border:1px solid rgba(20,184,166,.2);padding:6px 14px;border-radius:8px;color:var(--teal);font-weight:600}
.flow .arrow{color:var(--muted)}
</style>
</head>
<body>

<div class="hero">
    <h1>🟢 اختبار بوابة تمارا</h1>
    <p>Tamara Payment Gateway - Sandbox Testing</p>
    <div class="flow">
        <span class="step">1. إنشاء الطلب</span>
        <span class="arrow">→</span>
        <span class="step">2. التوجه لتمارا</span>
        <span class="arrow">→</span>
        <span class="step">3. إكمال الدفع</span>
        <span class="arrow">→</span>
        <span class="step">4. التحقق والتأكيد</span>
    </div>
</div>

<div class="wrap">

@if(isset($result))
    {{-- ============ RESULT VIEW ============ --}}
    <div class="card result-card">
        <h3>📋 نتيجة عملية الدفع</h3>

        @if(($result['status'] ?? '') === 'success' && ($result['authorised'] ?? false))
            <div class="status success">✅ تم الدفع بنجاح وتم تأكيد الطلب!</div>
        @elseif(($result['status'] ?? '') === 'success')
            <div class="status" style="display:block;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);color:var(--orange);">
                ⏳ تم الدفع ولكن الطلب في انتظار التأكيد
            </div>
        @elseif(($result['status'] ?? '') === 'cancel')
            <div class="status error">❌ تم إلغاء عملية الدفع من قبل المستخدم</div>
        @elseif(($result['status'] ?? '') === 'failure')
            <div class="status error">❌ فشلت عملية الدفع</div>
        @else
            <div class="status error">⚠️ حالة غير معروفة: {{ $result['status'] ?? 'N/A' }}</div>
        @endif

        <table>
            <tr><th>الحالة</th><td>
                @if(($result['status'] ?? '') === 'success') <span class="badge ok">نجاح</span>
                @elseif(($result['status'] ?? '') === 'cancel') <span class="badge pending">ملغي</span>
                @else <span class="badge fail">فشل</span> @endif
            </td></tr>
            <tr><th>Order ID</th><td><code>{{ $result['order_id'] ?? 'N/A' }}</code></td></tr>
            @if(isset($result['error']))
            <tr><th>خطأ</th><td style="color:var(--red)">{{ $result['error'] }}</td></tr>
            @endif
        </table>

        @if(!empty($result['data']))
        <h3 style="margin-top:16px">📦 البيانات الكاملة</h3>
        <pre>{{ json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        @endif

        <a href="{{ route('tamara.test') }}" style="display:block;text-align:center;margin-top:16px;color:var(--teal);text-decoration:none;font-weight:600">
            ← العودة لصفحة الاختبار
        </a>
    </div>

@else
    {{-- ============ CHECKOUT FORM ============ --}}
    <div class="card">
        <h3>💳 بيانات الدفع التجريبي</h3>

        <form id="tamaraForm">
            <label>المبلغ (ريال سعودي) *</label>
            <input type="number" id="amount" value="300" min="1" step="1" required>
            <p style="font-size:.75rem;color:var(--muted);margin-top:4px">⚠️ الحد الأدنى لتمارا: 100 ريال عادةً</p>

            <div class="row">
                <div>
                    <label>الاسم الأول *</label>
                    <input type="text" id="first_name" value="Mohammed" required>
                </div>
                <div>
                    <label>الاسم الأخير *</label>
                    <input type="text" id="last_name" value="Ali" required>
                </div>
            </div>

            <label>البريد الإلكتروني *</label>
            <input type="email" id="email" value="test@example.com" required>

            <label>رقم الهاتف *</label>
            <input type="text" id="phone" value="966544337866" required>
            <p style="font-size:.75rem;color:var(--muted);margin-top:4px">يجب أن يبدأ بـ 966 متبوعاً بـ 9 أرقام تبدأ بـ 5</p>

            <div class="row">
                <div>
                    <label>المدينة</label>
                    <input type="text" id="city" value="Riyadh">
                </div>
                <div>
                    <label>العنوان</label>
                    <input type="text" id="address" value="King Fahd Road">
                </div>
            </div>

            <label>وصف المنتج</label>
            <input type="text" id="item_name" value="رحلة سياحية تجريبية">

            <button type="submit" class="btn btn-primary" id="submitBtn">
                🛒 إنشاء جلسة الدفع
            </button>
        </form>

        <div id="statusMsg" class="status"></div>
    </div>

    {{-- ============ TEST INFO ============ --}}
    <div class="card">
        <h3>📌 معلومات بيئة الاختبار</h3>

        <div class="test-info">
            <h4>🔗 بيانات الاتصال</h4>
            <p>Sandbox URL: <code>https://api-sandbox.tamara.co</code></p>
            <p>العملة: <code>SAR</code> فقط</p>
            <p>نوع الدفع: <code>PAY_BY_INSTALMENTS</code> (تقسيط على 3 أقساط)</p>
        </div>

        <div class="test-info" style="margin-top:12px">
            <h4>📱 بيانات الاختبار</h4>
            <table>
                <tr><th>رقم الهاتف (KSA)</th><td><code>966544337866</code> أو أي رقم عشوائي يبدأ بـ 5</td></tr>
                <tr><th>OTP</th><td>يتم تعبئته تلقائياً في بيئة الاختبار</td></tr>
                <tr><th>التحقق من الهوية</th><td>يتم تخطيه تلقائياً في Sandbox</td></tr>
            </table>
        </div>

        <div class="test-info" style="margin-top:12px">
            <h4>🔄 سير العمل</h4>
            <p>1. إنشاء جلسة دفع → الحصول على <code>checkout_url</code></p>
            <p>2. التوجه لصفحة تمارا → المستخدم يكمل الدفع</p>
            <p>3. تمارا تعيد التوجه لـ callback URL مع <code>orderId</code></p>
            <p>4. الخادم يستدعي <code>POST /orders/{orderId}/authorise</code></p>
            <p>5. إذا نجح → تأكيد الحجز + تسجيل الدفع + إنشاء فاتورة</p>
        </div>
    </div>
@endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('tamaraForm');
    if (!form) return;

    const statusMsg = document.getElementById('statusMsg');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        submitBtn.disabled = true;
        submitBtn.textContent = '⏳ جاري الإنشاء...';
        statusMsg.className = 'status loading';
        statusMsg.style.display = 'block';
        statusMsg.textContent = 'جاري إنشاء جلسة الدفع مع تمارا...';

        try {
            const payload = {
                amount: document.getElementById('amount').value,
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                city: document.getElementById('city').value,
                address: document.getElementById('address').value,
                item_name: document.getElementById('item_name').value,
            };

            const response = await fetch('{{ route("tamara.checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.error) {
                throw new Error(data.message || 'Unknown error');
            }

            if (data.checkout_url) {
                statusMsg.className = 'status success';
                statusMsg.innerHTML = `
                    ✅ تم إنشاء جلسة الدفع بنجاح!<br>
                    <strong>Order ID:</strong> <code>${data.order_id || 'N/A'}</code><br>
                    <strong>Session ID:</strong> <code>${data.session_id || 'N/A'}</code><br>
                    <br>🔄 سيتم توجيهك لصفحة تمارا خلال 3 ثوان...
                `;

                setTimeout(() => {
                    window.location.href = data.checkout_url;
                }, 3000);
            } else {
                throw new Error('لم يتم استلام رابط الدفع من تمارا');
            }

        } catch (err) {
            statusMsg.className = 'status error';
            statusMsg.textContent = '❌ خطأ: ' + err.message;
            submitBtn.disabled = false;
            submitBtn.textContent = '🛒 إنشاء جلسة الدفع';
        }
    });
});
</script>

</body>
</html>
