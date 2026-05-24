@extends('layouts.main')

@section('title', 'Work Order Baru')

@section('subtitle')
    Buat work order produksi baru.
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('work-orders.store') }}">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">No. WO</label>
                        <input type="text" name="order_number" class="form-input" value="{{ old('order_number') }}" required>
                        @error('order_number') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Produk</label>
                        <select name="product_id" class="form-input" required>
                            <option value="">Pilih produk</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">BOM</label>
                        <select name="bom_id" class="form-input">
                            <option value="">Pilih BOM</option>
                            @foreach($boms as $bom)
                            <option value="{{ $bom->id }}" {{ old('bom_id') == $bom->id ? 'selected' : '' }}>{{ $bom->name }}</option>
                            @endforeach
                        </select>
                        @error('bom_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-input" value="{{ old('quantity', 1) }}" min="1" required>
                        @error('quantity') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-input" value="{{ old('start_date', date('Y-m-d')) }}" required>
                        @error('start_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Selesai (target)</label>
                        <input type="date" name="end_date" class="form-input" value="{{ old('end_date') }}">
                        @error('end_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-input" rows="2">{{ old('notes') }}</textarea>
                    @error('notes') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn-primary">Simpan</button>
                    <a href="{{ route('work-orders.index') }}" class="btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
