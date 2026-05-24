@extends('layouts.main')

@section('title', 'Edit Supplier')

@section('content')
<div class="page-header">
    <a href="{{ route('suppliers.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">Edit Supplier</h1>
</div>

<div class="card max-w-2xl">
    <div class="card-body">
        <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Kode Supplier</label>
                <input type="text" name="code" value="{{ old('code', $supplier->code) }}" required class="form-input" placeholder="Mis: SUP-001">
                @error('code') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nama Supplier</label>
                <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required class="form-input" placeholder="Nama supplier">
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Kontak Person</label>
                <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" class="form-input" placeholder="Nama kontak person">
                @error('contact_person') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" required class="form-input" placeholder="Nomor telepon">
                @error('phone') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="form-input" placeholder="email@example.com">
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Alamat <span class="text-gray-400 font-normal">(opsional)</span></label>
                <textarea name="address" rows="3" class="form-textarea" placeholder="Alamat lengkap">{{ old('address', $supplier->address) }}</textarea>
                @error('address') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">NPWP <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="text" name="npwp" value="{{ old('npwp', $supplier->npwp) }}" class="form-input" placeholder="Nomor NPWP">
                @error('npwp') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }} class="form-checkbox">
                    <span class="form-label mb-0">Aktif</span>
                </label>
                @error('is_active') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('suppliers.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
