<!-- Popup Container Started -->

<style>

    .popup-container {
        display: none;
        /* Other styles for the popup container */
    }

    .get-started-btn {
        display: block;
        /* Other styles for the "Get Started" button */
    }

</style>


<div class="popup-container fixed top-0 right-0 left-0 bottom-0 h-full w-full z-50"
     style="background: linear-gradient(21.64deg, #D9A629 19.97%, #0F5DA8 80.91%);">

    <!-- BG Dots -->
    <div class="top-right absolute -top-28 right-0 overflow-hidden z-0">
        <img src="{{ asset('img/bg/top-right.svg') }}" alt="">
    </div>

    <div class="left-center absolute -left-24 inset-1/4 overflow-hidden z-0">
        <img src="{{ asset('img/bg/center-left.svg') }}" alt="">
    </div>

    <div class="right-bottom absolute bottom-0 right-0 overflow-hidden z-0">
        <img src="{{ asset('img/bg/bottom-right.svg') }}" alt="">
    </div>
    <!-- BG Dots -->

    <div class="container px-4 mx-auto">
        <div class="flex justify-center items-center h-screen">
            <div class="w-full lg:w-[40%] flex flex-col gap-6 justify-center items-center text-center">

                <div class="flex flex-col gap-2 justify-center items-center text-center mb-10 z-10">
                    <img src="{{ asset('img/logo-white.png') }}" alt="">
                    <h1 class="text-white [font-size:var(--font-size-title)] font-bold relative after:absolute after:w-full after:h-[2px] after:left-0 after:-bottom-1 after:bg-gray-300">
                        সোনালী ব্যাংক লিমিটেড</h1>
                    <h2 class="text-white [font-size:var(--font-size-title)] font-bold">Sonali Bank Limited</h2>
                </div>

                <div class="bg-white rounded-md px-3 py-6 w-full mb-16 z-10">
                    <p class="text-[color:var(--brand-color-gray)] text-lg">{{ __('messages.get-started-text') }}</p>
                </div>

                <div class="w-full z-10">
                    <button type="submit" id="getStartedBtn"
                            class="get-started-btn text-[color:var(--brand-color-blue)] text-xl font-bold bg-white rounded-md h-12 w-full cursor-pointer">
                        {{ __('messages.get-started-btn') }}
                    </button>

                </div>

            </div>
        </div>

    </div>
</div>
<!-- End of Popup Container -->

<script type="application/javascript">

    document.addEventListener('DOMContentLoaded', function () {

        // get started consent save start

        const getStartedButton = document.getElementById('getStartedBtn');
        const popupContainer = document.querySelector('.popup-container');
        let showGetStartedBtn = true;

        // Check if the flag variable is set in sessionStorage
        try {
            const flag = sessionStorage.getItem('hideGetStartedBtn');
            if (flag === 'show') {
                showGetStartedBtn = false;
            }
        } catch (error) {
            // Handle any errors related to retrieving data from sessionStorage
            console.error('Error retrieving data from sessionStorage:', error);
        }

        // Show or hide the "Get Started" button based on the flag variable
        if (showGetStartedBtn) {
            popupContainer.style.display = 'block';
        } else {
            popupContainer.style.display = 'none';
        }

        // Add event listener to the "Get Started" button
        getStartedButton.addEventListener('click', function () {
            toggleSound();
            saveUserConsent();
            hidePopupContainer();
        });

        function hidePopupContainer() {
            popupContainer.style.display = 'none';
        }

        function saveUserConsent() {
            // Set the flag variable in sessionStorage to hide the "Get Started" button
            try {
                sessionStorage.setItem('hideGetStartedBtn', 'show');
                tUj('get-started', {'page': 'home', 'button': 'getStarted'});
            } catch (error) {
                // Handle any errors related to saving data in sessionStorage
                console.error('Error saving data in sessionStorage:', error);
            }
        }

        // end of get started consent save

    });


</script>
