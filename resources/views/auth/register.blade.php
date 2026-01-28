@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="col-md-6">
    <div class="authincation-content">
        <div class="row no-gutters">
            <div class="col-xl-12">
                <div class="auth-form">
                    <div class="text-center mb-3">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('images/logo-full.png') }}" alt="" style="max-width: 150px;">
                        </a>
                    </div>
                    <h4 class="text-center mb-4">Sign up your account</h4>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="mb-1"><strong>Name</strong></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="Your Name">
                            @error('name')
                                <span class="text-danger fs-12">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="mb-1"><strong>Email</strong></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="hello@example.com">
                            @error('email')
                                <span class="text-danger fs-12">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="mb-1"><strong>Password</strong></label>
                            <input type="password" name="password" class="form-control" required placeholder="Password">
                            @error('password')
                                <span class="text-danger fs-12">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="mb-1"><strong>Confirm Password</strong></label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="Confirm Password">
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign me up</button>
                        </div>
                    </form>

                    <div class="new-account mt-3">
                        <p>Already have an account? <a class="text-primary" href="{{ route('login') }}">Sign in</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
