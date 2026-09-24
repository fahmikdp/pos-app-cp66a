<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @wireUiScripts
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="w-full max-w-md px-4">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-600 rounded-2xl mb-4">
                <x-heroicons::solid.shopping-cart class="w-8 h-8 text-white" />
            </div>
            <h1 class="text-3xl font-bold text-primary-600">POS App</h1>
            <p class="text-gray-500 mt-1">Point of Sale — Sistem Kasir Digital</p>
        </div>
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
