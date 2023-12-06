<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">

    {{-- Script start from here --}}
    <script src="{{ asset('js/ui.js') }}"></script>
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    {{-- Script ends from here --}}

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' | ' . __('messages.sonali-bank-vivr') : __('messages.sonali-bank-vivr') }}</title>
    <script>
        // Configure Axios to include the CSRF token in the headers of all requests
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
            defaultNIDScriptTitleEn = "{{ config('voices.defaultNIDScript.title.en') }}";
    </script>

    <style>

        /*@media (max-width: 600px) {
            .swal2-popup {
                width: 100% !important;
                padding: 10px !important;
            }

            .swal2-title,
            .swal2-content,
            .swal2-actions,
            .swal2-footer {
                width: auto !important;
                box-sizing: border-box;
            }
        }*/

        /* Responsive styles for SweetAlert modal */
        @media (max-width: 767px) {
            .swal2-popup {
                width: 90% !important;
                max-width: 100%;
                margin-left: auto;
                margin-right: auto;
                padding: 15px !important;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .swal2-title {
                font-size: 1.8em;
                margin-bottom: 15px;
                color: #333;
            }

            .swal2-content {
                font-size: 1.2em;
                color: #555;
                margin-bottom: 20px;
            }

            .swal2-input,
            .swal2-textarea,
            .swal2-select {
                width: 90% !important;
                margin-bottom: 15px;
                font-size: 1em;
                border: 1px solid #ccc;
                border-radius: 5px;
                padding: 10px;
                box-sizing: border-box;
            }

            /* Increase the height of the input box for the "Reason" field */
            #reasonInput {
                height: 80px;
            }

            .swal2-select {
                appearance: none;
                background: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='6'><path d='M5 6L0 1l1.4-1.4L5 3.2 8.6.6 10 1z' fill='%23444444'/></svg>") no-repeat right 10px center/10px 6px;
                padding-right: 30px;
            }

            .swal2-cancel,
            .swal2-confirm {
                padding: 12px 20px;
                font-size: 1.2em;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            .swal2-cancel {
                margin-right: 10px;
                background-color: #ddd;
            }

            .swal2-confirm {
                background-color: #4caf50;
                color: #fff;
            }

            .swal2-cancel:hover,
            .swal2-confirm:hover {
                background-color: #ccc;
            }
        }

    </style>
</head>

<body class="w-full min-h-screen" style="background: linear-gradient(21.64deg, #D9A629 19.97%, #0F5DA8 80.91%);">

<div class="flex absolute top-2/4 left-2/4 -translate-x-2/4 -translate-y-2/4 loader z-20 hidden">
    <div class="flex flex-col text-center items-center gap-2">
        <img class="animate-spin w-20 h-20" src="{{ asset('img/logo.png') }}" alt="">
        <span class="animate-bounce text-black">Processing...</span>
    </div>
</div>

<audio id="playMedia" controls style="display: none;">
    <source src="{{ $prompt ?? "" }}" type="audio/mp4">
</audio>


@include('partials.bg-dots')
