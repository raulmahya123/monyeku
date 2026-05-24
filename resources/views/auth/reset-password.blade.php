@extends('layouts.main')

@section('title', 'Reset Password')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                <span class="text-white font-bold text-xl">M</span>
            </div>
            <h2 class="text-xl font-bold text-gray-800">Reset Password</h2>
            <p class="text-sm text-gray-400 mt-1">Buat password baru untuk akun Anda</p>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus class="form-input" placeholder="nama@email.com">
                        @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password Baru</label>
                        <input id="password" type="password" name="password" required class="form-input" placeholder="Min. 8 karakter">
                        @error('password')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required class="form-input" placeholder="Ulangi password">
                        @error('password_confirmation')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
