<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #333;
            line-height: 1.6;
        }
        /* Layout */
        .header {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .header-left, .header-right {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .header-left {
            text-align: left;
        }
        .header-right {
            text-align: right;
        }

        /* Typography & Logo */
        .logo {
            max-width: 160px;
            max-height: 80px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        /* Info Blocks */
        .info-section {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-box {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            background-color: #f8fafc;
            border-radius: 6px;
            padding: 15px;
            box-sizing: border-box;
        }
        .info-title {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 5px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #64748b;
            display: inline-block;
            width: 100px;
        }

        /* Tables */
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            margin-top: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            padding: 12px;
            text-align: right;
            border: 1px solid #cbd5e1;
        }
        td {
            padding: 12px;
            border: 1px solid #cbd5e1;
        }
        tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Totals */
        .totals-wrapper {
            width: 100%;
            margin-top: 15px;
        }
        .total-section {
            width: 300px;
            float: left;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
        .total-row {
            padding: 10px 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        .total-row:last-child {
            border-bottom: none;
            background-color: #1e40af;
            color: white;
            border-radius: 0 0 6px 6px;
        }
        .total-label {
            display: inline-block;
            width: 120px;
            font-weight: bold;
        }
        .total-value {
            display: inline-block;
            width: 130px;
            text-align: left;
            font-weight: bold;
        }
        .total-row:last-child .total-value {
            color: white;
            font-size: 16px;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #64748b;
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
        }
        .official-stamp {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
            color: #2563eb;
            opacity: 0.8;
            font-size: 14px;
        }
    </style>
</head>
<body>
    @php
        $siteName = \App\Models\Setting::get('site_name', 'وجهتك');
        $siteLogo = \App\Models\Setting::get('site_logo');
        $logoPath = $siteLogo ? public_path($siteLogo) : public_path('images/logo-full.png');

        $contactEmail = \App\Models\Setting::get('contact_email');
        $contactPhone = \App\Models\Setting::get('contact_phone');
    @endphp

    <div class="header">
        <div class="header-right">
            @if(file_exists($logoPath))
                <img src="{{ $logoPath }}" class="logo" alt="Logo">
            @else
                <h1 class="company-name">{{ $siteName }}</h1>
            @endif
            <div class="company-name">{{ $siteName }}</div>
            @if($contactEmail)
                <div style="color: #64748b; font-size: 12px;">{{ $contactEmail }}</div>
            @endif
            @if($contactPhone)
                <div style="color: #64748b; font-size: 12px;">{{ $contactPhone }}</div>
            @endif
        </div>
        <div class="header-left">
            <div class="invoice-title">فاتورة إلكترونية</div>
            <div><strong>رقم الفاتورة:</strong> #INV-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}-{{ date('Y', strtotime($booking->created_at ?? time())) }}</div>
            <div><strong>تاريخ الإصدار:</strong> {{ date('Y-m-d') }}</div>
            <div><strong>حالة الدفع:</strong> {{ $booking->status === 'confirmed' ? 'مدفوعة' : 'غير مدفوعة' }}</div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-box" style="margin-left: 3%;">
            <div class="info-title">بيانات العميل (فوترة إلى)</div>
            <div class="info-row"><span class="info-label">الاسم:</span> {{ $booking->user?->full_name ?? 'بدون اسم' }}</div>
            <div class="info-row"><span class="info-label">البريد الإلكتروني:</span> {{ $booking->user?->email ?? '---' }}</div>
            <div class="info-row"><span class="info-label">رقم الهاتف:</span> {{ $booking->user?->phone ?? '---' }}</div>
            @if($booking->user?->country)
                <div class="info-row"><span class="info-label">الدولة:</span> {{ $booking->user->country }}</div>
            @endif
        </div>
        <div class="info-box">
            <div class="info-title">ملخص الحجز</div>
            <div class="info-row"><span class="info-label">رقم الحجز المرجعي:</span> #BKG-{{ $booking->id }}</div>
            <div class="info-row"><span class="info-label">تاريخ الحجز:</span> {{ $booking->booking_date ? $booking->booking_date->format('Y-m-d') : '---' }}</div>
            <div class="info-row"><span class="info-label">حالة الحجز:</span> {{ __($booking->booking_state ?? 'received') }}</div>
        </div>
    </div>

    <div class="section-title">تفاصيل البرنامج السياحي</div>
    <table>
        <thead>
            <tr>
                <th>الوصف / اسم الرحلة</th>
                <th>الوجهة</th>
                <th>المدة</th>
                <th>عدد الأشخاص</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>{{ $booking->trip?->title ?? 'رحلة غير معروفة' }}</strong></td>
                <td>{{ $booking->trip?->toCountry?->name }} @if($booking->trip?->toCity?->name) - {{ $booking->trip?->toCity?->name }} @endif</td>
                <td>{{ $booking->trip?->duration ?? '---' }} يوم</td>
                <td>{{ $booking->tickets_count }}</td>
            </tr>
        </tbody>
    </table>

    @if($booking->passengers && $booking->passengers->count() > 0)
        <div class="section-title">بيانات المسافرين المرفقة</div>
        <table>
            <thead>
                <tr>
                    <th>م</th>
                    <th>الاسم الكامل</th>
                    <th>رقم الجواز</th>
                    <th>تاريخ انتهاء الجواز</th>
                    <th>الجنسية</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->passengers as $index => $passenger)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $passenger->name }}</td>
                    <td>{{ $passenger->passport_number ?? '---' }}</td>
                    <td>{{ $passenger->passport_expiry ? \Carbon\Carbon::parse($passenger->passport_expiry)->format('Y-m-d') : '---' }}</td>
                    <td>{{ $passenger->nationality ?? '---' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="totals-wrapper">
        <div class="total-section">
            <div class="total-row">
                <span class="total-label">المبلغ الأساسي:</span>
                <span class="total-value">{{ number_format($booking->trip?->price ?? 0, 2) }} ر.س</span>
            </div>
            @if($booking->total_price > ($booking->trip?->price ?? 0))
            <div class="total-row">
                <span class="total-label">إضافات أخرى:</span>
                <span class="total-value">{{ number_format($booking->total_price - ($booking->trip?->price ?? 0), 2) }} ر.س</span>
            </div>
            @endif
            <div class="total-row">
                <span class="total-label">الإجمالي النهائي:</span>
                <span class="total-value">{{ number_format($booking->total_price, 2) }} ر.س</span>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="official-stamp">
        *** تم اعتماد هذه الفاتورة إلكترونياً ولا تحتاج إلى توقيع أو ختم حي ***
    </div>

    <div class="footer">
        <p><strong>نشكر لكم ثقتكم واختياركم وكالة {{ $siteName }} للسياحة والسفر.</strong></p>
        <p>في حال وجود أي استفسارات، يرجى التواصل معنا عبر البريد الإلكتروني {{ $contactEmail ?? 'المعتمد' }} أو الرقم {{ $contactPhone ?? 'في الموقع' }}</p>
        <p>© {{ date('Y') }} {{ $siteName }} - جميع الحقوق محفوظة</p>
    </div>
</body>
</html>
