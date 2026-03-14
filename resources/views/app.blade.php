<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Kanbix') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preload" href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet"></noscript>
    <style>
        #app .loading-shell { display: flex; min-height: 100vh; align-items: center; justify-content: center; }
        #app .loading-spinner { width: 2rem; height: 2rem; border: 2px solid #4f46e5; border-top-color: transparent; border-radius: 9999px; animation: loading-spin 0.8s linear infinite; }
        @keyframes loading-spin { to { transform: rotate(360deg); } }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app/main.js'])
</head>
<body class="antialiased bg-[#FAFAFA] text-gray-900 min-h-screen">
    <div id="app">
        <div class="loading-shell" aria-live="polite" aria-busy="true">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                <div class="loading-spinner" role="status" aria-label="Loading"></div>
                <p style="font-size: 0.875rem; color: #6b7280;">Loading...</p>
            </div>
        </div>
    </div>
</body>
</html>
