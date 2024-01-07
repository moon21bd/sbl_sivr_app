const fileInputContainer = document.getElementById('fileInputContainer');
const userPhotoIcon = document.getElementById('userPhotoIcon');
const MAX_IMAGE_SIZE_MB = 10;
const MAX_IMAGE_SIZE_BYTES = MAX_IMAGE_SIZE_MB * 1024 * 1024;

async function handleImageUpload(event) {
    try {
        await checkLoginStatus();
        const file = event.target.files[0];

        if (file.size > MAX_IMAGE_SIZE_BYTES) {
            Swal.fire({
                icon: 'error',
                title: 'File Size Exceeded',
                text: 'The selected file size exceeds the maximum limit of 10 MB.',
                allowOutsideClick: false,
                willClose: () => {
                    stopAllAudioPlayback();
                },
                customClass: {
                    container: 'active-your-service-swal-bg'
                },
            });
            // playErrorAudio('/uploads/prompts/photo-upload-limit-exceeded.m4a');
            return;
        }

        if (!file.type.startsWith('image/')) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Please select an image file (JPEG, PNG, GIF, etc.).',
                allowOutsideClick: false,
                willClose: () => {
                    stopAllAudioPlayback();
                },
                customClass: {
                    container: 'active-your-service-swal-bg'
                },
            });
            return;
        }

        showLoader();

        const reader = new FileReader();
        reader.readAsDataURL(file);

        reader.onload = async () => {
            const base64Image = reader.result;
            const compressedImage = await compressImage(base64Image);
            const response = await axios.post('/upload-photo', {
                photo: compressedImage,
            });

            userPhotoIcon.src = response.data.image_url;
            fileInputContainer.innerHTML = '';
            fileInputContainer.style.display = 'none';

            hideLoader();
        };

        reader.onerror = (error) => {
            console.error('Error reading the file:', error);
            Swal.fire({
                icon: 'error',
                title: 'File Read Error',
                text: 'An error occurred while reading the file. Please try again.',
                allowOutsideClick: false,
                willClose: () => {
                    stopAllAudioPlayback();
                },
                customClass: {
                    container: 'active-your-service-swal-bg'
                },
            });
            hideLoader();
        };
    } catch (error) {
        console.error('ERROR', error.message, error.response.data, error, event.target.files[0]);

        Swal.fire({
            icon: 'error',
            title: 'File Upload Error',
            text: 'An error occurred while uploading the file. Please try again later.',
            allowOutsideClick: false,
            willClose: () => {
                stopAllAudioPlayback();
            },
            customClass: {
                container: 'active-your-service-swal-bg'
            },
        });
        hideLoader();
    }
}

async function compressImage(base64Image) {
    const img = new Image();
    img.src = base64Image;

    return new Promise((resolve) => {
        img.onload = () => {
            const MAX_WIDTH = 800;
            const scaleFactor = MAX_WIDTH / img.width;
            const height = img.height * scaleFactor;

            const canvas = document.createElement('canvas');
            canvas.width = MAX_WIDTH;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, MAX_WIDTH, height);

            canvas.toBlob(
                (blob) => {
                    const reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = () => {
                        resolve(reader.result);
                    };
                },
                'image/jpeg',
                0.7
            );
        };
    });
}

function createFileInput() {
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.name = 'photo';
    fileInput.style.display = 'none';
    fileInput.accept = 'image/*';
    fileInput.addEventListener('change', handleImageUpload);
    fileInputContainer.appendChild(fileInput);

    setTimeout(() => {
        fileInput.click();
    }, 100);
}

userPhotoIcon.addEventListener('click', () => {
    checkLoginStatus()
        .then(isLoggedIn => {
            if (isLoggedIn) {
                createFileInput();
            } else {
                showVerificationAlert();
            }
        })
        .catch(error => console.error(error));
});

document.body.addEventListener('click', (event) => {
    const isPhotoIconClicked = event.target === userPhotoIcon;
    const isSwalShown = Swal.isVisible();

    if (isSwalShown && isPhotoIconClicked) {
        event.stopPropagation();
    }
});
