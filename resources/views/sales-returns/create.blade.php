@extends('layouts.main')

@section('title', 'Retur Penjualan Baru')

@section('subtitle')
    Buat retur penjualan dari pelanggan.
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('sales-returns.store') }}" x-data="srForm()">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="form-group">
                            <label class="form-label">No. Retur</label>
                            <input type="text" name="return_number" class="form-input" value="{{ old('return_number') }}" required>
                            @error('return_number') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">DO (opsional)</label>
                            <select name="delivery_order_id" class="form-input">
                                <option value="">Pilih DO</option>
                                @foreach($deliveryOrders as $do)
                                <option value="{{ $do->id }}" {{ old('delivery_order_id') == $do->id ? 'selected' : '' }}>{{ $do->delivery_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Retur</label>
                            <input type="date" name="return_date" class="form-input" value="{{ old('return_date', date('Y-m-d')) }}" required>
                            @error('return_date') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Item</h3>
                        <div class="space-y-3">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex gap-3 items-start p-3 bg-gray-50 rounded-xl">
                                    <div class="flex-1">
                                        <select x-model="item.product_id" :name="`items[${index}][product_id]`" class="form-input text-sm">
                                            <option value="">Pilih produk</option>
                                            @foreach($products as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-20 shrink-0">
                                        <input type="number" x-model="item.quantity" :name="`items[${index}][quantity]`" min="1" placeholder="Qty" class="form-input text-sm text-center" @input="calcTotal">
                                    </div>
                                    <div class="w-28 shrink-0">
                                        <input type="number" x-model="item.price" :name="`items[${index}][price]`" min="0" placeholder="Harga" class="form-input text-sm text-right" @input="calcTotal">
                                    </div>
                                    <div class="w-28 shrink-0 pt-2.5 text-sm text-right font-semibold text-gray-800" x-text="'Rp ' + formatNumber(item.quantity * item.price)"></div>
                                    <button type="button" @click="removeItem(index)" class="p-2 rounded-lg text-red-400 hover:text-red-600 mt-0.5" x-show="items.length > 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="addItem" class="flex items-center gap-2 text-orange-600 hover:text-orange-700 text-sm font-medium px-1 py-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                Tambah Item
                            </button>
                            @error('items') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-input" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="card bg-gray-50 border-0">
                        <div class="card-body !p-4">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Ringkasan</h3>
                            <div class="flex justify-between text-base font-bold text-gray-800 border-t border-gray-200 pt-2">
                                <span>Total</span>
                                <span x-text="'Rp ' + formatNumber(total)"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary flex-1">Simpan</button>
                        <a href="{{ route('sales-returns.index') }}" class="btn-ghost">Batal</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function srForm() {
        return {
            items: [{ product_id: '', quantity: 1, price: 0 }],
            total: 0,
            addItem() { this.items.push({ product_id: '', quantity: 1, price: 0 }); },
            removeItem(index) { if (this.items.length > 1) { this.items.splice(index, 1); this.calcTotal(); } },
            calcTotal() { this.total = this.items.reduce((s, i) => s + (parseInt(i.quantity) || 0) * (parseFloat(i.price) || 0), 0); },
            formatNumber(n) { return n.toLocaleString('id-ID'); }
        }
    }
</script>
@endpush
@endsection
