document.addEventListener('DOMContentLoaded', function () {

    const inputs = document.querySelectorAll('input[type="number"]');
    const errorMessageDiv = document.getElementById('error_message');
    const successMessageDiv = document.getElementById('success_message');
    const submitButton = document.querySelector('button[type="submit"]');
    const reSendOTPButton = document.querySelector(".resendOtpBtn");
    const timerDisplay = document.getElementById("timer");
    const otpTimer = document.getElementById("otpTimer");
    const countDownFor = 180; // 180 seconds = 3 minutes
    let intervalId;

    otpTimer.style.display = "block";
    startTimer(countDownFor, timerDisplay);

    let locale = getSavedLocale();

    submitButton.addEventListener('click', function (event) {
        event.preventDefault();
        buttonDisable(submitButton, true);
        showLoader();

        const code = getCodeFromInputs(inputs);
        console.log('code', code, code.length);

        if (code === '000000' || code.length !== 6) {
            handleInvalidCode();
            return;
        }

        axios.post(verifyWrap, {'code': code})
            .then(response => handleResponse(response))
            .catch(error => handleError(error))
            .finally(() => {
                resetInputFields(inputs);
            });
    });

    reSendOTPButton.addEventListener('click', async function () {
        showLoader();
        try {
            await resendOtpApiCall();

            let minutes = Math.floor(countDownFor / 60);
            let seconds = countDownFor % 60;
            timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            otpTimer.style.display = 'block';
            reSendOTPButton.style.display = 'none';
            submitButton.style.display = 'block';
            startTimer(countDownFor, timerDisplay);
        } catch (error) {
            console.error('Error during OTP resend:', error);
        } finally {
            hideLoader();
        }
    });

    async function resendOtpApiCall() {
        try {
            showLoader();
            const apiResponse = await callDAPI({
                'purpose': 'RESEND-OTP', 'page': 'home', 'button': 'resendOtpBtn', 'mobile_no': getData('pn')
            });

            console.log('Resend OTP API Response:', apiResponse);

            if (apiResponse.code === 200) {
                console.log('Success');
                displayAndHideMessage(apiResponse.message, successMessageDiv);
            } else {
                hideLoader();
                displayErrorMessage(apiResponse.message, errorMessageDiv);
            }

        } catch (error) {
            console.error('Resend OTP API Error:', error);
        }
    }

    function displayAndHideMessage(message, messageDiv) {
        displayErrorMessage(message, messageDiv);
        setTimeout(function () {
            hideErrorMessage(messageDiv);
        }, 3000);
    }

    function hideErrorMessage(messageDiv) {
        messageDiv.style.display = 'none';
    }

    function startTimer(duration, display) {
        let timer = duration;
        intervalId = setInterval(function () {
            const minutes = Math.floor(timer / 60);
            const seconds = timer % 60;
            display.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

            if (--timer < 0) {
                clearInterval(intervalId); // Stop the timer
                // display.textContent = "OTP has expired.";
                reSendOTPButton.style.display = "block";
                otpTimer.style.display = "none";
                // submitButton.style.display = "none";
            }
        }, 1000);
    }

    inputs.forEach((input, index) => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');

            if (this.value.length === 1 || (this.value.length === 2 && this.value[0] === '0')) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                } else {
                    submitButton.focus();
                }
            }

            const allFilled = Array.from(inputs).every(input => input.value !== '');
            const code = Array.from(inputs).map(input => Number(input.value)).join('');

            if (allFilled && code.length === 6) {
                buttonDisable(submitButton, false);
                clearErrorMessage(errorMessageDiv);
            } else {
                buttonDisable(submitButton, true);
                if (code.length !== 6) {
                    displayErrorMessage((locale === 'en') ? "Please enter all 6 digits of the number." : "দয়া করে, ৬ সংখ্যার কোড লিখুন।", errorMessageDiv);
                } else {
                    clearErrorMessage(errorMessageDiv);
                }
            }
        });

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Backspace') {
                if (this.value.length === 0 && index > 0) {
                    event.preventDefault(); // Prevent default behavior of Backspace
                    inputs[index - 1].focus();
                } else if (this.value.length === 1 && this.value[0] === '0') {
                    event.preventDefault(); // Prevent removing the zero (0) value
                }
            }
        });
    });

    function handleResponse(response) {
        hideLoader();
        const {data: respData, status: statusCode} = response;

        if (statusCode === 200 && respData.status === 'success') {
            handleAccountResponse(respData.acLists);
        } else {
            const audioUrl = respData.prompt;
            playErrorAudio(audioUrl);
            displayErrorMessage(respData.message, errorMessageDiv);
        }
    }

    function handleAccountResponse(accountResponse) {
        const accounts = accountResponse.acList;
        showAccountSelectionPopup(accounts);
    }

    function showAccountSelectionPopup(accounts) {
        stopAllAudio();
        /*const accountOptions = accounts.map(account => `
            <div class="account-option">
                <p>${account.accountName}</p>
                <p>${account.accountNo}</p>
                <button class="ac-select-button" data-account-id="${account.accEnc}">Select</button>
            </div>`).join('');*/

        /*const accountOptions = accounts.map(account => `
    <div class="account-option">
        <input type="radio" name="selectedAccount" value="${account.accEnc}" id="account-${account.accEnc}">
        <label for="account-${account.accEnc}">
            <div class="account-details">
                <p>Account Name: ${account.accountName}</p>
                <p>Account No: ${account.accountNo}</p>
            </div>
        </label>
    </div>`).join('');*/

        const accountOptions = accounts.map(account => `
    <div class="account-option">
        <input type="radio" name="selectedAccount" value="${account.accEnc}" id="account-${account.accEnc}">
        <label for="account-${account.accEnc}">
            <div class="account-details">
                <p>Account Name: ${account.accountName}</p>
                <p>Account No: ${account.accountNo}</p>
            </div>
        </label>
    </div>`).join('');


        /*Swal.fire({
            title: (locale === 'en') ? selectAnAccountEn : selectAnAccountBn,
            html: accountOptions,
            showCancelButton: true,
            confirmButtonText: (locale === 'en') ? "OK" : "ওকে",
            cancelButtonText: (locale === 'en') ? "Cancel" : "বাতিল",
            showConfirmButton: false,
            allowOutsideClick: false
        });*/

        /*Swal.fire({
            title: (locale === 'en') ? selectAnAccountEn : selectAnAccountBn,
            html: `${accountOptions}<button class="ac-submit-button">${(locale === 'en') ? "Submit" : "জমা দিন"}</button>`,
            showCancelButton: true,
            cancelButtonText: (locale === 'en') ? "Cancel" : "বাতিল",
            showConfirmButton: false,
            allowOutsideClick: false
        });*/

        /*document.querySelectorAll('.ac-select-button').forEach(button => {
            button.addEventListener('click', handleSelectButtonClick);
        });*/
        /*const submitButton = document.querySelector('.ac-submit-button');
        submitButton.addEventListener('click', handleAccountSwitchSubmitButtonClick);*/

        Swal.fire({
            title: (locale === 'en') ? selectAnAccountEn : selectAnAccountBn,
            html: `
        ${accountOptions}
        <div class="button-container">
            <button class="ac-submit-button">${(locale === 'en') ? "Submit" : "জমা দিন"}</button>
            <button class="ac-cancel-button">${(locale === 'en') ? "Cancel" : "বাতিল"}</button>
        </div>
    `,
            showConfirmButton: false,
            allowOutsideClick: false
        });

        const submitButton = document.querySelector('.ac-submit-button');
        const cancelButton = document.querySelector('.ac-cancel-button');

        submitButton.addEventListener('click', handleAccountSwitchSubmitButtonClick);
        cancelButton.addEventListener('click', handleAccountSwitchCancelButtonClick);

        const accountOptionsDivs = document.querySelectorAll('.account-option');

        accountOptionsDivs.forEach(optionDiv => {
            optionDiv.addEventListener('click', handleAccountOptionClick);
        });
    }


    function handleAccountSwitchCancelButtonClick() {
        Swal.close();
    }

    function handleAccountSwitchSubmitButtonClick() {
        const selectedAccountId = document.querySelector('input[name="selectedAccount"]:checked');

        if (selectedAccountId) {
            console.log('Selected Account Id:', selectedAccountId.value);

            axios.post('/save', {"ac": selectedAccountId.value})
                .then(response => handleSaveResponse(response))
                .catch(error => console.error('Error saving selected account:', error));

            Swal.close(); // Close the SweetAlert popup
        } else {
            console.log('No account selected');
        }
    }


    function handleAccountOptionClick(event) {
        const clickedOption = event.currentTarget;
        const accountId = clickedOption.getAttribute('data-account-id');

        // Reset background color for all options
        document.querySelectorAll('.account-option').forEach(option => {
            option.style.backgroundColor = ''; // Reset to default
        });

        clickedOption.style.backgroundColor = '#E9B308';
    }

    function handleSelectButtonClick() {
        const selectedAccountId = this.getAttribute('data-account-id');
        console.log('selectedAccountId', selectedAccountId);

        axios.post('/save', {"ac": selectedAccountId})
            .then(response => handleSaveResponse(response))
            .catch(error => console.error('Error saving selected account:', error));
    }

    function handleSaveResponse(response) {
        const {data: respData, status: statusCode} = response;

        if (statusCode === 200 && respData.status === 'success') {
            storeData('pn', respData.pn);
            storeData('acn', respData.acn);
            goTo(respData.url);
        } else {
            const audioUrl = respData.prompt;
            playErrorAudio(audioUrl);
            displayErrorMessage(respData.message, errorMessageDiv);
        }
    }

    function resetInputFields(inputs) {
        inputs.forEach(input => (input.value = ''));
    }

    function getCodeFromInputs(inputs) {
        return Array.from(inputs).map(input => Number(input.value)).join('');
    }

    function stopAllAudio() {
        const audioElements = document.querySelectorAll('audio');
        audioElements.forEach(audio => audio.pause());
    }

    function handleInvalidCode() {
        hideLoader();
        displayErrorMessage((locale === 'en') ? "Please enter all 6 digits of the number." : "দয়া করে, ৬ সংখ্যার কোড লিখুন।", errorMessageDiv);
    }

    function handleError(error) {
        const {response} = error;
        hideLoader();

        if (response) {
            const {status, data: errMsg} = response;
            console.log(`catch statusCode: ${status}`, 'catch error:', errMsg);
            const audioUrl = errMsg.prompt;
            console.log('audioUrl', audioUrl);
            playErrorAudio(audioUrl);

            displayErrorMessage(errMsg.message, errorMessageDiv);
        } else {
            // Handle cases where there is no response (e.g., network error)
            console.error('Error without response:', error);
        }
    }

    async function callDAPI(data) {
        try {
            const response = await axios.post('/resend', data);
            return response.data;
        } catch (error) {
            throw error.response.data;
        }
    }

});


