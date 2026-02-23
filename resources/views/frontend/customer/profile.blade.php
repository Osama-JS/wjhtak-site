@extends('frontend.customer.layouts.customer-layout')

@section('title', __('ملفي الشخصي'))
@section('page-title', __('ملفي الشخصي'))

@push('styles')
<style>
.profile-grid {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 22px;
    align-items: start;
}

@media (max-width: 900px) { .profile-grid { grid-template-columns: 1fr; } }

.profile-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    overflow: hidden;
}

.profile-card-header {
    background: linear-gradient(135deg, #1a2537, #2d3f5e);
    padding: 28px 22px;
    text-align: center;
    color: #fff;
}

.profile-avatar-wrap {
    position: relative;
    width: 90px;
    margin: 0 auto 12px;
}

.profile-avatar {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--accent-color, #e8532e);
}

.profile-avatar-edit {
    position: absolute;
    bottom: 0;
    inset-inline-end: -4px;
    width: 28px;
    height: 28px;
    background: var(--accent-color, #e8532e);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    color: #fff;
    cursor: pointer;
    border: 2px solid #fff;
}

.profile-name {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 4px;
}

.profile-email {
    font-size: .8rem;
    opacity: .7;
}

.profile-card-body { padding: 18px; }

.profile-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f3f4f6;
    font-size: .88rem;
}

.profile-stat:last-child { border-bottom: none; }
.profile-stat .label { color: #6b7280; }
.profile-stat .value { font-weight: 700; color: #111827; }

/* Form card */
.form-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    margin-bottom: 22px;
}

.form-card-header {
    padding: 16px 22px;
    border-bottom: 1px solid #f3f4f6;
    font-weight: 700;
    font-size: .95rem;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-card-header i { color: var(--accent-color, #e8532e); }
.form-card-body { padding: 22px; }

.form-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

@media (max-width: 600px) { .form-row-2 { grid-template-columns: 1fr; } }

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    font-size: .83rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 5px;
}

.form-group input,
.form-group select {
    width: 100%;
    border: 1.5px solid #e5e7eb;
    border-radius: 9px;
    padding: 10px 14px;
    font-size: .9rem;
    outline: none;
    background: #fafafa;
    transition: border .2s;
}

.form-group input:focus,
.form-group select:focus {
    border-color: var(--accent-color, #e8532e);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(232,83,46,.08);
}

.form-group .error-msg {
    color: #ef4444;
    font-size: .78rem;
    margin-top: 4px;
}

.btn-save {
    padding: 11px 28px;
    background: var(--accent-color, #e8532e);
    color: #fff;
    border: none;
    border-radius: 9px;
    font-weight: 700;
    font-size: .9rem;
    cursor: pointer;
    transition: background .2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-save:hover { background: #d04525; }

/* Photo upload */
#photoInput { display: none; }
</style>
@endpush

@section('content')
<div class="profile-grid">

    {{-- ─── Left: Profile Card ─── --}}
    <div>
        <div class="profile-card">
            <div class="profile-card-header">
                <div class="profile-avatar-wrap">
                    <img id="avatarPreview" src="{{ $user->profile_photo_url }}" class="profile-avatar" alt="">
                    <label for="photoInput" class="profile-avatar-edit" title="{{ __('تغيير الصورة') }}">
                        <i class="fas fa-camera"></i>
                    </label>
                </div>
                <div class="profile-name">{{ $user->full_name }}</div>
                <div class="profile-email">{{ $user->email }}</div>
            </div>
            <div class="profile-card-body">
                <div class="profile-stat">
                    <span class="label">{{ __('Phone Number') }}</span>
                    <span class="value">{{ $user->phone ?? '—' }}</span>
                </div>
                <div class="profile-stat">
                    <span class="label">{{ __('City') }}</span>
                    <span class="value">{{ $user->city ?? '—' }}</span>
                </div>
                <div class="profile-stat">
                    <span class="label">{{ __('Gender') }}</span>
                    <span class="value">
                        @if($user->gender === 'male') {{ __('Male') }}
                        @elseif($user->gender === 'female') {{ __('Female') }}
                        @else —
                        @endif
                    </span>
                </div>
                <div class="profile-stat">
                    <span class="label">{{ __('Date of Birth') }}</span>
                    <span class="value">{{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d/m/Y') : '—' }}</span>
                </div>
                <div class="profile-stat">
                    <span class="label">{{ __('Member Since') }}</span>
                    <span class="value">{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Photo Upload (hidden form) --}}
        <form method="POST" action="{{ route('customer.profile.photo') }}" enctype="multipart/form-data" id="photoForm">
            @csrf
            <input type="file" id="photoInput" name="photo" accept="image/*" onchange="uploadPhoto()">
        </form>
    </div>

    {{-- ─── Right: Forms ─── --}}
    <div>
        {{-- Profile Info Form --}}
        <div class="form-card">
            <div class="form-card-header">
                <i class="fas fa-user-edit"></i> {{ __('Edit My Data') }}
            </div>
            <div class="form-card-body">
                <form method="POST" action="{{ route('customer.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('First Name') }}</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                            @error('first_name') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                            @error('last_name') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('Email Address') }}</label>
                            <input type="email" name="email" value="{{ $user->email }}" disabled style="opacity:.6;cursor:not-allowed;">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Phone Number') }}</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('City') }}</label>
                            <input type="text" name="city" value="{{ old('city', $user->city) }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Gender') }}</label>
                            <select name="gender">
                                <option value="">{{ __('Select') }}</option>
                                <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('Date of Birth') }}</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date', $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Address') }}</label>
                            <input type="text" name="address" value="{{ old('address', $user->address) }}">
                        </div>
                    </div>

                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> {{ __('Save Changes') }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="form-card">
            <div class="form-card-header">
                <i class="fas fa-lock"></i> {{ __('Change Password') }}
            </div>
            <div class="form-card-body">
                <form method="POST" action="{{ route('customer.profile.password') }}">
                    @csrf

                    <div class="form-group">
                        <label>{{ __('Current Password') }}</label>
                        <input type="password" name="current_password" placeholder="••••••••" required>
                        @error('current_password') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('New Password') }}</label>
                            <input type="password" name="password" placeholder="••••••••" minlength="8" required>
                            @error('password') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>{{ __('Confirm New Password') }}</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••" minlength="8" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-save">
                        <i class="fas fa-key"></i> {{ __('Change Password') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function uploadPhoto() {
    const input = document.getElementById('photoInput');
    if (input.files && input.files[0]) {
        // Preview
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
        // Submit form
        document.getElementById('photoForm').submit();
    }
}
</script>
@endpush
