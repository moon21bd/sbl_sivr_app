@include('partials.header')

<link rel="stylesheet" href="{{ asset('css/get-balance.css') }}">

<!-- Header Area Start -->
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
<!-- Header Area End -->

@include('partials.mobile-menu')

<!-- Main Area Start -->
<main>

    <div class="container px-4 mx-auto pt-24 pb-24">
        {{--        <a href="#" id="showToastButton">Show Toast</a>--}}
        <div class="px-3 py-5 flex flex-row gap-3 items-center bg-white rounded-md mb-4 z-10">
            <div class="p-2 rounded-md bg-[color:var(--brand-color-blue)] z-10" id="userPhotoDiv">
                <label for="photoInput" style="cursor: pointer;">
                    <img src="{{ asset('img/icon/user.svg') }}" alt="" id="userPhotoIcon" width="30" height="30">
                </label>

                <!-- The container to hold the file input -->
                <div id="fileInputContainer" style="display: none;"></div>
            </div>


            <div class=" z-10">
                <h2 class="text-xl text-[color:var(--brand-color-blue)] font-bold mb-1">{{ $name ?? "Guest User" }}</h2>
                <button id="balance-button" class="px-2 py-1 rounded-full w-36 bg-[color:var(--brand-color-blue)]">
                    <div id="balance-container" class="flex gap-2 items-center">
                        <img src="{{ asset('img/icon/taka.svg') }}" alt="">
                        <span id="balance-text" class="text-white text-sm">Tap for Balance</span>
                    </div>
                </button>
            </div>

            {{--<div class="z-10">
                <a class="flex gap-2 items-center text-[color:var(--brand-color-blue)] text-base" href="#">
                    <img src="{{ asset('img/icon/edit-user.svg') }}" alt="">
                    Edit Profile
                </a>
            </div>--}}

        </div>

        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-4 z-10">
                <a href="javascript:void(0)" id="btnCardActivate"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        {{ __('messages.card-activate-btn') }}</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#" id="btnSetPIN"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">

                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/set-pin.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        {{ __('messages.set-pin-btn') }}</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#" id="btnBalanceCheck"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/my-balance.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        {{ __('messages.my-balance-btn') }}</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#" id="btnLockCard"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/lock-card.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        {{ __('messages.lock-card-btn') }}</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#" id="btnResetPin"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/reset-pin.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        {{ __('messages.reset-pin-btn') }}</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#" id="btnPaymentInfo"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/payment-info.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        {{ __('messages.payment-info-btn') }}</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#" id="btnStatement"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/statement.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        {{ __('messages.statement-btn') }}</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#" id="btnCardDetails"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/card-details.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        {{ __('messages.card-details-btn') }}</h3>
                </a>
            </div>

            <div class="col-span-4 z-10">
                <a href="#" id="btnAgentAssist"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/agent-assist.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        {{ __('messages.agent-assist-btn') }}</h3>
                </a>
            </div>

        </div>

        <div>
            <a class="fixed bottom-24 right-4 lg:right-14 bg-[color:var(--brand-color-blue)] z-50 flex justify-center items-center w-14 h-14 rounded-md border-2 border-white"
               href="tel:{{config('bank.customer-support')}}" id="btnCustSupp">
                <img src="{{ asset('img/icon/headphone.svg') }}" alt="">
            </a>
        </div>

    </div>

</main>
<!-- Main Area End -->

<script>
    // Function to display the flash message using SweetAlert2
    function showFlashMessage() {
        // Check if the flash message exists in the session
        const status = '{{ session('status') }}';
        const message = '{{ session('message') }}';

        if (status && message) {
            // Display the SweetAlert2 message based on the flash status
            Swal.fire({
                icon: status,
                title: message,
                showConfirmButton: false,
                timer: 4000, // 4 seconds
            });
        }
    }

    document.addEventListener('DOMContentLoaded', showFlashMessage);
</script>

<script src="{{ asset('js/get-balance.js') }}"></script>
<script src="{{ asset('js/upload-user-photo.js') }}"></script>
@include('front.popup-container')
<script src="{{ asset('js/home.js') }}"></script>
@include('partials.footer-menu')
@include('partials.footer')
