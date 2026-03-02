@extends('frontend.agent.layouts.agent-layout')

@section('title', __('Add New Trip'))
@section('page-title', __('Add New Trip'))

@section('content')
@push('styles')
<style>
    :root {
        --accent-soft: rgba(232, 83, 46, 0.08);
        --accent-color: #e8532e;
        --accent-hover: #d14424;
    }

    .premium-form-container {
        max-width: 1100px;
        margin: 0 auto;
    }

    .form-section-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .card-header-premium {
        padding: 24px 30px;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-header-premium i {
        width: 40px;
        height: 40px;
        background: var(--accent-soft);
        color: var(--accent-color);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .card-header-premium h5 {
        margin: 0;
        font-weight: 800;
        color: #1e293b;
        font-size: 1.05rem;
    }

    .card-body-premium {
        padding: 30px;
    }

    .label-premium {
        display: block;
        font-weight: 700;
        font-size: 0.88rem;
        color: #475569;
        margin-bottom: 10px;
    }

    .input-premium {
        width: 100%;
        padding: 14px 18px;
        border-radius: 14px;
        border: 1.5px solid #e2e8f0;
        background: #fdfdfd;
        color: #1e293b;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .input-premium:focus {
        outline: none;
        border-color: var(--accent-color);
        background: #fff;
        box-shadow: 0 0 0 4px var(--accent-soft);
    }

    /* CKEditor Custom Styles */
    .ck-editor__editable {
        min-height: 300px;
        border-radius: 0 0 14px 14px !important;
        border: 1.5px solid #e2e8f0 !important;
    }
    .ck-toolbar {
        border-radius: 14px 14px 0 0 !important;
        border: 1.5px solid #e2e8f0 !important;
        border-bottom: none !important;
        background: #f8fafc !important;
    }

    /* Chips */
    .category-chips-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
    }

    .category-chip { position: relative; cursor: pointer; }
    .category-chip input { position: absolute; opacity: 0; }
    .chip-content {
        padding: 12px 16px;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        text-align: center;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        transition: all 0.2s;
    }

    .category-chip input:checked + .chip-content {
        background: var(--accent-color);
        border-color: var(--accent-color);
        color: #fff;
    }

    /* Switch Styling */
    .switch-group {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        background: #f8fafc;
        border-radius: 14px;
        border: 1.5px solid #e2e8f0;
    }

    .form-switch-premium {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
    }
    .form-switch-premium input { opacity: 0; width: 0; height: 0; }
    .slider {
        position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1; transition: .4s; border-radius: 24px;
    }
    .slider:before {
        position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px;
        background-color: white; transition: .4s; border-radius: 50%;
    }
    input:checked + .slider { background-color: var(--accent-color); }
    input:checked + .slider:before { transform: translateX(22px); }

    /* Redesigned Actions */
    .form-actions-premium {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 16px;
        padding: 20px 0 60px;
    }

    .btn-premium {
        padding: 16px 40px;
        border-radius: 16px;
        font-weight: 800;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        border: none;
    }

    .btn-create-premium {
        background: var(--accent-color);
        color: #fff;
        box-shadow: 0 10px 25px rgba(232, 83, 46, 0.25);
    }

    .btn-create-premium:hover {
        background: var(--accent-hover);
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 15px 30px rgba(232, 83, 46, 0.35);
    }

    .btn-discard-premium {
        background: #fff;
        color: #ef4444;
        border: 2px solid #fee2e2;
        text-decoration: none;
    }

    .btn-discard-premium:hover {
        background: #fef2f2;
        border-color: #fecaca;
        transform: translateY(-2px);
    }

    .premium-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 24px;
    }
    .premium-row-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 24px;
    }

    @media (max-width: 768px) {
        .premium-row, .premium-row-3 { grid-template-columns: 1fr; }
        .form-actions-premium { flex-direction: column-reverse; }
        .btn-premium { width: 100%; justify-content: center; }
    }
</style>
@endpush

