{{-- Footer Component --}}
<footer class="footer">
    <div class="container">
        {{-- Footer Top --}}
        <div class="footer-top">
            {{-- Brand Column --}}
            <div class="footer-brand">
                <a href="{{ route('home') }}" class="footer-logo">
                    @php
                        $footerLogo = \App\Models\Setting::get('site_logo');
                        $siteName = \App\Models\Setting::get('site_name_' . app()->getLocale(), config('app.name'));
                        $siteDescription = \App\Models\Setting::get('site_description_' . app()->getLocale(), config('app.name'));
                    @endphp
                    @if($footerLogo)
                        <img src="{{ asset($footerLogo) }}" alt="{{ $siteName }}">
                    @else
                        <img src="{{ asset('images/logo-full.png') }}" alt="{{ $siteName }}">
                    @endif
                    <span class="footer-logo-text">{{ $siteName }}</span>
                </a>
                <p class="footer-desc">
                    {{ $siteDescription }}
                </p>
                @php
                    $facebookUrl = \App\Models\Setting::get('facebook_url');
                    $twitterUrl = \App\Models\Setting::get('twitter_url' );
                    $instagramUrl = \App\Models\Setting::get('instagram_url');
                @endphp
                <div class="footer-social">
                    <a href="{{$facebookUrl}}" aria-label="{{ __('Facebook') }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                    </a>
                    <a href="{{$twitterUrl}}#" aria-label="{{ __('Twitter') }}">

                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"></path>
                            </svg>
                    </a>
                    <a href="{{$instagramUrl}}" aria-label="{{ __('Instagram') }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                        </svg>
                    </a>

                </div>
            </div>

            {{-- Quick Links --}}
            <div class="footer-column">
                <h4 class="footer-title">{{ __('Quick Links') }}</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}" class="footer-link">{{ __('Home') }}</a></li>
                    <li><a href="{{ route('trips.index') }}" class="footer-link">{{ __('All Trips') }}</a></li>
                    <li><a href="{{ route('destinations') }}" class="footer-link">{{ __('Destinations') }}</a></li>
                    <li><a href="{{ route('about') }}" class="footer-link">{{ __('About Us') }}</a></li>
                    <li><a href="{{ route('contact') }}" class="footer-link">{{ __('Contact Us') }}</a></li>
                </ul>
            </div>

            {{-- Support --}}
            <div class="footer-column">
                <h4 class="footer-title">{{ __('Support') }}</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('faq') }}" class="footer-link">{{ __('FAQ') }}</a></li>
                    @php
                        $footerPages = \App\Models\Page::where('is_active', true)->where('show_in_footer', true)->get();
                    @endphp
                    @foreach($footerPages as $fPage)
                        <li><a href="{{ route('pages.show', $fPage->slug) }}" class="footer-link">{{ $fPage->title }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact Info --}}
            @php
                                  $contactPhone = "+966920031822  +966536844469";
                $contactEmail = "contact@wjhtak.com";
            @endphp
            <div class="footer-column">
                <h4 class="footer-title">{{ __('Contact Us') }}</h4>
                <div class="footer-contact-item">
                    <svg class="footer-contact-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span class="footer-contact-text">{{ __('Riyadh, Saudi Arabia') }}</span>
                </div>
                <div class="footer-contact-item">
                    <svg class="footer-contact-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    <span class="footer-contact-text" dir="ltr">{{ $contactPhone }}</span>
                </div>
                <div class="footer-contact-item">
                    <svg class="footer-contact-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                    </svg>
                    <span class="footer-contact-text">{{$contactEmail}}</span>
                </div>
            </div>
        </div>

        {{-- Footer Bottom --}}
        <div class="footer-bottom">
            <p class="footer-copyright">
                © {{ date('Y') }} {{ config('app.name', 'Wjhtak') }}. {{ __('All Rights Reserved') }}
            </p>
            <div class="footer-payment">

                <!-- Visa -->
                <img src="https://t3.ftcdn.net/jpg/03/33/21/62/240_F_333216210_HjHUw1jjcYdGr3rRtYm3W1DIXAElEFJL.jpg" alt="Visa" class="pay-icon">

                <!-- Mastercard -->
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="pay-icon">

                <!-- Apple Pay -->
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b0/Apple_Pay_logo.svg" alt="Apple Pay" class="pay-icon">

                <!-- Mada -->
                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fb/Mada_Logo.svg" alt="Mada" class="pay-icon">

                <!-- Tamara -->
                <img src="https://cdn.tamara.co/assets/svg/tamara-logo-badge-ar.svg" alt="Tamara" class="pay-icon">
            </div>
        </div>
    </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var backToTopBtn = document.getElementById('backToTop');

        if(backToTopBtn) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopBtn.classList.add('show');
                } else {
                    backToTopBtn.classList.remove('show');
                }
            });

            backToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    });
</script>

@push('styles')
<style>
    .footer-payment {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .footer-payment svg {
        opacity: 0.8;
        transition: all 0.3s ease;
        filter: grayscale(1) brightness(1.5);
    }

    .footer-payment svg:hover {
        opacity: 1;
        filter: grayscale(0) brightness(1);
        transform: translateY(-2px);
    }

    .payment-badge {
        transition: all 0.3s ease;
    }

    .payment-badge:hover {
        transform: translateY(-2px);
    }
</style>
@endpush
