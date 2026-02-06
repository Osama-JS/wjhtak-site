@extends('layouts.app')

@section('title', __('User Profile'))
@section('page-title', __('User Profile'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Admin Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('User Profile') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="row">
    {{-- Left Column: Profile Card --}}
    <div class="col-xl-4 col-lg-5">
        <div class="card">
            <div class="card-body text-center">
                <div class="profile-photo mx-auto position-relative mb-4" style="width: 150px; height: 150px;">
                    <img id="profile-preview" src="{{ $user->profile_photo_url }}"
                         class="rounded-circle w-100 h-100 shadow"
                         style="object-fit: cover; border: 4px solid #fff; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
                    <label class="btn btn-primary btn-sm position-absolute shadow"
                           style="bottom: 5px; {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 5px; width: 40px; height: 40px; border-radius: 50%; padding: 0; line-height: 38px;">
                        <i class="fa fa-camera"></i>
                        <input type="file" class="d-none" id="photo-upload" accept="image/*" onchange="uploadPhoto(this)">
                    </label>
                </div>
                <h3 class="mb-1">{{ $user->full_name }}</h3>
                <p class="text-muted mb-3">{{ $user->email }}</p>
                <span class="badge badge-primary light px-3 py-2">{{ strtoupper($user->user_type) }}</span>

                <hr class="my-4">

                <div class="text-start">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-envelope text-primary me-3"></i>
                        <div>
                            <small class="text-muted d-block">{{ __('Email') }}</small>
                            <span>{{ $user->email }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-phone text-primary me-3"></i>
                        <div>
                            <small class="text-muted d-block">{{ __('Phone') }}</small>
                            <span>{{ $user->phone ?? '---' }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar text-primary me-3"></i>
                        <div>
                            <small class="text-muted d-block">{{ __('Member since') }}</small>
                            <span>{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Forms --}}
    <div class="col-xl-8 col-lg-7">
        {{-- Personal Information Card --}}
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Personal Information') }}</h4>
            </div>
            <div class="card-body">
                <form id="profile-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('First Name') }}</label>
                            <input type="text" class="form-control" name="first_name" value="{{ $user->first_name }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Last Name') }}</label>
                            <input type="text" class="form-control" name="last_name" value="{{ $user->last_name }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">
                                {{ __('Email Address') }}
                                <span class="badge badge-sm badge-danger light ms-1">{{ __('Read-only') }}</span>
                            </label>
                            <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
                            <small class="text-muted">{{ __('Contact support to change your email.') }}</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Phone Number') }}</label>
                            <input type="text" class="form-control" name="phone" value="{{ $user->phone }}">
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-2"></i>{{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Security Card --}}
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Change Password') }}</h4>
            </div>
            <div class="card-body">
                <form id="password-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">{{ __('Current Password') }}</label>
                            <input type="password" class="form-control" name="current_password" placeholder="{{ __('Enter current password') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('New Password') }}</label>
                            <input type="password" class="form-control" name="password" placeholder="{{ __('Enter new password') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Confirm Password') }}</label>
                            <input type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Confirm new password') }}">
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-lock me-2"></i>{{ __('Update Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Update Profile
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        var originalHtml = btn.html();

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>{{ __("Saving...") }}');

        $.ajax({
            url: "{{ route('admin.profile.update') }}",
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error("{{ __('Something went wrong') }}");
                }
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Update Password
    $('#password-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        var originalHtml = btn.html();

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>{{ __("Saving...") }}');

        $.ajax({
            url: "{{ route('admin.profile.password') }}",
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    form[0].reset();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error("{{ __('Something went wrong') }}");
                }
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
});

function uploadPhoto(input) {
    if (input.files && input.files[0]) {
        var formData = new FormData();
        formData.append('profile_photo', input.files[0]);
        formData.append('_token', "{{ csrf_token() }}");

        // Show loading state
        const previewImg = $('#profile-preview');
        const originalSrc = previewImg.attr('src');
        previewImg.css('opacity', '0.5');

        const uploadUrl = window.location.href.split('#')[0].split('?')[0].replace(/\/$/, '') + '/photo';
        console.log('Uploading photo to:', uploadUrl);

        $.ajax({
            url: uploadUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    previewImg.attr('src', response.photo_url);
                    toastr.success(response.message);
                    // Update header/sidebar photos if they exist
                    $('.user-img, .profile-pic').attr('src', response.photo_url);
                } else {
                    toastr.error(response.message || "{{ __('Failed to upload photo.') }}");
                    previewImg.attr('src', originalSrc);
                }
            },
            error: function(xhr) {
                previewImg.attr('src', originalSrc);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error("{{ __('An error occurred during upload.') }}");
                    console.error(xhr.responseText);
                }
            },
            complete: function() {
                previewImg.css('opacity', '1');
            }
        });
    }
}
</script>
@endsection
