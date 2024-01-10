@extends('partials.home-common')
@section('home-menu-content')

    <div class="grid grid-cols-12 gap-4">

        @if(Session::get('account_verification_status', false))
            <div class="col-span-4 z-10">
                <a href="javascript:void(0)" id="btnEWallet"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">

                    <div
                            class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/e-wallet.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        {{ __('messages.eWallet-btn') }}</h3>
                </a>
            </div>
        @else
            <div class="col-span-4 z-10">
                <a href="javascript:void(0)" id="btnEWalletDisable"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">

                    <div
                            class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/e-wallet-disable.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        {{ __('messages.eWallet-btn') }}</h3>
                </a>
            </div>
        @endif
        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnESheba"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/e-sheba.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.eSheba-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="https://sbl.com.bd:7070/" target="_blank"
               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
               data-text="{{ __('scripts.default-call-center-text') }}"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/spg.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.spg-btn') }}</h3>
            </a>
        </div>

        @if(Session::get('account_verification_status', false))
            <div class="col-span-4 z-10">
                <a href="javascript:void(0)" id="btnCards"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                            class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/cards.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        {{ __('messages.cards-btn') }}</h3>
                </a>
            </div>
        @else
            <div class="col-span-4 z-10">
                <a href="javascript:void(0)" id="btnCardsDisable"
                   class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                    <div
                            class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                        <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/cards-disable.svg') }}" alt="">
                    </div>
                    <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                        {{ __('messages.cards-btn') }}</h3>
                </a>
            </div>
        @endif


        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnAccountAndLoan"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/acc-loan.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.account-or-loan-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnIslamiBanking"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                        class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/islami-bank.svg') }}" alt="">
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
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/agent-bank.svg') }}" alt="">
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
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/bank-products.svg') }}" alt="">
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
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/my-complaint.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.my-complain-btn') }}</h3>
            </a>
        </div>

    </div>

@endsection
