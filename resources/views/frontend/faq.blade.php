@extends('frontend.layouts.app')

@section('title', __('FAQ'))

@section('content')
    {{-- Page Header --}}
    <section class="section" style="padding-top: calc(var(--space-24) + 60px); padding-bottom: var(--space-10); background: var(--gradient-primary);">
        <div class="container">
            <div class="text-center" style="color: white;">
                <h1 style="font-size: var(--text-4xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                    {{ __('Frequently Asked Questions') }}
                </h1>
                <p style="font-size: var(--text-lg); opacity: 0.9;">{{ __('Find answers to common questions') }}</p>
            </div>
        </div>
    </section>

    {{-- FAQ Content --}}
    <section class="section">
        <div class="container">
            <div style="max-width: 800px; margin: 0 auto;">
                {{-- Search --}}
                <div style="margin-bottom: var(--space-8);">
                    <input type="text" id="faqSearch" class="form-input" placeholder="{{ __('Search questions...') }}" style="padding-left: 48px;">
                </div>

                {{-- FAQ Accordion --}}
                <div class="accordion" data-single-open="true">
                    @forelse($questions ?? [] as $question)
                        <div class="accordion-item scroll-animate" data-question="{{ strtolower($question->question_ar . ' ' . $question->question_en) }}">
                            <button class="accordion-header">
                                <span class="accordion-title">{{ $question->question }}</span>
                                <span class="accordion-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </span>
                            </button>
                            <div class="accordion-content">
                                <div class="accordion-body">{!! nl2br(e($question->answer)) !!}</div>
                            </div>
                        </div>
                    @empty
                        {{-- Demo Questions --}}
                        @php
                            $demoQuestions = [
                                ['q' => __('How do I book a trip?'), 'a' => __('You can book a trip by browsing our available trips, selecting your preferred dates and number of travelers, and completing the checkout process.')],
                                ['q' => __('What payment methods do you accept?'), 'a' => __('We accept credit cards, debit cards, Apple Pay, and Mada. All payments are processed securely.')],
                                ['q' => __('Can I cancel my booking?'), 'a' => __('Yes, you can cancel your booking according to our cancellation policy. Please check the specific trip details for cancellation terms.')],
                                ['q' => __('Is travel insurance included?'), 'a' => __('Basic travel insurance is included with most trips. You can also opt for additional coverage during checkout.')],
                                ['q' => __('How can I contact customer support?'), 'a' => __('You can reach our 24/7 customer support via phone, email, or the contact form on our website.')],
                            ];
                        @endphp
                        @foreach($demoQuestions as $index => $item)
                            <div class="accordion-item scroll-animate delay-{{ ($index + 1) * 50 }}">
                                <button class="accordion-header">
                                    <span class="accordion-title">{{ $item['q'] }}</span>
                                    <span class="accordion-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </button>
                                <div class="accordion-content">
                                    <div class="accordion-body">{{ $item['a'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    @endforelse
                </div>

                {{-- CTA --}}
                <div class="text-center" style="margin-top: var(--space-10);">
                    <p class="text-muted" style="margin-bottom: var(--space-4);">{{ __("Didn't find what you're looking for?") }}</p>
                    <a href="{{ route('contact') }}" class="btn btn-primary">{{ __('Contact Us') }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('faqSearch');
    const items = document.querySelectorAll('.accordion-item');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }
</script>
@endpush
