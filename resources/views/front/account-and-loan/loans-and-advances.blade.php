@extends('partials.home-common')
@section('home-menu-content')

    <div class="grid grid-cols-12 gap-4">


        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnLADueDateInstallment"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.voiceForLADueDateInstallment.voice.en') : config('voices.voiceForLADueDateInstallment.voice.bn') }}"
               data-text="{{ app()->getLocale() === 'en' ? config('voices.voiceForLADueDateInstallment.text.en') : config('voices.voiceForLADueDateInstallment.text.bn') }}"
               data-title="{{ app()->getLocale() === 'en' ? config('voices.voiceForLADueDateInstallment.title.en') : config('voices.voiceForLADueDateInstallment.title.bn') }}"

               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.la-due-date-installment-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnLALoanClosureProcess"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.la-loan-closure-process-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnLALoanDetails"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.voiceForLALoanDetails.voice.en') : config('voices.voiceForLALoanDetails.voice.bn') }}"
               data-text="{{ app()->getLocale() === 'en' ? config('voices.voiceForLALoanDetails.text.en') : config('voices.voiceForLALoanDetails.text.bn') }}"
               data-title="{{ app()->getLocale() === 'en' ? config('voices.voiceForLALoanDetails.title.en') : config('voices.voiceForLALoanDetails.title.bn') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.la-loan-details-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnLAOutstandingLoanBalance"

               data-voice="{{ app()->getLocale() === 'en' ? config('voices.voiceForLAOutstandingLoanBalance.voice.en') : config('voices.voiceForLAOutstandingLoanBalance.voice.bn') }}"
               data-text="{{ app()->getLocale() === 'en' ? config('voices.voiceForLAOutstandingLoanBalance.text.en') : config('voices.voiceForLAOutstandingLoanBalance.text.bn') }}"
               data-title="{{ app()->getLocale() === 'en' ? config('voices.voiceForLAOutstandingLoanBalance.title.en') : config('voices.voiceForLAOutstandingLoanBalance.title.bn') }}"

               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.la-outstanding-loan-balance-btn') }}</h3>
            </a>
        </div>

    </div>

@endsection
