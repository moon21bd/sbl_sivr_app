document.addEventListener('DOMContentLoaded', function () {
    const balanceContainer = document.getElementById('balance-container');
    let dataTextValue = "";
    const balanceText = document.getElementById('balance-text');
    const btnTapForBalance = document.getElementById('balance-button');
    const balanceImage = document.querySelector('#balance-container img');
    const resetAnimationTimeout = 3000; // Change this value to configure the timeout

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

        // Fetch balance from API using Axios
        axios.get('/get-balance')
            .then(handleSuccess)
            .catch(handleError)
            .finally(hideLoader);
    }

    btnTapForBalance.addEventListener('click', () => {

        dataTextValue = balanceContainer.getAttribute('data-text');
        // console.log('dataTextValue', dataTextValue)
        checkLoginStatus()
            .then(isLoggedIn => {
                if (isLoggedIn) {
                    // User is logged in, proceed with the file upload
                    handleBalanceButtonClick();
                } else {
                    // User is not logged in, show the verification alert
                    showVerificationAlert();
                }
            })
            .catch(error => console.error(error));
    });

    function handleSuccess(response) {
        const balance = response?.data?.status === 'success' ? response.data.balance : 'Error occurred.';
        balanceText.textContent = `${balance}`;
        balanceImage.style.opacity = 0;
        balanceContainer.classList.remove('slide-out');
        balanceContainer.classList.add('slide-in');

        setTimeout(() => {
            resetAnimation(dataTextValue)
        }, resetAnimationTimeout);
    }


    function handleError(error) {
        console.error('Failed to fetch balance from API', error);
        balanceText.textContent = 'Error occurred.';
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
});
