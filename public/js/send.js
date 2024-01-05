document.addEventListener('DOMContentLoaded', function () {
    const mobileNoInput = document.getElementById('mobile_no');
    const errorMessageDiv = document.getElementById('error_message');
    const submitButton = document.querySelector('button[type="submit"]');

    buttonDisable(submitButton, true);
    mobileNoInput.addEventListener('input', function () {
        handlePhoneNumberInput(mobileNoInput, errorMessageDiv, submitButton);
    });

    document.querySelector('button[type="submit"]').addEventListener('click', function (event) {
        event.preventDefault();
        buttonDisable(submitButton, true);
        showLoader();

        let mobileNumberVal = mobileNoInput.value;
        if (!mobileNumberVal) return;

        axios.post(otpWrap, {
            'mobile_no': mobileNumberVal
        })
            .then(function (response) {
                hideLoader();

                let respData = response.data, statusCode = response.status;

                if (statusCode === 200) {
                    console.log('Success');
                    console.log('URL:', respData.url);
                    goTo(respData.url);
                } else {
                    console.log('ErrorCode:', statusCode);
                    console.log('Message:', respData.message);
                    const audioUrl = respData.prompt;
                    console.log('audioUrl', audioUrl)
                    playErrorAudio(audioUrl);
                    hideLoader();
                    displayErrorMessage(respData.message, errorMessageDiv);
                }

            })
            .catch(function (error) {
                let errMsg = error.response.data;
                console.log('catch statusCode', error.response.status);
                console.log('catch error', errMsg);
                const audioUrl = errMsg.prompt;
                console.log('audioUrl', audioUrl)
                playErrorAudio(audioUrl);

                hideLoader();
                displayErrorMessage(errMsg.message, errorMessageDiv);
            });
    });

});
