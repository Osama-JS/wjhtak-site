@extends('layouts.app')

@section('title', __('Pages Management'))

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Main Menu') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Pages') }}</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('Pages List') }}</h4>
                    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary btn-rounded">
                         <i class="fa fa-plus me-2"></i> {{ __('Add New Page') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="pages-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Title (Ar)') }}</th>
                                    <th>{{ __('Title (En)') }}</th>
                                    <th>{{ __('Slug') }}</th>
                                    <th>{{ __('Active') }}</th>
                                    <th>{{ __('Footer') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let pagesTable;
    $(document).ready(function() {
        pagesTable = $('#pages-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.pages.data') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'title_ar', name: 'title_ar' },
                { data: 'title_en', name: 'title_en' },
                { data: 'slug', name: 'slug' },
                { data: 'is_active', name: 'is_active' },
                { data: 'show_in_footer', name: 'show_in_footer' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            },
            order: [[0, 'desc']]
        });
    });

    function deletePage(id) {
        let url = "{{ route('admin.pages.destroy', ':id') }}".replace(':id', id);

        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This action cannot be undone!") }}',
            icon: 'warning',
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
                            pagesTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }
</script>
@endpush
