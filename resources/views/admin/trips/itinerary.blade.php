@extends('layouts.app')

@section('title', __('Trip Itinerary') . ' : ' . $trip->title)


@section('content')
<style>
    .trip-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none !important;
        border-radius: 20px !important;
    }
    .trip-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .bg-soft-primary { background-color: #e7f1ff; color: #0d6efd; }
    .bg-soft-success { background-color: #135846; color: #ffffff; }
    .price-tag { font-size: 1.5rem; color: #2c3e50; }
    .location-dot { color: #0d6efd; font-size: 12px; }
    .sortable-handler { cursor: grab; padding-right: 15px; display: flex; align-items: center; }
    .sortable-ghost { opacity: 0.4; background-color: #f8f9fa !important; border: 2px dashed #0d6efd !important; }
    .itinerary-item { position: relative; }
</style>

<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.trips.index') }}">{{ __('Trips') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Itinerary') }} : {{ $trip->title }}</a></li>
        </ol>
    </div>

    <!-- <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Trip Schedule') }}</h4>
                </div>
                <div class="card-body">
                        <div class="timeline-area">
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                                    <h5 class="mb-0">
                                        <i class="fas fa-plane-departure me-2"></i> {{ $trip->title }}
                                    </h5>
                                    <span class="badge {{ $trip->is_public ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $trip->is_public ? __('Public') : __('Private') }}
                                    </span>
                                </div>

                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-7 border-end">
                                            <p class="text-muted small mb-1">{{ __('Description') }}</p>
                                            <p class="fw-bold mb-4">{{ $trip->description }}</p>

                                            <div class="row text-center mb-4">
                                                <div class="col-6 border-end">
                                                    <i class="fas fa-clock text-primary mb-1"></i>
                                                    <p class="small text-muted mb-0">{{ __('Duration') }}</p>
                                                    <h6 class="fw-bold">{{ $trip->duration }}</h6>
                                                </div>
                                                <div class="col-6">
                                                    <i class="fas fa-users text-primary mb-1"></i>
                                                    <p class="small text-muted mb-0">{{ __('Capacity') }}</p>
                                                    <h6 class="fw-bold">{{ $trip->personnel_capacity }}</h6>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded">
                                                <div class="text-center">
                                                    <span class="d-block small text-muted">{{ __('From') }}</span>
                                                    <span class="fw-bold">{{ $trip->from_city_id }}</span>
                                                </div>
                                                <i class="fas fa-long-arrow-alt-right text-primary fs-4"></i>
                                                <div class="text-center">
                                                    <span class="d-block small text-muted">{{ __('To') }}</span>
                                                    <span class="fw-bold">{{ $trip->to_city_id }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-5 ps-md-4 mt-3 mt-md-0">
                                            <div class="mb-3">
                                                <span class="text-muted small">{{ __('Price') }}</span>
                                                <div class="d-flex align-items-center">
                                                    <h3 class="text-primary fw-bold mb-0 me-2">{{ $trip->price }}</h3>
                                                    @if($trip->price_before_discount > $trip->price)
                                                        <span class="text-danger text-decoration-line-through small">{{ $trip->price_before_discount }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <hr class="my-3">

                                            <ul class="list-unstyled">
                                                <li class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted small"><i class="fas fa-chart-line me-2"></i>{{ __('Profit') }}:</span>
                                                    <span class="fw-bold text-success">+{{ $trip->profit }}</span>
                                                </li>
                                                <li class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted small"><i class="fas fa-percentage me-2"></i>{{ __('Margin') }}:</span>
                                                    <span class="badge bg-soft-info text-info">{{ $trip->percentage_profit_margin }}%</span>
                                                </li>
                                                <li class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted small"><i class="fas fa-calendar-alt me-2"></i>{{ __('Expiry') }}:</span>
                                                    <span class="text-danger small fw-bold">{{ $trip->expiry_date }}</span>
                                                </li>
                                            </ul>

                                            @if($trip->is_ad)
                                                <div class="alert alert-warning py-1 px-2 mb-0 text-center small" style="font-size: 0.75rem;">
                                                    <i class="fas fa-ad me-1"></i> Sponsored Trip
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer bg-white d-flex justify-content-between border-top-0 pb-3">
                                    <small class="text-muted">ID: #{{ $trip->company_id }}</small>
                                    <div>
                                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3">{{ __('Edit') }}</button>
                                        <button class="btn btn-primary btn-sm rounded-pill px-3">{{ __('View Details') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div> -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Trip details') }}</h4>
                </div>
                <div class="card-body">
                       <div class="timeline-area">
                            <div class="card trip-card shadow-sm mb-4">
                                <div class="card-body p-0">
                                    <div class="row g-0">
                                        <div class="col-md-3 bg-soft-primary d-flex align-items-center justify-content-center py-4" style="border-radius: 20px 0 0 20px;">
                                            <div class="text-center">
                                                <i class="fas fa-map-marked-alt fa-3x mb-2"></i>
                                                <p class="mb-0 fw-bold">{{ $trip->company->name }}</p>
                                                @if($trip->is_ad)
                                                    <span class="badge bg-warning text-dark mt-2">{{  __('AD SPONSORED')}}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 p-4">
                                            <div class="d-flex justify-content-between align-items-start mb-2">

                                                <h4 class="fw-bold text-dark mb-0"> <i class="fas fa-plane-departure me-2"></i> {{ $trip->title }}</h4>
                                                <span class="badge rounded-pill {{ $trip->is_public ? 'bg-soft-success' : 'bg-secondary' }}">
                                                    {{ $trip->is_public ?  __('Public') :  __('Private') }}
                                                </span>
                                            </div>
                                                <p class="text-muted small mb-1 mt-4">{{ __('Description') }}</p>
                                                <p class="fw-bold mb-4">{{ Str::limit($trip->description, 100) }}</p>


                                            <div class="location-flow d-flex align-items-center mb-3">
                                                <div class="text-start">
                                                    <h6 class="mb-0 fw-bold">{{ $trip->fromCity->name ?? 'N/A' }}</h6>
                                                    <small class="text-muted">{{ $trip->fromCountry->name ?? 'N/A' }}</small>
                                                </div>

                                                <div class="flex-grow-1 border-bottom border-primary border-2 mx-3 position-relative">
                                                    <i class="fas fa-plane position-absolute top-50 start-50 translate-middle bg-white px-2 text-primary"></i>
                                                </div>

                                                <div class="text-end">
                                                    <h6 class="mb-0 fw-bold">{{ $trip->toCity->name ?? 'N/A' }}</h6>
                                                    <small class="text-muted">{{ $trip->toCountry->name }}</small>
                                                </div>
                                            </div>

                                            <div class="row text-center mb-4">
                                                <div class="col-4 border-end">
                                                    <i class="fas fa-clock text-primary mb-1"></i>
                                                    <p class="small text-muted mb-0">{{ __('Duration') }}</p>
                                                    <h6 class="fw-bold">{{ $trip->duration }}</h6>
                                                </div>
                                                <div class="col-4 border-end">
                                                    <i class="fas fa-ticket-alt me-1 text-primary mb-1"></i>
                                                    <p class="small text-muted mb-0">{{ __('Tickets') }}</p>
                                                    <h6 class="fw-bold">{{ $trip->tickets }}</h6>
                                                </div>
                                                <div class="col-4">
                                                    <i class="fas fa-users text-primary mb-1"></i>
                                                    <p class="small text-muted mb-0">{{ __('Capacity') }}</p>
                                                    <h6 class="fw-bold">{{ $trip->personnel_capacity }}</h6>
                                                </div>
                                            </div>
                                        </div>




                                        <div class="col-md-3 p-4 bg-light d-flex flex-column justify-content-center text-center" style="border-radius: 0 20px 20px 0;">
                                            <div class="mb-3">
                                                <p class="text-muted small mb-0">Total Price</p>
                                                <h2 class="fw-bold text-primary mb-0">${{ number_format($trip->price, 2) }}</h2>
                                                @if($trip->price_before_discount > $trip->price)
                                                    <small class="text-danger text-decoration-line-through">${{ number_format($trip->price_before_discount, 2) }}</small>
                                                @endif
                                            </div>
                                            <ul class="list-unstyled">
                                                <li class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted small"><i class="fas fa-chart-line me-2"></i>{{ __('Profit') }}:</span>
                                                    <span class="fw-bold text-success">+{{ $trip->profit }}</span>
                                                </li>
                                                <li class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted small"><i class="fas fa-percentage me-2"></i>{{ __('Margin') }}:</span>
                                                    <span class="badge bg-soft-info text-info">{{ $trip->percentage_profit_margin }}%</span>
                                                </li>
                                                <li class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted small"><i class="fas fa-calendar-alt me-2"></i>{{ __('Expiry') }}:</span>
                                                    <span class="text-danger small fw-bold">{{ $trip->expiry_date }}</span>
                                                </li>
                                            </ul>



                                            <!-- <div class="mb-3">
                                                <div class="d-flex justify-content-between px-3 small">
                                                    <span>Profit:</span>
                                                    <span class="text-success fw-bold">{{ $trip->profit }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between px-3 small">
                                                    <span>Margin:</span>
                                                    <span class="text-info fw-bold">{{ $trip->percentage_profit_margin }}%</span>
                                                </div>
                                            </div> -->


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Add Itinerary Form -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Add Day Details') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.trips.itinerary.store', $trip->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Day Number') }}</label>
                            <input type="number" name="day_number" class="form-control" value="{{ $trip->itineraries->count() + 1 }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Title') }}</label>
                            <input type="text" name="title" class="form-control" placeholder="{{ __('e.g. Arrival in Cairo') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="{{ __('Enter day details...') }}"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">{{ __('Add Day') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Itinerary List -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Trip Schedule') }}</h4>
                </div>
                <div class="card-body">
                    @if($trip->itineraries->isEmpty())
                        <div class="alert alert-warning text-center">
                            {{ __('No itinerary days added yet.') }}
                        </div>
                    @else
                        <div class="timeline-area" id="itinerary-list">
                            @foreach($trip->itineraries as $itinerary)
                                <div class="card border mb-3 itinerary-item" data-id="{{ $itinerary->id }}">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="sortable-handler">
                                                <i class="fas fa-grip-vertical text-muted"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <h5 class="text-primary mb-1">
                                                            <span class="badge badge-primary me-2">{{ __('Day') }} {{ $itinerary->day_number }}</span>
                                                            {{ $itinerary->title }}
                                                        </h5>
                                                        <p class="mb-0 text-muted mt-2">{{ $itinerary->description }}</p>
                                                    </div>
                                                    <div class="ms-3 d-flex align-items-center">
                                                        <button type="button" class="btn btn-primary shadow btn-xs sharp me-1"
                                                                onclick="editItinerary({{ $itinerary->id }}, {{ $itinerary->day_number }}, '{{ addslashes($itinerary->title) }}', '{{ addslashes($itinerary->description) }}')">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </button>
                                                        <form action="{{ route('admin.trips.itinerary.destroy', $itinerary->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Itinerary Modal -->
<div class="modal fade" id="editItineraryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Day Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editItineraryForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Day Number') }}</label>
                        <input type="number" id="edit_day_number" name="day_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Title') }}</label>
                        <input type="text" id="edit_title" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Description') }}</label>
                        <textarea id="edit_description" name="description" class="form-control" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('itinerary-list');
        if (el) {
            Sortable.create(el, {
                animation: 150,
                handle: '.sortable-handler',
                ghostClass: 'sortable-ghost',
                onEnd: function() {
                    let order = [];
                    document.querySelectorAll('.itinerary-item').forEach(item => {
                        order.push(item.getAttribute('data-id'));
                    });

                    fetch("{{ route('admin.trips.itinerary.reorder') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order : order })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message);
                        } else {
                            toastr.error('Failed to reorder');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('Error reordering itinerary');
                    });
                }
            });
        }
    });

    function editItinerary(id, dayNumber, title, description) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_day_number').value = dayNumber;
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_description').value = description;
        new bootstrap.Modal(document.getElementById('editItineraryModal')).show();
    }

    document.getElementById('editItineraryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('edit_id').value;
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        fetch(`/admin/trips/itinerary/${id}`, {
            method: 'POST', // Trick to use PUT with FormData
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': data._token
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                toastr.error(data.message || 'Error updating itinerary');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('Connection error');
        });
    });
</script>
@endpush
@endsection
