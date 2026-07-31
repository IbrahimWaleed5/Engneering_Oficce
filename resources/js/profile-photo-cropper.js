import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

document.addEventListener('DOMContentLoaded', () => {
    const fileInput =
        document.getElementById('profile_photo');

    const chooseButton =
        document.getElementById('choose_profile_photo');

    const preview =
        document.getElementById('profile_photo_preview');

    const modal =
        document.getElementById('profile_crop_modal');

    const cropImage =
        document.getElementById('profile_crop_image');

    const saveButton =
        document.getElementById('save_profile_crop');

    const cancelButton =
        document.getElementById('cancel_profile_crop');

    const zoomInButton =
        document.getElementById('crop_zoom_in');

    const zoomOutButton =
        document.getElementById('crop_zoom_out');

    const rotateLeftButton =
        document.getElementById('crop_rotate_left');

    const rotateRightButton =
        document.getElementById('crop_rotate_right');

    const resetButton =
        document.getElementById('crop_reset');

    if (
        !fileInput ||
        !chooseButton ||
        !preview ||
        !modal ||
        !cropImage
    ) {
        return;
    }

    let cropper = null;
    let originalFile = null;
    let imageUrl = null;

    function openModal() {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function destroyCropper() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }

        if (imageUrl) {
            URL.revokeObjectURL(imageUrl);
            imageUrl = null;
        }

        cropImage.removeAttribute('src');
    }

    function initializeCropper() {
        if (cropper) {
            cropper.destroy();
        }

        cropper = new Cropper(cropImage, {
            aspectRatio: 1,
            viewMode: 1,

            dragMode: 'move',
            autoCropArea: 1,

            responsive: true,
            restore: false,

            guides: true,
            center: true,
            highlight: false,

            background: false,

            movable: true,
            rotatable: true,
            scalable: true,
            zoomable: true,
            zoomOnTouch: true,
            zoomOnWheel: true,

            cropBoxMovable: true,
            cropBoxResizable: true,

            toggleDragModeOnDblclick: false,

            checkOrientation: true,
        });
    }

    chooseButton.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        const file = fileInput.files?.[0];

        if (!file) {
            return;
        }

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];

        if (!allowedTypes.includes(file.type)) {
            alert(
                'اختر صورة بصيغة JPG أو PNG أو WEBP.'
            );

            fileInput.value = '';
            return;
        }

        const maxFileSize = 5 * 1024 * 1024;

        if (file.size > maxFileSize) {
            alert(
                'حجم الصورة يجب ألا يتجاوز 5MB.'
            );

            fileInput.value = '';
            return;
        }

        originalFile = file;

        destroyCropper();

        imageUrl = URL.createObjectURL(file);
        cropImage.src = imageUrl;

        openModal();

        cropImage.onload = () => {
            initializeCropper();
        };
    });

    zoomInButton?.addEventListener('click', () => {
        cropper?.zoom(0.1);
    });

    zoomOutButton?.addEventListener('click', () => {
        cropper?.zoom(-0.1);
    });

    rotateLeftButton?.addEventListener('click', () => {
        cropper?.rotate(-90);
    });

    rotateRightButton?.addEventListener('click', () => {
        cropper?.rotate(90);
    });

    resetButton?.addEventListener('click', () => {
        cropper?.reset();
    });

    cancelButton?.addEventListener('click', () => {
        fileInput.value = '';
        originalFile = null;

        destroyCropper();
        closeModal();
    });

    saveButton?.addEventListener('click', () => {
        if (!cropper || !originalFile) {
            return;
        }

        saveButton.disabled = true;
        saveButton.textContent = 'جاري تجهيز الصورة...';

        const canvas = cropper.getCroppedCanvas({
            width: 800,
            height: 800,

            fillColor: '#ffffff',

            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!canvas) {
            saveButton.disabled = false;
            saveButton.textContent = 'حفظ الصورة';

            alert('تعذر قص الصورة، حاول مرة أخرى.');
            return;
        }

        canvas.toBlob(
            (blob) => {
                if (!blob) {
                    saveButton.disabled = false;
                    saveButton.textContent = 'حفظ الصورة';

                    alert(
                        'تعذر تجهيز الصورة، حاول مرة أخرى.'
                    );

                    return;
                }

                const croppedFile = new File(
                    [blob],
                    `profile-${Date.now()}.jpg`,
                    {
                        type: 'image/jpeg',
                        lastModified: Date.now(),
                    }
                );

                /*
                 * استبدال الملف الأصلي بالصورة المقصوصة،
                 * وبذلك يصل إلى Laravel كملف عادي.
                 */
                const transfer = new DataTransfer();

                transfer.items.add(croppedFile);
                fileInput.files = transfer.files;

                preview.src = URL.createObjectURL(blob);
                preview.classList.remove('hidden');

                const previewIcon =
                    document.getElementById(
                        'profile_photo_icon'
                    );

                previewIcon?.classList.add('hidden');

                const fileName =
                    document.getElementById(
                        'profile_photo_name'
                    );

                if (fileName) {
                    fileName.textContent =
                        'تم تجهيز الصورة بنجاح';
                }

                destroyCropper();
                closeModal();

                saveButton.disabled = false;
                saveButton.textContent = 'حفظ الصورة';
            },
            'image/jpeg',
            0.9
        );
    });

    modal.addEventListener('click', (event) => {
        if (event.target !== modal) {
            return;
        }

        fileInput.value = '';
        destroyCropper();
        closeModal();
    });

    document.addEventListener('keydown', (event) => {
        if (
            event.key === 'Escape' &&
            !modal.classList.contains('hidden')
        ) {
            fileInput.value = '';

            destroyCropper();
            closeModal();
        }
    });
});
