@extends('layouts.app')

@section('title', __('Manage Permissions'))
@section('page-title', __('Permissions'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Security & Access') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Permissions') }}</a></li>
    </ol>
</div>
@endsection
@section('content')
<div class="row my-2">
    <div class="col-xl-4 col-sm-6">
        <x-stats-card
            :label="__('Total Permissions')"
            :value="$stats['total']"
            icon="fas fa-key"
        />
    </div>
    <div class="col-xl-4 col-sm-6">
        <x-stats-card
            :label="__('In Use')"
            :value="$stats['in_use']"
            icon="fas fa-check-double"
        />
    </div>
    <div class="col-xl-4 col-sm-6">
        <x-stats-card
            :label="__('Not In Use')"
            :value="$stats['not_in_use']"
            icon="fas fa-exclamation-triangle"
        />
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Permissions List') }}</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#permissionModal" onclick="resetForm()">
                    <i class="fa fa-plus me-2"></i> {{ __('Add Permission') }}
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="permissionTable" class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>{{ __('ID') }}</strong></th>
                                <th><strong>{{ __('Name') }}</strong></th>
                                <th><strong>{{ __('Created At') }}</strong></th>
                                <th><strong>{{ __('Action') }}</strong></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permission Modal -->
<div class="modal fade" id="permissionModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="permissionForm">
                @csrf
                <input type="hidden" id="permission_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Permission Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="e.g. view reports" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('styles')
<link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
@endpush
@push('scripts')
<script>
    var addPermissionsUrl = "{{ route('admin.permissions.store') }}";
    var updatePermissionsUrl  = "{{ route('admin.permissions.update', ':id') }}";
    var editPermissionsUrl = "{{ route('admin.permissions.edit', ':id') }}";
</script>
<script>
    let permissionTable = $('#permissionTable').DataTable({
        ajax: '{{ route('admin.permissions.data') }}',
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'created_at' },
            { data: 'actions' }
        ],
        language: {
            url: '{{ app()->getLocale() == 'ar' ? "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" : "" }}'
        }
    });

    const permissionModal = new bootstrap.Modal(document.getElementById('permissionModal'));

    function resetForm() {
        $('#permissionForm')[0].reset();
        $('#permission_id').val('');
        $('#modalTitle').text('{{ __('Add Permission') }}');
        $('#saveBtn').text('{{ __('Save changes') }}');
    }

    function editPermission(id) {
        url = editPermissionsUrl.replace(':id', id);
        $.get(url, function(data) {
            if (data.success) {
                $('#permission_id').val(data.permission.id);
                $('#name').val(data.permission.name);
                $('#modalTitle').text('{{ __('Edit Permission') }}');
                $('#saveBtn').text('{{ __('Update') }}');
                permissionModal.show();
            }
        });
    }

    $('#permissionForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#saveBtn');
        WJHTAKAdmin.btnLoading(btn, true);

        const id = $('#permission_id').val();
        const updatePermissionsUrlId = updatePermissionsUrl.replace(':id', id);
        const url = id ? updatePermissionsUrlId : addPermissionsUrl;
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire('{{ __('Success') }}', response.message, 'success');
                    permissionModal.hide();
                    permissionTable.ajax.reload();
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMsg = '';
                $.each(errors, function(key, value) {
                    errorMsg += value[0] + '\n';
                });
                Swal.fire('{{ __('Error') }}', errorMsg || '{{ __('Something went wrong') }}', 'error');
            },
            complete: function() {
                WJHTAKAdmin.btnLoading(btn, false);
            }
        });
    });

    function deletePermission(id) {
        let url = "{{ route('admin.permissions.destroy', ':id') }}";
        url = url.replace(':id', id);
        Swal.fire({
            title: '{{ __('Are you sure?') }}',
            text: "{{ __('You won\'t be able to revert this!') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __('Yes, delete it!') }}',
            cancelButtonText: '{{ __('Cancel') }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('{{ __('Deleted!') }}', response.message, 'success');
                            permissionTable.ajax.reload();
                        }
                    }
                });
            }
        });
    }
</script>
@endpush



@endsection




