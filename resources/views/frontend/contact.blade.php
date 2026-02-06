@extends('frontend.layouts.app')

@section('title', __('Contact Us'))

@section('content')
    {{-- Page Header --}}
    <section class="section" style="padding-top: calc(var(--space-24) + 60px); padding-bottom: var(--space-10); background: var(--gradient-primary);">
        <div class="container">
            <div class="text-center" style="color: white;">
                <h1 style="font-size: var(--text-4xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                    {{ __('Contact Us') }}
                </h1>
                <p style="font-size: var(--text-lg); opacity: 0.9;">{{ __('We would love to hear from you') }}</p>
            </div>
        </div>
    </section>

    {{-- Contact Info Cards --}}
    <section class="section" style="margin-top: -60px;">
        <div class="container">
            <div class="grid grid-cols-1 md:grid-cols-3" style="gap: var(--space-6);">
                <div class="card text-center" style="padding: var(--space-6);">
                    <div style="width: 60px; height: 60px; background: var(--gradient-accent); border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary-dark)" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <h3 style="font-weight: var(--font-bold); margin-bottom: var(--space-2);">{{ __('Call Us') }}</h3>
                    <a href="tel:+966123456789" style="color: var(--color-primary);" dir="ltr">+966 12 345 6789</a>
                </div>
                <div class="card text-center" style="padding: var(--space-6);">
                    <div style="width: 60px; height: 60px; background: var(--gradient-accent); border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary-dark)" stroke-width="2">
                            <rect width="20" height="16" x="2" y="4" rx="2"/>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                    </div>
                    <h3 style="font-weight: var(--font-bold); margin-bottom: var(--space-2);">{{ __('Email Us') }}</h3>
                    <a href="mailto:info@wjhtak.com" style="color: var(--color-primary);">info@wjhtak.com</a>
                </div>
                <div class="card text-center" style="padding: var(--space-6);">
                    <div style="width: 60px; height: 60px; background: var(--gradient-accent); border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary-dark)" stroke-width="2">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <h3 style="font-weight: var(--font-bold); margin-bottom: var(--space-2);">{{ __('Visit Us') }}</h3>
                    <span style="color: var(--color-primary);">{{ __('Riyadh, Saudi Arabia') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact Form --}}
    <section class="section">
        <div class="container">
            <div style="max-width: 700px; margin: 0 auto;">
                <div class="card" style="padding: var(--space-8);">
                    <h2 style="font-size: var(--text-2xl); font-weight: var(--font-bold); margin-bottom: var(--space-6); text-align: center;">
                        {{ __('Send Us a Message') }}
                    </h2>
                    <form action="#" method="POST">
                        @csrf
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4);">
                            <div class="form-group">
                                <label class="form-label">{{ __('Full Name') }} *</label>
                                <input type="text" name="name" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Email') }} *</label>
                                <input type="email" name="email" class="form-input" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Subject') }} *</label>
                            <select name="subject" class="form-input form-select" required>
                                <option value="">{{ __('Select a subject') }}</option>
                                <option value="general">{{ __('General Inquiry') }}</option>
                                <option value="booking">{{ __('Booking Question') }}</option>
                                <option value="support">{{ __('Support') }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Message') }} *</label>
                            <textarea name="message" class="form-input form-textarea" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-full">{{ __('Send Message') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
