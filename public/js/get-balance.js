document.addEventListener('DOMContentLoaded', function () {
    const balanceContainer = document.getElementById('balance-container');
    const balanceText = document.getElementById('balance-text');
    const btnTapForBalance = document.getElementById('balance-button');
    const balanceImage = document.querySelector('#balance-container img');
    const resetAnimationTimeout = 3000; // Change this value to configure the timeout

    function handleButtonClick() {
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
        checkLoginStatus()
            .then(isLoggedIn => {
                if (isLoggedIn) {
                    // User is logged in, proceed with the file upload
                    handleButtonClick();
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

        setTimeout(resetAnimation, resetAnimationTimeout);
    }

    function handleError(error) {
        console.error('Failed to fetch balance from API', error);
        balanceText.textContent = 'Error occurred.';
        balanceImage.style.opacity = 0;
        balanceContainer.classList.remove('slide-out');
        balanceContainer.classList.add('slide-in');

        setTimeout(resetAnimation, resetAnimationTimeout);
    }

    function resetAnimation() {
        balanceContainer.classList.remove('slide-in');
        balanceText.textContent = 'Tap for Balance';
        balanceImage.style.opacity = 1;
        balanceContainer.classList.add('slide-in-reverse');

        setTimeout(function () {
            balanceContainer.classList.remove('slide-in-reverse');
        }, 500);
    }
});
