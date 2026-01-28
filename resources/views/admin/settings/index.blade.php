@extends('layouts.app')

@section('title', __('Platform Settings'))
@section('page-title', __('Platform Settings'))

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Manage Platform Settings') }}</h4>
            </div>
            <div class="card-body">
                <!-- Session Status -->
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">{{ __('General Settings') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab">{{ __('Logo & Icons') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">{{ __('Contact & Social') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="app-settings-tab" data-bs-toggle="tab" data-bs-target="#app-settings" type="button" role="tab">{{ __('App Settings') }}</button>
                        </li>
                    </ul>

                    <div class="tab-content mt-4" id="myTabContent">
                        <!-- General Settings Tab -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Site Name (English)') }}</label>
                                    <input type="text" class="form-control" name="site_name_en" value="{{ \App\Models\Setting::get('site_name_en') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Site Name (Arabic)') }}</label>
                                    <input type="text" class="form-control" name="site_name_ar" value="{{ \App\Models\Setting::get('site_name_ar') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Description (English)') }}</label>
                                    <textarea class="form-control" name="site_description_en" rows="3">{{ \App\Models\Setting::get('site_description_en') }}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Description (Arabic)') }}</label>
                                    <textarea class="form-control" name="site_description_ar" rows="3">{{ \App\Models\Setting::get('site_description_ar') }}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Primary Color') }}</label>
                                    <input type="color" class="form-control form-control-color" name="primary_color" value="{{ \App\Models\Setting::get('primary_color') ?? '#3b4bd3' }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Maintenance Mode') }}</label>
                                    <select class="form-control default-select" name="maintenance_mode">
                                        <option value="0" {{ \App\Models\Setting::get('maintenance_mode') == '0' ? 'selected' : '' }}>{{ __('Disabled (Live)') }}</option>
                                        <option value="1" {{ \App\Models\Setting::get('maintenance_mode') == '1' ? 'selected' : '' }}>{{ __('Enabled (Maintenance)') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Media Tab -->
                        <div class="tab-pane fade" id="media" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Platform Logo') }}</label>
                                    <input type="file" class="form-control" name="site_logo">
                                    @if(\App\Models\Setting::get('site_logo'))
                                        <div class="mt-2">
                                            <img id="logo-preview" src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="Logo" height="50">
                                        </div>
                                    @else
                                        <div class="mt-2">
                                            <img id="logo-preview" src="" alt="Logo" height="50" style="display:none;">
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Favicon Icon') }}</label>
                                    <input type="file" class="form-control" name="site_favicon">
                                    @if(\App\Models\Setting::get('site_favicon'))
                                        <div class="mt-2">
                                            <img id="favicon-preview" src="{{ asset(\App\Models\Setting::get('site_favicon')) }}" alt="Favicon" height="32">
                                        </div>
                                    @else
                                        <div class="mt-2">
                                            <img id="favicon-preview" src="" alt="Favicon" height="32" style="display:none;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Contact Tab -->
                        <div class="tab-pane fade" id="contact" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Support Email') }}</label>
                                    <input type="email" class="form-control" name="contact_email" value="{{ \App\Models\Setting::get('contact_email') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Contact Phone') }}</label>
                                    <input type="text" class="form-control" name="contact_phone" value="{{ \App\Models\Setting::get('contact_phone') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('Facebook URL') }}</label>
                                    <input type="url" class="form-control" name="facebook_url" value="{{ \App\Models\Setting::get('facebook_url') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('Twitter URL') }}</label>
                                    <input type="url" class="form-control" name="twitter_url" value="{{ \App\Models\Setting::get('twitter_url') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('Instagram URL') }}</label>
                                    <input type="url" class="form-control" name="instagram_url" value="{{ \App\Models\Setting::get('instagram_url') }}">
                                </div>
                            </div>
                        </div>

                        <!-- App Settings Tab -->
                        <div class="tab-pane fade" id="app-settings" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Minimum App Version') }}</label>
                                    <input type="text" class="form-control" name="app_min_version" value="{{ \App\Models\Setting::get('app_min_version', '1.0.0') }}" placeholder="e.g. 1.0.0">
                                    <small class="text-muted">{{ __('Force users to update if their version is lower than this.') }}</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('System Status') }}</label>
                                    <select class="form-control default-select" name="maintenance_mode">
                                        <option value="0" {{ \App\Models\Setting::get('maintenance_mode') == '0' ? 'selected' : '' }}>{{ __('Active (Live)') }}</option>
                                        <option value="1" {{ \App\Models\Setting::get('maintenance_mode') == '1' ? 'selected' : '' }}>{{ __('Maintenance (Offline)') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">{{ __('Android Store URL') }}</label>
                                    <input type="url" class="form-control" name="android_url" value="{{ \App\Models\Setting::get('android_url') }}" placeholder="https://play.google.com/store/...">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">{{ __('iOS Store URL') }}</label>
                                    <input type="url" class="form-control" name="ios_url" value="{{ \App\Models\Setting::get('ios_url') }}" placeholder="https://apps.apple.com/app/...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-end mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>{{ __('Save Settings') }}</button>
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
    $('#settings-form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var formData = new FormData(this);

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
                // Show loading
                var saveBtn = form.find('button[type="submit"]');
                var originalHtml = saveBtn.html();
                saveBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>{{ __("Saving...") }}');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message, "{{ __('Success') }}", {
                                positionClass: "toast-top-right",
                                timeOut: 5e3,
                                closeButton: !0,
                                debug: !1,
                                newestOnTop: !0,
                                progressBar: !0,
                                preventDuplicates: !0,
                                onclick: null,
                                showDuration: "300",
                                hideDuration: "1000",
                                extendedTimeOut: "1000",
                                showEasing: "swing",
                                hideEasing: "linear",
                                showMethod: "fadeIn",
                                hideMethod: "fadeOut",
                                tapToDismiss: !1
                            });

                            // Update images if provided
                            if (response.logo_url) {
                                $('#logo-preview').attr('src', response.logo_url).show();
                            }
                            if (response.favicon_url) {
                                $('#favicon-preview').attr('src', response.favicon_url).show();
                            }
                        } else {
                            toastr.error(response.message, "{{ __('Error') }}");
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0], "{{ __('Validation Error') }}");
                            });
                        } else {
                            var errorMsg = "{{ __('An error occurred while saving.') }}";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            toastr.error(errorMsg, "{{ __('Error') }}");
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