<div class="premium-form-container">
    <form action="{{ route('agent.trips.store') }}" method="POST">
        @csrf

        {{-- General Information --}}
        <div class="form-section-card">
            <div class="card-header-premium">
                <i class="fas fa-file-alt"></i>
                <h5>{{ __('General Information') }}</h5>
            </div>
            <div class="card-body-premium">
                <div class="premium-row">
                    <div>
                        <label class="label-premium">{{ __('Trip Title') }}</label>
                        <input type="text" name="title" class="input-premium" value="{{ old('title') }}" required placeholder="{{ __('e.g. Dream Escape to Maldives') }}">
                    </div>
                    <div>
                        <label class="label-premium">{{ __('Trip Categories') }}</label>
                        <div class="category-chips-grid">
                            @foreach($categories as $category)
                                <label class="category-chip">
                                    <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" {{ (collect(old('category_ids'))->contains($category->id)) ? 'checked' : '' }}>
                                    <div class="chip-content">{{ $category->name }}</div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-group-premium">
                    <label class="label-premium">{{ __('Detailed Description') }}</label>
                    <textarea id="description" name="description" class="input-premium">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Logistics & Pricing --}}
        <div class="form-section-card">
            <div class="card-header-premium">
                <i class="fas fa-map-marked-alt"></i>
                <h5>{{ __('Logistics & Pricing') }}</h5>
            </div>
            <div class="card-body-premium">
                <div class="premium-row">
                    <div>
                        <label class="label-premium">{{ __('Departure From') }}</label>
                        <select name="from_country_id" class="input-premium" required>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('from_country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label-premium">{{ __('Destination To') }}</label>
                        <select name="to_country_id" class="input-premium" required>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('to_country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="premium-row">
                    <div>
                        <label class="label-premium">{{ __('From City') }}</label>
                        <select name="from_city_id" class="input-premium" required>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('from_city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label-premium">{{ __('Destination City') }}</label>
                        <select name="to_city_id" class="input-premium" required>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('to_city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="premium-row-3">
                    <div>
                        <label class="label-premium">{{ __('Selling Price') }} (SAR)</label>
                        <input type="number" name="price" class="input-premium" value="{{ old('price') }}" required>
                    </div>
                    <div>
                        <label class="label-premium">{{ __('Original Price') }} (SAR)</label>
                        <input type="number" name="price_before_discount" class="input-premium" value="{{ old('price_before_discount') }}">
                    </div>
                    <div>
                        <label class="label-premium">{{ __('Tickets Info') }}</label>
                        <input type="text" name="tickets" class="input-premium" value="{{ old('tickets') }}" placeholder="{{ __('e.g. Economy Class') }}">
                    </div>
                </div>

                <div class="premium-row-3">
                    <div>
                        <label class="label-premium">{{ __('Max Capacity') }}</label>
                        <input type="number" name="personnel_capacity" class="input-premium" value="{{ old('personnel_capacity') }}">
                    </div>
                    <div>
                        <label class="label-premium">{{ __('Base Capacity') }}</label>
                        <input type="number" name="base_capacity" class="input-premium" value="{{ old('base_capacity') }}">
                    </div>
                    <div>
                        <label class="label-premium">{{ __('Extra Pax Price') }}</label>
                        <input type="number" name="extra_passenger_price" class="input-premium" value="{{ old('extra_passenger_price') }}">
                    </div>
                </div>

                <div class="premium-row">
                    <div>
                        <label class="label-premium">{{ __('Duration') }}</label>
                        <input type="text" name="duration" class="input-premium" value="{{ old('duration') }}" placeholder="{{ __('e.g. 5 Days') }}">
                    </div>
                    <div>
                        <label class="label-premium">{{ __('Expiry Date') }}</label>
                        <input type="date" name="expiry_date" class="input-premium" value="{{ old('expiry_date') }}" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Visibility & Status --}}
        <div class="form-section-card">
            <div class="card-header-premium">
                <i class="fas fa-cog"></i>
                <h5>{{ __('Settings & Visibility') }}</h5>
            </div>
            <div class="card-body-premium">
                <div class="premium-row">
                    <div class="switch-group">
                        <div>
                            <span class="label-premium mb-0">{{ __('Public Visibility') }}</span>
                            <small class="text-muted">{{ __('Make this trip visible to all users') }}</small>
                        </div>
                        <label class="form-switch-premium">
                            <input type="checkbox" name="is_public" value="1" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="switch-group">
                        <div>
                            <span class="label-premium mb-0">{{ __('Active Status') }}</span>
                            <small class="text-muted">{{ __('Enable or disable bookings for this trip') }}</small>
                        </div>
                        <label class="form-switch-premium">
                            <input type="checkbox" name="active" value="1" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions-premium">
            <a href="{{ route('agent.trips.index') }}" class="btn-premium btn-discard-premium">
                <i class="fas fa-times"></i> {{ __('Discard') }}
            </a>
            <button type="submit" class="btn-premium btn-create-premium">
                <i class="fas fa-paper-plane"></i> {{ __('Publish Trip') }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#description'), {
            language: '{{ app()->getLocale() }}',
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo' ]
        })
        .catch(error => { console.error(error); });
</script>
@endpush
