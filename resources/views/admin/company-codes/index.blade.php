@extends('layouts.app')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Company') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Company Codes') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
    <div class="row my-2">
        <div class="col-xl-4 col-sm-6">
            <x-stats-card
                :label="__('Total Company Codes')"
                :value="$stats['total']"
                icon="fas fa-ticket-alt"
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

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('Company Codes')}}</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCodeModal">
                <i class="fa fa-plus"></i> {{ __('Add Code')}}
            </button>
        </div>

        <div class="card-body">
            <table id="codes-table" class="display w-100">
                <thead>
                    <tr>
                        <th>{{ __('Company')}}</th>
                        <th>{{ __('Code')}}</th>
                        <th>{{ __('Type')}}</th>
                        <th>{{ __('Value')}}</th>
                        <th>{{ __('Status')}}</th>
                        <th>{{ __('Actions')}}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCodeModal">
    <div class="modal-dialog">
        <form id="addCodeForm"  class="modal-content">
            @csrf
            <div class="modal-header">
                <h5>{{ __('Add Company Code') }}</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label>{{ __('Company')}}</label>
                    <select name="company_id" class="form-control" required>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>{{ __('Code')}}</label>
                    <input type="text" name="code" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>{{ __('Type') }}</label>
                    <select name="type" class="form-control">
                        <option value="fixed">{{ __('Fixed')}}</option>
                        <option value="percentage">{{ __('Percentage')}}</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>{{ __('Value')}}</label>
                    <input type="number" step="0.01" name="value" class="form-control">
                </div>

                <div class="mb-3">
                    <label>{{ __('Status')}}</label>
                    <select name="active" class="form-control">
                        <option value="1">{{ __('Active')}}</option>
                        <option value="0">{{ __('Inactive')}}</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close')}}</button>
                <button class="btn btn-primary">{{ __('Save')}}</button>
            </div>
        </form>
    </div>
</div>
<!-- Edit code Modal -->
<div class="modal fade" id="editcodeModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Company Code') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editcodeForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_code_id">
                <div class="modal-body">
                    <div class="mb-3">
                    <label>{{ __('Company')}}</label>
                    <select name="company_id" id="edit_company_id" class="form-control" required>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('Code')}}</label>
                        <input type="text" name="code" id="edit_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('Type')}}</label>
                        <select name="type" id="edit_type" class="form-control">
                            <option value="fixed">{{ __('Fixed')}}</option>
                            <option value="percentage">{{ __('Percentage')}}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>{{ __('Value')}}</label>
                        <input type="number" step="0.01" name="value" id="edit_value" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>{{ __('Status')}}</label>
                        <select name="active" id="edit_active" class="form-control">
                            <option value="1">{{ __('Active')}}</option>
                            <option value="0">{{ __('Inactive')}}</option>
                        </select>
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

    let updatecodesUrl  = "{{ route('admin.company-codes.update', ':id') }}";
    let toggleStatusUrlTemplate = "{{ route('admin.company-codes.toggle-status', ':id') }}";
    let companyCodesTable;
    $(document).ready(function() {
         companyCodesTable = $('#codes-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('admin.company-codes.data') }}",
                type: 'GET',
                error: function(xhr) {
                    console.log('Ajax Error:', xhr.responseText); // لاظهار الخطأ إذا لم ينجح
                }
            },
            columns: [
                { data: 'company' },
                { data: 'code' },
                { data: 'type' },
                { data: 'value' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });




        $('#addCodeForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.company-codes.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        console.log(response);
                        $('#addCodeModal').modal('hide');
                        $('#addCodeForm')[0].reset();
                        companyCodesTable.ajax.reload(null, false);
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
                        toastr.error('Something went wrong');
                    }
                }
            });
        });



        // Handle Edit Form Submit
        $('#editcodeForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_code_id').val();
            const url = updatecodesUrl.replace(':id', id);
            const formData = $(this).serialize() + '&_method=PUT';

            $.ajax({
                url:url,
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#editcodeModal').modal('hide');
                        companyCodesTable.ajax.reload();
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

    // function editCode(id) {
    //     let url = "{{ route('admin.company-codes.show', ':id') }}";
    //     url = url.replace(':id', id);
    //     $.get(url, function (res) {
    //         let c = res.Company_Codes;
    //         $('#edit_code_id').val(c.id);
    //         $('#edit_company_id').val(c.company_id);
    //         $('#edit_code').val(c.code);
    //         $('#edit_type').val(c.type);
    //         $('#edit_value').val(c.value);
    //         $('#editModal').modal('show');
    //     });
    // }
    // function editCode(id) {
    //     let url = "{{ route('admin.company-codes.show', ':id') }}";
    //     url = url.replace(':id', id);
    //     console.log(url);
    //     $.get(url, function(response) {
    //         console.log(response);

    //         if (response.success) {
    //             let c = response.Company_Codes;
    //             $('#edit_code_id').val(c.id);
    //             $('#edit_company_id').val(c.company_id);
    //             $('#edit_code').val(c.code);
    //             $('#edit_type').val(c.type);
    //             $('#edit_value').val(c.value);
    //             $('#editcodeModal').modal('show');
    //         }
    //     });
    // }
    function editCode(id) {
        let url = "{{ route('admin.company-codes.show', ':id') }}".replace(':id', id);
        console.log('URL:', url);

        $.get(url, function(response) {
            console.log('Response:', response);

            if (response.success) {
                let c = response.Company_Codes;
                $('#edit_code_id').val(c.id);
                $('#edit_company_id').val(c.company_id);
                $('#edit_code').val(c.code);
                $('#edit_type').val(c.type);
                $('#edit_value').val(c.value);
                $('#editcodeModal').modal('show');
            } else {
                toastr.error('Could not load code data');
            }
        }).fail(function(xhr) {
            console.log('AJAX Error:', xhr.responseText);
            toastr.error('Failed to fetch code data');
        });
    }

    // function toggleCodeStatus(id) {
    //     $.post(`/admin/company-codes/${id}/toggle-status`,
    //         {_token: csrf_token},
    //         function () {
    //             companyCodesTable.ajax.reload();
    //             toastr.success('Status changed');
    //         }
    //     );
    // }
    function toggleCodeStatus(id) {
        const url = "{{ route('admin.company-codes.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this company Codes status?") }}',
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
                            companyCodesTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function deleteCode(id) {
        let url = "{{ route('admin.company-codes.destroy', ':id') }}";
        url = url.replace(':id', id);
        Swal.fire({
            title: '{{ __("Delete code??") }}',
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
                            companyCodesTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }
    // function deleteCode(id) {
    //     if (!confirm('Delete this code?')) return;

    //     $.ajax({
    //         url: `/admin/company-codes/${id}`,
    //         type: 'DELETE',
    //         data: {_token: csrf_token},
    //         success: function () {
    //             companyCodesTable.ajax.reload();
    //             toastr.success('Deleted');
    //         }
    //     });
    // }


</script>
@endsection
