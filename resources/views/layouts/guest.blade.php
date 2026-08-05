<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        <style>
            html, body, body [class], body [id] {
                background-color: #fff !important;
                background-image: none !important;
                color: #000 !important;
            }

            body *, body *::before, body *::after {
                color: #000 !important;
            }

            html, body, .auth-scene, .verify-scene {
                background: #fff !important;
                background-image: none !important;
            }

            .auth-frame, .auth-panel, .auth-card, .verify-card {
                background: #fff !important;
                color: #202124 !important;
            }

            .auth-frame h1, .auth-frame h2, .auth-frame h3,
            .auth-frame p, .auth-frame label, .verify-card h1,
            .verify-card h2, .verify-card p, .verify-card label {
                color: #202124 !important;
            }
            body a { color:#d94845 !important; }
            body button[type="submit"] {
                background:#f25f5c !important; border-color:#f25f5c !important; color:#fff !important;
                box-shadow:0 6px 16px rgba(242,95,92,.22);
            }
            .auth-frame, .auth-panel, .auth-card, .verify-card {
                border-color:#eadfdd !important; box-shadow:0 16px 40px rgba(31,41,55,.1) !important;
            }
        </style>
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
