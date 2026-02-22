@extends('layouts.app')

@section('title', __('Add New Page'))

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.pages.index') }}">{{ __('Pages') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Add New Page') }}</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="fa fa-plus-circle me-2"></i>{{ __('Add New Page') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pages.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <x-forms.input-text name="title_ar" :label="__('Title (Arabic)')" required icon="fa fa-pen" />
                            </div>
                            <div class="col-md-6">
                                <x-forms.input-text name="title_en" id="title_en" :label="__('Title (English)')" required icon="fa fa-pen" />
                            </div>
                            <div class="col-md-12">
                                <x-forms.input-text name="slug" id="slug" :label="__('Page Slug (URL)')" required icon="fa fa-link" />
                            </div>

                            <div class="col-md-12 mb-4">
                                <div class="form-group mb-3">
                                    <label class="form-label font-w600">{{ __('Content (Arabic)') }} <span class="text-danger">*</span></label>
                                    <textarea id="content_ar" name="content_ar" class="form-control" rows="10"></textarea>
                                </div>
                            </div>

                            <div class="col-md-12 mb-4">
                                <div class="form-group mb-3">
                                    <label class="form-label font-w600">{{ __('Content (English)') }} <span class="text-danger">*</span></label>
                                    <textarea id="content_en" name="content_en" class="form-control" rows="10"></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <x-forms.checkbox name="is_active" :label="__('Active')" checked type="switch" />
                            </div>
                            <div class="col-md-6">
                                <x-forms.checkbox name="show_in_footer" :label="__('Show in Footer')" checked type="switch" />
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <hr>
                            <button type="button" class="btn btn-danger light me-2" onclick="window.history.back()">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary btn-rounded px-5">{{ __('Save Page') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
<script>
    function initializeEditor(selector) {
        ClassicEditor
            .create(document.querySelector(selector), {
                language: '{{ app()->getLocale() }}',
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo' ]
            })
            .catch(error => {
                console.error(error);
            });
    }

    $(document).ready(function() {
        initializeEditor('#content_ar');
        initializeEditor('#content_en');

        $('#title_en').on('keyup', function() {
            let title = $(this).val();
            let slug = title.toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
            $('#slug').val(slug);
        });
    });
</script>
@endpush
