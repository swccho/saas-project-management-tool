<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.display_title', 'Kanbix — Project Management SaaS') }}</title>
    <meta name="description" content="Kanbix is a modern project management platform for teams to plan work, track tasks, and collaborate efficiently.">
    <meta property="og:title" content="Kanbix — Project Management SaaS">
    <meta property="og:description" content="Manage projects, track tasks, and collaborate with your team using Kanbix.">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Kanbix">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Kanbix — Project Management SaaS">
    <meta name="twitter:description" content="Manage projects, track tasks, and collaborate with your team using Kanbix.">
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
