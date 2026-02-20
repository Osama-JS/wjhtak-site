{{-- Search Box Component - Premium Redesign --}}
@props(['variant' => 'default'])

<form action="{{ route('search') }}" method="GET" class="search-box-premium">
    {{-- Segment: Destination --}}
    <div class="search-segment custom-dropdown" data-type="destination">
        <div class="search-segment-icon">
            <i class="fas fa-map-marker-alt"></i>
        </div>
        <div class="search-segment-content dropdown-trigger">
            <label class="search-segment-label">{{ __('Destination') }}</label>
            <div class="selected-value">{{ __('Where to go?') }}</div>
            <input type="hidden" name="destination" class="hidden-input">
        </div>

        <div class="dropdown-menu-premium">
            <div class="dropdown-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="{{ __('Search destinations...') }}" class="dropdown-search-input">
            </div>
            <ul class="dropdown-options-list">
                <li class="dropdown-option" data-value="">{{ __('Where to go?') }}</li>
                @foreach($countries ?? [] as $country)
                    <li class="dropdown-option" data-value="{{ $country->id }}">
                        {{ $country->nicename ?? $country->name }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="search-segment-divider"></div>

    {{-- Segment: Date --}}
    <div class="search-segment">
        <div class="search-segment-icon">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="search-segment-content dropdown-trigger">
            <label class="search-segment-label">{{ __('Date') }}</label>
            <input type="text" name="date" class="search-segment-input date-picker" placeholder="{{ __('Add dates') }}" readonly>
        </div>
    </div>

    <div class="search-segment-divider"></div>

    {{-- Segment: Travelers --}}
    <div class="search-segment custom-dropdown" data-type="travelers">
        <div class="search-segment-icon">
            <i class="fas fa-user-friends"></i>
        </div>
        <div class="search-segment-content dropdown-trigger">
            <label class="search-segment-label">{{ __('Travelers') }}</label>
            <div class="selected-value">2 {{ __('Guests') }}</div>
            <input type="hidden" name="travelers" value="2" class="hidden-input">
        </div>

        <div class="dropdown-menu-premium">
            <ul class="dropdown-options-list">
                <li class="dropdown-option" data-value="1">1 {{ __('Guest') }}</li>
                <li class="dropdown-option active" data-value="2">2 {{ __('Guests') }}</li>
                <li class="dropdown-option" data-value="3">3 {{ __('Guests') }}</li>
                <li class="dropdown-option" data-value="4">4 {{ __('Guests') }}</li>
                <li class="dropdown-option" data-value="5">5+ {{ __('Guests') }}</li>
            </ul>
        </div>
    </div>

    {{-- Search Action --}}
    <div class="search-action">
        <button type="submit" class="btn-search-premium">
            <i class="fas fa-search"></i>
            <span>{{ __('Search') }}</span>
        </button>
    </div>
</form>
