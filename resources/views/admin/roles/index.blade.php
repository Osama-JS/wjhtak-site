@extends('layouts.app')

@section('title', 'Manage Roles')
@section('page-title', 'Roles')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Roles List</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roleModal" onclick="resetForm()">
                    <i class="fa fa-plus me-2"></i> Add Role
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="roleTable" class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>{{ __('ID') }}</strong></th>
                                <th><strong>{{ __('Role Name') }}</strong></th>
                                <th><strong>{{ __('Permissions') }}</strong></th>
                                <th><strong>{{ __('Count') }}</strong></th>
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

<!-- Role Modal -->
<div class="modal fade" id="roleModal">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="roleForm">
                @csrf
                <input type="hidden" id="role_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="e.g. Editor" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Permissions</label>
                        <div class="row">
                            @foreach($permissions as $permission)
                            <div class="col-md-4">
                                <div class="form-check custom-checkbox mb-3">
                                    <input type="checkbox" class="form-check-input permission-checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}">
                                    <label class="form-check-label" for="perm_{{ $permission->id }}">{{ $permission->name }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
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
@push('scripts')
<script>
    var addRoleUrl = "{{ route('admin.roles.store') }}";
    var updateRoleUrl  = "{{ route('admin.roles.update', ':id') }}";
    var editRoleUrl = "{{ route('admin.roles.edit', ':id') }}";
</script>
<script>
    let roleTable = $('#roleTable').DataTable({
        ajax: '{{ route('admin.roles.data') }}',
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'permissions' },
            { data: 'permissions_count' },
            { data: 'actions' }
        ],
        language: {
            url: '{{ app()->getLocale() == 'ar' ? "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" : "" }}'
        }
    });

    const roleModal = new bootstrap.Modal(document.getElementById('roleModal'));

    function resetForm() {
        $('#roleForm')[0].reset();
        $('#role_id').val('');
        $('.permission-checkbox').prop('checked', false);
        $('#modalTitle').text('{{ __('Add Role') }}');
        $('#saveBtn').text('{{ __('Save changes') }}');
    }

    function editRole(id) {
        url = editRoleUrl.replace(':id', id);
        $.get(url, function(data) {
            if (data.success) {
                $('#role_id').val(data.role.id);
                $('#name').val(data.role.name);
                $('.permission-checkbox').prop('checked', false);
                data.permissions.forEach(perm => {
                    $(`input[value="${perm}"]`).prop('checked', true);
                });
                $('#modalTitle').text('{{ __('Edit Role') }}');
                $('#saveBtn').text('{{ __('Update') }}');
                roleModal.show();
            }
        });
    }

    $('#roleForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#role_id').val();
        const updateId = updateRoleUrl.replace(':id', id);
        const url = id ? updateId : addRoleUrl;
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire('{{ __('Success') }}', response.message, 'success');
                    roleModal.hide();
                    roleTable.ajax.reload();
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMsg = '';
                $.each(errors, function(key, value) {
                    errorMsg += value[0] + '\n';
                });
                Swal.fire('{{ __('Error') }}', errorMsg || '{{ __('Something went wrong') }}', 'error');
            }
        });
    });

    function deleteRole(id) {
        let url = "{{ route('admin.roles.destroy', ':id') }}";
        url = url.replace(':id', id);
        Swal.fire({
            title: '{{ __('Are you sure?') }}',
            text: "{{ __('All users with this role will lose its permissions!') }}",
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
                            roleTable.ajax.reload();
                        }
                    }
                });
            }
        });
    }
</script>
@endpush
@endsection


@push('styles')
<link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
@endpush
