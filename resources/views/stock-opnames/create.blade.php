@extends('layouts.main')

@section('title', 'Buat Stock Opname')

@section('subtitle')
    Isi data stock opname dan pilih produk yang akan diopname.
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('stock-opnames.store') }}" method="POST" x-data="{ products: [] }">
            @csrf

            <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">
                <div>
                    <label class="label">Gudang <span class="text-red-500">*</span></label>
                    <select name="warehouse_id" class="input" required>
                        <option value="">Pilih Gudang</option>
                        @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                    @error('warehouse_id') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Tanggal Opname <span class="text-red-500">*</span></label>
                    <input type="date" name="opname_date" class="input" value="{{ old('opname_date', date('Y-m-d')) }}" required>
                    @error('opname_date') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="label">Catatan</label>
                <textarea name="notes" class="input" rows="2">{{ old('notes') }}</textarea>
                @error('notes') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-800">Pilih Produk</h3>
                <p class="text-sm text-gray-500">Centang produk yang akan diopname dan masukkan stok fisik.</p>
            </div>

            @if($products->count() > 0)
            <div class="table-wrap border rounded-lg">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-10">
                                <input type="checkbox" @@click="products = $el.checked ? [...$el.closest('table').querySelectorAll('.product-checkbox')].map(cb => { cb.checked = true; return cb.value; }) : []" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            </th>
                            <th>Kode</th>
                            <th>Nama Produk</th>
                            <th class="text-center" style="width:120px">Stok Sistem</th>
                            <th class="text-center" style="width:120px">Stok Fisik</th>
                            <th>Catatan Item</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="product-checkbox rounded border-gray-300 text-orange-600 focus:ring-orange-500" @@change="products = [...document.querySelectorAll('.product-checkbox:checked')].map(cb => cb.value)">
                            </td>
                            <td class="text-sm font-mono text-gray-600">{{ $product->code }}</td>
                            <td class="text-sm font-medium text-gray-800">{{ $product->name }}</td>
                            <td>
                                <input type="number" name="system_qty[{{ $loop->index }}]" class="input text-center text-sm" value="0" step="0.01" readonly>
                            </td>
                            <td>
                                <input type="number" name="physical_qty[{{ $loop->index }}]" class="input text-center text-sm" value="0" step="0.01" min="0">
                            </td>
                            <td>
                                <input type="text" name="item_notes[{{ $loop->index }}]" class="input text-sm" placeholder="Catatan">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @error('product_ids') <p class="error mt-2">{{ $message }}</p> @enderror
            @else
            <div class="py-10 text-center text-gray-400">
                Belum ada produk aktif. Silakan tambahkan produk terlebih dahulu.
            </div>
            @endif

            <div class="flex justify-end gap-2 mt-6">
                <a href="{{ route('stock-opnames.index') }}" class="btn-ghost btn-sm">Batal</a>
                <button type="submit" class="btn-primary btn-sm">Simpan & Lanjutkan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.effect(() => {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach((cb, index) => {
            const row = cb.closest('tr');
            const systemInput = row.querySelector('input[name^="system_qty"]');
            const physicalInput = row.querySelector('input[name^="physical_qty"]');
            const noteInput = row.querySelector('input[name^="item_notes"]');
            if (cb.checked) {
                systemInput.removeAttribute('readonly');
                physicalInput.removeAttribute('readonly');
                noteInput.removeAttribute('readonly');
            } else {
                systemInput.setAttribute('readonly', 'readonly');
                physicalInput.setAttribute('readonly', 'readonly');
                noteInput.setAttribute('readonly', 'readonly');
                systemInput.value = '0';
                physicalInput.value = '0';
                noteInput.value = '';
            }
        });
    });
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('product-checkbox')) {
        const warehouseSelect = document.querySelector('select[name="warehouse_id"]');
        const warehouseId = warehouseSelect.value;
        if (!warehouseId) return;

        const row = e.target.closest('tr');
        const systemInput = row.querySelector('input[name^="system_qty"]');
        const productId = e.target.value;

        if (e.target.checked && warehouseId) {
            fetch(`/products/${productId}/stock?warehouse_id=${warehouseId}`)
                .then(res => res.json())
                .then(data => {
                    systemInput.value = data.stock ?? 0;
                })
                .catch(() => {
                    systemInput.value = 0;
                });
        } else {
            systemInput.value = 0;
        }
    }
});

document.querySelector('select[name="warehouse_id"]')?.addEventListener('change', function() {
    const warehouseId = this.value;
    if (!warehouseId) return;

    document.querySelectorAll('.product-checkbox:checked').forEach(cb => {
        const row = cb.closest('tr');
        const systemInput = row.querySelector('input[name^="system_qty"]');
        const productId = cb.value;

        fetch(`/products/${productId}/stock?warehouse_id=${warehouseId}`)
            .then(res => res.json())
            .then(data => {
                systemInput.value = data.stock ?? 0;
            })
            .catch(() => {
                systemInput.value = 0;
            });
    });
});
</script>
@endpush
