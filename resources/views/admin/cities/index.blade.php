@extends('layouts.app')

@section('title', __('Cities Management'))

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Cities') }}</a></li>
        </ol>
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
                                    <th>{{ __('Name (En)') }}</th>
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
                    <x-forms.input-text name="name_ar" :label="__('Name (Arabic)')" required />
                    <x-forms.input-text name="name_en" :label="__('Name (English)')" required />
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
                <input type="hidden" name="id" id="edit_city_id">
                <div class="modal-body">
                    <x-forms.select name="country_id" id="edit_country_id" :label="__('Select Country')" :options="$countries" searchable required />
                    <x-forms.input-text name="name_ar" id="edit_name_ar" :label="__('Name (Arabic)')" required />
                    <x-forms.input-text name="name_en" id="edit_name_en" :label="__('Name (English)')" required />
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

@endsection

@section('scripts')
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
                { data: 'name_ar' },
                { data: 'name_en' },
                { data: 'country' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
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
                $('#edit_name_ar').val(city.name_ar);
                $('#edit_name_en').val(city.name_en);
                $('#edit_country_id').val(city.country_id).trigger('change');
                $('#edit_active').prop('checked', city.active);

                $('#editCityModal').modal('show');
            }
        });
    }

    function toggleCityStatus(id) {
        let url = "{{ route('admin.cities.toggle-status', ':id') }}".replace(':id', id);

        WJHTAKAdmin.confirm('{{ __("Do you want to toggle this city status?") }}', function() {
            $.ajax({
                url: url,
                type: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if (response.success) {
                        citiesTable.ajax.reload();
                        toastr.success(response.message);
                    }
                }
            });
        });
    }

    function deleteCity(id) {
        let url = "{{ route('admin.cities.destroy', ':id') }}".replace(':id', id);

        WJHTAKAdmin.confirm('{{ __("Are you sure you want to delete this city?") }}', function() {
            $.ajax({
                url: url,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if (response.success) {
                        citiesTable.ajax.reload();
                        toastr.success(response.message);
                    }
                }
            });
        });
    }
</script>
@endsection
