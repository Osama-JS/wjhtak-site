@extends('frontend.layouts.app')

@section('title', __('Book Trip') . ': ' . $trip->title)

@section('content')
<section class="booking-flow-section" style="padding: var(--space-20) 0; background: #f8fafc;">
    <div class="container">
        <div class="booking-grid" style="display: grid; grid-template-columns: 1fr 380px; gap: 40px;">

            {{-- Form Column --}}
            <div class="booking-form-col">
                <div class="card" style="padding: var(--space-10); border-radius: 24px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.04);">
                    <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 8px;">{{ __('Booking Information') }}</h1>
                    <p style="color: #64748b; margin-bottom: 40px;">{{ __('Please enter the details for all travelers as they appear in their passports.') }}</p>

                    @if ($errors->any())
                        <div class="alert alert-danger" style="border-radius: 15px; margin-bottom: 30px; border: none; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1);">
                            <div style="font-weight: 700; margin-bottom: 5px;"><i class="fas fa-exclamation-circle me-2"></i> {{ __('Please correct the following errors:') }}</div>
                            <ul class="mb-0" style="font-size: 0.9rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('customer.bookings.store') }}" method="POST" id="detailedBookingForm">
                        @csrf
                        <input type="hidden" name="trip_id" value="{{ $trip->id }}">

                        {{-- Step 1: Number of Travelers --}}
                        <div class="form-section" style="margin-bottom: 40px;">
                            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                                <span style="width: 28px; height: 28px; background: var(--color-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">1</span>
                                {{ __('How many travelers?') }}
                            </h3>
                            @if (session('success'))
                                <div class="alert alert-success" style="border-radius: 12px; margin-bottom: 20px;">
                                    {{ session('success') }}
                                </div>
                            @endif
                            <div class="form-group" style="max-width: 300px;">
                                <select name="travelers_count" id="travelersCount" class="form-input" style="height: 54px; width: 100%; border: 2px solid #e2e8f0; border-radius: 12px; padding: 0 20px; font-weight: 600;" onchange="updatePassengerFields(); calculateTotal();">
                                    @for($i = 1; $i <= ($trip->personnel_capacity ?? 10); $i++)
                                        <option value="{{ $i }}" {{ old('travelers_count') == $i ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? __('Traveler') : __('Travelers') }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- Step 2: Passenger Details --}}
                        <div class="form-section">
                            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                                <span style="width: 28px; height: 28px; background: var(--color-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">2</span>
                                {{ __('Traveler Details') }}
                            </h3>
                            <div id="passengerFieldsContainer">
                                {{-- JS will populate this --}}
                            </div>
                        </div>

                        {{-- Step 3: Special Requests --}}
                        <div class="form-section" style="margin-top: 40px;">
                            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                                <span style="width: 28px; height: 28px; background: var(--color-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">3</span>
                                {{ __('Special Requests') }}
                            </h3>
                            <div class="form-group">
                                <textarea name="notes" class="form-input" rows="4" style="width: 100%; border: 2px solid #e2e8f0; border-radius: 12px; padding: 15px 20px;" placeholder="{{ __('Any dietary requirements or special assistance needed?') }}">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div style="margin-top: 50px; padding-top: 30px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn btn-primary btn-lg" style="height: 60px; padding: 0 40px; border-radius: 15px; font-weight: 800; font-size: 1.1rem; box-shadow: 0 10px 20px rgba(var(--color-primary-rgb), 0.2);">
                                {{ __('Complete Booking') }} <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Summary Column --}}
            <aside class="booking-summary-col">
                <div class="booking-summary-card" style="padding: var(--space-8); background: white; border-radius: 24px; position: sticky; top: 100px; box-shadow: 0 10px 30px rgba(0,0,0,0.04);">
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9;">{{ __('Trip Summary') }}</h3>

                    <div style="display: flex; gap: 15px; margin-bottom: 25px;">
                        @php $mainImg = $trip->images->first(); @endphp
                        <div style="width: 80px; height: 80px; border-radius: 12px; overflow: hidden; flex-shrink: 0;">
                            <img src="{{ $mainImg ? asset('storage/' . $mainImg->image_path) : asset('images/demo/trip-placeholder.jpg') }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div>
                            <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 5px; line-height: 1.3;">{{ $trip->title }}</h4>
                            <div style="font-size: 0.85rem; color: #64748b; display: flex; align-items: center; gap: 5px;">
                                <i class="fas fa-map-marker-alt"></i> {{ $trip->toCountry->name }}
                            </div>
                        </div>
                    </div>

                    <div style="background: #f8fafc; border-radius: 15px; padding: 20px; margin-bottom: 25px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                            <span style="color: #64748b; font-size: 0.9rem;">{{ __('Price per traveler') }}</span>
                            <span style="font-weight: 700;">${{ number_format($trip->price) }}</span>
                        </div>
                        <div id="extraChargesRow" style="display: none; justify-content: space-between; margin-bottom: 15px;">
                            <span style="color: #64748b; font-size: 0.9rem;">{{ __('Extra charges') }}</span>
                            <span style="font-weight: 700; color: #ef4444;" id="extraPriceValue">$0</span>
                        </div>
                        <div style="padding-top: 15px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 800; font-size: 1.1rem;">{{ __('Total Amount') }}</span>
                            <span style="font-weight: 800; font-size: 1.4rem; color: var(--color-primary);" id="totalPriceDisplay">${{ number_format($trip->price) }}</span>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div style="display: flex; gap: 12px; align-items: flex-start;">
                            <i class="fas fa-calendar-check" style="color: var(--color-primary); margin-top: 3px;"></i>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 700;">{{ __('Departure Date') }}</div>
                                <div style="font-size: 0.8rem; color: #64748b;">{{ $trip->expiry_date ? $trip->expiry_date->format('d M Y') : __('To be determined') }}</div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 12px; align-items: flex-start;">
                            <i class="fas fa-clock" style="color: var(--color-primary); margin-top: 3px;"></i>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 700;">{{ __('Duration') }}</div>
                                <div style="font-size: 0.8rem; color: #64748b;">{{ $trip->duration }}</div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 30px; padding: 20px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; display: flex; gap: 12px;">
                        <i class="fas fa-info-circle" style="color: #d97706; margin-top: 2px;"></i>
                        <p style="font-size: 0.8rem; color: #92400e; margin: 0;">{{ __('Your booking will be processed immediately after completion. Payment can be made in the next step.') }}</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .form-input:focus {
        border-color: var(--color-primary) !important;
        outline: none;
        box-shadow: 0 0 0 4px rgba(var(--color-primary-rgb), 0.1);
    }
    .passenger-card {
        background: #fff;
        border: 2px solid #f1f5f9;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 25px;
        transition: all 0.3s ease;
    }
    .passenger-card:hover {
        border-color: #e2e8f0;
        box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }
    .passenger-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 800;
        color: #1e293b;
    }
    .field-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
    }
    .field-input {
        width: 100%;
        height: 50px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0 15px;
        font-size: 0.95rem;
    }
    .field-input:focus {
        background: #fff;
        border-color: var(--color-primary);
        outline: none;
    }
    @media (max-width: 1024px) {
        .booking-grid { grid-template-columns: 1fr; }
        .booking-summary-col { order: -1; }
    }
