@include('partials.header')

<!-- Main Area Start -->
<main>
    <div class="container px-4 mx-auto">
        <div class="flex justify-center items-center h-screen z-10">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 lg:col-span-4 lg:col-start-5 z-10">
                    <div class="flex flex-col gap-6">
                        <h2 class="[font-size:var(--font-size-title)] font-bold text-white text-center">Mobile
                            Number</h2>
                        <p class="text-white text-base text-center">Please enter your phone number. We will send you
                            6-digits code to verify your account.</p>

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
                                    placeholder="Type Number: 1700000000"
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
                                type="submit">Send Code
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
        const mobileNoInput = document.getElementById('mobile_no');
        const errorMessageDiv = document.getElementById('error_message');
        const submitButton = document.querySelector('button[type="submit"]');

        // showLoader();
        buttonDisable(submitButton, true);
        mobileNoInput.addEventListener('input', function () {
            handlePhoneNumberInput(mobileNoInput, errorMessageDiv, submitButton);
        });

        document.querySelector('button[type="submit"]').addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default form submission
            buttonDisable(submitButton, true);
            showLoader();

            let mobileNumberVal = mobileNoInput.value;
            if (!mobileNumberVal) return;
            storeData('pn', mobileNumberVal);

            axios.post(otpWrap, {
                'mobile_no': mobileNumberVal
            })
                .then(function (response) {
                    hideLoader(); // Hide the loader

                    let respData = response.data,
                        statusCode = response.status;

                    if (statusCode === 200) {
                        console.log('Success');
                        console.log('URL:', respData.url);
                        goTo(respData.url);
                    } else {
                        // Handle non-200 response status codes
                        console.log('ErrorCode:', statusCode);
                        console.log('Message:', respData.message);
                        hideLoader(); // Hide the loader
                        displayErrorMessage(respData.message, errorMessageDiv);
                    }

                })
                .catch(function (error) {
                    let errMsg = error.response.data;
                    console.log('catch statusCode', error.response.status);
                    console.log('catch error', errMsg);

                    hideLoader(); // Hide the loader
                    displayErrorMessage(errMsg.message, errorMessageDiv);
                });
        });


    });

</script>

@include('partials.footer')
