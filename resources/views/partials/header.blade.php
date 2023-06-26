<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">

    {{-- Script start from here --}}
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    {{-- Script ends from here --}}

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' | Sonali Bank Smart IVR' : 'Sonali Bank Smart IVR' }}</title>
</head>

<body class="w-full min-h-screen" style="background: linear-gradient(21.64deg, #D9A629 19.97%, #0F5DA8 80.91%);">

<audio id="playMedia" controls style="display: none;">
    <source src="{{ $prompt ?? "" }}" type="audio/mpeg">
</audio>

@include('partials.bg-dots')
