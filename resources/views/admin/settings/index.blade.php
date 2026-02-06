@extends('layouts.app')

@section('title', __('Platform Settings'))
@section('page-title', __('Platform Settings'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Admin Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Platform Settings') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="row my-2">
    <div class="col-xl-6 col-sm-12">
        <x-stats-card
            :label="__('Total Config Keys')"
            :value="$stats['total_settings']"
            icon="fas fa-cog"
        />
    </div>
    <div class="col-xl-6 col-sm-12">
        <x-stats-card
            :label="__('Last System Update')"
            :value="$stats['last_updated']"
            icon="fas fa-sync"
        />
    </div>
</div>

<form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row">
        {{-- General Settings --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('General Settings') }}</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Site Name (English)') }}</label>
                        <input type="text" class="form-control" name="site_name_en" value="{{ \App\Models\Setting::get('site_name_en') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Site Name (Arabic)') }}</label>
                        <input type="text" class="form-control" name="site_name_ar" value="{{ \App\Models\Setting::get('site_name_ar') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Description (English)') }}</label>
                        <textarea class="form-control" name="site_description_en" rows="3">{{ \App\Models\Setting::get('site_description_en') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Description (Arabic)') }}</label>
                        <textarea class="form-control" name="site_description_ar" rows="3">{{ \App\Models\Setting::get('site_description_ar') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Primary Color') }}</label>
                            <input type="color" class="form-control form-control-color w-100" name="primary_color" value="{{ \App\Models\Setting::get('primary_color') ?? '#3b4bd3' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('System Mode') }}</label>
                            <select class="form-control default-select" name="maintenance_mode">
                                <option value="0" {{ \App\Models\Setting::get('maintenance_mode') == '0' ? 'selected' : '' }}>{{ __('Live (Public Access)') }}</option>
                                <option value="1" {{ \App\Models\Setting::get('maintenance_mode') == '1' ? 'selected' : '' }}>{{ __('Maintenance (Restricted)') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Visual Identity --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Visual Identity') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4 text-center">
                            <label class="form-label d-block">{{ __('Site Logo') }}</label>
                            <div class="mb-3 p-3 bg-light rounded" style="min-height: 100px;">
                                @if(\App\Models\Setting::get('site_logo'))
                                    <img id="logo-preview" src="{{ asset(\App\Models\Setting::get('site_logo')) }}" class="img-fluid" style="max-height: 80px;">
                                @else
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                @endif
                            </div>
                            <label class="btn btn-primary btn-sm">
                                <i class="fa fa-upload me-2"></i>{{ __('Upload Logo') }}
                                <input type="file" class="d-none" name="site_logo" onchange="previewImg(this, '#logo-preview')">
                            </label>
                            <p class="small text-muted mt-2 mb-0">{{ __('Recommended size: 200x50px') }}</p>
                        </div>

                        <div class="col-md-6 mb-4 text-center">
                            <label class="form-label d-block">{{ __('Favicon') }}</label>
                            <div class="mb-3 p-3 bg-light rounded d-flex align-items-center justify-content-center" style="min-height: 100px;">
                                @if(\App\Models\Setting::get('site_favicon'))
                                    <img id="favicon-preview" src="{{ asset(\App\Models\Setting::get('site_favicon')) }}" style="height: 48px;">
                                @else
                                    <i class="fas fa-globe fa-3x text-muted"></i>
                                @endif
                            </div>
                            <label class="btn btn-primary btn-sm">
                                <i class="fa fa-upload me-2"></i>{{ __('Upload Favicon') }}
                                <input type="file" class="d-none" name="site_favicon" onchange="previewImg(this, '#favicon-preview')">
                            </label>
                            <p class="small text-muted mt-2 mb-0">{{ __('SVG or ICO preferred') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact Information --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Contact Information') }}</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Support Email') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" name="contact_email" value="{{ \App\Models\Setting::get('contact_email') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Contact Phone') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" class="form-control" name="contact_phone" value="{{ \App\Models\Setting::get('contact_phone') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Social Media --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Social Media') }}</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Facebook') }}</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background: #3b5998; color: #fff;"><i class="fab fa-facebook-f"></i></span>
                            <input type="url" class="form-control" name="facebook_url" value="{{ \App\Models\Setting::get('facebook_url') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Twitter (X)') }}</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background: #1da1f2; color: #fff;"><i class="fab fa-twitter"></i></span>
                            <input type="url" class="form-control" name="twitter_url" value="{{ \App\Models\Setting::get('twitter_url') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Instagram') }}</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background: #e1306c; color: #fff;"><i class="fab fa-instagram"></i></span>
                            <input type="url" class="form-control" name="instagram_url" value="{{ \App\Models\Setting::get('instagram_url') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile App Settings --}}
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Mobile Application') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('Minimum Version') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                                <input type="text" class="form-control" name="app_min_version" value="{{ \App\Models\Setting::get('app_min_version', '1.0.0') }}">
                            </div>
                            <small class="text-muted">{{ __('Force users to update if their version is lower than this.') }}</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('Play Store Link') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-google-play"></i></span>
                                <input type="url" class="form-control" name="android_url" value="{{ \App\Models\Setting::get('android_url') }}">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('App Store Link') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-apple"></i></span>
                                <input type="url" class="form-control" name="ios_url" value="{{ \App\Models\Setting::get('ios_url') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Save Button --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa fa-save me-2"></i>{{ __('Save All Settings') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
function previewImg(input, previewId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $(previewId).attr('src', e.target.result).show();
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$(document).ready(function() {
    $('#settings-form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var formData = new FormData(this);
        var saveBtn = form.find('button[type="submit"]');
        var originalHtml = saveBtn.html();

        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('Do you want to save the changes?') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3b4bd3',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('Yes, save it!') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                saveBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>{{ __("Saving...") }}');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            if (response.logo_url) {
                                $('#logo-preview').attr('src', response.logo_url).show();
                            }
                            if (response.favicon_url) {
                                $('#favicon-preview').attr('src', response.favicon_url).show();
                            }
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else {
                            toastr.error("{{ __('An error occurred while saving.') }}");
                        }
                    },
                    complete: function() {
                        saveBtn.prop('disabled', false).html(originalHtml);
                    }
                });
            }
        });
    });
});
</script>
@endsection
