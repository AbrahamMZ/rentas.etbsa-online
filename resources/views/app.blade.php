<!DOCTYPE html>
<html class="h-full bg-gray-200">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet" />

    {{-- Inertia --}}
    <script
        src="https://polyfill.io/v3/polyfill.min.js?features=smoothscroll,NodeList.prototype.forEach,Promise,Object.values,Object.assign"
        defer></script>

    {{-- Ping CRM --}}
    <script src="https://polyfill.io/v3/polyfill.min.js?features=String.prototype.startsWith" defer></script>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('loader.css')); ?>" />
    <script src="{{ mix('/js/app.js') }}" defer></script>
    @routes
</head>

<body class="font-sans leading-none text-gray-700 antialiased">
    <div id="loading-bg">
        <div class="loading-logo">
            <img src="{{ asset('logo.png') }}" height="120" alt="Logo" />
        </div>
        <div class="loading">
            <div class="effect-1 effects"></div>
            <div class="effect-2 effects"></div>
            <div class="effect-3 effects"></div>
        </div>
    </div>

    @inertia

</body>

</html>
