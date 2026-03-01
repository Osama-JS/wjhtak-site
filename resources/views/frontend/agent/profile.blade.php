@extends('frontend.agent.layouts.agent-layout')

@section('title', __('My Profile'))
@section('page-title', __('My Profile'))

@push('styles')
<style>
.profile-grid {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 28px;
    align-items: start;
}

@media (max-width: 992px) { .profile-grid { grid-template-columns: 1fr; } }

.profile-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,.04);
    overflow: hidden;
    border: 1px solid #f1f5f9;
}

.profile-card-header {
    background: linear-gradient(135deg, #1e293b, #334155);
    padding: 36px 24px;
    text-align: center;
    color: #fff;
}

.profile-avatar-wrap {
    position: relative;
    width: 100px;
    margin: 0 auto 16px;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 20px;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,0.2);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.profile-avatar-edit {
    position: absolute;
    bottom: -6px;
    inset-inline-end: -6px;
    width: 32px;
    height: 32px;
    background: var(--accent-color, #e8532e);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .8rem;
    color: #fff;
    cursor: pointer;
    border: 2px solid #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all .2s;
}

.profile-avatar-edit:hover {
    transform: scale(1.1);
}

.profile-name {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 4px;
}

.profile-role {
    font-size: .8rem;
    opacity: .8;
    background: rgba(255,255,255,0.1);
    padding: 4px 12px;
    border-radius: 20px;
    display: inline-block;
}

.profile-card-body { padding: 24px; }

.profile-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f8fafc;
    font-size: .9rem;
}

.profile-stat:last-child { border-bottom: none; }
.profile-stat .label { color: #64748b; font-weight: 500; }
.profile-stat .value { font-weight: 700; color: #1e293b; }

/* Form card */
.form-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,.04);
    margin-bottom: 24px;
    border: 1px solid #f1f5f9;
}

.form-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    font-weight: 700;
    font-size: 1rem;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-card-header i { color: var(--accent-color, #e8532e); }
.form-card-body { padding: 24px; }

.form-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 600px) { .form-row-2 { grid-template-columns: 1fr; } }

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: .85rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 8px;
}

.form-group input,
.form-group select {
    width: 100%;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: .95rem;
    outline: none;
    background: #fcfdfe;
    transition: all .2s;
}

.form-group input:focus,
.form-group select:focus {
    border-color: var(--accent-color, #e8532e);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(232,83,46,0.06);
}

.form-group .error-msg {
    color: #ef4444;
    font-size: .8rem;
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.btn-save {
    padding: 12px 32px;
    background: var(--accent-color, #e8532e);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: .95rem;
    cursor: pointer;
    transition: all .2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-save:hover {
    background: #d04525;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(208,69,37,0.25);
}

/* Photo upload */
#photoInput { display: none; }

.profile-status {
    margin-top: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 0.8rem;
    font-weight: 700;
    color: #10b981;
}

.profile-status i { font-size: 0.6rem; }
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
                    <label for="photoInput" class="profile-avatar-edit" title="{{ __('Change Photo') }}">
                        <i class="fas fa-camera"></i>
                    </label>
                </div>
                <div class="profile-name">{{ $user->full_name }}</div>
                <h2 class="company-name h1 mb-1">{{ app()->getLocale() == 'en' && auth()->user()->company->en_name ? auth()->user()->company->en_name : auth()->user()->company->name }}</h2>
                <div class="profile-role">{{ __('Agent') }} @if($user->company) ({{ $user->company->name }}) @endif</div>
                <div class="profile-status"><i class="fas fa-circle"></i> {{ __('Active Account') }}</div>
            </div>
            <div class="profile-card-body">
                <div class="profile-stat">
                    <span class="label">{{ __('Member Since') }}</span>
                    <span class="value">{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="profile-stat">
                    <span class="label">{{ __('Email Address') }}</span>
                    <span class="value" style="font-size: 0.8rem;">{{ $user->email }}</span>
                </div>
            </div>
        </div>

        {{-- Photo Upload (hidden form) --}}
        <form method="POST" action="{{ route('agent.profile.photo') }}" enctype="multipart/form-data" id="photoForm">
            @csrf
            <input type="file" id="photoInput" name="photo" accept="image/*" onchange="uploadPhoto()">
        </form>
    </div>

    {{-- ─── Right: Forms ─── --}}
    <div>
        {{-- Profile Info Form --}}
        <div class="form-card">
            <div class="form-card-header">
                <i class="fas fa-user-edit"></i> {{ __('Edit Profile Information') }}
            </div>
            <div class="form-card-body">
                <form method="POST" action="{{ route('agent.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('First Name') }}</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                            @error('first_name') <div class="error-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                            @error('last_name') <div class="error-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('Email Address') }}</label>
                            <input type="email" name="email" value="{{ $user->email }}" disabled style="opacity:.6;background:#f1f5f9;cursor:not-allowed;">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Phone Number') }}</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required>
                            @error('phone') <div class="error-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
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
                        <i class="fas fa-save"></i> {{ __('Update Profile') }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="form-card">
            <div class="form-card-header">
                <i class="fas fa-lock"></i> {{ __('Security & Password') }}
            </div>
            <div class="form-card-body">
                <form method="POST" action="{{ route('agent.profile.password') }}">
                    @csrf

                    <div class="form-group">
                        <label>{{ __('Current Password') }}</label>
                        <input type="password" name="current_password" placeholder="••••••••" required>
                        @error('current_password') <div class="error-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('New Password') }}</label>
                            <input type="password" name="password" placeholder="••••••••" minlength="8" required>
                            @error('password') <div class="error-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>{{ __('Confirm New Password') }}</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••" minlength="8" required>
                        </div>
                    </div>

                    <div style="background: #f8fafc; padding: 16px; border-radius: 10px; margin-bottom: 20px; font-size: 0.8rem; color: #64748b; line-height: 1.5;">
                        <i class="fas fa-info-circle"></i> {{ __('Use a strong password with at least 8 characters including letters and numbers.') }}
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
