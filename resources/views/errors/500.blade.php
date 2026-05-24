<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 | {{ config('app.name', 'MoneyKu') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white antialiased flex items-center justify-center px-4">
    <div class="w-full max-w-md text-center">
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-50 rounded-2xl mb-4">
                <span class="text-orange-600 font-bold text-2xl">M</span>
            </div>
        </div>
        <p class="text-sm font-semibold tracking-widest text-orange-500 uppercase">Error</p>
        <h1 class="mt-3 text-7xl font-bold text-orange-500 leading-none">500</h1>
        <h2 class="mt-4 text-lg font-semibold text-gray-900">Kesalahan Server</h2>
        <p class="mt-2 text-sm text-gray-500 max-w-xs mx-auto">Terjadi kesalahan pada server. Silakan coba lagi nanti.</p>
        <div class="mt-8 flex items-center justify-center gap-3">
            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-5 py-2.5 bg-orange-500 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>
