{{--@php($prompt = Session::get('api_calling')['prompt'] ?? $prompt)--}}
@include('partials.header')

<script src="{{ asset('js/autoplay.js') }}"></script>

<!-- Audio Icon -->
<div class="absolute top-11 right-5 w-8 h-8">
    <button id="toggleAudio" onclick="toggleAudio()"
            class="play playAudioBtn cursor-pointer w-8 h-8 bg-no-repeat z-40">
    </button>
</div>
<!-- Audio Icon -->

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

<script type="application/javascript">

    document.addEventListener('DOMContentLoaded', function () {
        const inputs = document.querySelectorAll('input[type="number"]');
        const errorMessageDiv = document.getElementById('error_message');
        const submitButton = document.querySelector('button[type="submit"]');

        submitButton.addEventListener('click', function (event) {
            event.preventDefault();
            buttonDisable(submitButton, true);
            showLoader();

            const code = Array.from(inputs).map(input => Number(input.value)).join('');
            console.log('code', code);

            if (code.length !== 6) {
                handleInvalidCode();
                return;
            }

            axios.post(verifyWrap, {'code': code})
                .then(response => handleResponse(response))
                .catch(error => handleError(error));
        });

        inputs.forEach((input, index) => {
            input.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '');

                if (this.value.length === 1 || (this.value.length === 2 && this.value[0] === '0')) {
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    } else {
                        submitButton.focus();
                    }
                }

                const allFilled = Array.from(inputs).every(input => input.value !== '');
                const code = Array.from(inputs).map(input => Number(input.value)).join('');

                if (allFilled && code.length === 6) {
                    buttonDisable(submitButton, false);
                    clearErrorMessage(errorMessageDiv);
                } else {
                    buttonDisable(submitButton, true);
                    if (code.length !== 6) {
                        displayErrorMessage('Please enter all 6 digits of the number.', errorMessageDiv);
                    } else {
                        clearErrorMessage(errorMessageDiv);
                    }
                }
            });

            input.addEventListener('keydown', function (event) {
                if (event.key === 'Backspace') {
                    if (this.value.length === 0 && index > 0) {
                        event.preventDefault(); // Prevent default behavior of Backspace
                        inputs[index - 1].focus();
                    } else if (this.value.length === 1 && this.value[0] === '0') {
                        event.preventDefault(); // Prevent removing the zero (0) value
                    }
                }
            });
        });

        function handleInvalidCode() {
            hideLoader();
            displayErrorMessage('Please enter all 6 digits of the number.', errorMessageDiv);
        }

        function handleResponse(response) {
            hideLoader();
            const respData = response.data;
            const statusCode = response.status;

            console.log('respData', statusCode, respData);

            if (statusCode === 200 && respData.status === 'success') {
                console.log('Success');
                storeData('pn', respData.pn);
                storeData('acn', respData.acn);
                goTo(respData.url);
            } else {
                console.log('ErrorCode:', statusCode);
                console.log('Message:', respData.message);
                const audioUrl = respData.prompt;
                playErrorAudio(audioUrl);
                displayErrorMessage(respData.message, errorMessageDiv);
            }
        }

        function handleError(error) {
            hideLoader();
            const errMsg = error.response.data;
            console.log('catch statusCode', error.response.status);
            console.log('catch error', errMsg);
            const audioUrl = errMsg.prompt;
            console.log('audioUrl', audioUrl)
            playErrorAudio(audioUrl);

            displayErrorMessage(errMsg.message, errorMessageDiv);
        }
    });

</script>

@include('partials.footer')
