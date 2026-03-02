@extends('frontend.layouts.app')

@section('title', $company->name)

@section('content')
    {{-- Premium Hero Section --}}
    <section class="company-hero-premium">
        {{-- Background Decoration --}}
        <div class="hero-bg-overlay">
            <img src="{{ asset('images/hero-bg.jpg') }}" alt="Profile Background" class="hero-bg-img">
            <div class="hero-gradient"></div>
        </div>

        <div class="container hero-content-wrapper">
            <div class="company-brand-card card-glass shadow-2xl animate__animated animate__fadeInUp">
                <div class="brand-top">
                    <div class="logo-wrapper shadow-lg overflow-hidden">
                        @if($company->logo)
                            <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="w-100 h-100" style="object-fit: cover;">
                        @else
                            <i class="fas fa-building fa-2x text-primary"></i>
                        @endif
                    </div>
                    <div class="brand-info">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h1 class="company-name-title">{{ app()->getLocale() == 'en' && $company->en_name ? $company->en_name : $company->name }}</h1>
                            <span class="verified-badge" title="{{ __('Verified Agency') }}">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>
                        <p class="company-tagline text-muted">
                            <i class="fas fa-map-marker-alt me-1"></i> {{ __('Leading Tourism Agency') }}
                        </p>
                    </div>
                </div>

                <div class="brand-bottom mt-4">
                    <div class="company-contact-tags">
                        @if($company->email)
                            <a href="mailto:{{ $company->email }}" class="contact-tag">
                                <i class="fas fa-envelope text-primary"></i>
                                <span>{{ $company->email }}</span>
                            </a>
                        @endif
                        @if($company->phone)
                            <a href="tel:{{ ($company->phone_code ? '+'.$company->phone_code : '') . $company->phone }}" class="contact-tag">
                                <i class="fas fa-phone text-primary"></i>
                                <span>{{ ($company->phone_code ? '+'.$company->phone_code.' ' : '') . $company->phone }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Bar --}}
    <div class="container mt-n5 position-relative z-10">
        <div class="stats-glass-bar shadow-xl">
            <div class="stat-item">
                <span class="stat-value">{{ $trips->total() }}</span>
                <span class="stat-label">{{ __('Active Trips') }}</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-value">5.0</span>
                <span class="stat-label">{{ __('Avg Rating') }}</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-value">100%</span>
                <span class="stat-label">{{ __('Reliability') }}</span>
            </div>
        </div>
    </div>

    {{-- Main Content Section --}}
    <div class="container py-10">
        <div class="row g-10">
            {{-- Primary Side --}}
            <div class="col-lg-12">
                <div class="section-header mb-8 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="section-subtitle">{{ __("Our Selection") }}</span>
                        <h2 class="section-title text-start">{{ __("Discover Agency Trips") }}</h2>
                    </div>
                    <div class="trips-count-badge">
                        {{ $trips->total() }} {{ __('Trips Found') }}
                    </div>
                </div>

                <div class="row g-6">
                    @forelse($trips as $trip)
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            @include('frontend.components.trip-card', ['trip' => $trip])
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-state-card text-center py-20 card-glass">
                                <div class="empty-icon-wrapper mb-4">
                                    <i class="fas fa-plane-slash fa-4x text-muted opacity-50"></i>
                                </div>
                                <h3 class="h4 fw-bold">{{ __('No Trips Found') }}</h3>
                                <p class="text-muted">{{ __('This company has no active trips at the moment.') }}</p>
                                <a href="{{ route('trips.index') }}" class="btn btn-primary mt-4">
                                    {{ __('Browse Other Trips') }}
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-12 d-flex justify-content-center">
                    {{ $trips->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Company Hero Premium */
    .company-hero-premium {
        position: relative;
        padding: 120px 0 80px 0;
        background: var(--color-bg);
        overflow: hidden;
    }

    .hero-bg-overlay {
        position: absolute;
        inset: 0;
        z-index: 0;
    }

    .hero-bg-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.15;
    }

    .hero-gradient {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, transparent, var(--color-bg));
    }

    .hero-content-wrapper {
        position: relative;
        z-index: 2;
    }

    .company-brand-card {
        padding: var(--space-8);
        border-radius: var(--radius-3xl);
        max-width: 900px;
    }

    .brand-top {
        display: flex;
        align-items: center;
        gap: var(--space-6);
    }

    .logo-wrapper {
        width: 100px;
        height: 100px;
        background: white;
        border-radius: var(--radius-2xl);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .company-name-title {
        font-size: var(--text-4xl);
        font-weight: var(--font-bold);
        margin: 0;
        color: var(--color-text-main);
    }

    .verified-badge {
        color: var(--color-success);
        font-size: 1.5rem;
    }

    .contact-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-full);
        text-decoration: none;
        color: var(--color-text-secondary);
        font-size: var(--text-sm);
        transition: all 0.3s ease;
    }

    .contact-tag:hover {
        background: var(--color-surface-hover);
        border-color: var(--color-primary);
        color: var(--color-primary);
        transform: translateY(-2px);
    }

    .company-contact-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    /* Stats Bar */
    .stats-glass-bar {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: var(--radius-2xl);
        padding: var(--space-6);
        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    .stat-item {
        text-align: center;
        flex: 1;
    }

    .stat-value {
        display: block;
        font-size: var(--text-2xl);
        font-weight: var(--font-bold);
        color: var(--color-primary);
    }

    .stat-label {
        font-size: var(--text-xs);
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--color-text-muted);
    }

    .stat-divider {
        width: 1px;
        height: 40px;
        background: var(--color-border);
    }

    .trips-count-badge {
        background: var(--color-surface-hover);
        color: var(--color-primary);
        padding: 6px 16px;
        border-radius: var(--radius-full);
        font-weight: var(--font-semibold);
        font-size: var(--text-sm);
    }

    .empty-state-card {
        border-radius: var(--radius-3xl);
        border: 2px dashed var(--color-border);
    }

    .mt-n5 {
        margin-top: -3rem !important;
    }

    /* Custom Grid Fix for Company Profile */
    .row.g-6 {
        display: flex;
        flex-wrap: wrap;
        margin-inline: calc(-1 * var(--space-3));
    }

    .row.g-6 > [class*="col-"] {
        padding-inline: var(--space-3);
    }

    @media (min-width: 768px) {
        .col-md-6 { flex: 0 0 50%; max-width: 50%; }
    }

    @media (min-width: 992px) {
        .col-lg-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
    }

    @media (min-width: 1200px) {
        .col-xl-3 { flex: 0 0 25%; max-width: 25%; }
    }

    @media (max-width: 768px) {
        .brand-top {
            flex-direction: column;
            text-align: center;
        }
        .company-contact-tags {
            justify-content: center;
        }
        .stats-glass-bar {
            flex-direction: column;
            gap: 20px;
        }
        .stat-divider {
            width: 80%;
            height: 1px;
        }
    }
</style>
@endpush

