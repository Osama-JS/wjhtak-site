@extends('layouts.app')

@section('title', __('Banners Management'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Main Menu') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Banners') }}</a></li>
    </ol>
</div>
@endsection

@section('content')

    <div class="row my-2">
        <div class="col-xl-4 col-sm-6">
            <x-stats-card
                :label="__('Total Banners')"
                :value="$stats['total']"
                icon="fas fa-images"
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('Banners List') }}</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                         <i class="fa fa-plus me-2"></i> {{ __('Add Banner') }}
                     </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> {{ __('يمكنك إعادة ترتيب البانرات بسحب الصفوف في الجدول.') }}
                    </div>
                    <div class="table-responsive">
                        <table id="banners-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('Image') }}</th>
                                    <th>{{ __('Title (Ar)') }}</th>
                                    <th>{{ __('Title (En)') }}</th>
                                    <th>{{ __('Link') }}</th>
                                    <th>{{ __('Order') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="banners-list">
                                {{-- Loaded via DataTables with Drag & Drop enabled --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Banner Modal -->
<div class="modal fade" id="addBannerModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add New Banner') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.banners.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.input-text name="title_ar" :label="__('Title (Arabic)')" />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input-text name="title_en" :label="__('Title (English)')" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.textarea name="description_ar" :label="__('Description (Arabic)')" rows="3" />
                        </div>
                        <div class="col-md-6">
                            <x-forms.textarea name="description_en" :label="__('Description (English)')" rows="3" />
                        </div>
                    </div>
                    <x-forms.input-text name="link" :label="__('Link URL')" placeholder="https://..." />
                    <x-forms.file-upload name="image_path" :label="__('Banner Image')" accept="image/*" required />
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.input-text name="sort_order" :label="__('Display Order')" type="number" />
                        </div>
                        <div class="col-md-6">
                            <x-forms.checkbox name="active" :label="__('Active status')" checked type="switch" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Banner') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Banner Modal -->
<div class="modal fade" id="editBannerModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Banner') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBannerForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_banner_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.input-text name="title_ar" id="edit_title_ar" :label="__('Title (Arabic)')" />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input-text name="title_en" id="edit_title_en" :label="__('Title (English)')" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.textarea name="description_ar" id="edit_description_ar" :label="__('Description (Arabic)')" rows="3" />
                        </div>
                        <div class="col-md-6">
                            <x-forms.textarea name="description_en" id="edit_description_en" :label="__('Description (English)')" rows="3" />
                        </div>
                    </div>
                    <x-forms.input-text name="link" id="edit_link" :label="__('Link URL')" />
                    <x-forms.file-upload name="image_path" id="edit_image_path" :label="__('Banner Image')" accept="image/*" preview />
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.input-text name="sort_order" id="edit_sort_order" :label="__('Display Order')" type="number" />
                        </div>
                        <div class="col-md-6">
                            <x-forms.checkbox name="active" id="edit_active" :label="__('Active status')" type="switch" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update Banner') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
{{-- Include jQuery UI for Drag and Drop --}}
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
    let bannersTable;
    const bannersDataUrl = "{{ route('admin.banners.data') }}";

    $(document).ready(function() {
        // Initialize DataTable
        bannersTable = $('#banners-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: bannersDataUrl,
            columns: [
                { data: 'image_path' },
                { data: 'title_ar' },
                { data: 'title_en' },
                { data: 'link' },
                { data: 'sort_order' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            },
            rowCallback: function(row, data) {
                $(row).attr('data-id', data.id);
                $(row).addClass('draggable-row');
            }
        });

        // Enable Drag and Drop Reordering
        $("#banners-table tbody").sortable({
            helper: function(e, ui) {
                ui.children().each(function() {
                    $(this).width($(this).width());
                });
                return ui;
            },
            update: function(event, ui) {
                let sort_order = [];
                $('#banners-table tbody tr').each(function() {
                    sort_order.push($(this).data('id'));
                });

                $.ajax({
                    url: "{{ route('admin.banners.reorder') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        order: sort_order
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            bannersTable.ajax.reload(null, false);
                        }
                    }
                });
            }
        }).disableSelection();

        // Add Banner Form Submit
        $('#addBannerForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            WJHTAKAdmin.btnLoading(btn, true);

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.banners.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#addBannerModal').modal('hide');
                        $('#addBannerForm')[0].reset();
                        bannersTable.ajax.reload();
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

        // Edit Banner Form Submit
        $('#editBannerForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            WJHTAKAdmin.btnLoading(btn, true);

            const id = $('#edit_banner_id').val();
            let url = "{{ route('admin.banners.update', ':id') }}".replace(':id', id);
            let formData = new FormData(this);

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#editBannerModal').modal('hide');
                        bannersTable.ajax.reload();
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

    function editBanner(id) {
        let url = "{{ route('admin.banners.show', ':id') }}".replace(':id', id);

        $.get(url, function(response) {
            if (response.success) {
                const banner = response.banner;
                $('#edit_banner_id').val(banner.id);
                $('#edit_title_ar').val(banner.title_ar);
                $('#edit_title_en').val(banner.title_en);
                $('#edit_description_ar').val(banner.description_ar);
                $('#edit_description_en').val(banner.description_en);
                $('#edit_link').val(banner.link);
                $('#edit_sort_order').val(banner.sort_order);
                $('#edit_active').prop('checked', banner.active);

                // Show current image
                if (banner.image_path) {
                    $('#editBannerForm .current-image-preview img').attr('src', response.image_url);
                    $('#editBannerForm .current-image-preview').show();
                } else {
                    $('#editBannerForm .current-image-preview').hide();
                }

                $('#editBannerModal').modal('show');
            }
        });
    }

    function toggleBannerStatus(id) {
        let url = "{{ route('admin.banners.toggle-status', ':id') }}".replace(':id', id);

        WJHTAKAdmin.confirm('{{ __("Do you want to toggle this banner status?") }}', function() {
            $.ajax({
                url: url,
                type: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if (response.success) {
                        bannersTable.ajax.reload();
                        toastr.success(response.message);
                    }
                }
            });
        });
    }

    function deleteBanner(id) {
        let url = "{{ route('admin.banners.destroy', ':id') }}".replace(':id', id);

        WJHTAKAdmin.confirm('{{ __("Are you sure you want to delete this banner?") }}', function() {
            $.ajax({
                url: url,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if (response.success) {
                        bannersTable.ajax.reload();
                        toastr.success(response.message);
                    }
                }
            });
        });
    }
</script>
@endsection
