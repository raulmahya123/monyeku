@extends('layouts.main')

@section('title', 'Perusahaan Baru')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('companies.index') }}" class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 hover:text-orange-500 hover:border-orange-200 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h1 class="text-lg font-semibold text-gray-900">Perusahaan Baru</h1>
        <p class="text-sm text-gray-500">Buat perusahaan baru untuk mengelola keuangan</p>
    </div>
</div>

<div class="max-w-lg">
    <div class="card">
        <div class="card-body">
        <form method="POST" action="{{ route('companies.store') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Perusahaan</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all placeholder:text-gray-400"
                    placeholder="Nama perusahaan">
                @error('name') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
                <textarea name="address" rows="3"
                    class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all placeholder:text-gray-400"
                    placeholder="Alamat perusahaan">{{ old('address') }}</textarea>
                @error('address') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('companies.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
        </div>
    </div>
</div>
@endsection
