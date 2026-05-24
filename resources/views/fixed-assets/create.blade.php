@extends('layouts.main')

@section('title', 'Tambah Aset Tetap')

@section('subtitle')
    Masukkan informasi aset tetap baru.
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('fixed-assets.store') }}">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Kode</label>
                        <input type="text" name="code" class="form-input" value="{{ old('code') }}" required>
                        @error('code') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Harga Beli</label>
                        <input type="number" name="purchase_price" class="form-input" value="{{ old('purchase_price', 0) }}" min="0" required>
                        @error('purchase_price') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nilai Residu</label>
                        <input type="number" name="residual_value" class="form-input" value="{{ old('residual_value', 0) }}" min="0">
                        @error('residual_value') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Metode Depresiasi</label>
                        <select name="depreciation_method" class="form-input">
                            <option value="straight_line" {{ old('depreciation_method') === 'straight_line' ? 'selected' : '' }}>Garis Lurus</option>
                            <option value="declining" {{ old('depreciation_method') === 'declining' ? 'selected' : '' }}>Saldo Menurun</option>
                        </select>
                        @error('depreciation_method') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Umur Manfaat (bulan)</label>
                        <input type="number" name="useful_life" class="form-input" value="{{ old('useful_life', 60) }}" min="1">
                        @error('useful_life') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Tanggal Beli</label>
                        <input type="date" name="purchase_date" class="form-input" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                        @error('purchase_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                        @error('status') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-input" rows="2">{{ old('description') }}</textarea>
                    @error('description') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn-primary">Simpan</button>
                    <a href="{{ route('fixed-assets.index') }}" class="btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
