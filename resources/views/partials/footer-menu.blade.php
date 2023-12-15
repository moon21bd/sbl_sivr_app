<!-- Footer Area Start -->
<footer class="py-5 bg-yellow-500 fixed bottom-0 right-0 left-0 z-40">

    <div class="container px-4 mx-auto">
        <div class="flex gap-3 justify-between items-center">

            <div class="div">
                <a href="javascript:void(0)" id="btnBack">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-8 h-8 stroke-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </a>
            </div>

            <div class="flex justify-between items-center">
                <button id="toggleAudio" class="play audioBtn cursor-pointer z-50" onclick="toggleAudio()"></button>
            </div>

            <div class="div">
                <a href="javascript:void(0)" id="btnHome">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-8 h-8 stroke-white">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                    </svg>
                </a>
            </div>

            <div class="div">
                <a href="javascript:void(0)" id="btnExit">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-8 h-8 stroke-white rotate-90">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                </a>
            </div>

        </div>
    </div>

</footer>
<!-- Footer Area End -->

<script type="application/javascript">

    document.addEventListener('DOMContentLoaded', function () {
        const backButton = document.getElementById('btnBack');
        const btnHome = document.getElementById('btnHome');

        btnHome.addEventListener('click', () => goTo());
        backButton.addEventListener('click', () => goTo());

        /*btnHome.addEventListener('click', function () {
            goTo();
        });

        backButton.addEventListener('click', function () {
            // Check if there is history
            if (window.history.length > 1) {
                // Go back in history
                window.history.back();
            } else {
                // Fallback: Go to the home page URL
                window.location.href = window.location.origin;
            }
        });*/

    });

</script>

