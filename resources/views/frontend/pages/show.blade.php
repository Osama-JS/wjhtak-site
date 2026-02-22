@extends('frontend.layouts.app')

@section('title', $page->title)

@section('content')
    {{-- Page Header --}}
    <section class="page-header" style="position: relative; padding-top: calc(var(--space-24) + 60px); padding-bottom: var(--space-10); background: var(--color-primary); overflow: hidden;">
        @php
            $headerBg = \App\Models\Setting::get('page_header_bg');
        @endphp
        @if($headerBg)
            <div style="position: absolute; inset: 0; z-index: 0;">
                <img src="{{ asset($headerBg) }}" alt="" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.4;">
                <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, transparent, var(--color-primary));"></div>
            </div>
        @else
            <div style="position: absolute; inset: 0; background: var(--gradient-primary); z-index: 0;"></div>
        @endif

        <div class="container" style="position: relative; z-index: 1;">
            <div class="text-center" style="color: white !important;">
                <h1 style="font-size: var(--text-4xl); font-weight: var(--font-bold); margin-bottom: var(--space-4); color: white !important;">
                    {{ $page->title }}
                </h1>
            </div>

            {{-- Breadcrumb --}}
            <nav class="breadcrumb" style="justify-content: center; margin-top: var(--space-6);" aria-label="Breadcrumb">
                <span class="breadcrumb-item">
                    <a href="{{ route('home') }}" style="color: rgba(255,255,255,0.7);">{{ __('Home') }}</a>
                </span>
                <span class="breadcrumb-separator" style="color: rgba(255,255,255,0.5);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </span>
                <span class="breadcrumb-item active" style="color: white;">{{ $page->title }}</span>
            </nav>
        </div>
    </section>

    {{-- Page Content --}}
    <section class="section">
        <div class="container">
            <div class="rich-text-content" style="max-width: 900px; margin: 0 auto; line-height: 1.8; color: var(--color-text-secondary);">
                {!! $page->content !!}
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .rich-text-content h2, .rich-text-content h3, .rich-text-content h4 {
        color: var(--color-text-primary);
        margin-top: var(--space-8);
        margin-bottom: var(--space-4);
        font-weight: var(--font-bold);
    }
    .rich-text-content p {
        margin-bottom: var(--space-4);
    }
    .rich-text-content ul, .rich-text-content ol {
        margin-bottom: var(--space-6);
        padding-inline-start: var(--space-6);
    }
    .rich-text-content li {
        margin-bottom: var(--space-2);
    }
    .rich-text-content table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: var(--space-6);
    }
    .rich-text-content table td, .rich-text-content table th {
        border: 1px solid #eee;
        padding: var(--space-3);
    }
    .rich-text-content blockquote {
        border-inline-start: 4px solid var(--color-primary);
        padding: var(--space-4) var(--space-6);
        background: #f8f9fa;
        font-style: italic;
        margin-bottom: var(--space-6);
    }
</style>
@endpush
