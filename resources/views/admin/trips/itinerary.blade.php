@extends('layouts.app')

@section('title', __('Trip Itinerary') . ' : ' . $trip->title)

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.trips.index') }}">{{ __('Trips') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Itinerary') }} : {{ $trip->title }}</a></li>
        </ol>
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
                        <div class="timeline-area">
                            @foreach($trip->itineraries->sortBy('day_number') as $itinerary)
                                <div class="card border mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="text-primary mb-1">
                                                    <span class="badge badge-primary me-2">{{ __('Day') }} {{ $itinerary->day_number }}</span>
                                                    {{ $itinerary->title }}
                                                </h5>
                                                <p class="mb-0 text-muted mt-2">{{ $itinerary->description }}</p>
                                            </div>
                                            <div class="ms-3">
                                                <form action="{{ route('admin.trips.itinerary.destroy', $itinerary->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></button>
                                                </form>
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
@endsection
