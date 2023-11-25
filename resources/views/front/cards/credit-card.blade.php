@extends('partials.home-common')
@section('home-menu-content')

    <div class="grid grid-cols-12 gap-4">

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnCCreditCardActivation"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"

               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.c-credit-card-activation-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnCCreditCardBlock"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"

               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.c-credit-card-block-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnCCreditChangeOrResetPIN"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"

               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.c-credit-change-or-reset-pin-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnCCreditECommerceActivationOrDeactivation"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.c-credit-e-commerce-activation-or-deactivation-btn') }}</h3>
            </a>
        </div>


        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnCCreditGreenPINGeneration"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.c-credit-green-pin-generation-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnCCreditMiniStatement"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.c-credit-mini-statement-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnCCreditOutstandingBDT"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.c-credit-outstanding-bdt-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnCCreditOutstandingUSD"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.c-credit-outstanding-usd-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnCCreditCardPayment"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.c-credit-card-payment-btn') }}
                </h3>
            </a>
        </div>


    </div>

@endsection
