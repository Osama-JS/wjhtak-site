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
                    @endphp
                    @if($footerLogo)
                        <img src="{{ asset($footerLogo) }}" alt="{{ $siteName }}">
                    @else
                        <img src="{{ asset('images/logo-full.png') }}" alt="{{ $siteName }}">
                    @endif
                    <span class="footer-logo-text">{{ $siteName }}</span>
                </a>
                <p class="footer-desc">
                    {{ __('Discover amazing travel destinations and create unforgettable memories with Wjhtak. Your trusted partner for premium tourism experiences.') }}
                </p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                    </a>
                    <a href="#" aria-label="Twitter">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
                        </svg>
                    </a>
                    <a href="#" aria-label="Instagram">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                        </svg>
                    </a>
                    <a href="#" aria-label="YouTube">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path>
                            <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" fill="white"></polygon>
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
                    <li><a href="#" class="footer-link">{{ __('Privacy Policy') }}</a></li>
                    <li><a href="#" class="footer-link">{{ __('Terms of Service') }}</a></li>
                    <li><a href="#" class="footer-link">{{ __('Refund Policy') }}</a></li>
                    <li><a href="#" class="footer-link">{{ __('Help Center') }}</a></li>
                </ul>
            </div>

            {{-- Contact Info --}}
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
                    <span class="footer-contact-text" dir="ltr">+966 12 345 6789</span>
                </div>
                <div class="footer-contact-item">
                    <svg class="footer-contact-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                    </svg>
                    <span class="footer-contact-text">info@wjhtak.com</span>
                </div>
            </div>
        </div>

        {{-- Footer Bottom --}}
        <div class="footer-bottom">
            <p class="footer-copyright">
                © {{ date('Y') }} {{ config('app.name', 'Wjhtak') }}. {{ __('All Rights Reserved') }}
            </p>
            <div class="footer-payment">
                <img src="{{ asset('images/payment/visa.svg') }}" alt="Visa">
                <img src="{{ asset('images/payment/mastercard.svg') }}" alt="Mastercard">
                <img src="{{ asset('images/payment/apple-pay.svg') }}" alt="Apple Pay">
                <img src="{{ asset('images/payment/mada.svg') }}" alt="Mada">
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
