@extends('layouts.app')

@section('title', __('Countries Management'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Locations') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Countries') }}</a></li>
    </ol>
</div>
@endsection

@section('content')

    <div class="row my-2">
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Total Countries')"
                :value="$stats['total']"
                icon="fas fa-globe"
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
                :label="__('With Cities')"
                :value="$stats['with_cities']"
                icon="fas fa-city"
            />
        </div>
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
                                    <th>{{ __('Landmark') }}</th>
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
                    <x-forms.input-text name="name" :label="__('Name (Arabic)')" required />
                    <x-forms.input-text name="nicename" :label="__('Name (English)')" required />
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.input-text name="numcode" :label="__('Country Code (ISO)')" placeholder="SA" required />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input-text name="phonecode" :label="__('Phone Code')" placeholder="+966" />
                        </div>
                    </div>
                    <x-forms.file-upload name="flag" :label="__('Country Flag')" accept="image/*" />
                    <x-forms.file-upload name="landmark_image" :label="__('Landmark Image')" accept="image/*" />
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
                <input type="hidden"  id="edit_country_id" >
                <div class="modal-body">
                    <x-forms.input-text  id="edit_name" name="name" :label="__('Name (Arabic)')" required />
                    <x-forms.input-text  id="edit_nicename" name="nicename" :label="__('Name (English)')" required />
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.input-text  id="edit_numcode" name="numcode" :label="__('Country Code (ISO)')" required />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input-text  id="edit_phonecode" name="phonecode" :label="__('Phone Code')" />
                        </div>
                    </div>
                    <x-forms.file-upload  id="edit_flag" name="flag" :label="__('Country Flag')" accept="image/*" preview />
                    <x-forms.file-upload  id="edit_landmark_image" name="landmark_image" :label="__('Landmark Image')" accept="image/*" preview />
                    <x-forms.checkbox  id="edit_active" name="active" :label="__('Active status')" type="switch" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update Country') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    let countriesTable;
    const countriesDataUrl = "{{ route('admin.countries.data') }}";
    const urlstore = "{{ route('admin.countries.store') }}";

    $(document).ready(function() {
        // Initialize DataTable
        countriesTable = $('#countries-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: countriesDataUrl,
            columns: [
                { data: 'flag' },
                { data: 'landmark' },
                { data: 'name' },
                { data: 'nicename' },
                { data: 'numcode' },
                { data: 'phonecode' },
                { data: 'cities_count' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });

        // Add Country Form Submit
       $('#addCountryModal').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData($('#addCountryForm')[0]); // يدعم الملفات

            $.ajax({
                url: "{{ route('admin.countries.store') }}",
                type: "POST",
                data: formData,
                processData: false, // لمنع تحويل البيانات إلى سلسلة نصية
                contentType: false, // لمنع تغيير Content-Type
                success: function(response) {
                    if (response.success) {
                        $('#addCountryModal').modal('hide');
                        $('#addCountryForm')[0].reset();
                        if (typeof countriesTable !== 'undefined') {
                            countriesTable.ajax.reload();
                        }
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
            console.log(url);
            const formElement = $('#editCountryForm')[0];
            if (!formElement) {
                console.error("Form element not found!");
                return;
            }

            let formData = new FormData(formElement);

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#editCountryModal').modal('hide');
                        if (typeof countriesTable !== 'undefined') {
                            countriesTable.ajax.reload();
                        }
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
        console.log(url);
        $.get(url, function(response) {
            if (response.success) {
                const country = response.country;
                console.log(country);
                $('#edit_country_id').val(country.id);
                $('#edit_name').val(country.name);
                $('#edit_nicename').val(country.nicename);
                $('#edit_numcode').val(country.numcode);
                $('#edit_phonecode').val(country.phonecode);
                $('#edit_active').prop('checked', country.active);

                // Show current landmark image
                if (country.landmark_image) {
                    $('#editCountryForm .landmark_image-preview img').attr('src', response.landmark_image_url);
                    $('#editCountryForm .landmark_image-preview').show();
                } else {
                    $('#editCountryForm .landmark_image-preview').hide();
                }

                $('#editCountryModal').modal('show');
            }
        });
    }

    // function toggleCountryStatus(id) {
    //     let url = "{{ route('admin.countries.toggle-status', ':id') }}".replace(':id', id);

    //     WJHTAKAdmin.confirm('{{ __("Do you want to toggle this country status?") }}', function() {
    //         $.ajax({
    //             url: url,
    //             type: "POST",
    //             data: { _token: "{{ csrf_token() }}" },
    //             success: function(response) {
    //                 if (response.success) {
    //                     countriesTable.ajax.reload();
    //                     toastr.success(response.message);
    //                 }
    //             }
    //         });
    //     });



    // }
    function toggleCountryStatus(id) {
        let url = "{{ route('admin.countries.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this country status?") }}',
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
                                countriesTable.ajax.reload();
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

    // function deleteCountry(id) {
    //     let url = "{{ route('admin.countries.destroy', ':id') }}".replace(':id', id);
    //     console.log(url)
    //     WJHTAKAdmin.confirm('{{ __("Are you sure you want to delete this country? This will affect related cities and data!") }}', function() {
    //         $.ajax({
    //             url: url,
    //             type: "DELETE",
    //             data: { _token: "{{ csrf_token() }}" },
    //             success: function(response) {
    //                 if (response.success) {
    //                     countriesTable.ajax.reload();
    //                     toastr.success(response.message);
    //                 }
    //                 else {
    //                     toastr.error(response.message || '{{ __("Something went wrong") }}');
    //                 }
    //             }
    //         });
    //     });
    // }
    function deleteCountry(id) {
        let url = "{{ route('admin.countries.destroy', ':id') }}".replace(':id', id);

        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This will delete the country and related data!") }}',
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

