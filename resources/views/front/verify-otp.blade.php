{{--@php($prompt = Session::get('api_calling')['prompt'] ?? $prompt)--}}
@include('partials.header')

<script src="{{ asset('js/ap.js') }}"></script>

<!-- Audio Icon -->
<div class="absolute top-11 right-5 w-8 h-8">
    <button id="toggleAudio" onclick="toggleAudio()"
            class="play playAudioBtn cursor-pointer w-8 h-8 bg-no-repeat z-40">
    </button>
</div>
<!-- Audio Icon -->

<style>
    .select-button {
        background-color: #4CAF50;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .select-button:hover {
        background-color: #45a049;
    }

    .select-button:focus {
        outline: none;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
    }

</style>

<!-- Main Area Start -->
<main>

    <div class="container px-4 mx-auto">
        <div class="flex justify-center items-center h-screen z-10">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 lg:col-span-4 lg:col-start-5 z-10">
                    <div class="flex flex-col gap-4 text-center">
                        <h2 class="[font-size:var(--font-size-title)] font-bold text-white">{{ __('messages.verify-otp-verify-account-text') }}</h2>
                        <p class="text-white text-base">{{ __('messages.verify-otp-verify-message-text') }}
                            @if(Session::has('otp.phone_masked'))
                                :<b>+88{{ Session::get('otp.phone_masked') }}</b>
                            @endif.</p>

                        <div class="flex gap-3 justify-between items-center">
                            <div
                                class="flex w-10 h-10 text-2xl text-white font-bold items-center justify-center relative after:absolute after:w-full after:h-[2px] after:left-0 after:bottom-0 after:bg-white/60">
                                <input type="number" maxlength="1" autocomplete="off"
                                       class="w-10 h-10 text-center bg-transparent focus:outline-none">
                            </div>
                            <div
                                class="flex w-10 h-10 text-2xl text-white font-bold items-center justify-center relative after:absolute after:w-full after:h-[2px] after:left-0 after:bottom-0 after:bg-white/60">
                                <input type="number" maxlength="1" autocomplete="off"
                                       class="w-10 h-10 text-center bg-transparent focus:outline-none">
                            </div>
                            <div
                                class="flex w-10 h-10 text-2xl text-white font-bold items-center justify-center relative after:absolute after:w-full after:h-[2px] after:left-0 after:bottom-0 after:bg-white/60">
                                <input type="number" maxlength="1" autocomplete="off"
                                       class="w-10 h-10 text-center bg-transparent focus:outline-none">
                            </div>
                            <div
                                class="flex w-10 h-10 text-2xl text-white font-bold items-center justify-center relative after:absolute after:w-full after:h-[2px] after:left-0 after:bottom-0 after:bg-white/60">
                                <input type="number" maxlength="1" autocomplete="off"
                                       class="w-10 h-10 text-center bg-transparent focus:outline-none">
                            </div>
                            <div
                                class="flex w-10 h-10 text-2xl text-white font-bold items-center justify-center relative after:absolute after:w-full after:h-[2px] after:left-0 after:bottom-0 after:bg-white/60">
                                <input type="number" maxlength="1" autocomplete="off"
                                       class="w-10 h-10 text-center bg-transparent focus:outline-none">
                            </div>
                            <div
                                class="flex w-10 h-10 text-2xl text-white font-bold items-center justify-center relative after:absolute after:w-full after:h-[2px] after:left-0 after:bottom-0 after:bg-white/60">
                                <input type="number" maxlength="1" autocomplete="off"
                                       class="w-10 h-10 text-center bg-transparent focus:outline-none">
                            </div>

                        </div>

                        <span class="text-red-700 text-base pt-1 block hidden"
                              id="error_message"></span>

                        {{--<p class="text-white text-base mt-3">Didn’t received the code?</p>
                        <div class="div">
                            <button class="text-white text-lg border-b-[1px] border-white">Resend</button>
                        </div>--}}

                        <button
                            class="text-[color:var(--brand-color-blue)] text-lg rounded-md w-full py-2 mt-10 bg-white"
                            type="submit">Submit
                        </button>
                    </div>

                </div>

            </div>

        </div>

    </div>

</main>
<!-- Main Area End -->

<script src="{{ asset('js/verify.js') }}"></script>

@include('partials.footer')
