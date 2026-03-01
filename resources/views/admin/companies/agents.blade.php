@extends('layouts.app')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">{{ __('Companies') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $company->name }} - {{ __('Agents') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Manage Agents for') }} {{ $company->name }}</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAgentModal">
                    <i class="fa fa-plus me-2"></i> {{ __('Add Agent') }}
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="agents-table" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Email') }}</th>
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

<!-- Add Agent Modal -->
<div class="modal fade" id="addAgentModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add New Agent') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAgentForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('First Name') }}</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Last Name') }}</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('Code') }}</label>
                            <select name="country_code" class="form-control" required>
                                @foreach($countries as $country)
                                    <option value="{{ $country->phonecode }}" {{ $country->phonecode == '966' ? 'selected' : '' }}>
                                        +{{ $country->phonecode }} ({{ $country->nicename }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" class="form-control" required placeholder="5xxxxxxxx">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Password') }}</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Confirm Password') }}</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Add Agent') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Agent Modal -->
<div class="modal fade" id="editAgentModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Agent') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAgentForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('First Name') }}</label>
                        <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Last Name') }}</label>
                        <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('Code') }}</label>
                            <select name="country_code" id="edit_country_code" class="form-control" required>
                                @foreach($countries as $country)
                                    <option value="{{ $country->phonecode }}">
                                        +{{ $country->phonecode }} ({{ $country->nicename }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control" required placeholder="5xxxxxxxx">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Password') }} <small class="text-muted">({{ __('Leave empty to keep current') }})</small></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Confirm Password') }}</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update Agent') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let agentsTable;
    $(document).ready(function() {
        agentsTable = $('#agents-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.companies.agents.data', $company->id) }}",
            columns: [
                { data: 'name' },
                { data: 'phone' },
                { data: 'email' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });

        $('#addAgentForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.companies.agents.store', $company->id) }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#addAgentModal').modal('hide');
                        $('#addAgentForm')[0].reset();
                        agentsTable.ajax.reload();
                        toastr.success(response.message);
                    }
                }
            });
        });

        $('#editAgentForm').on('submit', function(e) {
            e.preventDefault();
            let agentId = $(this).data('id');
            $.ajax({
                url: "{{ url('admin/agents') }}/" + agentId,
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#editAgentModal').modal('hide');
                        $('#editAgentForm')[0].reset();
                        agentsTable.ajax.reload();
                        toastr.success(response.message);
                    }
                }
            });
        });
    });

    function editAgent(id) {
        $.ajax({
            url: "{{ url('admin/agents') }}/" + id + "/edit",
            type: "GET",
            success: function(response) {
                if (response.success) {
                    let agent = response.data;
                    $('#editAgentForm').data('id', agent.id);
                    $('#edit_first_name').val(agent.first_name);
                    $('#edit_last_name').val(agent.last_name);
                    $('#edit_email').val(agent.email);
                    $('#edit_country_code').val(agent.country_code);
                    $('#edit_phone').val(agent.phone);
                    $('#editAgentModal').modal('show');
                }
            }
        });
    }

    function toggleAgentStatus(id) {
        $.ajax({
            url: "{{ url('admin/agents') }}/" + id + "/toggle-status",
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    agentsTable.ajax.reload(null, false);
                    toastr.success(response.message);
                }
            }
        });
    }

    function deleteAgent(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This agent will be deleted!") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes, delete it!") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "{{ route('admin.companies.agents.destroy', ':id') }}".replace(':id', id),
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            agentsTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }
</script>
@endsection