</style>
@endpush

@push('scripts')
<script>
    function updatePassengerFields(prefillData = null) {
        const container = document.getElementById('passengerFieldsContainer');
        const count = parseInt(document.getElementById('travelersCount').value);
        const currentCount = container.querySelectorAll('.passenger-card').length;

        if (count > currentCount) {
            for (let i = currentCount + 1; i <= count; i++) {
                const passengerIndex = i - 1;
                const prefill = (prefillData && prefillData[passengerIndex]) ? prefillData[passengerIndex] : {};

                const card = document.createElement('div');
                card.className = 'passenger-card animate__animated animate__fadeInUp';
                card.innerHTML = `
                    <div class="passenger-header">
                        <span style="width: 32px; height: 32px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--color-primary);"><i class="fas fa-user"></i></span>
                        {{ __('Traveler') }} ${i}
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                        <div>
                            <label class="field-label">{{ __('Full Name') }}</label>
                            <input type="text" name="passengers[${passengerIndex}][name]" value="${prefill.name || ''}" required class="field-input" placeholder="{{ __('As in passport') }}">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <label class="field-label">{{ __('Phone') }}</label>
                                <input type="text" name="passengers[${passengerIndex}][phone]" value="${prefill.phone || ''}" required class="field-input" placeholder="05xxxxxxxx">
                            </div>
                            <div>
                                <label class="field-label">{{ __('Nationality') }}</label>
                                <input type="text" name="passengers[${passengerIndex}][nationality]" value="${prefill.nationality || ''}" required class="field-input" placeholder="Saudi">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <label class="field-label">{{ __('Passport Number') }}</label>
                                <input type="text" name="passengers[${passengerIndex}][passport_number]" value="${prefill.passport_number || ''}" required class="field-input">
                            </div>
                            <div>
                                <label class="field-label">{{ __('Passport Expiry') }}</label>
                                <input type="date" name="passengers[${passengerIndex}][passport_expiry]" value="${prefill.passport_expiry || ''}" required class="field-input">
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            }
        } else if (count < currentCount) {
            const cards = container.querySelectorAll('.passenger-card');
            for (let i = currentCount - 1; i >= count; i--) {
                cards[i].remove();
            }
        }
    }

    function calculateTotal() {
        const count = parseInt(document.getElementById('travelersCount').value);
        const basePrice = {{ $trip->price }};
        const baseCapacity = {{ $trip->base_capacity ?? 2 }};
        const extraPrice = {{ $trip->extra_passenger_price ?? 0 }};

        let total = basePrice;
        let extraTotal = 0;

        if (count > baseCapacity) {
            extraTotal = (count - baseCapacity) * extraPrice;
            total += extraTotal;
            document.getElementById('extraChargesRow').style.display = 'flex';
            document.getElementById('extraPriceValue').textContent = '$' + extraTotal.toLocaleString();
        } else {
            document.getElementById('extraChargesRow').style.display = 'none';
        }

        document.getElementById('totalPriceDisplay').textContent = '$' + total.toLocaleString();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const oldPassengers = @json(old('passengers'));
        if (oldPassengers) {
            updatePassengerFields(oldPassengers);
        } else {
            updatePassengerFields();
        }
        calculateTotal();
    });
</script>
@endpush
