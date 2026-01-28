@extends('emails.layouts.master')

@section('content')
    <h2 style="color: #333; margin-top: 0;">{{ $title }}</h2>

    {!! $content !!}

    @if(isset($buttonUrl) && isset($buttonText))
        <div style="text-align: center;">
            <a href="{{ $buttonUrl }}" class="btn">{{ $buttonText }}</a>
        </div>
    @endif

    <p style="margin-top: 30px;">Best regards,<br>The {{ config('app.name') }} Team</p>
@endsection
