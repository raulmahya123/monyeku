@extends('layouts.main')

@section('title', 'Edit BOM')

@section('subtitle')
    Ubah bill of materials.
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('boms.update', $bom) }}">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Kode</label>
                        <input type="text" name="code" class="form-input" value="{{ old('code', $bom->code) }}" required>
                        @error('code') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-input" value="{{ old('name', $bom->name) }}" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Produk Jadi</label>
                        <select name="product_id" class="form-input" required>
                            <option value="">Pilih produk</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id', $bom->product_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-input" value="{{ old('quantity', $bom->quantity) }}" min="1" required>
                        @error('quantity') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-input" rows="2">{{ old('description', $bom->description) }}</textarea>
                </div>

                <div class="flex items-center gap-2 mt-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $bom->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                    <label for="is_active" class="text-sm text-gray-700">Aktif</label>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn-primary">Simpan</button>
                    <a href="{{ route('boms.index') }}" class="btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
