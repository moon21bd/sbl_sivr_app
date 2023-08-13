document.addEventListener('DOMContentLoaded', function () {

    const currentPath = window.location.pathname;

    // Function to handle API calls
    async function callDynamicAPI(data) {
        try {
            const response = await axios.post(callDynamically, data);
            return response.data;
        } catch (error) {
            throw error.response.data;
        }
    }

    // Function to enter reason in SweetAlert
    async function enterReason(title, message, audioFile) {
        const popupAudio = new Audio(`/uploads/prompts/${audioFile}.mp3`);
        popupAudio.play();

        return Swal.fire({
            title: title,
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Submit',
            showLoaderOnConfirm: false,
            allowOutsideClick: () => !Swal.isLoading(),
            inputValidator: (reason) => {
                if (!reason) {
                    popupAudio.play();
                    return message;
                }
                return null;
            }
        }).then((result) => {
            if (popupAudio) {
                popupAudio.pause();
            }
            return result;
        });
    }

    // Function to handle reset PIN
    async function handleResetPin() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for resetting PIN?', "Please enter the reason for resetting PIN.", 'enter-reason-resetting-pin');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'resetPin', 'page': 'home', 'button': 'btnResetPin', 'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error'
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in handleResetPin:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    // Function to handle card activation
    async function handleCardActivateClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for card activation?', "Please enter the reason for card activation.", 'enter-reason-card-activation');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'cardActivate', 'page': 'home', 'button': 'btnCardActivate', 'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error'
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnCardActivate click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    // Function to handle device binding
    async function handleDeviceBindClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for device binding?', "Please enter the reason for binding the device.", 'enter-reason-device-bind');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'deviceBind', 'page': 'home', 'button': 'btnDeviceBind', 'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error'
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnDeviceBind click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleLockWalletClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for locking the wallet?', "Please enter the reason for locking the wallet.", 'enter-reason-locking-wallet');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'lockWallet', 'page': 'home', 'button': 'btnLockWallet', 'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error',
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnLockWallet click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleCreateIssueClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for creating the issue?', "Please enter the reason for creating an issue.", 'enter-reason-creating-issue');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'createIssue', 'page': 'home', 'button': 'btnCreateIssue', 'reason': reason.value
                    });
                    console.log('apiResponse', apiResponse)

                    const issueId = apiResponse.data?.issueId;
                    const issue = issueId ? issueId : null;
                    Swal.fire({
                        title: apiResponse.message,
                        icon: apiResponse.status === 'success' ? 'success' : 'error',
                        text: "IssueId: " + issue
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnCreateIssue click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    console.log('currentPath: ' + currentPath)
    if (currentPath === '/') {

        // Event listener for cards button
        const btnCards = document.getElementById('btnCards');
        btnCards.addEventListener('click', handleCardsButtonClick);

        // Event listener for accountOrLoan button
        // const accountOrLoan = document.getElementById('btnAccountOrLoan');
        // accountOrLoan.addEventListener('click', handleAccountOrLoanButtonClick);

        addClickEventWithAsyncHandler('btnAccountOrLoan', showMessageForHelp);


        // Event listener for creation issue button
        const btnCreateIssue = document.getElementById('btnCreateIssue');
        btnCreateIssue.addEventListener('click', handleCreateIssueClick);

        // Handle click events on the buttons
        addClickEventWithAsyncHandler('btnSonaliBankProduct', showMessageForHelp);
        addClickEventWithAsyncHandler('btnAgentBanking', showMessageForHelp);
        addClickEventWithAsyncHandler('btnIslamiBanking', showMessageForHelp);
        addClickEventWithAsyncHandler('btnSPG', showMessageForHelp);
        addClickEventWithAsyncHandler('btnEWallet', showMessageForHelp);
        addClickEventWithAsyncHandler('btnESheba', showMessageForHelp);

        /*// Event listener for create issue button
        const btnLockWallet = document.getElementById('btnLockWallet');
        btnLockWallet.addEventListener('click', handleLockWalletClick);

        // Event listener for device binding button
        const btnDeviceBind = document.getElementById('btnDeviceBind');
        btnDeviceBind.addEventListener('click', handleDeviceBindClick);

        // Event listener for reset PIN button
        const btnResetPin = document.getElementById('btnResetPin');
        btnResetPin.addEventListener('click', handleResetPin);

        // Event listener for card activation button
        const btnCardActivate = document.getElementById('btnCardActivate');
        btnCardActivate.addEventListener('click', handleCardActivateClick);

        // Handle click events on the buttons
        addClickEventWithAsyncHandler('btnPaymentInfo', showMessageForHelp);
        addClickEventWithAsyncHandler('btnStatement', showMessageForHelp);
        addClickEventWithAsyncHandler('btnCardDetails', showMessageForHelp);
        addClickEventWithAsyncHandler('btnAgentAssist', showMessageForHelp);*/


    } // end of pathname detect and conditionally assign event listener
    else if (currentPath === '/cards') {

        const btnDebitCardActivate = document.getElementById('btnDebitCard');
        btnDebitCardActivate.addEventListener('click', handleDebitCardActivationClick);

        const btnCreditCardActivate = document.getElementById('btnCreditCard');
        btnCreditCardActivate.addEventListener('click', handleCreditCardActivationClick);

        const btnPrepaidCardActivate = document.getElementById('btnPrepaidCard');
        btnPrepaidCardActivate.addEventListener('click', handlePrepaidCardActivationClick);

    }

    async function handleDebitCardActivationClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for debit card activation?', "Please enter the reason for debit card activation.", 'enter-reason-for-debit-card-activation');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'debitCardActivation',
                        'page': 'cards',
                        'button': 'btnDebitCard',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error'
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnDebitCardActivate click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleCreditCardActivationClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for credit card activation?', "Please enter the reason for credit card activation.", 'enter-reason-for-credit-card-activation');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'creditCardActivation',
                        'page': 'cards',
                        'button': 'btnCreditCard',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error'
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnCreditCard click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handlePrepaidCardActivationClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                const reason = await enterReason('Reason for prepaid card activation?', "Please enter the reason for prepaid card activation.", 'enter-reason-for-prepaid-card-activation');

                if (reason.isConfirmed) {
                    const apiResponse = await callDynamicAPI({
                        'purpose': 'prepaidCardActivation',
                        'page': 'cards',
                        'button': 'btnPrepaidCard',
                        'reason': reason.value
                    });

                    Swal.fire({
                        title: apiResponse.message, icon: apiResponse.status === 'success' ? 'success' : 'error'
                    });
                    playErrorAudio(apiResponse.prompt);
                }
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in btnPrepaidCard click:', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    // Event listener for card activation button


    document.querySelectorAll('.radioBtn a').forEach(button => {
        button.addEventListener('click', function () {
            const locale = this.getAttribute('data-locale');

            // Update the active state based on the clicked locale
            setActiveState(locale);

            // Show loader before making the AJAX request
            showLoader();

            // Simulate axios post request with vanilla JavaScript fetch API
            axios.post('/change-locale', {locale: locale})
                .then(response => {
                    console.log(response.data);
                    // Redirect to the received URL
                    goTo(response.data.redirect);
                })
                .catch(error => {
                    console.error(error);
                    // Handle any errors that occur during the request
                })
                .finally(() => {
                    // Hide loader after the AJAX request is complete (success or error)
                    hideLoader();
                });
        });
    });

    // Check for the saved locale in cookie or localStorage
    const savedLocale = getSavedLocale();
    // Set the initial active state based on the saved locale
    setActiveState(savedLocale);

    document.getElementById('btnLogout').addEventListener('click', function (event) {
        event.preventDefault();

        // Make an AJAX request to the logout endpoint
        axios.post('/logout')
            .then(response => {
                // Handle the successful logout response (if needed)
                console.log('logout response', response.data);
                goTo();
            })
            .catch(error => {
                // Handle any errors that occur during the logout request
                console.error(error);
            });
    });

    async function showMessageForHelp() {
        // Show the loader while the async operation is in progress
        showLoader();

        // Simulate an async operation (e.g., API call or any other processing)
        // Here, we use setTimeout to simulate the async operation
        await new Promise(resolve => setTimeout(resolve, 500));

        // Close the loader
        hideLoader();

        // Play the error audio
        playErrorAudio('/uploads/prompts/call-for-help.mp3');

        // Show the message for help with the "Call" button
        const result = await Swal.fire({
            icon: 'info',
            title: 'Service Not Available',
            text: 'Please call 16639 to get help.',
            showCancelButton: true,
            confirmButtonText: 'Call',
            cancelButtonText: 'OK',
            reverseButtons: true, // To switch the "Call" and "OK" buttons' positions
        });

        // Call the "goTo" function if the user clicked the "Call" button
        if (result.isConfirmed) {
            goTo('tel:' + helpCenterNumber);
        }
    }

    // Helper function to add click event listener with async function
    function addClickEventWithAsyncHandler(elementId, asyncHandler) {
        document.getElementById(elementId).addEventListener('click', asyncHandler);
    }


    // Handling cards functionality from here.

    async function handleCardsButtonClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                // redirect to cards page
                goTo('/cards');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in cardsButtonClick :', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }

    async function handleAccountOrLoanButtonClick() {
        try {
            const isLoggedIn = await checkLoginStatus();
            if (isLoggedIn) {
                // redirect to cards page
                goTo('/account-or-loan');
            } else {
                showVerificationAlert();
            }
        } catch (error) {
            console.error('Error in cardsButtonClick :', error);

            if (error.status === 'error') {
                Swal.fire({
                    title: error.message, icon: 'error'
                });
                playErrorAudio(error.prompt);
            }
        }
    }


});
