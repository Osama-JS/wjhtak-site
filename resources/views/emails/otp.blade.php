@extends('emails.layouts.master')

@section('content')
    <h2 style="color: #333; margin-top: 0;">Confirm Your Email Address</h2>
    <p>Hello,</p>
    <p>Thank you for signing up for <strong>{{ config('app.name') }}</strong>. Please use the following verification code to activate your account:</p>

    <div class="otp-box">
        {{ $otp }}
    </div>

    <p>This code is valid for <strong>10 minutes</strong>. If you did not request this verification, please ignore this email.</p>

    <p>Best regards,<br>The {{ config('app.name') }} Team</p>
@endsection
