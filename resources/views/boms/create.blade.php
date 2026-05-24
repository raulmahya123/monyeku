@extends('layouts.main')

@section('title', 'BOM Baru')

@section('subtitle')
    Buat bill of materials baru.
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('boms.store') }}" x-data="bomForm()">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-5">
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
                            <label class="form-label">Produk Jadi</label>
                            <select name="product_id" class="form-input" required>
                                <option value="">Pilih produk</option>
                                @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-input" value="{{ old('quantity', 1) }}" min="1" required>
                            @error('quantity') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Material</h3>
                        <div class="space-y-3">
                            <template x-for="(mat, index) in materials" :key="index">
                                <div class="flex gap-3 items-start p-3 bg-gray-50 rounded-xl">
                                    <div class="flex-1">
                                        <select x-model="mat.product_id" :name="`materials[${index}][product_id]`" class="form-input text-sm">
                                            <option value="">Pilih material</option>
                                            @foreach($materials as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-24 shrink-0">
                                        <input type="number" x-model="mat.quantity" :name="`materials[${index}][quantity]`" min="1" placeholder="Qty" class="form-input text-sm text-center">
                                    </div>
                                    <button type="button" @click="removeMaterial(index)" class="p-2 rounded-lg text-red-400 hover:text-red-600 mt-0.5" x-show="materials.length > 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="addMaterial" class="flex items-center gap-2 text-orange-600 hover:text-orange-700 text-sm font-medium px-1 py-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                Tambah Material
                            </button>
                            @error('materials') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-input" rows="2">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="card bg-gray-50 border-0">
                        <div class="card-body !p-4">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Ringkasan</h3>
                            <div class="text-sm text-gray-500">Total material: <span class="font-medium text-gray-800" x-text="materials.length"></span></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                        <label for="is_active" class="text-sm text-gray-700">Aktif</label>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary flex-1">Simpan</button>
                        <a href="{{ route('boms.index') }}" class="btn-ghost">Batal</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function bomForm() {
        return {
            materials: [{ product_id: '', quantity: 1 }],
            addMaterial() { this.materials.push({ product_id: '', quantity: 1 }); },
            removeMaterial(index) { if (this.materials.length > 1) this.materials.splice(index, 1); }
        }
    }
</script>
@endpush
@endsection
