@extends('layouts.main')

@section('title', 'Masuk')

@section('content')
<div class="-mx-5 -my-6 lg:-mx-7 min-h-screen bg-white">
    <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
        <section class="hidden lg:flex items-center justify-center bg-white px-12">
            <div class="text-center text-orange-600">
                <div class="text-[10rem] leading-none font-bold tracking-tight">M</div>
                <p class="mt-4 text-2xl font-semibold tracking-wide">MoneyKu</p>
                <p class="mt-2 text-sm uppercase tracking-[0.24em] text-orange-400">Kelola uang, maksimalkan potensi</p>
            </div>
        </section>

        <section class="bg-gradient-to-br from-orange-500 via-orange-500 to-orange-600 px-5 py-8 sm:px-8 md:px-10 lg:px-14 flex items-center justify-center">
            <div class="w-full max-w-md rounded-3xl bg-white shadow-2xl border border-orange-100 overflow-hidden">
                <div class="p-7 sm:p-9">
                    <h2 class="text-4xl font-bold text-gray-900 tracking-tight">Welcome back!</h2>
                    <p class="text-sm text-gray-500 mt-2">Secure Login</p>

                    @if (session('status'))
                    <div class="mt-5 flex items-center gap-2.5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-medium">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('status') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-800 mb-2">Email Address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full rounded-xl border border-gray-200 bg-orange-50/40 px-4 py-3 text-sm text-gray-900 focus:border-orange-400 focus:ring-orange-400" placeholder="nama@email.com">
                            @error('email')<p class="form-error mt-2">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-800 mb-2">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full rounded-xl border border-gray-200 bg-orange-50/40 px-4 py-3 text-sm text-gray-900 focus:border-orange-400 focus:ring-orange-400" placeholder="••••••••">
                            @error('password')<p class="form-error mt-2">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400" style="accent-color:#f97316">
                                Ingat saya
                            </label>
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700">Forgot Password?</a>
                            @endif
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-orange-500 hover:bg-orange-600 text-white py-3.5 text-lg font-semibold transition-colors">Login</button>
                    </form>

                    <p class="text-center text-base text-gray-500 mt-7">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-orange-600 hover:text-orange-700 font-semibold">Register</a>
                    </p>
                </div>

                <div class="border-t border-gray-100 px-7 sm:px-9 py-5 bg-white">
                    <p class="text-center text-sm text-gray-500">
                        Powered by <span class="font-bold text-orange-600">BERKEMAH TEAM</span>
                    </p>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
