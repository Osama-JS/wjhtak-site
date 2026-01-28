@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'icon' => null,
    'helpText' => null,
    'error' => null,
])

<div class="form-group mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label {{ $required ? 'required' : '' }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="input-wrapper position-relative">
        @if($icon)
            <span class="input-icon position-absolute start-0 top-50 translate-middle-y ps-3">
                <i class="{{ $icon }}"></i>
            </span>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            class="form-control {{ $icon ? 'ps-5' : '' }} {{ $errors->has($name) || $error ? 'is-invalid' : '' }} {{ $attributes->get('class') }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $attributes->except('class') }}
        >

        @if($type === 'password')
            <span class="password-toggle position-absolute end-0 top-50 translate-middle-y pe-3" style="cursor: pointer;">
                <i class="fas fa-eye"></i>
            </span>
        @endif
    </div>

    @if($helpText)
        <small class="form-text text-muted">{{ $helpText }}</small>
    @endif

    @error($name)
        <div class="invalid-feedback d-block">
            <i class="fas fa-exclamation-circle me-1"></i>
            {{ $message }}
        </div>
    @enderror

    @if($error)
        <div class="invalid-feedback d-block">
            <i class="fas fa-exclamation-circle me-1"></i>
            {{ $error }}
        </div>
    @endif
</div>
