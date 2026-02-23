@extends('frontend.customer.layouts.customer-layout')

@section('title', __('My Favorites'))
@section('page-title', __('My Favorites'))

@push('styles')
<style>
.fav-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.fav-card {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    transition: transform .2s, box-shadow .2s;
    display: flex;
    flex-direction: column;
}

.fav-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0,0,0,.1);
}

.fav-img-wrap {
    position: relative;
    height: 180px;
    overflow: hidden;
}

.fav-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .4s;
}

.fav-card:hover .fav-img-wrap img { transform: scale(1.06); }

.fav-img-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: #94a3b8;
}

.fav-remove-btn {
    position: absolute;
    top: 10px;
    inset-inline-end: 10px;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(255,255,255,.9);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ef4444;
    font-size: .9rem;
    transition: all .2s;
    backdrop-filter: blur(4px);
}

.fav-remove-btn:hover {
    background: #ef4444;
    color: #fff;
}

.fav-body {
    padding: 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.fav-title {
    font-weight: 700;
    font-size: .95rem;
    color: #111827;
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.fav-meta {
    font-size: .78rem;
    color: #6b7280;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 12px;
}

.fav-meta span { display: flex; align-items: center; gap: 4px; }

.fav-footer {
    margin-top: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.fav-price {
    font-weight: 700;
    font-size: 1rem;
    color: var(--accent-color, #e8532e);
}

.fav-price small { font-size: .72rem; color: #9ca3af; font-weight: 400; }

.btn-view {
    padding: 7px 15px;
    background: #f1f5f9;
    border-radius: 8px;
    text-decoration: none;
    color: #374151;
    font-size: .8rem;
    font-weight: 600;
    transition: all .2s;
}

.btn-view:hover {
    background: var(--accent-color, #e8532e);
    color: #fff;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    grid-column: 1 / -1;
}

.empty-state .empty-icon { font-size: 4rem; color: #e2e8f0; margin-bottom: 16px; }
.empty-state h3 { font-size: 1.1rem; font-weight: 700; color: #374151; margin-bottom: 8px; }
.empty-state p { color: #9ca3af; font-size: .9rem; margin-bottom: 20px; }

.btn-accent-sm {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 22px;
    background: var(--accent-color, #e8532e);
    color: #fff;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 700;
    font-size: .88rem;
    transition: background .2s;
}

.btn-accent-sm:hover { background: #d04525; color: #fff; }

.fav-toast {
    position: fixed;
    bottom: 24px;
    inset-inline-start: 24px;
    background: #111827;
    color: #fff;
    padding: 12px 20px;
    border-radius: 10px;
    font-size: .88rem;
    font-weight: 600;
    display: none;
    align-items: center;
    gap: 8px;
    z-index: 9999;
    box-shadow: 0 8px 24px rgba(0,0,0,.2);
    animation: slideUp .3s ease;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
@endpush

@section('content')

@if($favorites->isEmpty())
    <div class="fav-grid">
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-heart"></i></div>
            <h3>{{ __('No favorite trips') }}</h3>
            <p>{{ __('Add trips you like to your favorites to find them here easily.') }}</p>
            <a href="{{ route('trips.index') }}" class="btn-accent-sm">
                <i class="fas fa-search"></i> {{ __('Explore Trips') }}
            </a>
        </div>
    </div>
@else
    <div class="fav-grid">
        @foreach($favorites as $fav)
            @php $trip = $fav->trip; $img = $trip?->images?->first(); @endphp
            <div class="fav-card" id="fav-card-{{ $trip?->id }}">
                <div class="fav-img-wrap">
                    @if($img)
                        <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $trip->title }}">
                    @else
                        <div class="fav-img-placeholder"><i class="fas fa-map-marked-alt"></i></div>
                    @endif

                    <button class="fav-remove-btn" onclick="removeFavorite({{ $trip?->id }}, this)" title="{{ __('Remove from favorites') }}">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
                <div class="fav-body">
                    <div class="fav-title">{{ $trip?->title }}</div>
                    <div class="fav-meta">
                        @if($trip?->toCountry)
                            <span><i class="fas fa-globe-asia"></i> {{ $trip->toCountry->name }}</span>
                        @endif
                        @if($trip?->duration_days)
                            <span><i class="fas fa-clock"></i> {{ $trip->duration_days }} {{ __('Day') }}</span>
                        @endif
                    </div>
                    <div class="fav-footer">
                        <div class="fav-price">
                            {{ number_format($trip?->price ?? 0, 0) }} {{ __('ر.س') }}
                            <small>/{{ __('للشخص') }}</small>
                        </div>
                        <a href="{{ route('trips.show', $trip?->id) }}" class="btn-view">
                            {{ __('عرض') }} <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'left' : 'right' }}"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($favorites->hasPages())
        <div style="margin-top: 24px; display: flex; justify-content: center;">
            {{ $favorites->links() }}
        </div>
    @endif
@endif

<div class="fav-toast" id="favToast">
    <i class="fas fa-check-circle" style="color:#10b981;"></i>
    <span id="favToastMsg"></span>
</div>

@endsection

@push('scripts')
<script>
const favToggleUrl = '{{ route("customer.favorites.toggle", ":id") }}';
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function removeFavorite(tripId, btn) {
    const url = favToggleUrl.replace(':id', tripId);
    btn.disabled = true;

    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.error && !data.is_favorite) {
            const card = document.getElementById('fav-card-' + tripId);
            card.style.transition = 'opacity .3s, transform .3s';
            card.style.opacity = '0';
            card.style.transform = 'scale(.9)';
            setTimeout(() => card.remove(), 300);
            showToast(data.message);
        }
    })
    .catch(() => { btn.disabled = false; });
}

function showToast(msg) {
    const t = document.getElementById('favToast');
    document.getElementById('favToastMsg').textContent = msg;
    t.style.display = 'flex';
    setTimeout(() => { t.style.display = 'none'; }, 3000);
}
</script>
@endpush
