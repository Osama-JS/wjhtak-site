@extends('layouts.app')

@section('title', __('Notifications Management'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Main Menu') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Notifications') }}</a></li>
    </ol>
</div>
@endsection

@push('styles')
<style>
    .notif-type-badge {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 500;
    }
    .notif-type-badge.general { background: #e3f2fd; color: #1565c0; }
    .notif-type-badge.promotion { background: #fce4ec; color: #c62828; }
    .notif-type-badge.new_trip { background: #e8f5e9; color: #2e7d32; }
    .notif-type-badge.payment_success { background: #e8f5e9; color: #1b5e20; }
    .notif-type-badge.payment_failed { background: #ffebee; color: #b71c1c; }
    .notif-type-badge.booking_confirmed { background: #e3f2fd; color: #0d47a1; }
    .notif-type-badge.booking_cancelled { background: #fff3e0; color: #e65100; }
    .notif-type-badge.booking_reminder { background: #f3e5f5; color: #6a1b9a; }
    .notif-type-badge.favorite_trip_update { background: #fff8e1; color: #f57f17; }

    .user-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0f4f8;
        border: 1px solid #d0d7de;
        border-radius: 20px;
        padding: 4px 12px 4px 8px;
        margin: 3px;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .user-chip:hover { background: #e3e8ed; }
    .user-chip .remove-user {
        cursor: pointer;
        color: #cf222e;
        font-weight: bold;
        font-size: 1rem;
        line-height: 1;
    }
    .user-chip .fcm-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .user-chip .fcm-dot.active { background: #2da44e; }
    .user-chip .fcm-dot.inactive { background: #cf222e; }

    .search-results-dropdown {
        position: absolute;
        z-index: 1050;
        width: 100%;
        max-height: 250px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #d0d7de;
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        display: none;
    }
    .search-results-dropdown .search-item {
        padding: 10px 14px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.15s;
    }
    .search-results-dropdown .search-item:hover { background: #f6f8fa; }
    .search-results-dropdown .search-item:last-child { border-bottom: none; }
    .search-results-dropdown .search-item .user-info { font-weight: 500; }
    .search-results-dropdown .search-item .user-meta { font-size: 0.8rem; color: #656d76; }

    .char-counter { font-size: 0.8rem; color: #656d76; text-align: left; }
    .char-counter.warning { color: #cf222e; }

    .send-mode-tabs .nav-link {
        border-radius: 8px !important;
        margin-left: 4px;
        margin-right: 4px;
        font-weight: 500;
    }
    .send-mode-tabs .nav-link.active {
        background: var(--primary) !important;
        color: #fff !important;
    }

    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }
</style>
@endpush

@section('content')

{{-- Stats Cards --}}
<div class="row mb-4">
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['total']) }}</h3>
                    <small class="text-muted">{{ __('Total Notifications') }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['unread']) }}</h3>
                    <small class="text-muted">{{ __('Unread') }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['today']) }}</h3>
                    <small class="text-muted">{{ __('Sent Today') }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stats-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['users_with_fcm']) }} <small class="text-muted fw-normal">/ {{ $stats['total_users'] }}</small></h3>
                    <small class="text-muted">{{ __('FCM Active Users') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Send Notification Card --}}
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i class="fas fa-paper-plane me-2"></i>{{ __('Send Notification') }}</h4>
            </div>
            <div class="card-body">
                <form id="sendNotificationForm">
                    @csrf

                    {{-- Target Selection Tabs --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('Send To') }}</label>
                        <ul class="nav nav-pills send-mode-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="pill" data-target-mode="all" type="button">
                                    <i class="fas fa-globe me-1"></i> {{ __('All Users') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="pill" data-target-mode="selected" type="button">
                                    <i class="fas fa-user-check me-1"></i> {{ __('Select Users') }}
                                </button>
                            </li>
                        </ul>
                        <input type="hidden" name="target" id="targetMode" value="all">
                    </div>

                    {{-- User Selection (hidden by default) --}}
                    <div id="userSelectionBox" style="display: none;" class="mb-4">
                        <label class="form-label fw-bold">{{ __('Search Users') }}</label>
                        <div class="position-relative">
                            <input type="text" id="userSearchInput" class="form-control"
                                   placeholder="{{ __('Search by name, email or phone...') }}" autocomplete="off">
                            <div id="searchResultsDropdown" class="search-results-dropdown"></div>
                        </div>
                        <div id="selectedUsersContainer" class="mt-2"></div>
                        <small class="text-muted" id="selectedCount"></small>
                    </div>

                    {{-- Notification Type --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('Notification Type') }}</label>
                        <select name="type" class="form-select" id="notifType">
                            <option value="general">{{ __('General') }} 📢</option>
                            <option value="promotion">{{ __('Promotion') }} 🎁</option>
                            <option value="new_trip">{{ __('New Trip') }} ✈️</option>
                            <option value="booking_reminder">{{ __('Booking Reminder') }} ⏰</option>
                            <option value="favorite_trip_update">{{ __('Favorite Trip Update') }} ⭐</option>
                        </select>
                    </div>

                    {{-- Title --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Title (Arabic)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title_ar" class="form-control" maxlength="255"
                                   placeholder="{{ __('عنوان الإشعار بالعربية') }}" required dir="rtl">
                            <div class="char-counter"><span class="title-ar-count">0</span>/255</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Title (English)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title_en" class="form-control" maxlength="255"
                                   placeholder="Notification title in English" required dir="ltr">
                            <div class="char-counter"><span class="title-en-count">0</span>/255</div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Body (Arabic)') }} <span class="text-danger">*</span></label>
                            <textarea name="body_ar" class="form-control" rows="4" maxlength="1000"
                                      placeholder="{{ __('نص الإشعار بالعربية') }}" required dir="rtl"></textarea>
                            <div class="char-counter"><span class="body-ar-count">0</span>/1000</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Body (English)') }} <span class="text-danger">*</span></label>
                            <textarea name="body_en" class="form-control" rows="4" maxlength="1000"
                                      placeholder="Notification body in English" required dir="ltr"></textarea>
                            <div class="char-counter"><span class="body-en-count">0</span>/1000</div>
                        </div>
                    </div>

                    {{-- Preview --}}
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body p-3">
                            <h6 class="mb-2"><i class="fas fa-eye me-1"></i> {{ __('Preview') }}</h6>
                            <div class="d-flex align-items-start gap-3 bg-white rounded p-3 shadow-sm">
                                <div class="stats-icon bg-primary bg-opacity-10 text-primary" style="min-width: 48px;">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div>
                                    <strong id="previewTitle">{{ __('Notification Title') }}</strong>
                                    <p class="mb-0 text-muted" id="previewBody">{{ __('Notification body text...') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Send Button --}}
                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-1"></i> {{ __('Reset') }}
                        </button>
                        <button type="submit" class="btn btn-primary px-4" id="sendBtn">
                            <i class="fas fa-paper-plane me-1"></i> {{ __('Send Notification') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Notifications History --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="card-title mb-0"><i class="fas fa-history me-2"></i>{{ __('Notification History') }}</h4>
                <div class="d-flex gap-2 flex-wrap">
                    <select id="filterType" class="form-select form-select-sm" style="width: auto;">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="general">{{ __('General') }}</option>
                        <option value="promotion">{{ __('Promotion') }}</option>
                        <option value="new_trip">{{ __('New Trip') }}</option>
                        <option value="payment_success">{{ __('Payment Success') }}</option>
                        <option value="payment_failed">{{ __('Payment Failed') }}</option>
                        <option value="booking_confirmed">{{ __('Booking Confirmed') }}</option>
                        <option value="booking_cancelled">{{ __('Booking Cancelled') }}</option>
                        <option value="booking_reminder">{{ __('Booking Reminder') }}</option>
                        <option value="favorite_trip_update">{{ __('Favorite Update') }}</option>
                    </select>
                    <input type="date" id="filterFromDate" class="form-control form-control-sm" style="width: auto;"
                           placeholder="{{ __('From Date') }}">
                    <input type="date" id="filterToDate" class="form-control form-control-sm" style="width: auto;"
                           placeholder="{{ __('To Date') }}">
                    <button class="btn btn-sm btn-outline-primary" onclick="loadHistory()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="historyTable">
                        <thead>
                            <tr>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Content') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="historyBody">
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin me-2"></i>{{ __('Loading...') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div id="historyPagination" class="d-flex justify-content-center mt-3"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // ─── State ──────────────────────────────────────────────
    let selectedUsers = {};
    let searchTimeout = null;
    let currentPage = 1;

    $(document).ready(function() {
        loadHistory();

        // ─── Target Mode Tabs ───────────────────────────────
        $('.send-mode-tabs .nav-link').on('click', function() {
            const mode = $(this).data('target-mode');
            $('#targetMode').val(mode);
            if (mode === 'selected') {
                $('#userSelectionBox').slideDown(200);
            } else {
                $('#userSelectionBox').slideUp(200);
            }
        });

        // ─── User Search ────────────────────────────────────
        $('#userSearchInput').on('input', function() {
            const query = $(this).val().trim();
            clearTimeout(searchTimeout);

            if (query.length < 2) {
                $('#searchResultsDropdown').hide();
                return;
            }

            searchTimeout = setTimeout(() => {
                $.get("{{ route('admin.notifications.search-users') }}", { q: query }, function(users) {
                    const dropdown = $('#searchResultsDropdown');
                    dropdown.empty();

                    if (users.length === 0) {
                        dropdown.append(`<div class="search-item text-muted text-center">{{ __('No users found') }}</div>`);
                    } else {
                        users.forEach(user => {
                            if (!selectedUsers[user.id]) {
                                const fcmIcon = user.has_fcm
                                    ? '<span class="badge bg-success" style="font-size:0.65rem;">FCM ✓</span>'
                                    : '<span class="badge bg-secondary" style="font-size:0.65rem;">No FCM</span>';
                                dropdown.append(`
                                    <div class="search-item" onclick="addUser(${user.id}, '${user.name.replace(/'/g, "\\'")}', ${user.has_fcm})">
                                        <div class="user-info">${user.name} ${fcmIcon}</div>
                                        <div class="user-meta">${user.email} · ${user.phone || '-'}</div>
                                    </div>
                                `);
                            }
                        });
                    }

                    dropdown.show();
                });
            }, 300);
        });

        // Close dropdown on outside click
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#userSearchInput, #searchResultsDropdown').length) {
                $('#searchResultsDropdown').hide();
            }
        });

        // ─── Character Counters ─────────────────────────────
        $('input[name="title_ar"]').on('input', function() {
            $('.title-ar-count').text($(this).val().length);
            updatePreview();
        });
        $('input[name="title_en"]').on('input', function() {
            $('.title-en-count').text($(this).val().length);
            updatePreview();
        });
        $('textarea[name="body_ar"]').on('input', function() {
            const len = $(this).val().length;
            const counter = $('.body-ar-count');
            counter.text(len);
            counter.parent().toggleClass('warning', len > 900);
            updatePreview();
        });
        $('textarea[name="body_en"]').on('input', function() {
            const len = $(this).val().length;
            const counter = $('.body-en-count');
            counter.text(len);
            counter.parent().toggleClass('warning', len > 900);
            updatePreview();
        });

        // ─── Send Form ─────────────────────────────────────
        $('#sendNotificationForm').on('submit', function(e) {
            e.preventDefault();

            const target = $('#targetMode').val();
            if (target === 'selected' && Object.keys(selectedUsers).length === 0) {
                toastr.error('{{ __("Please select at least one user") }}');
                return;
            }

            const formData = $(this).serializeArray();

            // Add selected user IDs
            if (target === 'selected') {
                Object.keys(selectedUsers).forEach(id => {
                    formData.push({ name: 'user_ids[]', value: id });
                });
            }

            const targetText = target === 'all'
                ? '{{ __("all users") }}'
                : Object.keys(selectedUsers).length + ' {{ __("selected users") }}';

            Swal.fire({
                title: '{{ __("Confirm Send") }}',
                html: `{{ __("Send this notification to") }} <strong>${targetText}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __("Yes, Send it!") }}',
                cancelButtonText: '{{ __("Cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    const btn = $('#sendBtn');
                    const originalHtml = btn.html();
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> {{ __("Sending...") }}');

                    $.ajax({
                        url: "{{ route('admin.notifications.send') }}",
                        method: 'POST',
                        data: $.param(formData),
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                $('#sendNotificationForm')[0].reset();
                                selectedUsers = {};
                                $('#selectedUsersContainer').empty();
                                $('#selectedCount').text('');
                                updatePreview();
                                loadHistory();
                            } else {
                                toastr.error(response.message || '{{ __("Something went wrong") }}');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                Object.values(errors).forEach(err => toastr.error(err[0]));
                            } else {
                                toastr.error('{{ __("Something went wrong") }}');
                            }
                        },
                        complete: function() {
                            btn.prop('disabled', false).html(originalHtml);
                        }
                    });
                }
            });
        });

        // Reset form handler
        $('#sendNotificationForm').on('reset', function() {
            setTimeout(() => {
                selectedUsers = {};
                $('#selectedUsersContainer').empty();
                $('#selectedCount').text('');
                $('.char-counter span').text('0');
                updatePreview();
            }, 10);
        });
    });

    // ─── Functions ──────────────────────────────────────────

    function addUser(id, name, hasFcm) {
        if (selectedUsers[id]) return;
        selectedUsers[id] = { name, hasFcm };

        const fcmClass = hasFcm ? 'active' : 'inactive';
        const chip = `
            <span class="user-chip" data-user-id="${id}">
                <span class="fcm-dot ${fcmClass}"></span>
                ${name}
                <span class="remove-user" onclick="removeUser(${id})">×</span>
            </span>
        `;
        $('#selectedUsersContainer').append(chip);
        $('#searchResultsDropdown').hide();
        $('#userSearchInput').val('').focus();
        updateSelectedCount();
    }

    function removeUser(id) {
        delete selectedUsers[id];
        $(`.user-chip[data-user-id="${id}"]`).remove();
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = Object.keys(selectedUsers).length;
        const noFcm = Object.values(selectedUsers).filter(u => !u.hasFcm).length;

        let text = count + ' {{ __("user(s) selected") }}';
        if (noFcm > 0) {
            text += ` (${noFcm} {{ __("without FCM token") }})`;
        }
        $('#selectedCount').html(count > 0 ? text : '');
    }

    function updatePreview() {
        const titleAr = $('input[name="title_ar"]').val() || '{{ __("Notification Title") }}';
        const bodyAr = $('textarea[name="body_ar"]').val() || '{{ __("Notification body text...") }}';
        $('#previewTitle').text(titleAr);
        $('#previewBody').text(bodyAr);
    }

    // ─── History ────────────────────────────────────────────

    function loadHistory(page = 1) {
        currentPage = page;
        const params = {
            page: page,
            type: $('#filterType').val(),
            from_date: $('#filterFromDate').val(),
            to_date: $('#filterToDate').val(),
        };

        $('#historyBody').html(`
            <tr><td colspan="7" class="text-center text-muted py-4">
                <i class="fas fa-spinner fa-spin me-2"></i>{{ __('Loading...') }}
            </td></tr>
        `);

        $.get("{{ route('admin.notifications.data') }}", params, function(response) {
            const tbody = $('#historyBody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html(`
                    <tr><td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-bell-slash me-2"></i>{{ __('No notifications found') }}
                    </td></tr>
                `);
                $('#historyPagination').empty();
                return;
            }

            response.data.forEach(n => {
                const readBadge = n.is_read
                    ? '<span class="badge bg-secondary">{{ __("Read") }}</span>'
                    : '<span class="badge bg-primary">{{ __("Unread") }}</span>';

                tbody.append(`
                    <tr>
                        <td><span class="notif-type-badge ${n.type}">${n.type_label}</span></td>
                        <td><strong>${n.title}</strong></td>
                        <td>${n.content}</td>
                        <td>
                            <div>${n.user_name}</div>
                            <small class="text-muted">${n.user_email}</small>
                        </td>
                        <td>${readBadge}</td>
                        <td>
                            <div>${n.created_at}</div>
                            <small class="text-muted">${n.time_ago}</small>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteNotification(${n.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            // Pagination
            renderPagination(response.pagination);
        });
    }

    function renderPagination(pagination) {
        const container = $('#historyPagination');
        container.empty();

        if (pagination.last_page <= 1) return;

        let html = '<nav><ul class="pagination pagination-sm mb-0">';

        // Previous
        html += `<li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadHistory(${pagination.current_page - 1}); return false;">«</a>
        </li>`;

        // Pages
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === 1 || i === pagination.last_page || Math.abs(i - pagination.current_page) <= 2) {
                html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadHistory(${i}); return false;">${i}</a>
                </li>`;
            } else if (Math.abs(i - pagination.current_page) === 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next
        html += `<li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadHistory(${pagination.current_page + 1}); return false;">»</a>
        </li>`;

        html += '</ul></nav>';
        container.html(html);
    }

    function deleteNotification(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This notification will be deleted.") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.notifications.destroy', ':id') }}".replace(':id', id),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            loadHistory(currentPage);
                        }
                    },
                    error: function() {
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                });
            }
        });
    }
</script>
@endsection
