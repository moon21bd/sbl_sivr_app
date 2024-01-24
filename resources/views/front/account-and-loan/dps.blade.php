@extends('partials.home-common')
@section('home-menu-content')

    <div class="grid grid-cols-12 gap-4">

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnALAccountDPSAvailableBalance"

               {{--data-voice="{{ app()->getLocale() === 'en' ? config('voices.voiceForALAccountDPSAvailableBalance.voice.en') : config('voices.voiceForALAccountDPSAvailableBalance.voice.bn') }}"
               data-text="{{ app()->getLocale() === 'en' ? config('voices.voiceForALAccountDPSAvailableBalance.text.en') : config('voices.voiceForALAccountDPSAvailableBalance.text.bn') }}"
               data-title="{{ app()->getLocale() === 'en' ? config('voices.voiceForALAccountDPSAvailableBalance.title.en') : config('voices.voiceForALAccountDPSAvailableBalance.title.bn') }}"--}}

               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"

               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold text-center">
                    {{ __('messages.account-dps-available-balance-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnALDPSDetails"
               {{--data-voice="{{ app()->getLocale() === 'en' ? config('voices.voiceForALDPSDetails.voice.en') : config('voices.voiceForALDPSDetails.voice.bn') }}"
               data-text="{{ app()->getLocale() === 'en' ? config('voices.voiceForALDPSDetails.text.en') : config('voices.voiceForALDPSDetails.text.bn') }}"
               data-title="{{ app()->getLocale() === 'en' ? config('voices.voiceForALDPSDetails.title.en') : config('voices.voiceForALDPSDetails.title.bn') }}"--}}

               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"

               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold text-center">
                    {{ __('messages.account-dps-details-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnALAccountDPSEncashmentProcess"

               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold text-center">
                    {{ __('messages.account-dps-encashment-process-btn') }}</h3>
            </a>
        </div>


        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnALAccountDPSInstalmentDetails"

               {{--data-voice="{{ app()->getLocale() === 'en' ? config('voices.voiceForALAccountDPSInstalmentDetails.voice.en') : config('voices.voiceForALAccountDPSInstalmentDetails.voice.bn') }}"
               data-text="{{ app()->getLocale() === 'en' ? config('voices.voiceForALAccountDPSInstalmentDetails.text.en') : config('voices.voiceForALAccountDPSInstalmentDetails.text.bn') }}"
               data-title="{{ app()->getLocale() === 'en' ? config('voices.voiceForALAccountDPSInstalmentDetails.title.en') : config('voices.voiceForALAccountDPSInstalmentDetails.title.bn') }}"--}}

               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"

               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold text-center">
                    {{ __('messages.account-dps-instalment-details-btn') }}</h3>
            </a>
        </div>

    </div>

@endsection
