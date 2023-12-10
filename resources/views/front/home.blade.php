@extends('partials.home-common')
@section('home-menu-content')

    <div class="grid grid-cols-12 gap-4">

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnEWallet"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">

                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/set-pin.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.eWallet-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnCards"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.cards-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnAccountAndLoan"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.account-or-loan-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnESheba"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/reset-pin.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.eSheba-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnSPG"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/lock-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.spg-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnIslamiBanking"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/payment-info.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.islami-banking-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnAgentBanking"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/statement.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.agent-banking-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnSonaliBankProduct"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/card-details.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.sonali-bank-product-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnCreateIssue"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/my-balance.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.my-complain-btn') }}</h3>
            </a>
        </div>

        <script>
            /*document.getElementById('btnCreateIssue').addEventListener('click', () => {
                showCascadingDropdowns();
            });*/

            /*function showCascadingDropdownsForCreatingAnIssue() {
                Swal.fire({
                    title: 'Create Issue',
                    html:
                        `<label for="callTypeSelect">Call Type:</label>
            <select id="callTypeSelect" class="swal2-input" required>
                <option value="" disabled selected>Select Type</option>
                <option value="type1">Type 1</option>
                <option value="type2">Type 2</option>
            </select>
            <label for="callCategorySelect">Call Category:</label>
            <select id="callCategorySelect" class="swal2-input" required>
                <option value="" disabled selected>Select Category</option>
            </select>
            <label for="reasonInput">Reason:</label>
            <input id="reasonInput" class="swal2-input" required />`,
                    focusConfirm: false,
                    preConfirm: () => {
                        const callType = document.getElementById('callTypeSelect').value;
                        const callCategory = document.getElementById('callCategorySelect').value;
                        const reason = document.getElementById('reasonInput').value;

                        // Validate and perform actions

                        // Close the modal
                        return {callType, callCategory, reason};
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel',
                    didOpen: () => {
                        // Attach event listener to dynamically update "Call Category" options
                        const callTypeSelect = document.getElementById('callTypeSelect');
                        const callCategorySelect = document.getElementById('callCategorySelect');

                        callTypeSelect.addEventListener('input', () => {
                            const callType = callTypeSelect.value;
                            const callCategories = getCallCategories(callType);

                            // Clear existing options
                            callCategorySelect.innerHTML = '<option value="" disabled selected>Select Category</option>';

                            // Populate the call category dropdown
                            callCategories.forEach((category) => {
                                const option = document.createElement('option');
                                option.value = category;
                                option.textContent = category;
                                callCategorySelect.appendChild(option);
                            });
                        });
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        const {value} = result;
                        // Perform actions with the selected values
                        console.log(value);
                    }
                });
            }

            function getCallCategories(callType) {
                // Replace this with your logic to fetch call categories based on the selected type
                switch (callType) {
                    case 'type1':
                        return ['Category 1A', 'Category 1B'];
                    case 'type2':
                        return ['Category 2A', 'Category 2B'];
                    default:
                        return [];
                }
            }*/

        </script>

        {{--<div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnAgentAssist"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/agent-assist.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.agent-assist-btn') }}</h3>
            </a>
        </div>--}}

        {{--<div class="col-span-4 z-10">
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
            <a href="javascript:void(0)" id="btnResetPin"
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
            <a href="javascript:void(0)" id="btnDeviceBind"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">

                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/set-pin.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.device-bind-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnCreateIssue"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/my-balance.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.create-issue-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnLockWallet"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/lock-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.lock-wallet-btn') }}</h3>
            </a>
        </div>


        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnPaymentInfo"
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
            <a href="javascript:void(0)" id="btnStatement"
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
            <a href="javascript:void(0)" id="btnCardDetails"
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
            <a href="javascript:void(0)" id="btnAgentAssist"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/agent-assist.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.agent-assist-btn') }}</h3>
            </a>
        </div>--}}

    </div>

@endsection
