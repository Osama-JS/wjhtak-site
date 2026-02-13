@forelse($trips as $trip)
    <a href="{{ route('trips.show', $trip->id) }}" class="search-result-card">
        <div class="result-img-wrapper">
            @if($trip->images->isNotEmpty())
                <img src="{{ asset('storage/' . $trip->images->first()->image_path) }}" class="result-img">
            @else
                <div class="result-img placeholder-img"><i class="fas fa-image"></i></div>
            @endif
        </div>
        
        <div class="result-content">
            <h4 style="margin:0; color:#333;">{{ $trip->title }}</h4>
            <div style="font-size: 0.85rem; color:#777; margin-top:4px;">
                <i class="fas fa-map-marker-alt"></i> {{ $trip->toCountry->name ?? '' }}
                <span style="margin: 0 8px;">•</span>
                <span style="color:var(--primary-color); font-weight:bold;">${{ number_format($trip->price, 0) }}</span>
            </div>
        </div>
        
        <i class="fas fa-chevron-right" style="margin-left:auto; color:#ccc;"></i>
    </a>
@empty
    <div class="text-center" style="padding:40px;">
        <img src="{{ asset('images/no-results.png') }}" style="width:120px; opacity:0.5;">
        <p style="color:#999; margin-top:15px;">{{ __('No trips found for this search') }}</p>
    </div>
@endforelse

<style>
    .results-list { display: flex; flex-direction: column; gap: 10px; }
    .search-result-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 8px;
        text-decoration: none;
        color: #333;
        transition: background 0.3s;
    }
    .search-result-item:hover { background: #f8f9fa; border-color: #007bff; }
    .result-image img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 15px; /* للعربي استخدم margin-left */
    }
    .result-info h4 { margin: 0; font-size: 1rem; color: #007bff; }
    .result-info p { margin: 0; font-size: 0.85rem; color: #666; }
    .result-price { margin-left: auto; font-weight: bold; color: #28a745; }
    
    /* تنسيق للمتصفحات التي تدعم اتجاه RTL */
    [dir="rtl"] .result-image img { margin-right: 0; margin-left: 15px; }
    [dir="rtl"] .result-price { margin-left: 0; margin-right: auto; }
</style>