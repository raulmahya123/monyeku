@extends('layouts.main')

@section('title', 'Lupa Password')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                <span class="text-white font-bold text-xl">M</span>
            </div>
            <h2 class="text-xl font-bold text-gray-800">Lupa Password</h2>
            <p class="text-sm text-gray-400 mt-1">Kami akan kirim tautan reset ke email Anda</p>
        </div>

        <div class="card">
            <div class="card-body">
                @if (session('status'))
                <div class="flex items-center gap-2 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm mb-4">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-input" placeholder="nama@email.com">
                        @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn-primary w-full justify-center">Kirim Tautan Reset</button>
                </form>
            </div>
        </div>

        <p class="text-center text-sm text-gray-400 mt-5">
            <a href="{{ route('login') }}" class="text-orange-600 hover:text-orange-700 font-medium">Kembali ke Login</a>
        </p>
    </div>
</div>
@endsection
