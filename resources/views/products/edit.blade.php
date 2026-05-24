@extends('layouts.main')

@section('title', 'Edit Produk')

@section('subtitle')
    Ubah informasi produk.
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('products.update', $product) }}">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="code" class="form-label">Kode</label>
                        <input type="text" id="code" name="code" class="form-input" value="{{ old('code', $product->code) }}" required>
                        @error('code') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $product->name) }}" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select id="category_id" name="category_id" class="form-input">
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="unit" class="form-label">Satuan</label>
                        <input type="text" id="unit" name="unit" class="form-input" value="{{ old('unit', $product->unit) }}">
                        @error('unit') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="type" class="form-label">Tipe</label>
                        <select id="type" name="type" class="form-input">
                            <option value="product" {{ old('type', $product->type) === 'product' ? 'selected' : '' }}>Produk</option>
                            <option value="material" {{ old('type', $product->type) === 'material' ? 'selected' : '' }}>Material</option>
                            <option value="service" {{ old('type', $product->type) === 'service' ? 'selected' : '' }}>Jasa</option>
                        </select>
                        @error('type') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="barcode" class="form-label">Barcode</label>
                        <input type="text" id="barcode" name="barcode" class="form-input" value="{{ old('barcode', $product->barcode) }}">
                        @error('barcode') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="purchase_price" class="form-label">Harga Beli</label>
                        <input type="number" id="purchase_price" name="purchase_price" class="form-input" value="{{ old('purchase_price', $product->purchase_price) }}" min="0">
                        @error('purchase_price') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="selling_price" class="form-label">Harga Jual</label>
                        <input type="number" id="selling_price" name="selling_price" class="form-input" value="{{ old('selling_price', $product->selling_price) }}" min="0">
                        @error('selling_price') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="stock_min" class="form-label">Stok Minimal</label>
                        <input type="number" id="stock_min" name="stock_min" class="form-input" value="{{ old('stock_min', $product->stock_min) }}" min="0">
                        @error('stock_min') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="stock_max" class="form-label">Stok Maksimal</label>
                        <input type="number" id="stock_max" name="stock_max" class="form-input" value="{{ old('stock_max', $product->stock_max) }}" min="0">
                        @error('stock_max') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea id="description" name="description" class="form-input" rows="2">{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-2 mt-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                    <label for="is_active" class="text-sm text-gray-700">Aktif</label>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn-primary">Simpan</button>
                    <a href="{{ route('products.index') }}" class="btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
