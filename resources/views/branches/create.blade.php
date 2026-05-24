@extends('layouts.main')

@section('title', 'Cabang Baru')

@section('content')
<div class="page-header">
    <a href="{{ route('branches.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">Cabang Baru</h1>
</div>

<div class="card max-w-2xl">
    <div class="card-body">
        <form method="POST" action="{{ route('branches.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Kode Cabang</label>
                <input type="text" name="code" value="{{ old('code') }}" required class="form-input" placeholder="Mis: BR-001">
                @error('code') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nama Cabang</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="Nama cabang">
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Alamat <span class="text-gray-400 font-normal">(opsional)</span></label>
                <textarea name="address" rows="3" class="form-textarea" placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                @error('address') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Telepon <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="Nomor telepon">
                @error('phone') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="form-checkbox">
                    <span class="form-label mb-0">Aktif</span>
                </label>
                @error('is_active') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('branches.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
