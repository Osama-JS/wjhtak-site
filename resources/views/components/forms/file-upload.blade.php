@props([
    'name',
    'label' => null,
    'accept' => null,
    'multiple' => false,
    'required' => false,
    'disabled' => false,
    'preview' => true,
    'currentImage' => null,
    'helpText' => null,
    'error' => null,
    'maxSize' => '5MB',
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

    <div class="file-upload-wrapper {{ $errors->has($name) || $error ? 'border-danger' : '' }}">
        <input
            type="file"
            name="{{ $name }}{{ $multiple ? '[]' : '' }}"
            id="{{ $name }}"
            class="file-upload-input {{ $attributes->get('class') }}"
            {{ $accept ? "accept=$accept" : '' }}
            {{ $multiple ? 'multiple' : '' }}
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except('class') }}
        >

        <div class="file-upload-content">
            <div class="file-upload-icon">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <h5 class="file-upload-title">{{ __('اسحب الملفات هنا') }}</h5>
            <p class="file-upload-text text-muted">
                {{ __('أو انقر للاختيار') }}
            </p>
            <small class="text-muted">
                {{ __('الحد الأقصى:') }} {{ $maxSize }}
                @if($accept)
                    | {{ $accept }}
                @endif
            </small>
        </div>
    </div>

    @if($preview)
        <div class="current-image-preview mt-3" style="{{ !$currentImage ? 'display: none;' : '' }}">
            <label class="form-label text-muted">{{ __('الصورة الحالية') }}:</label>
            <div class="position-relative d-inline-block">
                <img src="{{ $currentImage ? asset($currentImage) : '' }}" alt="Current Image" class="img-thumbnail" style="max-width: 200px;">
            </div>
        </div>
    @endif

    <div class="file-preview-container mt-3" id="{{ $name }}_preview" style="display: none;">
        <label class="form-label text-muted">{{ __('معاينة') }}:</label>
        <div class="file-preview-list d-flex flex-wrap gap-2"></div>
    </div>

    @if($helpText)
        <small class="form-text text-muted d-block mt-2">{{ $helpText }}</small>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('{{ $name }}');
    const previewContainer = document.getElementById('{{ $name }}_preview');
    const previewList = previewContainer?.querySelector('.file-preview-list');

    if (input && previewContainer && previewList) {
        const wrapper = input.closest('.file-upload-wrapper');

        // Drag and drop visual feedback
        if (wrapper) {
            ['dragenter', 'dragover'].forEach(eventName => {
                wrapper.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    wrapper.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                wrapper.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    wrapper.classList.remove('dragover');
                }, false);
            });
        }

        input.addEventListener('change', function(e) {
            previewList.innerHTML = '';

            if (this.files.length > 0) {
                previewContainer.style.display = 'block';

                Array.from(this.files).forEach(function(file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const preview = document.createElement('div');
                        preview.className = 'file-preview-item position-relative';

                        if (file.type.startsWith('image/')) {
                            preview.innerHTML = `
                                <img src="${e.target.result}" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                <span class="file-name d-block text-truncate" style="max-width: 150px;">${file.name}</span>
                            `;
                        } else {
                            preview.innerHTML = `
                                <div class="p-3 bg-light rounded text-center" style="min-width: 150px;">
                                    <i class="fas fa-file fa-3x text-muted mb-2"></i>
                                    <span class="file-name d-block text-truncate">${file.name}</span>
                                    <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                                </div>
                            `;
                        }

                        previewList.appendChild(preview);
                    };

                    reader.readAsDataURL(file);
                });
            } else {
                previewContainer.style.display = 'none';
            }
        });
    }
});
</script>
@endpush
