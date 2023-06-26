@include('partials.header')

<!-- Main Araea Start -->
<main>

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
                    <a id="getStartedBtn" href="javascript:void(0)"
                       class="text-[color:var(--brand-color-blue)] text-xl font-bold bg-white rounded-md py-3 w-full cursor-pointer block">Get
                        Started</a>
                </div>

            </div>
        </div>

    </div>

</main>
<!-- Main Araea End -->

<script>

    document.addEventListener('DOMContentLoaded', function () {
        // Check if the unique identifier exists in localStorage
        const getStartedButton = document.getElementById("getStartedBtn"),
            redirectTo = "{{ route('home') }}" + "?autoplay=true";
        let userId = localStorage.getItem("userId");

        if (userId) {
            // User already exists, redirect to another page
            window.location.href = redirectTo;
        } else {
            // Add event listener to the "Get Started" button
            getStartedButton.addEventListener("click", function () {
                // Generate a unique identifier for the user
                userId = generateUniqueId(); // Replace with your own function to generate a unique identifier

                // Store the unique identifier in localStorage
                localStorage.setItem("userId", userId);

                // Redirect to another page
                window.location.href = redirectTo;
            });
        }

        function generateUniqueId() {
            const timestamp = new Date().getTime();
            const random = Math.floor(Math.random() * 10000);
            return timestamp + "_" + random;
        }

    });


</script>


@include('partials.footer')
