<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MoneyKu') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-gray-800">
    <div class="min-h-screen flex">
        <div class="hidden lg:flex w-[480px] bg-orange-500 p-12 flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-12">
                    <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <span class="text-white font-bold text-xl">M</span>
                    </div>
                    <span class="text-xl font-bold text-white">MoneyKu</span>
                </div>
                <h1 class="text-3xl font-bold text-white leading-tight">Kelola keuangan bisnis<br>dengan lebih rapi.</h1>
                <p class="mt-4 text-orange-100 text-sm leading-relaxed max-w-sm">Pantau transaksi, budget, dan arus kas dalam satu dashboard yang mudah digunakan.</p>
                <div class="mt-8 flex flex-wrap gap-2">
                    <span class="px-3 py-1.5 bg-white/10 rounded-lg text-xs text-white/80 backdrop-blur-sm">Multi Perusahaan</span>
                    <span class="px-3 py-1.5 bg-white/10 rounded-lg text-xs text-white/80 backdrop-blur-sm">Approval 2 Level</span>
                    <span class="px-3 py-1.5 bg-white/10 rounded-lg text-xs text-white/80 backdrop-blur-sm">Laporan Keuangan</span>
                    <span class="px-3 py-1.5 bg-white/10 rounded-lg text-xs text-white/80 backdrop-blur-sm">Invoice & Hutang</span>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-xs text-orange-200">&copy; {{ date('Y') }} MoneyKu. All rights reserved.</p>
            </div>
            <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-orange-400/20 rounded-full blur-3xl"></div>
            <div class="absolute -top-20 -left-20 w-60 h-60 bg-orange-300/20 rounded-full blur-3xl"></div>
        </div>
        <div class="flex-1 flex items-center justify-center p-8 lg:p-12">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
