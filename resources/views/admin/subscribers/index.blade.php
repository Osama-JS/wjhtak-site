@extends('layouts.app')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Subscriber Management') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('All Subscribers') }}</a></li>
    </ol>
</div>
@endsection

@section('content')

    <div class="row my-2">
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Total Subscribers')"
                :value="$stats['total']"
                icon="fas fa-users"
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
                    <h4 class="card-title">{{ __('Subscriber Management') }}</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubscriberModal" onclick="resetForm()">
                         <i class="fa fa-plus me-2"></i> {{ __('Add Subscriber') }}
                     </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="subscribers-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('Photo') }}</th>
                                    <th>{{ __('Info') }}</th>
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

<!-- Add Subscriber Modal -->
<div class="modal fade" id="addSubscriberModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Subscriber') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSubscriberForm">
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
                            <input type="text" name="country_code"  class="form-control" placeholder="+966">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone"  class="form-control">
                        </div>
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
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Add Subscriber') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Subscriber Modal -->
<div class="modal fade" id="editSubscriberModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Subscriber') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSubscriberForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_subscriber_id">
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
                            <input type="text" name="country_code" id="edit_country_code" class="form-control">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                        </div>
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

@endsection

@section('scripts')
<script>
    let subscribersTable;
    $(document).ready(function() {
        subscribersTable = $('#subscribers-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.subscribers.data') }}",
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

        $('#addSubscriberForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            WJHTAKAdmin.btnLoading(btn, true);

            $.ajax({
                url: "{{ route('admin.subscribers.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        $('#addSubscriberModal').modal('hide');
                        $('#addSubscriberForm')[0].reset();
                        subscribersTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                },
                complete: function() {
                    WJHTAKAdmin.btnLoading(btn, false);
                }
            });
        });

        $('#editSubscriberForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            WJHTAKAdmin.btnLoading(btn, true);

            const id = $('#edit_subscriber_id').val();
            const url = "{{ route('admin.subscribers.update', ':id') }}".replace(':id', id);

            $.ajax({
                url: url,
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#editSubscriberModal').modal('hide');
                        subscribersTable.ajax.reload(null, false);
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
                },
                complete: function() {
                    WJHTAKAdmin.btnLoading(btn, false);
                }
            });
        });
    });

    function editSubscriber(id) {
        $.get("{{ route('admin.subscribers.show', ':id') }}".replace(':id', id), function(response) {
            if (response.success) {
                const user = response.user;
                $('#edit_subscriber_id').val(user.id);
                $('#edit_first_name').val(user.first_name);
                $('#edit_last_name').val(user.last_name);
                $('#edit_email').val(user.email);
                $('#edit_country_code').val(user.country_code);
                $('#edit_phone').val(user.phone);
                $('#edit_status').val(user.status);
                $('#editSubscriberModal').modal('show');
            }
        });
    }

    function toggleSubscriberStatus(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, Change it!") }}'
        }).then((result) => {
            if (result.value) {
                $.post("{{ route('admin.subscribers.toggle-status', ':id') }}".replace(':id', id), {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        subscribersTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                });
            }
        });
    }

    function deleteSubscriber(id) {
        Swal.fire({
            title: '{{ __("Delete Subscriber?") }}',
            text: '{{ __("This action cannot be undone!") }}',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes, delete it!") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "{{ route('admin.subscribers.destroy', ':id') }}".replace(':id', id),
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                    success: function(response) {
                        if (response.success) {
                            subscribersTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function resetForm() {
        $('#addSubscriberForm')[0].reset();
    }
</script>
@endsection
