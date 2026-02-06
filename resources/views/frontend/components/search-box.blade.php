{{-- Search Box Component --}}
@props(['variant' => 'default'])

<form action="{{ route('search') }}" method="GET" class="hero-search-form">
    {{-- Destination --}}
    <div class="search-field">
        <svg class="search-field-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
            <circle cx="12" cy="10" r="3"/>
        </svg>
        <div class="search-field-content">
            <label class="search-field-label">{{ __('Destination') }}</label>
            <select name="destination" class="search-field-input form-select" style="border: none; padding: 0;">
                <option value="">{{ __('Where to?') }}</option>
                @foreach($countries ?? [] as $country)
                    <option value="{{ $country->id }}">{{ $country->nicename ?? $country->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Date --}}
    <div class="search-field">
        <svg class="search-field-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
            <line x1="16" x2="16" y1="2" y2="6"/>
            <line x1="8" x2="8" y1="2" y2="6"/>
            <line x1="3" x2="21" y1="10" y2="10"/>
        </svg>
        <div class="search-field-content">
            <label class="search-field-label">{{ __('When') }}</label>
            <input
                type="date"
                name="date"
                class="search-field-input"
                placeholder="{{ __('Select date') }}"
            >
        </div>
    </div>

    {{-- Travelers --}}
    <div class="search-field">
        <svg class="search-field-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        <div class="search-field-content">
            <label class="search-field-label">{{ __('Travelers') }}</label>
            <select name="travelers" class="search-field-input form-select" style="border: none; padding: 0;">
                <option value="1">1 {{ __('Guest') }}</option>
                <option value="2" selected>2 {{ __('Guests') }}</option>
                <option value="3">3 {{ __('Guests') }}</option>
                <option value="4">4 {{ __('Guests') }}</option>
                <option value="5">5+ {{ __('Guests') }}</option>
            </select>
        </div>
    </div>

    {{-- Search Button --}}
    <button type="submit" class="btn btn-accent btn-lg">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.3-4.3"/>
        </svg>
        {{ __('Search') }}
    </button>
</form>
