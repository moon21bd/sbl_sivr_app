// Function to handle file upload and form submission
const fileInputContainer = document.getElementById('fileInputContainer');
const userPhotoIcon = document.getElementById('userPhotoIcon');

async function handleImageUpload(event) {
    try {
        // Check login status before proceeding with the file upload
        await checkLoginStatus();

        // User is logged in, continue with the file upload process
        const file = event.target.files[0];
        const formData = new FormData();
        formData.append('photo', file);

        const response = await axios.post('/upload-photo', formData);
        userPhotoIcon.src = response.data.image_url;
        fileInputContainer.innerHTML = ''; // Remove the file input
        fileInputContainer.style.display = 'none'; // Hide the container
    } catch (error) {
        console.error(error);
        goTo('/send-otp');
    }
}

// Function to create a new file input and trigger it
function createFileInput() {
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.name = 'photo';
    fileInput.style.display = 'none';
    fileInput.addEventListener('change', handleImageUpload);
    fileInputContainer.appendChild(fileInput);
    fileInput.click();
}

/*userPhotoIcon.addEventListener('click', () => {
    checkLoginStatus().then(createFileInput).catch(error => console.error(error));
});*/

userPhotoIcon.addEventListener('click', () => {
    checkLoginStatus()
        .then(isLoggedIn => {
            if (isLoggedIn) {
                // User is logged in, proceed with the file upload
                createFileInput();
            } else {
                // User is not logged in, show the verification alert
                showVerificationAlert();
            }
        })
        .catch(error => console.error(error));
});
