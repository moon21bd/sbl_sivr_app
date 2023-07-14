document.addEventListener('DOMContentLoaded', function () {
    const mobileNoInput = document.getElementById('mobile_no');
    const errorMessageDiv = document.getElementById('error_message');
    const submitButton = document.querySelector('button[type="submit"]');

    buttonDisable(submitButton, true);
    mobileNoInput.addEventListener('input', function () {
        handlePhoneNumberInput(mobileNoInput, errorMessageDiv, submitButton);
    });

    document.querySelector('button[type="submit"]').addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission
        buttonDisable(submitButton, true);
        showLoader();

        let mobileNumberVal = mobileNoInput.value;
        if (!mobileNumberVal) return;

        axios.post(otpWrap, {
            'mobile_no': mobileNumberVal
        })
            .then(function (response) {
                hideLoader(); // Hide the loader

                let respData = response.data,
                    statusCode = response.status;

                if (statusCode === 200) {
                    console.log('Success');
                    console.log('URL:', respData.url);
                    goTo(respData.url);
                } else {
                    // Handle non-200 response status codes
                    console.log('ErrorCode:', statusCode);
                    console.log('Message:', respData.message);
                    hideLoader(); // Hide the loader
                    displayErrorMessage(respData.message, errorMessageDiv);
                }

            })
            .catch(function (error) {
                let errMsg = error.response.data;
                console.log('catch statusCode', error.response.status);
                console.log('catch error', errMsg);

                hideLoader(); // Hide the loader
                displayErrorMessage(errMsg.message, errorMessageDiv);
            });
    });

});
