@extends('layouts.app')

@section('title', __('Countries Management'))

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Countries') }}</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Countries List') }}</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                         <i class="fa fa-plus me-2"></i> {{ __('Add Country') }}
                     </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="countries-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('Flag') }}</th>
                                    <th>{{ __('Name (Ar)') }}</th>
                                    <th>{{ __('Name (En)') }}</th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Phone Code') }}</th>
                                    <th>{{ __('Cities') }}</th>
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

<!-- Add Country Modal -->
<div class="modal fade" id="addCountryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add New Country') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCountryForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <x-forms.input-text name="name_ar" :label="__('Name (Arabic)')" required />
                    <x-forms.input-text name="name_en" :label="__('Name (English)')" required />
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.input-text name="code" :label="__('Country Code (ISO)')" placeholder="SA" required />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input-text name="phone_code" :label="__('Phone Code')" placeholder="+966" />
                        </div>
                    </div>
                    <x-forms.file-upload name="flag" :label="__('Country Flag')" accept="image/*" />
                    <x-forms.checkbox name="active" :label="__('Active status')" checked type="switch" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Country') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Country Modal -->
<div class="modal fade" id="editCountryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Country') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCountryForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_country_id">
                <div class="modal-body">
                    <x-forms.input-text name="name_ar" id="edit_name_ar" :label="__('Name (Arabic)')" required />
                    <x-forms.input-text name="name_en" id="edit_name_en" :label="__('Name (English)')" required />
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.input-text name="code" id="edit_code" :label="__('Country Code (ISO)')" required />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input-text name="phone_code" id="edit_phone_code" :label="__('Phone Code')" />
                        </div>
                    </div>
                    <x-forms.file-upload name="flag" id="edit_flag" :label="__('Country Flag')" accept="image/*" preview />
                    <x-forms.checkbox name="active" id="edit_active" :label="__('Active status')" type="switch" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update Country') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let countriesTable;
    const countriesDataUrl = "{{ route('admin.countries.data') }}";

    $(document).ready(function() {
        // Initialize DataTable
        countriesTable = $('#countries-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: countriesDataUrl,
            columns: [
                { data: 'flag' },
                { data: 'name_ar' },
                { data: 'name_en' },
                { data: 'code' },
                { data: 'phone_code' },
                { data: 'cities_count' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });

        // Add Country Form Submit
        $('#addCountryForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.countries.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#addCountryModal').modal('hide');
                        $('#addCountryForm')[0].reset();
                        countriesTable.ajax.reload();
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

        // Edit Country Form Submit
        $('#editCountryForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_country_id').val();
            let url = "{{ route('admin.countries.update', ':id') }}".replace(':id', id);
            let formData = new FormData(this);

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#editCountryModal').modal('hide');
                        countriesTable.ajax.reload();
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

    function editCountry(id) {
        let url = "{{ route('admin.countries.show', ':id') }}".replace(':id', id);

        $.get(url, function(response) {
            if (response.success) {
                const country = response.country;
                $('#edit_country_id').val(country.id);
                $('#edit_name_ar').val(country.name_ar);
                $('#edit_name_en').val(country.name_en);
                $('#edit_code').val(country.code);
                $('#edit_phone_code').val(country.phone_code);
                $('#edit_active').prop('checked', country.active);

                // Show current flag
                if (country.flag) {
                    $('#editCountryForm .current-image-preview img').attr('src', response.flag_url);
                    $('#editCountryForm .current-image-preview').show();
                } else {
                    $('#editCountryForm .current-image-preview').hide();
                }

                $('#editCountryModal').modal('show');
            }
        });
    }

    function toggleCountryStatus(id) {
        let url = "{{ route('admin.countries.toggle-status', ':id') }}".replace(':id', id);

        WJHTAKAdmin.confirm('{{ __("Do you want to toggle this country status?") }}', function() {
            $.ajax({
                url: url,
                type: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if (response.success) {
                        countriesTable.ajax.reload();
                        toastr.success(response.message);
                    }
                }
            });
        });
    }

    function deleteCountry(id) {
        let url = "{{ route('admin.countries.destroy', ':id') }}".replace(':id', id);

        WJHTAKAdmin.confirm('{{ __("Are you sure you want to delete this country? This will affect related cities and data!") }}', function() {
            $.ajax({
                url: url,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if (response.success) {
                        countriesTable.ajax.reload();
                        toastr.success(response.message);
                    }
                }
            });
        });
    }
</script>
@endsection
