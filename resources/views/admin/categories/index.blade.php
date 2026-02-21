@extends('layouts.app')

@section('title', __('Trip Categories'))

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Categories') }}</a></li>
        </ol>
    </div>

    @push('styles')
    <style>
        .premium-filter-bar {
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border: 1px solid #edf2f7;
        }
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="premium-filter-bar d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 font-w600">{{ __('Manage Categories') }}</h4>
                    <p class="mb-0 text-muted">{{ __('Create and manage trip types like Economy, Family, and Royal.') }}</p>
                </div>
                <button type="button" class="btn btn-primary btn-rounded shadow" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                     <i class="fa fa-plus me-2"></i> {{ __('Add New Category') }}
                </button>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="categories-table" class="display table-responsive-md" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th><strong>#</strong></th>
                                    <th><strong>{{ __('Name (AR)') }}</strong></th>
                                    <th><strong>{{ __('Name (EN)') }}</strong></th>
                                    <th><strong>{{ __('Actions') }}</strong></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCategoryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-w600">{{ __('Add New Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCategoryForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-w600">{{ __('Name (AR)') }}</label>
                        <input type="text" name="name_ar" class="form-control solid" required placeholder="{{ __('Enter Arabic Name') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-w600">{{ __('Name (EN)') }}</label>
                        <input type="text" name="name_en" class="form-control solid" required placeholder="{{ __('Enter English Name') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Category') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editCategoryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-w600">{{ __('Edit Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_cat_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-w600">{{ __('Name (AR)') }}</label>
                        <input type="text" id="edit_name_ar" name="name_ar" class="form-control solid" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-w600">{{ __('Name (EN)') }}</label>
                        <input type="text" id="edit_name_en" name="name_en" class="form-control solid" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update Category') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let categoriesTable;

    $(document).ready(function() {
        categoriesTable = $('#categories-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.trip-categories.data') }}",
            columns: [
                { data: 'id' },
                { data: 'name_ar' },
                { data: 'name_en' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });

        $('#addCategoryForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.trip-categories.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (res) {
                    if(res.success) {
                        $('#addCategoryModal').modal('hide');
                        $('#addCategoryForm')[0].reset();
                        categoriesTable.ajax.reload();
                        toastr.success(res.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('Something went wrong');
                    }
                }
            });
        });

        $('#editCategoryForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#edit_cat_id').val();
            let url = "{{ route('admin.trip-categories.update', ':id') }}".replace(':id', id);
            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize() + '&_method=PUT',
                success: function(res) {
                    if(res.success) {
                        $('#editCategoryModal').modal('hide');
                        categoriesTable.ajax.reload();
                        toastr.success(res.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('Something went wrong');
                    }
                }
            });
        });
    });

    function editCategory(id) {
        let url = "{{ route('admin.trip-categories.show', ':id') }}".replace(':id', id);
        $.get(url, function(res) {
            if(res.success) {
                $('#edit_cat_id').val(res.category.id);
                $('#edit_name_ar').val(res.category.name_ar);
                $('#edit_name_en').val(res.category.name_en);
                $('#editCategoryModal').modal('show');
            }
        });
    }

    function deleteCategory(id) {
        let url = "{{ route('admin.trip-categories.destroy', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This category will be permanently deleted.") }}',
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
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        if(res.success) {
                            categoriesTable.ajax.reload();
                            toastr.success(res.message);
                        }
                    }
                });
            }
        });
    }
</script>
@endpush
@endsection
