<!-- Mobile Menu Start -->

@php
    $userInfo = getUserInfoFromSession();
    $photoForMenu = $userInfo['userImageMMenu'];
    $accountForMenu = $userInfo['userAccountNo'] ?? null;
@endphp

<div class="fixed top-0 w-screen h-screen flex-row justify-start z-[2147483647]" id="mobileMenu" style="display: none;">
    <div class="flex-1 bg-[#cfcfcf6e]" onclick="closeNav()">
    </div>
    <div class="select-none h-screen bg-gradient-to-r from-[#E9B308] to-[#1D629F] shadow-lg flex flex-col fixed top-0 right-0 w-72">
        <div class="pt-4 inline-block">
            <a href="javascript:void(0)" onclick="closeNav()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-8 h-8 stroke-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </a>
        </div>
        <div class="p-3 overflow-y-auto no-scrollbar">

            <div class="flex flex-col justify-center items-center">

                <img class="w-12 h-12 mb-3 rounded-full" src="{{ $photoForMenu }}" alt="">
                <h2 class="text-white text-2xl mb-2">{{ $name ?? __('messages.guest-user') }}</h2>

                @if($accountForMenu)
                    <p class="text-white text-base">{{ __('messages.account-no-text') }} {{ $accountForMenu }}</p>
                @endif

                @if(session()->has('logInfo') && session('logInfo.is_logged'))
                    <div class="flex flex-col gap-4 mt-3 w-full">
                        <button id="btnAccountSwitch"
                                class="text-base text-white w-full h-12 rounded-full bg-[#7367EF]">{{ __('messages.account-switch') }}
                        </button>

                        <button id="btnLogout"
                                class="text-base text-white w-full h-12 rounded-full bg-[#7367EF]">{{ __('messages.log-out') }}</button>
                    </div>
                @endif

            </div>

            <div class="flex flex-col py-4">

                @if(Session::get('account_verification_status', false))
                    <details class="group [&amp;_summary::-webkit-details-marker]:hidden select-none">
                        <summary class="flex cursor-pointer items-center flex-row justify-between">
                            <a class="text-[color:var(--brand-color-blue)] text-lg font-bold block"
                               href="javascript:void(0)">{{ __('messages.eWallet-btn') }}</a>

                            <div class="shrink-0 px-4 py-2">
                                  <span class="transition duration-300 group-open:-rotate-180">
                                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                          fill="currentColor">
                                        <path fill-rule="evenodd"
                                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                              clip-rule="evenodd"></path>
                                     </svg>
                                  </span>
                            </div>
                        </summary>

                        <div class="flex flex-col mb-2">

                            <a class="flex items-center ml-5 py-2 text-[color:var(--brand-color-blue)] text-lg font-bold"
                               href="javascript:void(0)"
                               id="btnEWApproveOrRejectMenu">{{ __('messages.ew-approve-or-reject-btn') }}</a>

                            <a class="flex items-center ml-5 text-[color:var(--brand-color-blue)] text-lg font-bold"
                               href="javascript:void(0)"
                               id="btnEWChangeOrResetEWalletPINMenu">
                                {{ __('messages.ew-change-or-reset-e-wallet-pin-btn') }}
                            </a>

                            <a class="flex items-center ml-5 text-[color:var(--brand-color-blue)] text-lg font-bold"
                               href="javascript:void(0)"
                               id="btnEWDeviceBindMenu">{{ __('messages.ew-device-bind-btn') }}</a>

                            <a class="flex items-center ml-5 text-[color:var(--brand-color-blue)] text-lg font-bold"
                               href="javascript:void(0)"
                               id="btnEWEWalletCloseMenu">{{ __('messages.ew-e-wallet-close-btn') }}</a>

                            <a class="flex items-center ml-5 text-[color:var(--brand-color-blue)] text-lg font-bold"
                               href="javascript:void(0)" id="btnEWLockOrBlockMenu">
                                {{ __('messages.ew-lock-or-block-btn') }}
                            </a>


                            <a class="flex items-center ml-5 text-[color:var(--brand-color-blue)] text-lg font-bold"
                               href="javascript:void(0)" id="btnEWUnlockOrActiveMenu">
                                {{ __('messages.ew-unlock-or-active-btn') }}
                            </a>

                            <a class="flex items-center ml-5 text-[color:var(--brand-color-blue)] text-lg font-bold"
                               href="javascript:void(0)" id="btnEWAboutSonaliEWalletMenu"
                               data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
                               data-text="{{ __('scripts.default-call-center-text') }}">
                                {{ __('messages.ew-about-sonali-e-wallet-btn') }}
                            </a>

                            <a class="flex items-center ml-5 text-[color:var(--brand-color-blue)] text-lg font-bold"
                               href="javascript:void(0)" id="btnCreateIssueEWalletMenu">
                                {{ __('messages.my-complain-btn') }}
                            </a>

                        </div>

                    </details>
                @else
                    <a class="text-[color:var(--brand-color-blue)] text-lg font-bold py-1 my-1"
                       href="javascript:void(0)" id="btnEWalletDisableMenu">{{ __('messages.eWallet-btn') }}</a>
                @endif

                <a class="text-[color:var(--brand-color-blue)] text-lg font-bold py-1 my-1"
                   href="javascript:void(0)" id="btnEShebaMenu">{{ __('messages.eSheba-btn') }}</a>

                <a class="text-[color:var(--brand-color-blue)] text-lg font-bold py-1 my-1"
                   href="https://sbl.com.bd:7070/" target="_blank">{{ __('messages.spg-btn') }}</a>

                <a class="text-[color:var(--brand-color-blue)] text-lg font-bold py-1 my-1"
                   href="javascript:void(0)" id="btnAgentBankingMenu"
                   data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
                   data-text="{{ __('scripts.default-call-center-text') }}">{{ __('messages.agent-banking-btn') }}</a>

                <a class="text-[color:var(--brand-color-blue)] text-lg font-bold py-1 my-1"
                   href="javascript:void(0)" id="btnSonaliBankProductMenu"
                   data-voice="{{ app()->getLocale() === 'en' ? config('voices.defaultCallForHelp.voice.en') : config('voices.defaultCallForHelp.voice.bn') }}"
                   data-text="{{ __('scripts.default-call-center-text') }}">{{ __('messages.sonali-bank-product-btn') }}</a>

                <a class="text-[color:var(--brand-color-blue)] text-lg font-bold py-1 my-1"
                   href="javascript:void(0)" id="btnCreateIssueMenu">{{ __('messages.my-complain-btn') }}</a>

                <a href="javascript:void(0)"
                   class="text-[color:var(--brand-color-blue)] text-lg font-bold py-1 my-1"
                   onclick="closeNav()">{{ __('messages.cancel') }}</a>
            </div>
        </div>
    </div>

</div>
<!-- Mobile Menu End -->
