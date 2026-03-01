@extends('layouts.app')

@section('title', __('Add New Trip'))

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.trips.index') }}">{{ __('Trips') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Add New Trip') }}</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="fa fa-plus-circle me-2"></i>{{ __('Add New Trip') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.trips.store') }}" method="POST" id="addTripsForm">
                        @csrf
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6 border-end">
                                <div class="form-section-title mb-3">
                                    <h5 class="font-w600"><i class="fas fa-info-circle me-2 text-primary"></i> {{ __('General Information') }}</h5>
                                    <hr>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-forms.input-text name="title" :label="__('Trip Title')" required icon="fa fa-pen" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.select name="category_ids" :label="__('Categories')" :options="$categories" multiple searchable />
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label class="form-label font-w600">{{ __('Description') }} <span class="text-danger">*</span></label>
                                            <textarea id="description" name="description" class="form-control" rows="10"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.select name="company_id" :label="__('Company')" :options="$companies" optionLabel="localized_name" searchable required />
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.input-text name="duration" :label="__('Duration')" placeholder="e.g. 5 Days" icon="fa fa-clock" />
                                    </div>
                                </div>
                            </div>

                            <!-- Logistics & Pricing -->
                            <div class="col-md-6">
                                <div class="form-section-title mb-3">
                                    <h5 class="font-w600"><i class="fas fa-map-marker-alt me-2 text-primary"></i> {{ __('Logistics & Pricing') }}</h5>
                                    <hr>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-forms.select name="from_country_id" :label="__('From Country')" :options="$countries" searchable required />
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.select name="to_country_id" :label="__('To Country')" :options="$countries" searchable required />
                                    </div>
                                    <div class="col-md-12">
                                        <x-forms.select name="from_city_id" :label="__('From City')" :options="$cities" searchable required />
                                    </div>
                                    <div class="col-md-4">
                                        <x-forms.input-text name="price" :label="__('Current Price')" required icon="fa fa-dollar-sign" />
                                    </div>
                                    <div class="col-md-4">
                                        <x-forms.input-text name="price_before_discount" :label="__('Old Price')" icon="fa fa-tag" />
                                    </div>
                                    <div class="col-md-4">
                                        <x-forms.input-text name="tickets" :label="__('Tickets')" required icon="fa fa-ticket-alt" />
                                    </div>
                                    <div class="col-md-4">
                                        <x-forms.input-text name="personnel_capacity" :label="__('Max Capacity')" icon="fa fa-users" />
                                    </div>
                                    <div class="col-md-4">
                                        <x-forms.input-text name="base_capacity" :label="__('Base Capacity')" icon="fa fa-user-plus" />
                                    </div>
                                    <div class="col-md-4">
                                        <x-forms.input-text name="extra_passenger_price" :label="__('Extra Pax Price')" icon="fa fa-money-bill-wave" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-w600">{{ __('Expiry Date') }} <span class="text-danger">*</span></label>
                                        <input type="date" name="expiry_date" class="form-control" required>
                                    </div>
                                </div>

                                <div class="form-section-title mb-3 mt-4">
                                    <h5 class="font-w600"><i class="fas fa-cogs me-2 text-primary"></i> {{ __('Visibility & Status') }}</h5>
                                    <hr>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <x-forms.checkbox name="is_public" :label="__('Public')" checked type="switch" />
                                    </div>
                                    <div class="col-md-4">
                                        <x-forms.checkbox name="is_featured" :label="__('Featured')" type="switch" />
                                    </div>
                                    <div class="col-md-4">
                                        <x-forms.checkbox name="is_ad" :label="__('Advertisement')" checked type="switch" />
                                    </div>
                                    <div class="col-md-4">
                                        <x-forms.checkbox name="active" :label="__('Active')" checked type="switch" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <hr>
                            <button type="button" class="btn btn-danger light me-2" onclick="window.history.back()">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary btn-rounded px-5">{{ __('Save Trip') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
        .then(editor => {
            window.editor = editor;
        })
        .catch(error => {
            console.error(error);
        });

    $(document).ready(function() {
        // Dynamic city loading based on country could be added here if needed
        $('#from_country_id').on('change', function() {
            let countryId = $(this).val();
            if (countryId) {
                $.get("{{ route('admin.cities.by-country', ':id') }}".replace(':id', countryId), function(data) {
                    let citySelect = $('#from_city_id');
                    citySelect.empty();
                    citySelect.append('<option value="">{{ __("Select City") }}</option>');
                    $.each(data, function(key, value) {
                        citySelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                    if ($.fn.niceSelect) citySelect.niceSelect('update');
                    if ($.fn.select2) citySelect.trigger('change.select2');
                });
            }
        });
    });
</script>
@endpush
