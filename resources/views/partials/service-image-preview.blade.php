<div id="service-image-preview" hidden style="margin-top:12px">
    <small>Selected image</small>
    <img alt="Selected service image preview" style="display:block;width:120px;height:90px;max-width:100%;object-fit:cover;border-radius:8px;margin-top:6px">
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('image');
        const preview = document.getElementById('service-image-preview');
        let previewUrl;

        input.addEventListener('change', () => {
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            const file = input.files[0];
            preview.hidden = true;
            preview.querySelector('img').removeAttribute('src');

            if (file && ['image/jpeg', 'image/png', 'image/webp'].includes(file.type) && file.size <= 2097152) {
                previewUrl = URL.createObjectURL(file);
                preview.querySelector('img').src = previewUrl;
                preview.hidden = false;
            }
        });
    });
</script>
