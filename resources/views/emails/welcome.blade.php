@extends('emails.layouts.master')

@section('content')
    <h2 style="color: #333; margin-top: 0;">Welcome to {{ config('app.name') }}!</h2>
    <p>Hi {{ $name }},</p>
    <p>Your account has been successfully verified! We're excited to have you with us.</p>
    <p>Explore the world's best flight and hotel deals with <strong>{{ config('app.name') }}</strong>. Your journey starts here.</p>

    <div style="text-align: center;">
        <a href="{{ url('/') }}" class="btn">Get Started Now</a>
    </div>

    <p style="margin-top: 30px;">If you have any questions, feel free to reply to this email or visit our support center.</p>

    <p>Happy travels,<br>The {{ config('app.name') }} Team</p>
@endsection
