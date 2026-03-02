@extends('layouts.app')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Staff & Users') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Manage Admins') }}</a></li>
    </ol>
</div>
@endsection

@section('content')

    <div class="row my-2">
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Total Admins')"
                :value="$stats['total']"
                icon="fas fa-users-cog"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Active')"
                :value="$stats['active']"
                icon="fas fa-user-check"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Inactive')"
                :value="$stats['inactive']"
                icon="fas fa-user-slash"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Unverified')"
                :value="$stats['unverified']"
                icon="fas fa-user-clock"
            />
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Admin Management') }}</h4>
                    @can('manage users')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal" onclick="resetForm()">
                         <i class="fa fa-plus me-2"></i> {{ __('Add Admin') }}
                     </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="users-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('Photo') }}</th>
                                    <th>{{ __('User Info') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Verification') }}</th>
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

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('User Profile') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewUserBody">
                <!-- Data loaded via AJAX -->

            </div>
        </div>
    </div>
</div>

<!-- <form action="{{ route('admin.users.store') }}" method="POST">
    @csrf

    <button type="submit" class="btn btn-primary">
        {{ __('Add User') }}
    </button>
</form> -->

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Admin') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('First Name') }}</label>
                            <input type="text" name="first_name"  class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Last Name') }}</label>
                            <input type="text" name="last_name"  class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" name="email"  class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('Code') }}</label>
                            <input type="text" name="country_code"  class="form-control" placeholder="+1">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone"  class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('City') }}</label>
                        <input type="text" name="city"  class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Status') }}</label>
                        <select name="status"  class="form-control" required>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Password') }}</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Add Admin') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Admin') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_user_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('First Name') }}</label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('Code') }}</label>
                            <input type="text" name="country_code" id="edit_country_code" class="form-control" placeholder="+1">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('City') }}</label>
                        <input type="text" name="city" id="edit_city" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Status') }}</label>
                        <select name="status" id="edit_status" class="form-control" required>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('New Password (leave blank to keep current)') }}</label>
                        <input type="password" name="password" class="form-control">
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
    var usersDataUrl = "{{ route('admin.users.data') }}";
    let updateUserUrl  = "{{ route('admin.users.update', ':id') }}";
    let toggleStatusUrlTemplate = "{{ route('admin.users.toggle-status', ':id') }}";
</script>
<script>
    let usersTable;
$(document).ready(function() {
    usersTable = $('#users-table').DataTable({
            processing: true,
            serverSide: false, // Set to true if huge data
            ajax: usersDataUrl,
            columns: [
                { data: 'photo' },
                { data: 'info' },
                { data: 'phone' },
                { data: 'status' },
                { data: 'verified' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });

        $.get("{{ route('admin.users.data') }}", function(response) {
        console.log('Full Response from Controller:', response);

        // تحقق من أن response.data موجودة
        if(response.data && Array.isArray(response.data)) {
            response.data.forEach(user => {
                console.log('User ID:', user.id);
                console.log('Name:', user.name);
                console.log('Email:', user.email);
                console.log('Phone:', user.phone);
                console.log('---'); // للفصل بين المستخدمين
            });
        } else {
            console.log('No data found or wrong JSON format');
        }
    });

        $('#addUserForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            WJHTAKAdmin.btnLoading(btn, true);

            $.ajax({
                url: "{{ route('admin.users.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        $('#addUserModal').modal('hide');
                        $('#addUserForm')[0].reset();
                        usersTable.ajax.reload(null, false);
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
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                },
                complete: function() {
                    WJHTAKAdmin.btnLoading(btn, false);
                }
            });
        });
     // Handle Edit Form Submit
        $('#editUserForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            WJHTAKAdmin.btnLoading(btn, true);

            const id = $('#edit_user_id').val();
            const url = updateUserUrl.replace(':id', id);
            const formData = $(this).serialize() + '&_method=PUT';

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#editUserModal').modal('hide');
                        usersTable.ajax.reload();
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
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                },
                complete: function() {
                    WJHTAKAdmin.btnLoading(btn, false);
                }
            });
        });

});


function viewUser(id) {
        let url = "{{ route('admin.users.show', ':id') }}";
        url = url.replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                const user = response.user;
                const html = `
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="${response.photo_url}" class="img-fluid rounded shadow mb-3" style="max-width: 150px;">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-bordered table-striped">
                                <tr><th>{{ __('First Name') }}</th><td>${user.first_name}</td></tr>
                                <tr><th>{{ __('Last Name') }}</th><td>${user.last_name}</td></tr>
                                <tr><th>{{ __('Email') }}</th><td>${user.email}</td></tr>
                                <tr><th>{{ __('Phone') }}</th><td>${user.country_code ? user.country_code + ' ' : ''}${user.phone || '---'}</td></tr>
                                <tr><th>{{ __('City') }}</th><td>${user.city || '---'}</td></tr>
                                <tr><th>{{ __('Country') }}</th><td>${user.country || '---'}</td></tr>
                                <tr><th>{{ __('Address') }}</th><td>${user.address || '---'}</td></tr>
                                <tr><th>{{ __('Gender') }}</th><td>${user.gender || '---'}</td></tr>
                                <tr><th>{{ __('Birthday') }}</th><td>${user.date_of_birth || '---'}</td></tr>
                                <tr><th>{{ __('Joined') }}</th><td>${response.created_at}</td></tr>
                            </table>
                        </div>
                    </div>
                `;
                $('#viewUserBody').html(html);
                $('#viewUserModal').modal('show');
            }
        });
    }



    function editUser(id) {
        let url = "{{ route('admin.users.show', ':id') }}";
        url = url.replace(':id', id);

        $.get(url, function(response) {
            console.log(response);

            if (response.success) {
                const user = response.user;
                $('#edit_user_id').val(user.id);
                $('#edit_first_name').val(user.first_name);
                $('#edit_last_name').val(user.last_name);
                $('#edit_email').val(user.email);
                $('#edit_country_code').val(user.country_code);
                $('#edit_phone').val(user.phone);
                $('#edit_city').val(user.city);
                $('#edit_status').val(user.status);
                $('#editUserModal').modal('show');
            }
        });
    }

    function toggleUserStatus(id) {
        const url = "{{ route('admin.users.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this user status?") }}',
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
                            usersTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function deleteUser(id) {
        let url = "{{ route('admin.users.show', ':id') }}";
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
                            usersTable.ajax.reload();
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



</script>
@endsection
