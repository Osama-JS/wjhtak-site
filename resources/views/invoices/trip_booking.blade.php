<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            margin: 10px 0;
        }
        .info-section {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-box {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .info-title {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            padding: 10px;
            text-align: right;
            border-bottom: 2px solid #e2e8f0;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .total-section {
            width: 250px;
            float: left;
            margin-top: 20px;
        }
        .total-row {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .total-label {
            width: 150px;
            display: inline-block;
            font-weight: bold;
        }
        .total-value {
            width: 80px;
            display: inline-block;
            text-align: left;
            font-weight: bold;
            color: #4f46e5;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo-full.png') }}" class="logo">
        <div class="invoice-title">فاتورة رحلة إلكترونية</div>
        <div>رقم الفاتورة: #INV-{{ $booking->id }}-{{ time() }}</div>
    </div>

    <div class="info-section">
        <div class="info-box">
            <div class="info-title">بيانات العميل</div>
            <div>الاسم: {{ $booking->user->full_name }}</div>
            <div>البريد الإلكتروني: {{ $booking->user->email }}</div>
            <div>رقم الهاتف: {{ $booking->user->phone }}</div>
        </div>
        <div class="info-box">
            <div class="info-title">بيانات الحجز</div>
            <div>تاريخ الحجز: {{ $booking->booking_date->format('Y-m-d') }}</div>
            <div>حالة الحجز: مؤكد</div>
            <div>تاريخ الإصدار: {{ date('Y-m-d') }}</div>
        </div>
    </div>

    <div class="info-title">تفاصيل الرحلة</div>
    <table>
        <thead>
            <tr>
                <th>الرحلة</th>
                <th>الوجهة</th>
                <th>الوقت</th>
                <th>عدد التذاكر</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $booking->trip->title }}</td>
                <td>{{ $booking->trip->toCountry->name }} - {{ $booking->trip->toCity->name }}</td>
                <td>{{ $booking->trip->duration }}</td>
                <td>{{ $booking->tickets_count }}</td>
            </tr>
        </tbody>
    </table>

    <div class="info-title">قائمة المسافرين</div>
    <table>
        <thead>
            <tr>
                <th>الاسم</th>
                <th>رقم الهاتف</th>
                <th>الجنسية</th>
                <th>رقم الجواز</th>
            </tr>
        </thead>
        <tbody>
            @foreach($booking->passengers as $passenger)
            <tr>
                <td>{{ $passenger->name }}</td>
                <td>{{ $passenger->phone }}</td>
                <td>{{ $passenger->nationality }}</td>
                <td>{{ $passenger->passport_number }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span class="total-label">السعر الأساسي:</span>
            <span class="total-value">{{ number_format($booking->trip->price, 2) }} ر.س</span>
        </div>
        <div class="total-row">
            <span class="total-label">الإجمالي:</span>
            <span class="total-value">{{ number_format($booking->total_price, 2) }} ر.س</span>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        <p>شكراً لاختياركم وكالة وجهتك للسياحة والسفر</p>
        <p>هذه فاتورة إلكترونية معتمدة ولا تحتاج لختم</p>
        <p>© {{ date('Y') }} وجهتك - جميع الحقوق محفوظة</p>
    </div>
</body>
</html>
