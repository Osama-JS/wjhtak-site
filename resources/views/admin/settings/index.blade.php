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

@push('styles')
<style>
    /* Custom Toastr Styles for Visibility */
    #toast-container > .toast-success {
        background-color: #28a745 !important; /* Green */
        opacity: 1 !important;
        font-size: 16px !important;
        padding: 20px !important;
        width: 400px !important;
    }
    #toast-container > .toast-error {
        background-color: #dc3545 !important; /* Red */
        opacity: 1 !important;
        font-size: 16px !important;
    }
    .toast-message {
        font-weight: bold;
    }
</style>
@endpush

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

        {{-- Frontend Backgrounds --}}
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Frontend Backgrounds') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label d-block">{{ __('Hero Section Background') }}</label>
                            <p class="small text-muted mt-0 mb-2">{{ __('Background image for the homepage hero section') }}</p>
                            <div class="mb-3 p-3 bg-light rounded position-relative" style="min-height: 150px; background-size: cover; background-position: center;">
                                @if(\App\Models\Setting::get('hero_bg'))
                                    <img id="hero-bg-preview" src="{{ asset(\App\Models\Setting::get('hero_bg')) }}" class="img-fluid rounded" style="max-height: 140px; width: 100%; object-fit: cover;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100" style="min-height: 130px;">
                                        <i class="fas fa-mountain fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <label class="btn btn-primary btn-sm">
                                <i class="fa fa-upload me-2"></i>{{ __('Upload Hero Background') }}
                                <input type="file" class="d-none" name="hero_bg" onchange="previewImg(this, '#hero-bg-preview')">
                            </label>
                            <p class="small text-muted mt-2 mb-0">{{ __('Recommended size: 1920x1080px') }}</p>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label d-block">{{ __('Page Header Background') }}</label>
                            <p class="small text-muted mt-0 mb-2">{{ __('Background image for internal pages header (Trips, About, Contact, etc.)') }}</p>
                            <div class="mb-3 p-3 bg-light rounded position-relative" style="min-height: 150px; background-size: cover; background-position: center;">
                                @if(\App\Models\Setting::get('page_header_bg'))
                                    <img id="page-header-bg-preview" src="{{ asset(\App\Models\Setting::get('page_header_bg')) }}" class="img-fluid rounded" style="max-height: 140px; width: 100%; object-fit: cover;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100" style="min-height: 130px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <label class="btn btn-primary btn-sm">
                                <i class="fa fa-upload me-2"></i>{{ __('Upload Page Header Background') }}
                                <input type="file" class="d-none" name="page_header_bg" onchange="previewImg(this, '#page-header-bg-preview')">
                            </label>
                            <p class="small text-muted mt-2 mb-0">{{ __('Recommended size: 1920x400px') }}</p>
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


        {{-- About App Settings --}}
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('About Information') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Our Story (English)') }}</label>
                            <div class="input-group">
                                 <textarea class="form-control" name="story_en" rows="3">{{ \App\Models\Setting::get('story_en') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Our Story (Arabic)') }}</label>
                            <div class="input-group">
                                 <textarea class="form-control" name="story_ar" rows="3">{{ \App\Models\Setting::get('story_ar') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Our Mission (English)') }}</label>
                            <div class="input-group">
                                <textarea class="form-control" name="mission_en" rows="3">{{ \App\Models\Setting::get('mission_en') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Our Mission (Arabic)') }}</label>
                            <div class="input-group">
                                <textarea class="form-control" name="mission_ar" rows="3">{{ \App\Models\Setting::get('mission_ar') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Our Vision (English)') }}</label>
                            <div class="input-group">
                                <textarea class="form-control" name="vision_en" rows="3">{{ \App\Models\Setting::get('vision_en') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Our Vision (Arabic)') }}</label>
                            <div class="input-group">
                                <textarea class="form-control" name="vision_ar" rows="3">{{ \App\Models\Setting::get('vision_ar') }}</textarea>
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
            type: 'warning', // Changed from icon to type for compatibility
            showCancelButton: true,
            confirmButtonColor: '#3b4bd3',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('Yes, save it!') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed || result.value) { // Check both for compatibility
                saveBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>{{ __("Saving...") }}');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            } else {
                                alert(response.message);
                            }

                            if (response.logo_url) {
                                $('#logo-preview').attr('src', response.logo_url).show();
                            }
                            if (response.favicon_url) {
                                $('#favicon-preview').attr('src', response.favicon_url).show();
                            }
                            if (response.hero_bg_url) {
                                $('#hero-bg-preview').attr('src', response.hero_bg_url).show();
                            }
                            if (response.page_header_bg_url) {
                                $('#page-header-bg-preview').attr('src', response.page_header_bg_url).show();
                            }
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message);
                            } else {
                                alert(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                if (typeof toastr !== 'undefined') {
                                    toastr.error(value[0]);
                                } else {
                                    alert(value[0]);
                                }
                            });
                        } else {
                            var msg = "{{ __('An error occurred while saving.') }}";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg += ' ' + xhr.responseJSON.message;
                            }
                            if (typeof toastr !== 'undefined') {
                                toastr.error(msg);
                            } else {
                                alert(msg);
                            }
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
