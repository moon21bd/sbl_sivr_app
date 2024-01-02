document.addEventListener('DOMContentLoaded', function () {
    let locale = getSavedLocale();

    const balanceContainer = document.getElementById('balance-container');
    let dataTextValue = "";
    const btnTapForBalance = document.getElementById('balance-button');
    const balanceImage = document.querySelector('#balance-container img');
    const resetAnimationTimeout = 2000; // 2 Secs. Change this value to configure the timeout
    const balanceText = document.getElementById('balance-text');

    function handleBalanceButtonClick() {
        balanceContainer.classList.add('slide-out');
        showLoader();
        tUj('tap-for-balance', {
            'purpose': 'tapForBalance',
            'page': 'home',
            'button': 'balance-button',
            'user_phone_no': getData('pn'),
            'user_account_no': getData('acn')
        });

        axios.get('/get-balance')
            .then(handleSuccess)
            .catch(handleError)
            .finally(hideLoader);
    }

    if (btnTapForBalance) {
        btnTapForBalance.addEventListener('click', () => {

            dataTextValue = balanceContainer.getAttribute('data-text');
            checkLoginStatus()
                .then(isLoggedIn => {
                    if (isLoggedIn) {
                        handleBalanceButtonClick();
                    } else {
                        showVerificationAlert();
                    }
                })
                .catch(error => console.error(error));
        });
    }

    function handleSuccess(response) {
        const status = response?.data?.status;

        if (status === 'success') {
            const balance = response.data.balance;
            balanceText.textContent = `${balance}`;
            balanceImage.style.opacity = 0;
            balanceContainer.classList.remove('slide-out');
            btnTapForBalance.disabled = true; // to prevent second time balance button click
        } else {
            const errorMessage = response?.data?.message || 'Error occurred.';
            showCustomMessage('Error', errorMessage);
            balanceContainer.classList.remove('slide-out');
            balanceContainer.classList.add('slide-in');

            setTimeout(() => {
                resetAnimation(dataTextValue)
            }, resetAnimationTimeout);
        }
    }

    /*function handleSuccess(response) {
        console.log('respon', response)
        const balance = response?.data?.status === 'success' ? response.data.balance : 'Error occurred.';
        balanceText.textContent = `${balance}`;
        balanceImage.style.opacity = 0;
        balanceContainer.classList.remove('slide-out');
        btnTapForBalance.disabled = true; // to prevent second time balance button click

        // balanceContainer.classList.add('slide-in');
        //
        // setTimeout(() => {
        //     resetAnimation(dataTextValue)
        // }, resetAnimationTimeout);
    }*/


    function handleError(error) {
        console.error('Failed to fetch balance from API', error);
        balanceText.textContent = 'Error.';
        balanceImage.style.opacity = 0;
        balanceContainer.classList.remove('slide-out');
        balanceContainer.classList.add('slide-in');

        setTimeout(() => {
            resetAnimation(dataTextValue)
        }, resetAnimationTimeout);
    }

    function resetAnimation(text) {
        balanceContainer.classList.remove('slide-in');
        balanceText.textContent = text;
        balanceImage.style.opacity = 1;
        balanceContainer.classList.add('slide-in-reverse');

        setTimeout(function () {
            balanceContainer.classList.remove('slide-in-reverse');
        }, 500);
    }

    function showCustomMessage(title, message) {
        Swal.fire({
            text: message,
            icon: 'error',
            confirmButtonText: (locale === 'en') ? "Ok" : "ঠিক আছে",
            focusConfirm: false,
            allowOutsideClick: false,
            customClass: {
                container: 'show-custom-message-swal-bg'
            },
        });
    }

});
