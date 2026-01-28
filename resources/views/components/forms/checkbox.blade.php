@props([
    'name',
    'label',
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'type' => 'checkbox', // checkbox or switch
    'helpText' => null,
    'error' => null,
])

<div class="form-group mb-3">
    <div class="form-check {{ $type === 'switch' ? 'form-switch' : '' }}">
        <input
            type="checkbox"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value }}"
            class="form-check-input {{ $errors->has($name) || $error ? 'is-invalid' : '' }} {{ $attributes->get('class') }}"
            {{ old($name, $checked) ? 'checked' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except('class') }}
        >
        <label class="form-check-label" for="{{ $name }}">
            {{ $label }}
        </label>
    </div>

    @if($helpText)
        <small class="form-text text-muted d-block mt-1">{{ $helpText }}</small>
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
