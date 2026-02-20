<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم النجاح - وجهتك</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #7c3aed; --bg: #0f172a; --card: #1e293b; --success: #10b981; }
        body { font-family: 'Cairo', sans-serif; background-color: var(--bg); color: #f8fafc; margin: 0; display: flex; align-items: center; justify-content: center; height: 100vh; padding: 20px; }
        .card { background: var(--card); border-radius: 24px; padding: 40px 20px; width: 100%; max-width: 400px; text-align: center; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .icon { width: 80px; height: 80px; background: rgba(16,185,129,0.1); color: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 24px; animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        @keyframes scaleIn { from { transform: scale(0.5); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        h1 { font-weight: 800; margin-bottom: 12px; font-size: 1.5rem; }
        p { color: #94a3b8; margin-bottom: 30px; font-size: 1rem; }
        .btn { display: inline-block; width: 100%; padding: 14px; background: linear-gradient(135deg, var(--primary), #a855f7); color: white; border-radius: 14px; text-decoration: none; font-weight: 700; transition: transform 0.2s; }
        .btn:active { transform: scale(0.98); }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✓</div>
        <h1>تم الدفع بنجاح!</h1>
        <p>لقد استلمنا دفعتك بنجاح، وتم تأكيد حجزك. نتمنى لك رحلة ممتعة.</p>
        <a href="{{ route('home') }}" class="btn">العودة للرئيسية</a>
    </div>
</body>
</html>
