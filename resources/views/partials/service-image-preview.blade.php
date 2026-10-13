<div id="service-image-preview" hidden style="margin-top:12px">
    <small>Selected image</small>
    <div style="position:relative;width:120px;max-width:100%;margin-top:6px">
        <img alt="Selected service image preview" style="display:block;width:100%;height:90px;object-fit:cover;border-radius:8px">
        <button id="remove-service-image" type="button" aria-label="Remove selected image" title="Remove image" style="position:absolute;top:4px;right:4px;display:grid;place-items:center;width:22px;height:22px;padding:0;border:1px solid #dfe5ef;border-radius:50%;background:#fff;color:#34415a;font-size:17px;line-height:1;cursor:pointer">&times;</button>
    </div>
</div>
<div id="service-image-selection-error" class="error" role="alert" hidden></div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('image');
        const preview = document.getElementById('service-image-preview');
        const selectionError = document.getElementById('service-image-selection-error');
        let previewUrl;

        document.getElementById('remove-service-image').addEventListener('click', () => {
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                previewUrl = undefined;
            }
            input.value = '';
            input.setCustomValidity('');
            preview.hidden = true;
            preview.querySelector('img').removeAttribute('src');
            selectionError.hidden = true;
            selectionError.textContent = '';
            input.focus();
        });

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
