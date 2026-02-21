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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('Categories List') }}</h4>
                    <button type="button" class="btn btn-primary btn-rounded" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                         <i class="fa fa-plus me-2"></i> {{ __('Add New Category') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th><strong>#</strong></th>
                                    <th><strong>{{ __('Name (AR)') }}</strong></th>
                                    <th><strong>{{ __('Name (EN)') }}</strong></th>
                                    <th><strong>{{ __('Actions') }}</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                <tr>
                                    <td><strong>{{ $loop->iteration }}</strong></td>
                                    <td>{{ $category->name_ar }}</td>
                                    <td>{{ $category->name_en }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <button onclick="editCategory({{ $category->id }})" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></button>
                                            <button onclick="deleteCategory({{ $category->id }})" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
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
                <h5 class="modal-title">{{ __('Add New Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCategoryForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Name (AR)') }}</label>
                        <input type="text" name="name_ar" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Name (EN)') }}</label>
                        <input type="text" name="name_en" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
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
                <h5 class="modal-title">{{ __('Edit Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_cat_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Name (AR)') }}</label>
                        <input type="text" id="edit_name_ar" name="name_ar" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Name (EN)') }}</label>
                        <input type="text" id="edit_name_en" name="name_en" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#addCategoryForm').on('submit', function(e) {
        e.preventDefault();
        $.post("{{ route('trip-categories.store') }}", $(this).serialize(), function(res) {
            if(res.success) {
                location.reload();
            }
        });
    });

    function editCategory(id) {
        $.get("{{ url('admin/trip-categories') }}/" + id, function(res) {
            if(res.success) {
                $('#edit_cat_id').val(res.category.id);
                $('#edit_name_ar').val(res.category.name_ar);
                $('#edit_name_en').val(res.category.name_en);
                $('#editCategoryModal').modal('show');
            }
        });
    }

    $('#editCategoryForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#edit_cat_id').val();
        $.ajax({
            url: "{{ url('admin/trip-categories') }}/" + id,
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if(res.success) {
                    location.reload();
                }
            }
        });
    });

    function deleteCategory(id) {
        if(confirm('{{ __("Are you sure?") }}')) {
            $.ajax({
                url: "{{ url('admin/trip-categories') }}/" + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if(res.success) {
                        location.reload();
                    }
                }
            });
        }
    }
</script>
@endpush
@endsection
