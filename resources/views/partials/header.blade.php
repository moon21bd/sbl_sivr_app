<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">

    <!-- Include Select2 CSS -->
    {{--    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}"/>--}}
    <!-- Include Smoothness jquery-ui CSS -->
    <link rel="stylesheet" href="{{ asset('css/smoothness-jquery-ui.css') }}">

    {{-- Script start from here --}}
    <script src="{{ asset('js/ui.js') }}"></script>
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>

    <!-- Include Select2 JS -->
    {{--    <script src="{{ asset('js/select2.full.min.js') }}"></script>--}}

    <!-- Include jqueryui related JS -->
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.ui.touch-punch.min.js') }}"></script>
    {{-- Script ends from here --}}

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' | ' . __('messages.sonali-bank-vivr') : __('messages.sonali-bank-vivr') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
    {{--<link rel="manifest" href="{{ asset('img/favicon/site.webmanifest') }}">--}}

    <script type="application/javascript">
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

        const helpCenterNumber = {{ config('bank.customer-support') }},
            defaultContactOurCallCenter = "{{ __('scripts.default-contact-our-call-center') }}",
            defaultCallCenterText = "{{ __('scripts.default-contact-our-call-center') }}",
            defaultConfirmButtonText = "{{ __('scripts.default-confirm-button-text') }}",
            defaultCancelButtonText = "{{ __('scripts.default-cancel-button-text') }}",
            defaultVerificationText = "{{ __('scripts.default-verification-button-text') }}",
            defaultVerificationCancelText = "{{ __('scripts.default-verification-cancel-button-text') }}",
            verificationTextEn = "{{ config('voices.defaultVerification.text.en') }}",
            verificationTextBn = "{{ config('voices.defaultVerification.text.bn') }}",
            verificationTitleEn = "{{ config('voices.defaultVerification.title.en') }}",
            verificationTitleBn = "{{ config('voices.defaultVerification.title.bn') }}",
            defaultNIDScriptTextEn = "{{ config('voices.defaultNIDScript.text.en') }}",
            defaultNIDScriptTextBn = "{{ config('voices.defaultNIDScript.text.bn') }}",
            defaultNIDScriptTitleBn = "{{ config('voices.defaultNIDScript.title.bn') }}",
            defaultNIDScriptTitleEn = "{{ config('voices.defaultNIDScript.title.en') }}",
            selectAnAccountEn = "Select an account",
            selectAnAccountBn = "অ্যাকাউন্ট নির্বাচন করুন",
            eShebaAndroid = "{{ config('bank.eSheba.android') }}",
            eShebaiOS = "{{ config('bank.eSheba.ios') }}",
            SPGiOS = "{{ config('bank.SPG.ios') }}",
            SPGAndroid = "{{ config('bank.SPG.android') }}",
            eWalletDisableTitleEn = "{{ config('voices.eWalletDisable.title.en') }}",
            eWalletDisableTitleBn = "{{ config('voices.eWalletDisable.title.bn') }}",
            eWalletDisableTextEn = "{{ config('voices.eWalletDisable.text.en') }}",
            eWalletDisableTextBn = "{{ config('voices.eWalletDisable.text.bn') }}",
            cardsDisableTitleEn = "{{ config('voices.cardsDisable.title.en') }}",
            cardsDisableTitleBn = "{{ config('voices.cardsDisable.title.bn') }}",
            cardsDisableTextEn = "{{ config('voices.cardsDisable.text.en') }}",
            cardsDisableTextBn = "{{ config('voices.cardsDisable.text.bn') }}"
        ;
    </script>

    <style>
        .complaint-swal-bg.swal2-container.swal2-shown .swal2-html-container > div {
            text-align: left;
        }

        .complaint-swal-bg.swal2-container.swal2-shown .swal2-html-container label {
            display: block;
            margin-bottom: 5px;
            text-align: left !important;
        }

        .complaint-swal-bg.swal2-container.swal2-shown .swal2-html-container select,
        .complaint-swal-bg.swal2-container.swal2-shown .swal2-html-container input {
            width: 100% !important;
            box-sizing: border-box;
        }
    </style>
</head>

<body class="w-full min-h-screen" style="background: linear-gradient(21.64deg, #D9A629 19.97%, #0F5DA8 80.91%);">

<div
    class="flex absolute top-2/4 left-2/4 -translate-x-2/4 -translate-y-2/4 loader bg-gradient-to-r from-[#E9B308] to-[#1D629F] p-3 rounded-md z-20 hidden">
    <div class="flex flex-col text-center items-center gap-2">
        <img class="animate-spin w-20 h-20" src="{{ asset('img/logo.png') }}" alt="">
        <span class="animate-bounce text-white">{{ __('messages.processing') }}</span>
    </div>
</div>

<audio id="playMedia" controls style="display: none;">
    <source src="{{ $prompt ?? "" }}" type="audio/mp4">
</audio>


@include('partials.bg-dots')
