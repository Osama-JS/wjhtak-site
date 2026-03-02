@extends('layouts.app')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Company') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Companies') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
    <div class="row my-2">
        <div class="col-xl-4 col-sm-6">
            <x-stats-card
                :label="__('Total Companies')"
                :value="$stats['total']"
                icon="fas fa-building"
            />
        </div>
        <div class="col-xl-4 col-sm-6">
            <x-stats-card
                :label="__('Active')"
                :value="$stats['active']"
                icon="fas fa-check-circle"
            />
        </div>
        <div class="col-xl-4 col-sm-6">
            <x-stats-card
                :label="__('Inactive')"
                :value="$stats['inactive']"
                icon="fas fa-times-circle"
            />
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Company Management') }}</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal" onclick="resetForm()">
                         <i class="fa fa-plus me-2"></i> {{ __('Add Company') }}
                     </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="Companys-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('Logo') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('English Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Notes') }}</th>
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

<!-- View Company Modal -->
<div class="modal fade" id="viewCompanyModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Company Profile') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewCompanyBody">
                <!-- Data loaded via AJAX -->

            </div>
        </div>
    </div>
</div>


<!-- Add Company Modal -->
<div class="modal fade" id="addCompanyModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Company') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCompanyForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <label class="form-label d-block">{{ __('Company Logo') }}</label>
                        <div class="mb-2">
                            <img id="logoPreviewAdd" src="{{ asset('images/demo/company-placeholder.jpg') }}" class="rounded-circle border" width="100" height="100" style="object-fit: cover;">
                        </div>
                        <input type="file" name="logo" class="form-control" accept="image/*" onchange="previewImage(this, 'logoPreviewAdd')">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name"  class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('English Name') }}</label>
                            <input type="text" name="en_name"  class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" name="email"  class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('Code') }}</label>
                            <input type="text" name="phone_code" class="form-control" placeholder="966">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone"  class="form-control">
                        </div>
                    </div>

                     <div class="mb-3">
                        <label class="form-label">{{ __('Notes') }}</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <x-forms.checkbox name="active" :label="__('Active status')" checked type="switch" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Add Company') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Company Modal -->
<div class="modal fade" id="editCompanyModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Company') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCompanyForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_Company_id">
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <label class="form-label d-block">{{ __('Company Logo') }}</label>
                        <div class="mb-2">
                            <img id="logoPreviewEdit" src="{{ asset('images/demo/company-placeholder.jpg') }}" class="rounded-circle border" width="100" height="100" style="object-fit: cover;">
                        </div>
                        <input type="file" name="logo" class="form-control" accept="image/*" onchange="previewImage(this, 'logoPreviewEdit')">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('English Name') }}</label>
                            <input type="text" name="en_name" id="edit_en_name" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('Code') }}</label>
                            <input type="text" name="phone_code" id="edit_phone_code" class="form-control" placeholder="966">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Notes') }}</label>
                        <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <x-forms.checkbox  id="edit_active" name="active" :label="__('Active status')" type="switch" />
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
<script>
    var CompanysDataUrl = "{{ route('admin.companies.data') }}";
    let updateCompanyUrl  = "{{ route('admin.companies.update', ':id') }}";
    let toggleStatusUrlTemplate = "{{ route('admin.companies.toggle-status', ':id') }}";
</script>
<script>
    let CompanysTable;
$(document).ready(function() {
    CompanysTable = $('#Companys-table').DataTable({
            processing: true,
            serverSide: false, // Set to true if huge data
            ajax: CompanysDataUrl,
            columns: [
                { data: 'logo', orderable: false, searchable: false },
                { data: 'name' },
                { data: 'en_name' },
                { data: 'email' },
                { data: 'phone' },
                { data: 'notes' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });

        $.get("{{ route('admin.companies.data') }}", function(response) {
        console.log('Full Response from Controller:', response);

        // تحقق من أن response.data موجودة
        if(response.data && Array.isArray(response.data)) {
            response.data.forEach(Company => {
                console.log('Company ID:', Company.id);
                console.log('Name:', Company.name);
                console.log('Email:', Company.email);
                console.log('Phone:', Company.phone);
                console.log('---'); // للفصل بين المستخدمين
            });
        } else {
            console.log('No data found or wrong JSON format');
        }
    });

        $('#addCompanyForm').on('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('admin.companies.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $('#addCompanyModal').modal('hide');
                        $('#addCompanyForm')[0].reset();
                        $('#logoPreviewAdd').attr('src', "{{ asset('images/demo/company-placeholder.jpg') }}");
                        CompanysTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => {
                            toastr.error(err[0]);
                        });
                    } else {
                        toastr.error("{{ __('Something went wrong') }}");
                    }
                }
            });
        });
     // Handle Edit Form Submit
        $('#editCompanyForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_Company_id').val();
            const url = updateCompanyUrl.replace(':id', id);
            let formData = new FormData(this);
            formData.append('_method', 'PUT');

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#editCompanyModal').modal('hide');
                        CompanysTable.ajax.reload();
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error('Something went wrong');
                    }
                }
            });
        });

});

    function editCompany(id) {
        let url = "{{ route('admin.companies.show', ':id') }}";
        url = url.replace(':id', id);
        console.log('edit', url);

        $.get(url, function(response) {
            console.log(response.Company);

            if (response.success) {
                const company = response.Company;
                $('#edit_Company_id').val(company.id);
                $('#edit_name').val(company.name);
                $('#edit_en_name').val(company.en_name);
                $('#edit_email').val(company.email);
                $('#edit_phone_code').val(company.phone_code);
                $('#edit_phone').val(company.phone);
                $('#edit_notes').val(company.notes);
                $('#edit_active').prop('checked', company.active);
                $('#logoPreviewEdit').attr('src', response.logo_url);
                $('#editCompanyModal').modal('show');
            }
        });
    }

    function togglecompanytatus(id) {
        const url = "{{ route('admin.companies.toggle-status', ':id') }}".replace(':id', id);
        console.log(url);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this Company status?") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes, Change it!") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            CompanysTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function deletecompanie(id) {
        let url = "{{ route('admin.companies.show', ':id') }}";
        url = url.replace(':id', id);
        Swal.fire({
            title: '{{ __("Delete Account?") }}',
            text: '{{ __("This action cannot be undone!") }}',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete it!") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            CompanysTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }
</script>

@endsection

@section('scripts')


<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + previewId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resetForm() {
        $('#addCompanyForm')[0].reset();
        $('#logoPreviewAdd').attr('src', "{{ asset('images/demo/company-placeholder.jpg') }}");
    }
</script>
@endsection
