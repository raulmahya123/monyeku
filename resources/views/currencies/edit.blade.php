@extends('layouts.main')

@section('title', 'Edit Mata Uang')

@section('content')
<div class="page-header">
    <a href="{{ route('currencies.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">Edit Mata Uang</h1>
</div>

<div class="card max-w-2xl">
    <div class="card-body">
        <form method="POST" action="{{ route('currencies.update', $currency) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Kode Mata Uang</label>
                <input type="text" name="code" value="{{ old('code', $currency->code) }}" required class="form-input" placeholder="Mis: USD">
                @error('code') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nama Mata Uang</label>
                <input type="text" name="name" value="{{ old('name', $currency->name) }}" required class="form-input" placeholder="Mis: US Dollar">
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Simbol</label>
                <input type="text" name="symbol" value="{{ old('symbol', $currency->symbol) }}" required class="form-input" placeholder="Mis: $">
                @error('symbol') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Kurs</label>
                <input type="number" step="0.0001" name="exchange_rate" value="{{ old('exchange_rate', $currency->exchange_rate) }}" required class="form-input" placeholder="1.0000">
                @error('exchange_rate') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_base" value="1" {{ old('is_base', $currency->is_base) ? 'checked' : '' }} class="form-checkbox">
                    <span class="form-label mb-0">Mata Uang Dasar</span>
                </label>
                @error('is_base') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $currency->is_active) ? 'checked' : '' }} class="form-checkbox">
                    <span class="form-label mb-0">Aktif</span>
                </label>
                @error('is_active') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('currencies.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
