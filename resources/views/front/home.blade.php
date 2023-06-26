@include('partials.header')

<script src="{{ asset('js/axios.min.js') }}"></script>
<!-- Header Araea Start bg-gradient-to-tr from-[color:var(--brand-color-yellow)] to-[color:var(--brand-color-blue)] -->
<header class="py-3 bg-yellow-500 fixed top-0 right-0 left-0 rounded-b-lg z-40">

    <div class="container px-3 mx-auto">
        <div class="flex gap-3 justify-between items-center">
            <div
                class="bg-white relative w-20 h-10 rounded-full cursor-pointer p-2 flex items-center justify-center radioBtn">
                <a class="w-1/2 h-8 text-sm font-semibold rounded-full flex items-center justify-center"
                   data-locale="bn">বাং</a>
                <a class="w-1/2 h-8 text-sm font-semibold rounded-full flex items-center justify-center"
                   data-locale="en">EN</a>
            </div>

            <div class="flex gap-3 justify-between items-center">
                <div class="text-white text-xl font-bold">
                    {{ __('messages.main-menu') }}
                </div>
                <div class="cursor-pointer p-3" onclick="openNav()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-7 h-7 stroke-white">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

</header>
<!-- Header Araea End -->

@include('partials.mobile-menu')

<!-- Main Araea Start -->
<main>

    <div class="container px-4 mx-auto pt-24 pb-24">

        <div class="grid grid-cols-12 gap-4">

            <div class="col-span-4 z-10">
                <a href="#"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        {{ __('messages.card-activation') }}</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">

                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/set-pin.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        Set PIN</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/my-balance.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        My Balance</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/lock-card.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        Lock Card</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/reset-pin.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        Reset Pin</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/e-commerce.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        E-commerce</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/payment-info.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        Payment Info</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/statement.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        Statement</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/card-details.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        Card Details</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/agent-assist.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        Agent Assist</h3>
                </a>
            </div>

        </div>

        <div>
            <a class="fixed bottom-24 right-4 lg:right-14 bg-[color:var(--brand-color-blue)] z-50 flex justify-center items-center w-14 h-14 rounded-md border-2 border-white"
               href="#">
                <img src="{{ asset('img/icon/headphone.svg') }}" alt="">
            </a>
        </div>

    </div>

</main>
<!-- Main Araea End -->

@include('front.popup-container');

<script src="{{ asset('js/home.js') }}"></script>

@include('partials.footer-menu')
@include('partials.footer')
