document.addEventListener('DOMContentLoaded', function () {
    const btnCardActivate = document.getElementById('btnCardActivate');

    // Function to call the desired API using Promise
    function handleCallingDynamicAPI(data) {
        return new Promise((resolve, reject) => {
            axios.post(callDynamically, data)
                .then(response => {
                    hideLoader();
                    resolve(response.data);
                })
                .catch(error => {
                    hideLoader();
                    reject(error.response.data);
                });
        });
    }

    // Function to handle the card activation button click
    btnCardActivate.addEventListener('click', () => {
        showLoader();

        checkLoginStatus()
            .then(isLoggedIn => {
                if (isLoggedIn) {
                    // User is logged in, proceed with the OTP verification
                    tUj('card-activate', {
                        'purpose': 'cardActivate',
                        'page': 'home',
                        'button': 'btnCardActivate',
                        'user_phone_no': getData('pn'),
                        'user_account_no': getData('acn')
                    });

                    // Call the desired API and handle the response
                    handleCallingDynamicAPI({
                        'purpose': 'cardActivate', 'page': 'home', 'button': 'btnCardActivate',
                    })
                        .then(response => {
                            // Show the SweetAlert with success or error message
                            showToast(response.status, response.message, response.status === 'success' ? 'success' : 'warning');
                            playErrorAudio(response.prompt); // Play audio with the received prompt URL
                        })
                        .catch(error => {
                            // console.log('error', error)
                            showToast('Error', error.message, 'warning');
                            playErrorAudio(error.prompt);
                        })
                        .finally(() => {
                            // Hide loader in the finally block to ensure it's hidden even in case of an error
                            hideLoader();
                        });
                } else {
                    // User is not logged in, show the verification alert
                    console.error('User is not logged in.');
                    showVerificationAlert();
                    hideLoader();
                }
            })
            .catch(error => {
                // console.error(error);
                showVerificationAlert();
                hideLoader();
            });
    });


    // Handle click event on language buttons
    $('.radioBtn a').on('click', function () {
        const locale = $(this).data('locale');

        // Update the active state based on the clicked locale
        setActiveState(locale);

        axios.post('/change-locale', {locale: locale})
            .then(response => {
                console.log(response.data);
                // Redirect to the received URL
                goTo(response.data.redirect);
            })
            .catch(error => {
                console.error(error);
                // Handle any errors that occur during the request
            });
    });

    // Check for the saved locale in cookie or localStorage
    const savedLocale = getSavedLocale();
    // Set the initial active state based on the saved locale
    setActiveState(savedLocale);

});
