@extends('layouts.app')

@section('title', __('Cities Management'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Locations') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Cities') }}</a></li>
    </ol>
</div>
@endsection

@section('content')

    <div class="row my-2">
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Total Cities')"
                :value="$stats['total']"
                icon="fas fa-city"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Active')"
                :value="$stats['active']"
                icon="fas fa-check-circle"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Inactive')"
                :value="$stats['inactive']"
                icon="fas fa-times-circle"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('In Use (Countries)')"
                :value="$stats['countries_count']"
                icon="fas fa-globe"
            />
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('Cities List') }}</h4>
                    <div class="d-flex align-items-center mt-2 mt-sm-0">
                        <select id="country-filter" class="form-select me-2" style="width: 200px;">
                            <option value="">{{ __('Filter by Country') }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCityModal">
                             <i class="fa fa-plus me-2"></i> {{ __('Add City') }}
                         </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="cities-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name (Ar)') }}</th>
                                    <th>{{ __('Country') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add City Modal -->
<div class="modal fade" id="addCityModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add New City') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCityForm">
                @csrf
                <div class="modal-body">
                    <x-forms.select name="country_id" :label="__('Select Country')" :options="$countries" searchable required />
                    <x-forms.input-text name="title" :label="__('Name (Arabic)')" required />
                    <x-forms.checkbox name="active" :label="__('Active status')" checked type="switch" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save City') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit City Modal -->
<div class="modal fade" id="editCityModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit City') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCityForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_city_id">
                <div class="modal-body">
                    <x-forms.select name="country_id" id="edit_country_id" :label="__('Select Country')" :options="$countries" searchable required />
                    <x-forms.input-text name="title" id="edit_title" :label="__('Name (Arabic)')" required />
                    <x-forms.checkbox name="active" id="edit_active" :label="__('Active status')" type="switch" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update City') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let citiesTable;
    const citiesDataUrl = "{{ route('admin.cities.data') }}";

    $(document).ready(function() {
        // Initialize DataTable
        citiesTable = $('#cities-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: citiesDataUrl,
                data: function(d) {
                    d.country_id = $('#country-filter').val();
                }
            },
            columns: [
                { data: 'id' },
                { data: 'title' },
                { data: 'country' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });

            $.get(citiesDataUrl, { country_id: $('#country-filter').val() }, function(response) {
                console.log(response);
            });

        // Filter change
        $('#country-filter').on('change', function() {
            citiesTable.ajax.reload();
        });

        // Add City Form Submit
        $('#addCityForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('admin.cities.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#addCityModal').modal('hide');
                        $('#addCityForm')[0].reset();
                        citiesTable.ajax.reload(null,false);
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                }
            });
        });

        // Edit City Form Submit
        $('#editCityForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_city_id').val();
            let url = "{{ route('admin.cities.update', ':id') }}".replace(':id', id);

            $.ajax({
                url: url,
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#editCityModal').modal('hide');
                        citiesTable.ajax.reload();
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                }
            });
        });
    });

    function editCity(id) {
        let url = "{{ route('admin.cities.show', ':id') }}".replace(':id', id);

        $.get(url, function(response) {
            if (response.success) {
                const city = response.city;
                $('#edit_city_id').val(city.id);
                $('#edit_title').val(city.name_ar);
                $('#edit_country_id').val(city.country_id).trigger('change');
                $('#edit_active').prop('checked', city.active);

                $('#editCityModal').modal('show');
            }
        });
    }

    function toggleCityStatus(id) {
        let url = "{{ route('admin.cities.toggle-status', ':id') }}".replace(':id', id);
         Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this city status?") }}',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, toggle it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            if (typeof countriesTable !== 'undefined') {
                                citiesTable.ajax.reload(null, false);
                            }
                            Swal.fire('{{ __("Updated!") }}', response.message, 'success'); // عرض رسالة نجاح
                        } else {
                            Swal.fire('{{ __("Error!") }}', response.message || '{{ __("Something went wrong") }}', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong") }}', 'error');
                    }
                });
            }
        });
    }

    function deleteCity(id) {
        let url = "{{ route('admin.cities.destroy', ':id') }}".replace(':id', id);

        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This will delete the city and related data!") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            if (typeof countriesTable !== 'undefined') {
                                countriesTable.ajax.reload();
                            }
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message || '{{ __("Something went wrong") }}');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || '{{ __("Something went wrong") }}');
                    }
                });
            }
        });
    }
</script>

@endsection

@section('scripts')

@endsection
