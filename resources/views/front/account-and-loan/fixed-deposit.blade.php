@extends('partials.home-common')
@section('home-menu-content')

    <div class="grid grid-cols-12 gap-4">


        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnFDEncashmentProcess"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.fd-encashment-process-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnFDFixedDepositDetails"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.fd-fixed-deposit-details-btn') }}</h3>
            </a>
        </div>

        <div class="col-span-4 z-10">
            <a href="javascript:void(0)" id="btnFDMaturityDate"
               class="flex flex-col gap-3 lg:gap-4 justify-center items-center bg-white rounded-md px-2 py-4 lg:px-4 lg:py-6 cursor-pointer">
                <div
                    class="w-10 h-10 lg:w-14 lg:h-14 flex justify-center items-center rounded-md bg-[color:var(--brand-color-blue)]">
                    <img class="w-6 h-6 lg:w-8 lg:h-8" src="{{ asset('img/icon/active-card.svg') }}" alt="">
                </div>
                <h3 class="text-[color:var(--text-black)] [font-size:var(--font-size-box-sm)] lg:[font-size:var(--font-size-box)] font-bold">
                    {{ __('messages.fd-maturity-date-btn') }}</h3>
            </a>
        </div>

    </div>

@endsection
