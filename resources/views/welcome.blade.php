<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MoneyKu') }} - Kelola Keuangan Bisnis dengan Lebih Rapi</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            *, ::before, ::after { box-sizing: border-box; border-width: 0; border-style: solid; }
            html { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; -webkit-font-smoothing: antialiased; }
            body { margin: 0; background: #ffffff; color: #1f2937; }
            .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
            @media (min-width: 768px) { .container { padding: 0 2rem; } }
            .flex { display: flex; }
            .flex-col { flex-direction: column; }
            .items-center { align-items: center; }
            .items-start { align-items: flex-start; }
            .justify-between { justify-content: space-between; }
            .justify-center { justify-content: center; }
            .gap-2 { gap: 0.5rem; }
            .gap-3 { gap: 0.75rem; }
            .gap-4 { gap: 1rem; }
            .gap-6 { gap: 1.5rem; }
            .gap-8 { gap: 2rem; }
            .gap-12 { gap: 3rem; }
            .grid { display: grid; }
            .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
            .text-center { text-align: center; }
            .text-left { text-align: left; }
            .w-full { width: 100%; }
            .h-full { height: 100%; }
            .min-h-screen { min-height: 100vh; }
            .relative { position: relative; }
            .overflow-hidden { overflow: hidden; }
            .px-4 { padding-left: 1rem; padding-right: 1rem; }
            .px-5 { padding-left: 1.25rem; padding-right: 1.25rem; }
            .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
            .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
            .py-2\.5 { padding-top: 0.625rem; padding-bottom: 0.625rem; }
            .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
            .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
            .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
            .py-12 { padding-top: 3rem; padding-bottom: 3rem; }
            .py-16 { padding-top: 4rem; padding-bottom: 4rem; }
            .py-20 { padding-top: 5rem; padding-bottom: 5rem; }
            .pt-4 { padding-top: 1rem; }
            .pb-4 { padding-bottom: 1rem; }
            .mt-2 { margin-top: 0.5rem; }
            .mt-3 { margin-top: 0.75rem; }
            .mt-4 { margin-top: 1rem; }
            .mt-6 { margin-top: 1.5rem; }
            .mt-8 { margin-top: 2rem; }
            .mt-12 { margin-top: 3rem; }
            .mt-16 { margin-top: 4rem; }
            .mb-2 { margin-bottom: 0.5rem; }
            .mb-3 { margin-bottom: 0.75rem; }
            .mb-4 { margin-bottom: 1rem; }
            .mb-6 { margin-bottom: 1.5rem; }
            .mb-8 { margin-bottom: 2rem; }
            .mb-12 { margin-bottom: 3rem; }
            .mx-auto { margin-left: auto; margin-right: auto; }
            .-mx-4 { margin-left: -1rem; margin-right: -1rem; }
            .space-y-2 > * + * { margin-top: 0.5rem; }
            .space-y-3 > * + * { margin-top: 0.75rem; }
            .space-y-4 > * + * { margin-top: 1rem; }
            .space-y-6 > * + * { margin-top: 1.5rem; }
            .text-xs { font-size: 0.75rem; line-height: 1rem; }
            .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
            .text-base { font-size: 1rem; line-height: 1.5rem; }
            .text-lg { font-size: 1.125rem; line-height: 1.75rem; }
            .text-xl { font-size: 1.25rem; line-height: 1.75rem; }
            .text-2xl { font-size: 1.5rem; line-height: 2rem; }
            .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
            .text-4xl { font-size: 2.25rem; line-height: 2.5rem; }
            .font-medium { font-weight: 500; }
            .font-semibold { font-weight: 600; }
            .font-bold { font-weight: 700; }
            .font-extrabold { font-weight: 800; }
            .leading-tight { line-height: 1.25; }
            .leading-relaxed { line-height: 1.625; }
            .tracking-tight { letter-spacing: -0.025em; }
            .tracking-wider { letter-spacing: 0.05em; }
            .text-white { color: #ffffff; }
            .text-gray-400 { color: #9ca3af; }
            .text-gray-500 { color: #6b7280; }
            .text-gray-600 { color: #4b5563; }
            .text-gray-700 { color: #374151; }
            .text-gray-800 { color: #1f2937; }
            .text-gray-900 { color: #111827; }
            .text-orange-50 { color: #fff7ed; }
            .text-orange-100 { color: #ffedd5; }
            .text-orange-400 { color: #fb923c; }
            .text-orange-500 { color: #f97316; }
            .text-orange-600 { color: #ea580c; }
            .bg-white { background-color: #ffffff; }
            .bg-gray-50 { background-color: #f9fafb; }
            .bg-gray-100 { background-color: #f3f4f6; }
            .bg-gray-200 { background-color: #e5e7eb; }
            .bg-orange-50 { background-color: #fff7ed; }
            .bg-orange-100 { background-color: #ffedd5; }
            .bg-orange-500 { background-color: #f97316; }
            .bg-orange-600 { background-color: #ea580c; }
            .border { border-width: 1px; }
            .border-t { border-top-width: 1px; }
            .border-b { border-bottom-width: 1px; }
            .border-gray-100 { border-color: #f3f4f6; }
            .border-gray-200 { border-color: #e5e7eb; }
            .border-orange-100 { border-color: #ffedd5; }
            .border-orange-200 { border-color: #fed7aa; }
            .border-transparent { border-color: transparent; }
            .rounded-lg { border-radius: 0.5rem; }
            .rounded-xl { border-radius: 0.75rem; }
            .rounded-2xl { border-radius: 1rem; }
            .rounded-full { border-radius: 9999px; }
            .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
            .shadow-md { box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1); }
            .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1); }
            .transition-colors { transition-property: color, background-color, border-color; transition-duration: 150ms; }
            .transition-all { transition-property: all; transition-duration: 300ms; }
            .hover\:bg-orange-50:hover { background-color: #fff7ed; }
            .hover\:bg-orange-400:hover { background-color: #fb923c; }
            .hover\:bg-orange-600:hover { background-color: #ea580c; }
            .hover\:bg-gray-50:hover { background-color: #f9fafb; }
            .hover\:bg-gray-100:hover { background-color: #f3f4f6; }
            .hover\:text-orange-600:hover { color: #ea580c; }
            .hover\:text-gray-700:hover { color: #374151; }
            .hover\:border-orange-200:hover { border-color: #fed7aa; }
            .hover\:border-gray-300:hover { border-color: #d1d5db; }
            .focus\:outline-none:focus { outline: none; }
            .focus\:ring-2:focus { box-shadow: 0 0 0 2px rgba(249,115,22,0.3); }
            .focus\:ring-orange-500:focus { --tw-ring-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,0.3); }
            .inline-flex { display: inline-flex; }
            .block { display: block; }
            .hidden { display: none; }
            .list-none { list-style: none; margin: 0; padding: 0; }
            .no-underline { text-decoration: none; }
            .object-cover { object-fit: cover; }
            .object-center { object-position: center; }
            .whitespace-nowrap { white-space: nowrap; }
            .shrink-0 { flex-shrink: 0; }
            .p-3 { padding: 0.75rem; }
            .p-4 { padding: 1rem; }
            .p-6 { padding: 1.5rem; }
            .max-w-xs { max-width: 20rem; }
            .max-w-sm { max-width: 24rem; }
            .max-w-md { max-width: 28rem; }
            .max-w-lg { max-width: 32rem; }
            .max-w-xl { max-width: 36rem; }
            .max-w-2xl { max-width: 42rem; }
            .max-w-3xl { max-width: 48rem; }
            .max-w-4xl { max-width: 56rem; }
            .max-w-5xl { max-width: 64rem; }
            .max-w-6xl { max-width: 72rem; }
            .max-w-7xl { max-width: 80rem; }
            .w-5 { width: 1.25rem; }
            .w-6 { width: 1.5rem; }
            .w-8 { width: 2rem; }
            .w-10 { width: 2.5rem; }
            .w-12 { width: 3rem; }
            .w-16 { width: 4rem; }
            .h-5 { height: 1.25rem; }
            .h-6 { height: 1.5rem; }
            .h-8 { height: 2rem; }
            .h-10 { height: 2.5rem; }
            .h-12 { height: 3rem; }
            .h-16 { height: 4rem; }
            .flex-wrap { flex-wrap: wrap; }
            .flex-1 { flex: 1 1 0%; }
            .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border-width: 0; }
            @media (min-width: 640px) { .sm\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); } .sm\:text-lg { font-size: 1.125rem; } }
            @media (min-width: 768px) { .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); } .md\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); } .md\:text-xl { font-size: 1.25rem; } .md\:text-4xl { font-size: 2.25rem; } .md\:text-5xl { font-size: 3rem; } .md\:py-24 { padding-top: 6rem; padding-bottom: 6rem; } }
            @media (min-width: 1024px) { .lg\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); } .lg\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); } .lg\:flex-row { flex-direction: row; } .lg\:text-left { text-align: left; } .lg\:px-8 { padding-left: 2rem; padding-right: 2rem; } .lg\:py-32 { padding-top: 8rem; padding-bottom: 8rem; } .lg\:text-6xl { font-size: 3.75rem; } .lg\:block { display: block; } }
            .list-image-none { list-style-type: none; }
            ul, ol { padding: 0; margin: 0; }
            a { color: inherit; text-decoration: none; }
            svg { display: inline-block; vertical-align: middle; }
        </style>
    @endif
</head>
<body class="font-sans antialiased text-gray-800 bg-white">

    {{-- Navigation --}}
    <header class="border-b border-gray-100 bg-white/95 backdrop-blur-sm sticky top-0 z-50">
        <div class="container flex items-center justify-between h-16">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 no-underline">
                <div class="w-9 h-9 bg-orange-500 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-base">M</span>
                </div>
                <span class="font-bold text-orange-600 text-base">MoneyKu</span>
            </a>
            @if (Route::has('login'))
                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-5 py-2 bg-orange-500 text-white text-sm font-semibold rounded-lg hover:bg-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors no-underline">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors no-underline">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2 bg-orange-500 text-white text-sm font-semibold rounded-lg hover:bg-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors no-underline">
                                Daftar
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    {{-- Hero Section --}}
    <section class="relative overflow-hidden">
        <div class="container py-16 md:py-24 lg:py-32">
            <div class="max-w-3xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-orange-50 border border-orange-200 rounded-full text-sm font-medium text-orange-600 mb-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Solusi Keuangan untuk Bisnis Anda
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight leading-tight">
                    Kelola Keuangan Bisnis<br>
                    <span class="text-orange-500">dengan Lebih Rapi</span>
                </h1>
                <p class="mt-6 text-base md:text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed">
                    Pantau transaksi, budget, approval, dan arus kas perusahaan Anda dalam satu dashboard yang terintegrasi, aman, dan mudah digunakan.
                </p>
                <div class="mt-8 flex items-center justify-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-6 py-3 bg-orange-500 text-white font-semibold text-sm rounded-lg hover:bg-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors no-underline">
                                Buka Dashboard
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-orange-500 text-white font-semibold text-sm rounded-lg hover:bg-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors no-underline">
                                Mulai Sekarang
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border border-gray-200 text-gray-700 font-semibold text-sm rounded-lg hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors no-underline">
                                    Daftar Gratis
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="border-t border-gray-100 bg-gray-50">
        <div class="container py-16 md:py-24">
            <div class="max-w-2xl mx-auto text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Fitur Unggulan</h2>
                <p class="mt-3 text-base text-gray-500">Semua yang Anda butuhkan untuk mengelola keuangan perusahaan</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white border border-gray-100 rounded-xl p-6 hover:shadow-md hover:border-orange-200 transition-all">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Multi Perusahaan</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Kelola beberapa perusahaan dalam satu akun. Beralih antar perusahaan dengan mudah.</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-6 hover:shadow-md hover:border-orange-200 transition-all">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Approval 2 Level</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Sistem persetujuan dua tingkat untuk memastikan setiap transaksi melewati verifikasi yang tepat.</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-6 hover:shadow-md hover:border-orange-200 transition-all">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Laporan Keuangan</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Laporan keuangan lengkap dengan visualisasi data untuk analisis bisnis yang lebih baik.</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-6 hover:shadow-md hover:border-orange-200 transition-all">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Invoice & Hutang</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Buat dan kelola invoice, pantau hutang piutang dengan sistem yang terintegrasi.</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-6 hover:shadow-md hover:border-orange-200 transition-all">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Manajemen Budget</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Atur anggaran perusahaan per kategori dan pantau realisasi anggaran secara real-time.</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-6 hover:shadow-md hover:border-orange-200 transition-all">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Multi Role</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Atur hak akses pengguna dengan role yang berbeda-beda untuk keamanan data yang lebih baik.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="border-t border-gray-100">
        <div class="container py-16 md:py-24">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Siap Mengelola Keuangan Bisnis?</h2>
                <p class="mt-3 text-base text-gray-500">Mulai gunakan MoneyKu untuk mengelola keuangan perusahaan Anda dengan lebih rapi dan efisien.</p>
                <div class="mt-8 flex items-center justify-center gap-4">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-orange-500 text-white font-semibold text-sm rounded-lg hover:bg-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors no-underline">
                            Daftar Gratis
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    @endif
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 border border-gray-200 text-gray-700 font-semibold text-sm rounded-lg hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors no-underline">
                            Masuk
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-gray-100 bg-gray-50">
        <div class="container py-12">
            <div class="flex flex-col items-center gap-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 no-underline">
                    <div class="w-8 h-8 bg-orange-500 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-sm">M</span>
                    </div>
                    <span class="font-bold text-orange-600 text-sm">MoneyKu</span>
                </a>
                <p class="text-sm text-gray-400 text-center max-w-md">Solusi manajemen keuangan bisnis yang rapi, aman, dan mudah digunakan.</p>
                <div class="flex items-center gap-6 text-sm text-gray-400">
                    <span>&copy; {{ date('Y') }} MoneyKu. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
