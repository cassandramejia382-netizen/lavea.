<div id="service-image-preview" hidden style="margin-top:12px">
    <small>Selected image</small>
    <img alt="Selected service image preview" style="display:block;width:120px;height:90px;max-width:100%;object-fit:cover;border-radius:8px;margin-top:6px">
</div>
<div id="service-image-selection-error" class="error" role="alert" hidden></div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('image');
        const preview = document.getElementById('service-image-preview');
        const selectionError = document.getElementById('service-image-selection-error');
        let previewUrl;

        input.addEventListener('change', () => {
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            const file = input.files[0];
            preview.hidden = true;
            preview.querySelector('img').removeAttribute('src');
            input.setCustomValidity('');
            selectionError.hidden = true;
            selectionError.textContent = '';

            if (!file) {
                return;
            }

            let errorMessage = '';
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                errorMessage = 'Choose a JPG, JPEG, PNG or WEBP image.';
            }

            if (errorMessage) {
                input.setCustomValidity(errorMessage);
                selectionError.textContent = errorMessage;
                selectionError.hidden = false;
            } else {
                previewUrl = URL.createObjectURL(file);
                preview.querySelector('img').src = previewUrl;
                preview.hidden = false;
            }
        });
    });
</script>