/*document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('input[type="number"]');
    const errorMessageDiv = document.getElementById('error_message');
    const submitButton = document.querySelector('button[type="submit"]');
    let locale = getSavedLocale();

    submitButton.addEventListener('click', function (event) {
        event.preventDefault();
        buttonDisable(submitButton, true);
        showLoader();

        const code = Array.from(inputs).map(input => Number(input.value)).join('');
        console.log('code', code);

        if (code.length !== 6) {
            handleInvalidCode();
            return;
        }

        axios.post(verifyWrap, {'code': code})
            .then(response => handleResponse(response))
            .catch(error => handleError(error))
            .finally(() => {
                // Reset input fields
                inputs.forEach(input => (input.value = ''));
            });

    });

    function handleResponse(response) {
        hideLoader();
        const respData = response.data;
        const statusCode = response.status;

        if (statusCode === 200 && respData.status === 'success') {
            // console.log('Success', respData.acLists);
            handleAccountResponse(respData.acLists)
        } else {
            // console.log('ErrorCode:', statusCode);
            // console.log('Message:', respData.message);
            const audioUrl = respData.prompt;
            playErrorAudio(audioUrl);
            displayErrorMessage(respData.message, errorMessageDiv);
        }
    }

    function handleError(error) {
        hideLoader();
        const errMsg = error.response.data;
        console.log('catch statusCode', error.response.status);
        console.log('catch error', errMsg);
        const audioUrl = errMsg.prompt;
        console.log('audioUrl', audioUrl)
        playErrorAudio(audioUrl);

        displayErrorMessage(errMsg.message, errorMessageDiv);
    }

    inputs.forEach((input, index) => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');

            if (this.value.length === 1 || (this.value.length === 2 && this.value[0] === '0')) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                } else {
                    submitButton.focus();
                }
            }

            const allFilled = Array.from(inputs).every(input => input.value !== '');
            const code = Array.from(inputs).map(input => Number(input.value)).join('');

            if (allFilled && code.length === 6) {
                buttonDisable(submitButton, false);
                clearErrorMessage(errorMessageDiv);
            } else {
                buttonDisable(submitButton, true);
                if (code.length !== 6) {
                    displayErrorMessage((locale === 'en') ? "Please enter all 6 digits of the number." : "দয়া করে, ৬ সংখ্যার কোড লিখুন।", errorMessageDiv);
                } else {
                    clearErrorMessage(errorMessageDiv);
                }
            }
        });

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Backspace') {
                if (this.value.length === 0 && index > 0) {
                    event.preventDefault(); // Prevent default behavior of Backspace
                    inputs[index - 1].focus();
                } else if (this.value.length === 1 && this.value[0] === '0') {
                    event.preventDefault(); // Prevent removing the zero (0) value
                }
            }
        });
    });

    function handleInvalidCode() {
        hideLoader();
        displayErrorMessage((locale === 'en') ? "Please enter all 6 digits of the number." : "দয়া করে, ৬ সংখ্যার কোড লিখুন।", errorMessageDiv);
    }

    function handleAccountResponse(accountResponse) {
        const accounts = accountResponse.acList;
        showAccountSelectionPopup(accounts);
    }

    function showAccountSelectionPopup(accounts) {
        stopAllAudio();
        const accountOptions = accounts.map(account => {
            return `<div class="account-option">
                    <p>${account.accountName}</p>
                    <p>${account.accountNo}</p>
                    <button class="ac-select-button" data-account-id="${account.accEnc}">Select</button>
                </div>`;
        }).join('');

        Swal.fire({
            title: 'Select an account',
            html: accountOptions,
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            showConfirmButton: false,
            allowOutsideClick: false
        });

        // Attach event listeners to the "Select" buttons
        document.querySelectorAll('.ac-select-button').forEach(button => {
            button.addEventListener('click', function () {
                const selectedAccountId = this.getAttribute('data-account-id');
                console.log('selectedAccountId', selectedAccountId);

                axios.post('/save', {"ac": selectedAccountId})
                    .then(response => {
                        const respData = response.data;
                        const statusCode = response.status;
                        if (statusCode === 200 && respData.status === 'success') {
                            storeData('pn', respData.pn);
                            storeData('acn', respData.acn);
                            goTo(respData.url);
                        } else {
                            console.log('ErrorCode:', statusCode);
                            console.log('Message:', respData.message);
                            const audioUrl = respData.prompt;
                            playErrorAudio(audioUrl);
                            displayErrorMessage(respData.message, errorMessageDiv);
                        }

                    })
                    .catch(error => {
                        console.error('Error saving selected account:', error);
                    });
            });
        });
    }

    function stopAllAudio() {
        // Get all audio elements on the page
        const audioElements = document.querySelectorAll('audio');

        // Pause each audio element
        audioElements.forEach(audio => {
            audio.pause();
        });
    }
});*/
