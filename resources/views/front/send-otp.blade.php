@php($prompt = Session::get('api_calling')['prompt'] ?? $prompt)
@include('partials.header')

<script src="{{ asset('js/ap.js') }}"></script>

<!-- Audio Icon -->
<div class="absolute top-11 right-5 w-8 h-8">
    <button id="toggleAudio" onclick="toggleAudio()"
            class="play playAudioBtn cursor-pointer w-8 h-8 bg-no-repeat z-40"></button>
</div>
<!-- Audio Icon -->

<!-- Main Area Start -->
<main>
    <div class="container px-4 mx-auto">
        <div class="flex justify-center items-center h-screen z-10">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 lg:col-span-4 lg:col-start-5 z-10">
                    <div class="flex flex-col gap-6">
                        <h2 class="[font-size:var(--font-size-title)] font-bold text-white text-center">{{ __('messages.send-otp-mobile-number-text') }}</h2>
                        <p class="text-white text-base text-center">{{ __('messages.send-otp-input-phone-text') }}</p>

                        <div class="flex flex-col">
                            <div class="flex gap-3 w-full px-4 py-3 rounded-md bg-white">
                                <div class="flex gap-2 justify-end items-center w-[25%]">
                                    <img class="w-auto h-4 z-10" src="{{ asset('img/bd-flag.jpg') }}" alt="">
                                    <p>+88</p>
                                </div>

                                <input
                                    class="z-10 text-[color:var(--text-black)] bg-[color:var(--brand-color-gray)/30] focus:outline-none w-[75%]"
                                    id="mobile_no"
                                    name="mobile_no"
                                    placeholder="017XXXXXXXX"
                                    maxlength="11"
                                    autocomplete="off"
                                    type="number"
                                    inputmode="tel"
                                    pattern="[0-9][0-9]{11}"
                                    oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"

                                />
                            </div>
                            <span class="text-red-700 text-base pt-1 block hidden"
                                  id="error_message"></span>
                        </div>

                        <button id="btnPhoneSubmit"
                                class="text-[color:var(--brand-color-blue)] text-lg rounded-md w-full py-2 mt-5 bg-white"
                                type="submit">{{ __('messages.send-otp-send-code-text') }}
                        </button>

                        {{--<div class="flex gap-3 items-center bg-white rounded-full px-3">
                            <p class="text-base text-red-600 w-[90%] flex justify-center">Invalid phone number</p>
                            <button class="w-[10%] p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>--}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- Main Area End -->

<script src="{{ asset('js/send.js') }}"></script>

@include('partials.footer')
