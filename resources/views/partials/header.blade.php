<!DOCTYPE html>
<html lang="en">
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
    <title>{{ isset($title) ? $title . ' | Sonali Bank Smart IVR' : 'Sonali Bank Smart IVR' }}</title>
    <script>
        // Configure Axios to include the CSRF token in the headers of all requests
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
    </script>
</head>

<body class="w-full min-h-screen" style="background: linear-gradient(21.64deg, #D9A629 19.97%, #0F5DA8 80.91%);">

<div class="flex absolute top-2/4 left-2/4 -translate-x-2/4 -translate-y-2/4 loader z-20 hidden">
    <div class="flex flex-col text-center items-center gap-2">
        <img class="animate-spin w-20 h-20" src="{{ asset('img/logo.png') }}" alt="">
        <span class="animate-bounce text-black">Processing...</span>
    </div>
</div>

<audio id="playMedia" controls style="display: none;">
    <source src="{{ $prompt ?? "" }}" type="audio/mpeg">
</audio>

@include('partials.bg-dots')
