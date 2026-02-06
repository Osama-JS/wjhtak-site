@extends('layouts.app')

@section('title', __('Available Flights'))
@section('page-title', __('Available Flights'))

@section('content')
<div class="row">
    <div class="col-xl-4 col-sm-12">
        <x-stats-card
            :label="__('Total Routes')"
            :value="$stats['total_routes']"
            icon="fas fa-route"
        />
    </div>
    <div class="col-xl-4 col-sm-12">
        <x-stats-card
            :label="__('Airlines')"
            :value="$stats['airlines']"
            icon="fas fa-plane"
        />
    </div>
    <div class="col-xl-4 col-sm-12">
        <x-stats-card
            :label="__('Today\'s Searches')"
            :value="$stats['today_searches']"
            icon="fas fa-search"
        />
    </div>
</div>

<div class="container-fluid">
    <!-- Search Section -->
    <div class="row">
        <div class="col-12">
            <div class="card search-card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 pt-4">
                    <h4 class="card-title"><i class="fa fa-plane me-2 text-primary"></i>{{ __('Search for Flights') }}</h4>
                </div>
                <div class="card-body">
                    <form id="flight-search-form">
                        @csrf
                        <div class="row align-items-center mb-3">
                            <div class="col-md-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="journeyType" id="oneWay" value="OneWay" checked>
                                    <label class="form-check-label" for="oneWay">{{ __('One Way') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="journeyType" id="roundTrip" value="Return">
                                    <label class="form-check-label" for="roundTrip">{{ __('Round Trip') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="journeyType" id="multiCity" value="MultiCity">
                                    <label class="form-check-label" for="multiCity">{{ __('Multi City') }}</label>
                                </div>
                            </div>
                        </div>

                        <div id="segments-container">
                            <div class="row segment-row mb-3">
                                <div class="col-md-3 mb-2">
                                    <label class="form-label font-w600">{{ __('From') }}</label>
                                    <select class="form-control airport-select" name="OriginDestinationInfo[0][airportOriginCode]" required>
                                        <option value="">{{ __('Select Origin') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label font-w600">{{ __('To') }}</label>
                                    <select class="form-control airport-select" name="OriginDestinationInfo[0][airportDestinationCode]" required>
                                        <option value="">{{ __('Select Destination') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label font-w600">{{ __('Departure Date') }}</label>
                                    <input type="date" class="form-control" name="OriginDestinationInfo[0][departureDate]" required min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3 mb-2 return-date-col d-none">
                                    <label class="form-label font-w600">{{ __('Return Date') }}</label>
                                    <input type="date" class="form-control" name="OriginDestinationInfo[0][returnDate]" min="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-2 mb-3">
                                <label class="form-label font-w600">{{ __('Class') }}</label>
                                <select class="form-control default-select" name="class">
                                    <option value="Economy">{{ __('Economy') }}</option>
                                    <option value="Business">{{ __('Business') }}</option>
                                    <option value="First">{{ __('First Class') }}</option>
                                    <option value="PremiumEconomy">{{ __('Premium Economy') }}</option>
                                </select>
                            </div>
                            <div class="col-md-1 mb-3">
                                <label class="form-label font-w600">{{ __('Adults') }}</label>
                                <input type="number" class="form-control" name="adults" value="1" min="1" max="9">
                            </div>
                            <div class="col-md-1 mb-3">
                                <label class="form-label font-w600">{{ __('Childs') }}</label>
                                <input type="number" class="form-control" name="childs" value="0" min="0" max="9">
                            </div>
                            <div class="col-md-1 mb-3">
                                <label class="form-label font-w600">{{ __('Infants') }}</label>
                                <input type="number" class="form-control" name="infants" value="0" min="0" max="9">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label font-w600">{{ __('Preferred Airline') }}</label>
                                <select class="form-control airline-select" name="airlineCode">
                                    <option value="">{{ __('All Airlines') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label font-w600">{{ __('Currency') }}</label>
                                <select class="form-control default-select" name="requiredCurrency">
                                    <option value="SAR">SAR</option>
                                    <option value="USD">USD</option>
                                    <option value="AED">AED</option>
                                    <option value="EGP">EGP</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 btn-lg shadow">
                                    <span class="search-btn-text"><i class="fa fa-search me-2"></i>{{ __('Search Flights') }}</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div id="search-results-container" class="row">
        <div class="col-12 text-center py-5 d-none" id="no-results">
            <img src="{{ asset('assets/images/no-data.svg') }}" alt="No Data" width="200" class="mb-3 opacity-50">
            <h4 class="text-muted">{{ __('No flights found matching your criteria.') }}</h4>
        </div>

        <div class="col-12" id="results-placeholder">
            <!-- Loading Skeleton would go here -->
        </div>

        <div class="col-12" id="flights-list">
            <!-- Flight cards will be injected here via JS -->
        </div>
    </div>
</div>

<!-- Passenger Details Modal -->
<div class="modal fade" id="paxDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-users me-2"></i>{{ __('Passenger Details') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="booking-form">
                    @csrf
                    <input type="hidden" name="flight_session_id" id="modal-session-id">
                    <input type="hidden" name="fare_source_code" id="modal-fare-source-code">
                    <input type="hidden" name="fareType" id="modal-fare-type">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label font-w600">{{ __('Contact Email') }}</label>
                            <input type="email" class="form-control" name="customerEmail" required placeholder="example@mail.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-w600">{{ __('Contact Phone') }}</label>
                            <input type="text" class="form-control" name="customerPhone" required placeholder="+966xxxxxxxxx">
                        </div>
                    </div>

                    <div id="passengers-inputs-container">
                        <!-- Pax inputs dynamically generated -->
                    </div>

                    <div class="modal-footer border-0 px-0 mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-success btn-lg px-5 shadow">
                            <i class="fa fa-check-circle me-2"></i>{{ __('Confirm Booking') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- PNR Confirmation Modal -->
<div class="modal fade" id="pnrModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg text-center">
            <div class="modal-body p-5">
                <div class="mb-4">
                    <i class="fa fa-check-circle text-success fs-100"></i>
                </div>
                <h2 class="font-w700 mb-2">{{ __('Booking Successful!') }}</h2>
                <p class="text-muted fs-16">{{ __('Your flight has been reserved successfully.') }}</p>

                <div class="bg-light p-4 rounded-3 my-4">
                    <h5 class="text-uppercase mb-1 text-muted">{{ __('Booking Reference (PNR)') }}</h5>
                    <h1 class="font-w800 text-black mb-0" id="pnr-value">------</h1>
                </div>

                <div class="row g-2">
                    <div class="col-12">
                        <button class="btn btn-primary w-100 btn-lg" onclick="location.reload()">{{ __('Back to Search') }}</button>
                    </div>
                    <div class="col-12">
                        <a href="{{ route('admin.bookings.flights.requests') }}" class="btn btn-outline-secondary w-100">{{ __('View My Bookings') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 for Airports
    $('.airport-select').select2({
        placeholder: '{{ __("Type airport name or code...") }}',
        minimumInputLength: 2,
        ajax: {
            url: '{{ route("admin.bookings.flights.airports") }}',
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: `${item.City} (${item.AirportCode}) - ${item.AirportName}`,
                            id: item.AirportCode
                        }
                    })
                };
            },
            cache: true
        }
    });

    // Initialize Select2 for Airlines
    $('.airline-select').select2({
        ajax: {
            url: '{{ route("admin.bookings.flights.airlines") }}',
            dataType: 'json',
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.AirLineName,
                            id: item.AirLineCode
                        }
                    })
                };
            }
        }
    });

    // Toggle Return Date
    $('input[name="journeyType"]').change(function() {
        if ($(this).val() === 'Return') {
            $('.return-date-col').removeClass('d-none');
            $('.return-date-col input').attr('required', true);
        } else {
            $('.return-date-col').addClass('d-none');
            $('.return-date-col input').attr('required', false);
        }
    });

    // Handle Search Submission
    $('#flight-search-form').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const $list = $('#flights-list');
        const formData = $(this).serialize();

        WJHTAKAdmin.btnLoading($btn, true, '{{ __("Searching...") }}');

        $list.empty();
        $('#no-results').addClass('d-none');

        $.ajax({
            url: '{{ route("admin.bookings.flights.search") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.data && response.data.data) {
                    renderFlights(response.data.data);
                } else {
                    $('#no-results').removeClass('d-none');
                }
            },
            error: function(err) {
                Swal.fire('Error', err.responseJSON.message || 'Error occurred', 'error');
            },
            complete: function() {
                WJHTAKAdmin.btnLoading($btn, false);
            }
        });
    });

    function renderFlights(data) {
        const flights = data.AirSearchResponse.AirSearchResult.FareItineraries.FareItinerary;
        const sessionId = data.AirSearchResponse.AirSearchResult.SessionId;
        const $list = $('#flights-list');

        if (!flights || flights.length === 0) {
            $('#no-results').removeClass('d-none');
            return;
        }

        const flightArray = Array.isArray(flights) ? flights : [flights];

        flightArray.forEach((itin, index) => {
            const fareInfo = itin.AirItineraryFareInfo;
            const price = fareInfo.ItinTotalFares.TotalFare.Amount;
            const currency = fareInfo.ItinTotalFares.TotalFare.CurrencyCode;
            const fareSourceCode = fareInfo.FareSourceCode;
            const isRefundable = fareInfo.IsRefundable === "Yes";
            const validatingCarrier = itin.ValidatingAirlineCode;

            let legsHtml = '';

            // Loop through each leg (Outbound, Inbound, etc.)
            const options = itin.OriginDestinationOptions;
            const optionsArray = Array.isArray(options) ? options : [options];

            optionsArray.forEach((option, legIndex) => {
                const segs = Array.isArray(option.OriginDestinationOption) ? option.OriginDestinationOption : [option.OriginDestinationOption];
                const firstSeg = segs[0].FlightSegment;
                const lastSeg = segs[segs.length - 1].FlightSegment;

                legsHtml += `
                <div class="row align-items-center text-center ${legIndex > 0 ? 'mt-4 pt-3 border-top border-light' : ''}">
                    <div class="col-12 text-start mb-2">
                        <span class="badge badge-xs light badge-secondary">${legIndex === 0 ? '{{ __("Outbound") }}' : '{{ __("Inbound") }}'}</span>
                    </div>
                    <div class="col-4">
                        <h2 class="mb-1 font-w600">${firstSeg.DepartureAirportLocationCode}</h2>
                        <div class="text-muted fs-14">${new Date(firstSeg.DepartureDateTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                        <div class="small text-muted">${new Date(firstSeg.DepartureDateTime).toLocaleDateString()}</div>
                    </div>
                    <div class="col-4">
                        <div class="flight-path">
                            <div class="path-line"></div>
                            <i class="fa fa-plane path-plane"></i>
                            <div class="mt-2 small text-muted font-w500">${segs.length - 1} {{ __("Stops") }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <h2 class="mb-1 font-w600">${lastSeg.ArrivalAirportLocationCode}</h2>
                        <div class="text-muted fs-14">${new Date(lastSeg.ArrivalDateTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                        <div class="small text-muted">${new Date(lastSeg.ArrivalDateTime).toLocaleDateString()}</div>
                    </div>
                </div>`;
            });

            const cardHtml = `
            <div class="card flight-card border-0 shadow-sm overflow-hidden mb-4 animate__animated animate__fadeInUp" style="animation-delay: ${index * 0.1}s">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-sm-2 d-flex flex-column align-items-center justify-content-center bg-light p-3">
                            <img src="https://travelnext.works/api/airlines/${validatingCarrier}.gif" alt="${validatingCarrier}" width="60" class="mb-2">
                            <span class="badge light badge-dark fs-12">${validatingCarrier}</span>
                        </div>
                        <div class="col-sm-10 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="badge ${isRefundable ? 'badge-success' : 'badge-danger'} light me-2">
                                        ${isRefundable ? '{{ __("Refundable") }}' : '{{ __("Non-Refundable") }}'}
                                    </span>
                                    <span class="badge badge-info light">{{ __("Economy") }}</span>
                                </div>
                                <h3 class="text-primary mb-0 font-w700">${price} ${currency}</h3>
                            </div>

                            ${legsHtml}

                            <hr class="my-3 opacity-10">

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    <i class="fa fa-info-circle me-1"></i> {{ __("Multiple segments may apply") }}
                                </div>
                                <button class="btn btn-primary px-4 btn-validate"
                                        data-session="${sessionId}"
                                        data-fare-source="${fareSourceCode}">
                                    {{ __('Book Now') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
            $list.append(cardHtml);
        });
    }

    // Handle Validate Fare
    $(document).on('click', '.btn-validate', function() {
        const $btn = $(this);
        const data = {
            _token: '{{ csrf_token() }}',
            session_id: $btn.data('session'),
            fare_source_code: $btn.data('fare-source')
        };

        WJHTAKAdmin.btnLoading($btn, true, '{{ __("Validating...") }}');

        $.ajax({
            url: '{{ route("admin.bookings.flights.validate") }}',
            method: 'POST',
            data: data,
            success: function(response) {
                openPaxModal($btn.data('session'), $btn.data('fare-source'), response.data);
            },
            error: function(err) {
                Swal.fire('Error', err.responseJSON.message || 'Fare no longer available', 'error');
            },
            complete: function() {
                WJHTAKAdmin.btnLoading($btn, false);
            }
        });
    });

    function openPaxModal(sessionId, fareSourceCode, valData) {
        const $container = $('#passengers-inputs-container');
        $container.empty();

        $('#modal-session-id').val(sessionId);
        $('#modal-fare-source-code').val(fareSourceCode);

        // Setup inputs based on number of adults, childs, infants in search
        const adults = parseInt($('input[name="adults"]').val()) || 1;
        const childs = parseInt($('input[name="childs"]').val()) || 0;
        const infants = parseInt($('input[name="infants"]').val()) || 0;

        let paxIndex = 0;

        const addPaxField = (type, titleOptions) => {
            const card = `
            <div class="card bg-light border-0 mb-3">
                <div class="card-header bg-transparent border-0 py-2">
                    <span class="badge badge-dark text-capitalize">${type} #${paxIndex + 1}</span>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <input type="hidden" name="passengers[${paxIndex}][type]" value="${type}">
                        <div class="col-md-2 mb-2">
                            <label class="small">{{ __('Title') }}</label>
                            <select class="form-control form-control-sm" name="passengers[${paxIndex}][title]">
                                ${titleOptions.map(t => `<option value="${t}">${t}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small">{{ __('First Name') }}</label>
                            <input type="text" class="form-control form-control-sm" name="passengers[${paxIndex}][first_name]" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small">{{ __('Last Name') }}</label>
                            <input type="text" class="form-control form-control-sm" name="passengers[${paxIndex}][last_name]" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="small">{{ __('DOB') }}</label>
                            <input type="date" class="form-control form-control-sm" name="passengers[${paxIndex}][dob]" required>
                        </div>
                        <div class="col-md-2 mb-2 text-end">
                            <label class="small">{{ __('Passport') }}</label>
                            <input type="text" class="form-control form-control-sm" name="passengers[${paxIndex}][passport_no]" placeholder="Num">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small">{{ __('Passport Country') }}</label>
                            <input type="text" class="form-control form-control-sm" name="passengers[${paxIndex}][passport_issue_country]" maxlength="2" placeholder="SA">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small">{{ __('Expiry') }}</label>
                            <input type="date" class="form-control form-control-sm" name="passengers[${paxIndex}][passport_expiry_date]">
                        </div>
                         <div class="col-md-3 mb-2">
                            <label class="small">{{ __('Nationality') }}</label>
                            <input type="text" class="form-control form-control-sm" name="passengers[${paxIndex}][nationality]" maxlength="2" placeholder="SA">
                        </div>
                    </div>
                </div>
            </div>`;
            $container.append(card);
            paxIndex++;
        };

        for (let i = 0; i < adults; i++) addPaxField('adult', ['Mr', 'Mrs', 'Ms']);
        for (let i = 0; i < childs; i++) addPaxField('child', ['Master', 'Miss']);
        for (let i = 0; i < infants; i++) addPaxField('infant', ['Master', 'Miss']);

        $('#paxDetailsModal').modal('show');
    }

    // Handle Booking Submission
    $('#booking-form').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const formData = $(this).serialize();

        WJHTAKAdmin.btnLoading($btn, true, '{{ __("Processing...") }}');

        $.ajax({
            url: '{{ route("admin.bookings.flights.book") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#paxDetailsModal').modal('hide');
                const result = response.data.BookFlightResponse.BookFlightResult;
                if (result.Success === true || result.Success === "true") {
                    $('#pnr-value').text(result.UniqueID);
                    $('#pnrModal').modal('show');
                } else {
                    Swal.fire('Booking Failed', result.Errors.Error.ErrorMessage || 'Unexpected error', 'error');
                }
            },
            error: function(err) {
                 Swal.fire('Error', err.responseJSON.message || 'Error occurred during booking', 'error');
            },
            complete: function() {
                WJHTAKAdmin.btnLoading($btn, false);
            }
        });
    });
});
</script>
@push('styles')
<link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
    .search-card {
        background: white;
        border-radius: 12px;
    }
    .airport-select, .airline-select {
        width: 100% !important;
    }
    .flight-card {
        border-radius: 12px;
        transition: transform 0.3s ease;
    }
    .flight-card:hover {
        transform: translateY(-5px);
    }
    .flight-path {
        position: relative;
        padding: 0 20px;
    }
    .path-line {
        border-top: 2px dashed #ddd;
        position: absolute;
        top: 15px;
        left: 0;
        right: 0;
        z-index: 1;
    }
    .path-plane {
        position: relative;
        z-index: 2;
        background: white;
        padding: 0 10px;
        color: #ddd;
        font-size: 20px;
    }
    .animate__animated {
        animation-duration: 0.8s;
    }
    .fs-100 { font-size: 100px; }

    /* Ensure Arabic RTL compatibility if needed */
    [dir="rtl"] .path-plane {
        transform: scaleX(-1);
    }
</style>
@endpush
@endsection

