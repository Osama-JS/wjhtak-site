@extends('frontend.layouts.app')

@section('title', __('Book Hotel') . ': ' . $hotel_name)

@section('content')
<section class="booking-flow-section" style="padding: var(--space-20) 0; background: #f8fafc;">
    <div class="container">
        <div class="booking-grid" style="display: grid; grid-template-columns: 1fr 380px; gap: 40px;">

            {{-- Form Column --}}
            <div class="booking-form-col">
                <div class="card" style="padding: var(--space-10); border-radius: 24px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.04); background: #fff;">
                    <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 8px;">{{ __('Booking Information') }}</h1>
                    <p style="color: #64748b; margin-bottom: 40px;">{{ __('Please enter the details for all guests as they appear in their passports.') }}</p>

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

                    <form action="{{ route('hotels.booking.store') }}" method="POST" id="hotelBookingForm">
                        @csrf
                        <input type="hidden" name="session_id" value="{{ $sessionId }}">
                        <input type="hidden" name="result_token" value="{{ $resultToken }}">
                        <input type="hidden" name="hotel_code" value="{{ $hotelCode }}">
                        <input type="hidden" name="hotel_name" value="{{ $hotel_name }}">
                        <input type="hidden" name="room_type_name" value="{{ $roomTypeName }}">
                        <input type="hidden" name="total_price" value="{{ $totalPrice }}">
                        <input type="hidden" name="check_in" value="{{ $searchCriteria['check_in'] }}">
                        <input type="hidden" name="check_out" value="{{ $searchCriteria['check_out'] }}">
                        <input type="hidden" name="adults" value="{{ $searchCriteria['adults'] }}">
                        <input type="hidden" name="children" value="{{ $searchCriteria['children'] }}">
                        <input type="hidden" name="rooms" value="{{ $searchCriteria['rooms'] }}">

                        {{-- Passenger Details --}}
                        <div class="form-section">
                            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                                <span style="width: 28px; height: 28px; background: var(--color-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">1</span>
                                {{ __('Guest Details') }}
                            </h3>
                            
                            <div id="passengerFieldsContainer">
                                @php $guestIndex = 0; @endphp
                                
                                {{-- Adults --}}
                                @for($i = 1; $i <= $searchCriteria['adults']; $i++)
                                    <div class="passenger-card">
                                        <div class="passenger-header">
                                            <span class="guest-icon"><i class="fas fa-user"></i></span>
                                            {{ __('Adult') }} {{ $i }}
                                            @if($guestIndex == 0)
                                                <span style="font-size: 0.7rem; background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 12px; margin-left: 10px;">{{ __('Lead Passenger') }}</span>
                                            @endif
                                        </div>
                                        <input type="hidden" name="guests[{{ $guestIndex }}][type]" value="adult">
                                        <div class="guest-form-grid">
                                            <div class="form-group">
                                                <label class="field-label">{{ __('Title') }}</label>
                                                <select name="guests[{{ $guestIndex }}][title]" class="field-input" required>
                                                    <option value="Mr" {{ old("guests.$guestIndex.title") == 'Mr' ? 'selected' : '' }}>Mr</option>
                                                    <option value="Mrs" {{ old("guests.$guestIndex.title") == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                                    <option value="Ms" {{ old("guests.$guestIndex.title") == 'Ms' ? 'selected' : '' }}>Ms</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="field-label">{{ __('First Name') }}</label>
                                                <input type="text" name="guests[{{ $guestIndex }}][first_name]" value="{{ old("guests.$guestIndex.first_name") }}" required class="field-input" placeholder="{{ __('As in passport') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="field-label">{{ __('Last Name') }}</label>
                                                <input type="text" name="guests[{{ $guestIndex }}][last_name]" value="{{ old("guests.$guestIndex.last_name") }}" required class="field-input" placeholder="{{ __('As in passport') }}">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="field-label">{{ __('Date of Birth') }}</label>
                                                <input type="date" name="guests[{{ $guestIndex }}][dob]" value="{{ old("guests.$guestIndex.dob") }}" required class="field-input">
                                            </div>
                                            <div class="form-group">
                                                <label class="field-label">{{ __('Nationality') }}</label>
                                                <select name="guests[{{ $guestIndex }}][nationality]" class="field-input" required>
                                                    <option value="SA" {{ old("guests.$guestIndex.nationality", 'SA') == 'SA' ? 'selected' : '' }}>Saudi Arabia</option>
                                                    <option value="EG" {{ old("guests.$guestIndex.nationality") == 'EG' ? 'selected' : '' }}>Egypt</option>
                                                    <option value="AE" {{ old("guests.$guestIndex.nationality") == 'AE' ? 'selected' : '' }}>United Arab Emirates</option>
                                                    <option value="JO" {{ old("guests.$guestIndex.nationality") == 'JO' ? 'selected' : '' }}>Jordan</option>
                                                    <option value="KW" {{ old("guests.$guestIndex.nationality") == 'KW' ? 'selected' : '' }}>Kuwait</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="field-label">{{ __('Passport Number') }}</label>
                                                <input type="text" name="guests[{{ $guestIndex }}][passport_number]" value="{{ old("guests.$guestIndex.passport_number") }}" class="field-input" placeholder="{{ __('Optional for now') }}">
                                            </div>
                                        </div>
                                    </div>
                                    @php $guestIndex++; @endphp
                                @endfor

                                {{-- Children --}}
                                @for($i = 1; $i <= $searchCriteria['children']; $i++)
                                    <div class="passenger-card">
                                        <div class="passenger-header">
                                            <span class="guest-icon"><i class="fas fa-child"></i></span>
                                            {{ __('Child') }} {{ $i }}
                                        </div>
                                        <input type="hidden" name="guests[{{ $guestIndex }}][type]" value="child">
                                        <div class="guest-form-grid">
                                            <div class="form-group">
                                                <label class="field-label">{{ __('Title') }}</label>
                                                <select name="guests[{{ $guestIndex }}][title]" class="field-input" required>
                                                    <option value="Mstr" {{ old("guests.$guestIndex.title") == 'Mstr' ? 'selected' : '' }}>Mstr</option>
                                                    <option value="Ms" {{ old("guests.$guestIndex.title") == 'Ms' ? 'selected' : '' }}>Ms</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="field-label">{{ __('First Name') }}</label>
                                                <input type="text" name="guests[{{ $guestIndex }}][first_name]" value="{{ old("guests.$guestIndex.first_name") }}" required class="field-input" placeholder="{{ __('As in passport') }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="field-label">{{ __('Last Name') }}</label>
                                                <input type="text" name="guests[{{ $guestIndex }}][last_name]" value="{{ old("guests.$guestIndex.last_name") }}" required class="field-input" placeholder="{{ __('As in passport') }}">
                                            </div>

                                            <div class="form-group">
                                                <label class="field-label">{{ __('Date of Birth') }}</label>
                                                <input type="date" name="guests[{{ $guestIndex }}][dob]" value="{{ old("guests.$guestIndex.dob") }}" required class="field-input">
                                            </div>
                                            <div class="form-group">
                                                <label class="field-label">{{ __('Nationality') }}</label>
                                                <select name="guests[{{ $guestIndex }}][nationality]" class="field-input" required>
                                                    <option value="SA" {{ old("guests.$guestIndex.nationality", 'SA') == 'SA' ? 'selected' : '' }}>Saudi Arabia</option>
                                                    {{-- common ones --}}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @php $guestIndex++; @endphp
                                @endfor
                            </div>
                        </div>

                        {{-- Special Requests --}}
                        <div class="form-section" style="margin-top: 40px;">
                            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                                <span style="width: 28px; height: 28px; background: var(--color-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">2</span>
                                {{ __('Special Requests') }}
                            </h3>
                            <div class="form-group">
                                <textarea name="notes" class="field-input" rows="4" style="height: auto; padding: 15px 20px;" placeholder="{{ __('Any special requirements or requests?') }}">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div style="margin-top: 50px; padding-top: 30px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn btn-primary btn-lg" style="height: 60px; padding: 0 40px; border-radius: 15px; font-weight: 800; font-size: 1.1rem; box-shadow: 0 10px 20px rgba(var(--color-primary-rgb), 0.2);">
                                {{ __('Proceed to Payment') }} <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Summary Column --}}
            <aside class="booking-summary-col">
                <div class="booking-summary-card" style="padding: var(--space-8); background: white; border-radius: 24px; position: sticky; top: 100px; box-shadow: 0 10px 30px rgba(0,0,0,0.04);">
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9;">{{ __('Booking Summary') }}</h3>

                    <div style="margin-bottom: 25px;">
                        <h4 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 5px;">{{ $hotel_name }}</h4>
                        <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 15px;">{{ $roomTypeName }}</p>
                        
                        <div class="summary-details">
                            <div class="summary-item">
                                <i class="far fa-calendar-alt"></i>
                                <div>
                                    <span>{{ __('Check-in') }}</span>
                                    <strong>{{ \Carbon\Carbon::parse($searchCriteria['check_in'])->format('d M Y') }}</strong>
                                </div>
                            </div>
                            <div class="summary-item">
                                <i class="far fa-calendar-alt"></i>
                                <div>
                                    <span>{{ __('Check-out') }}</span>
                                    <strong>{{ \Carbon\Carbon::parse($searchCriteria['check_out'])->format('d M Y') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="background: #f8fafc; border-radius: 15px; padding: 20px; margin-bottom: 25px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: #64748b; font-size: 0.9rem;">{{ __('Rooms') }}</span>
                            <span style="font-weight: 700;">{{ $searchCriteria['rooms'] }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: #64748b; font-size: 0.9rem;">{{ __('Adults') }}</span>
                            <span style="font-weight: 700;">{{ $searchCriteria['adults'] }}</span>
                        </div>
                        @if($searchCriteria['children'] > 0)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: #64748b; font-size: 0.9rem;">{{ __('Children') }}</span>
                            <span style="font-weight: 700;">{{ $searchCriteria['children'] }}</span>
                        </div>
                        @endif
                        <div style="padding-top: 15px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                            <span style="font-weight: 800; font-size: 1.1rem;">{{ __('Total Price') }}</span>
                            <span style="font-weight: 800; font-size: 1.4rem; color: var(--color-primary);">{{ number_format($totalPrice, 2) }} SAR</span>
                        </div>
                    </div>

                    <div style="padding: 15px; background: #f0fdf4; border: 1px solid #dcfce7; border-radius: 12px; display: flex; gap: 10px;">
                        <i class="fas fa-shield-alt" style="color: #16a34a; margin-top: 2px;"></i>
                        <p style="font-size: 0.8rem; color: #166534; margin: 0;">{{ __('Secure Booking: Your personal information is protected with industry-standard encryption.') }}</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .passenger-card {
        background: #fff;
        border: 2px solid #f1f5f9;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    .passenger-card:hover {
        border-color: var(--color-primary);
        box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }
    .passenger-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        font-weight: 800;
        color: #1e293b;
        font-size: 1.1rem;
    }
    .guest-icon {
        width: 32px; 
        height: 32px; 
        background: #f1f5f9; 
        border-radius: 8px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        color: var(--color-primary);
    }
    .guest-form-grid {
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); 
        gap: 20px;
    }
    .guest-form-grid .form-group:nth-child(1) {
        max-width: 120px;
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
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0 15px;
        font-size: 0.95rem;
        color: #1e293b;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .field-input:focus {
        background: #fff;
        border-color: var(--color-primary);
        outline: none;
        box-shadow: 0 0 0 4px rgba(var(--color-primary-rgb), 0.1);
    }
    .summary-details {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .summary-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }
    .summary-item i {
        color: var(--color-primary);
        margin-top: 4px;
        font-size: 1.1rem;
    }
    .summary-item div span {
        display: block;
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
    }
    .summary-item div strong {
        font-size: 0.95rem;
        color: #1e293b;
    }
    @media (max-width: 1024px) {
        .booking-grid { grid-template-columns: 1fr; }
        .booking-summary-col { order: -1; }
        .guest-form-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush
