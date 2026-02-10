@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => '',
    'rows' => 4,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'helpText' => null,
    'error' => null,
    'maxlength' => null,
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

    <textarea
        name="{{ $name }}"
        id="{{ $attributes->get('id', $name) }}"
        class="form-control {{ $errors->has($name) || $error ? 'is-invalid' : '' }} {{ $attributes->get('class') }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $maxlength ? "maxlength=$maxlength" : '' }}
        {{ $attributes->except('class') }}
    >{{ old($name, $value) }}</textarea>

    @if($maxlength)
        <div class="d-flex justify-content-between">
            @if($helpText)
                <small class="form-text text-muted">{{ $helpText }}</small>
            @endif
            <small class="form-text text-muted char-count">
                <span class="current">0</span>/{{ $maxlength }}
            </small>
        </div>
    @elseif($helpText)
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

@if($maxlength)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('{{ $name }}');
    const counter = textarea.closest('.form-group').querySelector('.current');

    if (textarea && counter) {
        counter.textContent = textarea.value.length;

        textarea.addEventListener('input', function() {
            counter.textContent = this.value.length;
        });
    }
});
</script>
@endpush
@endif
