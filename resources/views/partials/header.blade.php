<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
    <!-- Include Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}"/>


    {{-- Script start from here --}}
    <script src="{{ asset('js/ui.js') }}"></script>
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <!-- Include Select2 JS -->
    <script src="{{ asset('js/select2.min.js') }}"></script>
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
            defaultNIDScriptTitleEn = "{{ config('voices.defaultNIDScript.title.en') }}",
            selectAnAccountEn = "Select an account",
            selectAnAccountBn = "অ্যাকাউন্ট নির্বাচন করুন",
            eShebaAndroid = "{{ config('bank.eSheba.android') }}",
            eShebaiOS = "{{ config('bank.eSheba.ios') }}",
            SPGiOS = "{{ config('bank.SPG.ios') }}",
            SPGAndroid = "{{ config('bank.SPG.android') }}";
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

        .ac-select-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .ac-select-button:hover {
            background-color: #45a049;
        }

        .ac-select-button:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }

        .transparent-button {
            opacity: 0.7; /* Adjust the transparency level (0.0 - 1.0) */
        }


        /*.account-option {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }*/

        .account-option {
            margin-bottom: 20px;
            padding: 25px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .account-option:last-child {
            margin-bottom: 0px;
        }

        .account-option:hover {
            background-color: #f5f5f5;
        }

        input[type="radio"] {
            margin-right: 8px;
        }

        .account-details {
            margin-bottom: 8px;
        }

        .account-list-title {
            font-size: 24px;
            text-align: left;
            color: rgba(115, 103, 239, 1);
        }

        .account-details p:first-child {
            margin-bottom: 20px;
        }


        /* Optional: Add styles for the labels */
        .account-option label {
            display: block;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        /* Optional: Style for the submit button */
        .ac-submit-button {
            display: block;
            margin-top: 20px;
            padding: 12px 24px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .ac-submit-button:hover {
            background-color: #45a049;
        }

        /* Add to your existing styles or create a new CSS file */
        .ac-cancel-button {
            display: inline-block;
            padding: 10px 20px;
            margin-right: 10px;
            background-color: #d9534f; /* Red color */
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .ac-cancel-button:hover {
            background-color: #c9302c; /* Darker red color on hover */
        }

        .button-container {
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
        }

        .button-container button {
            margin: 0;
            border-color: rgba(115, 103, 239, 1);
            background-color: transparent;
            color: rgba(115, 103, 239, 1);
            border-style: solid;
            border-width: 1px;
        }

        .button-container button.ac-submit-button {
            margin-right: 20px;
            background-color: rgba(115, 103, 239, 1);
            color: #fff;
        }

        .ac-submit-button,
        .ac-cancel-button {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px; /* Add some margin between buttons */
            background-color: #4caf50; /* Green color for Submit button */
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .ac-cancel-button {
            background-color: #d9534f; /* Red color for Cancel button */
        }

        .ac-submit-button:hover,
        .ac-cancel-button:hover {
            background-color: #45a049; /* Darker green on hover for Submit button */
        }

        .ac-cancel-button:hover {
            background-color: #c9302c; /* Darker red on hover for Cancel button */
        }

        #swal2-html-container {
            display: flex !important;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        #swal2-html-container input[type=radio] {
            opacity: 0;
            display: none;
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
