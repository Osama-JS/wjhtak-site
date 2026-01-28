<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f9;
            color: #333;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f7f9;
            padding-bottom: 40px;
        }
        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            font-family: sans-serif;
            color: #4a4a4a;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #3b4bd3;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.6;
        }
        .footer {
            background-color: #f4f7f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3b4bd3;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }
        .otp-box {
            background-color: #f1f3ff;
            border: 1px dashed #3b4bd3;
            padding: 20px;
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #3b4bd3;
            margin: 20px 0;
            border-radius: 8px;
        }
        @media screen and (max-width: 600px) {
            .main {
                width: 95% !important;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main" width="100%">
            <!-- Header -->
            <tr>
                <td class="header">
                    <h1>{{ config('app.name') }}</h1>
                </td>
            </tr>

            <!-- Content -->
            <tr>
                <td class="content">
                    @yield('content')
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="footer">
                    <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                    <p>{{ \App\Models\Setting::get('contact_address') ?? 'Global Travel Services' }}</p>
                    <p>
                        <a href="{{ url('/') }}" style="color: #3b4bd3; text-decoration: none;">Visit Website</a> |
                        <a href="mailto:{{ \App\Models\Setting::get('contact_email') ?? 'support@mytrip.com' }}" style="color: #3b4bd3; text-decoration: none;">Support</a>
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
