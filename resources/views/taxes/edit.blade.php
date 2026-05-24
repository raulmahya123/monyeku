@extends('layouts.main')

@section('title', 'Edit Pajak')

@section('content')
<div class="page-header">
    <a href="{{ route('taxes.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">Edit Pajak</h1>
</div>

<div class="card max-w-2xl">
    <div class="card-body">
        <form method="POST" action="{{ route('taxes.update', $tax) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Kode Pajak</label>
                <input type="text" name="code" value="{{ old('code', $tax->code) }}" required class="form-input" placeholder="Mis: PPN-11">
                @error('code') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nama Pajak</label>
                <input type="text" name="name" value="{{ old('name', $tax->name) }}" required class="form-input" placeholder="Mis: PPN 11%">
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tarif (%)</label>
                <input type="number" step="0.01" name="rate" value="{{ old('rate', $tax->rate) }}" required class="form-input" placeholder="11">
                @error('rate') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tipe</label>
                <select name="type" required class="form-select">
                    <option value="">Pilih tipe</option>
                    <option value="ppn" {{ old('type', $tax->type) === 'ppn' ? 'selected' : '' }}>PPN</option>
                    <option value="pph" {{ old('type', $tax->type) === 'pph' ? 'selected' : '' }}>PPh</option>
                </select>
                @error('type') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tax->is_active) ? 'checked' : '' }} class="form-checkbox">
                    <span class="form-label mb-0">Aktif</span>
                </label>
                @error('is_active') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('taxes.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
