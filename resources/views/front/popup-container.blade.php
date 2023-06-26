<style>
    /* Styles for the popup container */
    .popup-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(21.64deg, #D9A629 19.97%, #0F5DA8 80.91%);
        z-index: 100;
    }

    /* Adjust position of the background dots */
    .popup-container .top-right {
        position: absolute;
        top: -28px;
        right: 0;
    }

    .popup-container .left-center {
        position: absolute;
        left: -24px;
        top: calc(50% - 100px);
    }

    .popup-container .right-bottom {
        position: absolute;
        bottom: 0;
        right: 0;
    }
</style>

<!-- Popup Container Started -->

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

<div class="popup-container">
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
                    <p class="text-[color:var(--brand-color-gray)] text-lg">Welcome to Sonali Bank Smart IVR !
                        Get in touch for better deals and 24/7 customer support.</p>
                </div>

                <div class="w-full z-10">
                    <button type="submit" id="getStartedBtn"
                            class="text-[color:var(--brand-color-blue)] text-xl font-bold bg-white rounded-md h-12 w-full cursor-pointer">
                        Get Started
                    </button>

                </div>

            </div>
        </div>

    </div>
</div>
<!-- End of Popup Container -->

<script type="application/javascript">
    // Hide the popup container after clicking the "Get Started" button
    document.getElementById('getStartedBtn').addEventListener('click', function () {
        document.querySelector('.popup-container').style.display = 'none';
        toggleSound();

    });
</script>
