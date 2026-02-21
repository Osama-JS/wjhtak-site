@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => 'اختر...',
    'required' => false,
    'disabled' => false,
    'multiple' => false,
    'searchable' => false,
    'helpText' => null,
    'error' => null,
    'optionKey' => 'id',
    'optionLabel' => 'name',
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

    <select
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        id="{{ $name }}"
        class="form-select {{ $searchable ? 'select2' : '' }} {{ $errors->has($name) || $error ? 'is-invalid' : '' }} {{ $attributes->get('class') }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $multiple ? 'multiple' : '' }}
        {{ $attributes->except('class') }}
    >
        @if($placeholder && !$multiple)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $key => $option)
            @php
                $optionValue = is_object($option) ? $option->{$optionKey} : (is_array($option) ? $option[$optionKey] : $key);
                $optionText = is_object($option) ? $option->{$optionLabel} : (is_array($option) ? $option[$optionLabel] : $option);
                $isSelected = $multiple
                    ? in_array($optionValue, (array) old($name, $selected ?? []))
                    : $optionValue == old($name, $selected);
            @endphp
            <option value="{{ $optionValue }}" {{ $isSelected ? 'selected' : '' }}>
                {{ $optionText }}
            </option>
        @endforeach
    </select>

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

@if($searchable)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $.fn.select2 !== 'undefined') {
        const selectId = '{{ $attributes->get('id', $name) }}';
        const $select = $('#' + selectId);
        const $modal = $select.closest('.modal');

        $select.select2({
            theme: 'bootstrap-5',
            placeholder: '{{ $placeholder }}',
            allowClear: true,
            dir: document.dir || 'ltr',
            dropdownParent: $modal.length ? $modal : $(document.body)
        });
    }
});
</script>
@endpush
@endif
