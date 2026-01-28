@extends('layouts.auth')

@section('title', 'Page Not Found')

@section('content')
<div class="col-md-7">
    <div class="form-input-content text-center error-page">
        <h1 class="error-text fw-bold">404</h1>
        <h4><i class="fa fa-exclamation-triangle text-warning"></i> The page you were looking for is not found!</h4>
        <p>You may have mistyped the address or the page may have moved.</p>
        <div>
            <a class="btn btn-primary" href="{{ url('/dashboard') }}">Back to Dashboard</a>
        </div>
    </div>
</div>
@endsection
