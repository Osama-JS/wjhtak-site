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
                            <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
                        </svg>
                    </a>
                    <a href="{{$instagramUrl}}" aria-label="{{ __('Instagram') }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                        </svg>
                    </a>
                    <a href="#" aria-label="{{ __('YouTube') }}">
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
                $contactPhone = \App\Models\Setting::get('contact_phone');
                $contactEmail = \App\Models\Setting::get('contact_email');
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
                {{-- Visa --}}
                <svg width="40" height="25" viewBox="0 0 45 15" fill="none"><path d="M17.443.243l-2.4 14.514h-3.837L8.805.243h3.838l.6 4.37h2.822l.538-4.37h3.84zM32.89 10.372c0 2.821-4.368 2.946-4.368 4.605 0 .54.499.982 1.54.982 1.346 0 2.503-.352 3.424-.92l.635 2.895c-1.02.491-2.485.882-4.144.882-3.614 0-6.173-1.89-6.173-4.823 0-2.82 4.385-3.003 4.385-4.605 0-.52-.519-.982-1.712-.982-1.154 0-2.193.303-2.923.702l-.654-2.921c.884-.39 2.308-.76 3.933-.76 3.48 0 6.057 1.833 6.057 4.945M22.04 14.757l-1.635-14.514h4.143l1.636 14.514h-4.144zM44.757.243l-3.327 14.514h-3.578L36.012.243h3.913l.865 6.368 2.057-6.368h3.91z" fill="#1434CB"/></svg>
                {{-- Mastercard --}}
                <svg width="35" height="25" viewBox="0 0 24 15" fill="none"><circle cx="7.5" cy="7.5" r="7.5" fill="#EB001B"/><circle cx="16.5" cy="7.5" r="7.5" fill="#F79E1B"/><path d="M12 1.402a7.472 7.472 0 012.916 6.098c0 2.39-1.116 4.52-2.863 5.903a7.473 7.473 0 01-3.027-5.903c0-2.443 1.168-4.615 2.974-5.998z" fill="#FF5F00"/></svg>
                {{-- Apple Pay --}}
                <svg width="40" height="25" viewBox="0 0 50 20" fill="none"><path d="M7.74 3.7c.94-1.12 1.55-2.67 1.38-4.22-1.34.05-2.95.88-3.9 1.99-.86.99-1.6 2.58-1.4 4.09 1.48.11 3-.74 3.92-1.86" fill="#000"/><path d="M10.15 13.56c-.05 3.39 2.95 4.54 3 4.57s-.47 1.62-1.58 3.23c-1 1.46-2.03 2.93-3.66 2.93s-2.09-.99-3.95-.99-2.39.96-3.89 1.02c-1.57.06-2.73-1.61-3.73-3.06-2.05-2.96-3.62-8.36-1.52-12 1.04-1.8 2.9-2.94 4.93-2.97 1.55-.03 3.01 1.04 3.96 1.04s2.72-1.28 4.56-1.09c.77.03 2.93.31 4.31 2.33-.12.06-2.58 1.44-2.53 4.92" fill="#000"/><text x="22" y="16" font-family="Arial" font-weight="bold" font-size="14" fill="#000">Pay</text></svg>
                {{-- Mada --}}
                <svg width="40" height="25" viewBox="0 0 40 15" fill="none"><rect width="40" height="15" rx="2" fill="#004A97"/><text x="5" y="11" font-family="Arial" font-weight="bold" font-size="10" fill="white">mada</text></svg>
                {{-- Tamara --}}
                <div class="payment-badge tamara">
                    <span style="background: #ffc20e; color: black; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 10px;">tamara</span>
                </div>
                {{-- Tabby --}}
                <div class="payment-badge tabby">
                    <span style="background: #3affbe; color: black; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 10px;">tabby</span>
                </div>
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
