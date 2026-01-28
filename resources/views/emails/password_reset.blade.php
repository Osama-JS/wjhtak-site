@extends('emails.layouts.master')

@section('content')
    <h2 style="color: #333; margin-top: 0;">Reset Your Password</h2>
    <p>Hello,</p>
    <p>We received a request to reset your password for your <strong>{{ config('app.name') }}</strong> account. Use the following code to proceed:</p>

    <div class="otp-box" style="border-color: #ff9800; color: #ff9800; background-color: #fffaf0;">
        {{ $otp }}
    </div>

    <p>This code is valid for <strong>15 minutes</strong>. If you did not request a password reset, no further action is required.</p>

    <p>Best regards,<br>The {{ config('app.name') }} Team</p>
@endsection
