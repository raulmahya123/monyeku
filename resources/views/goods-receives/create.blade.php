@extends('layouts.main')

@section('title', 'Terima Barang')

@section('subtitle')
    Catat penerimaan barang dari purchase order.
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('goods-receives.store') }}" x-data="grForm()">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-5">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Informasi Penerimaan</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="form-group">
                                <label class="form-label">No. Penerimaan</label>
                                <input type="text" name="receive_number" class="form-input" value="{{ old('receive_number') }}" required>
                                @error('receive_number') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">PO</label>
                                <select name="purchase_order_id" class="form-input" required x-model="poId" @change="loadPOItems">
                                    <option value="">Pilih PO</option>
                                    @foreach($purchaseOrders as $po)
                                    <option value="{{ $po->id }}" {{ old('purchase_order_id') == $po->id ? 'selected' : '' }}>{{ $po->order_number }}</option>
                                    @endforeach
                                </select>
                                @error('purchase_order_id') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Gudang</label>
                                <select name="warehouse_id" class="form-input" required>
                                    <option value="">Pilih gudang</option>
                                    @foreach($warehouses as $w)
                                    <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="form-group mt-4">
                            <label class="form-label">Tanggal Terima</label>
                            <input type="date" name="received_date" class="form-input" value="{{ old('received_date', date('Y-m-d')) }}" required>
                            @error('received_date') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Item</h3>
                        <div class="space-y-3">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex gap-3 items-start p-3 bg-gray-50 rounded-xl">
                                    <div class="flex-1">
                                        <input type="hidden" :name="`items[${index}][product_id]`" x-model="item.product_id">
                                        <span class="text-sm font-medium text-gray-800" x-text="item.product_name"></span>
                                    </div>
                                    <div class="w-20 shrink-0">
                                        <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" min="0" placeholder="Terima" class="form-input text-sm text-center">
                                    </div>
                                    <div class="w-20 shrink-0 pt-2 text-sm text-gray-500 text-center" x-text="'PO: ' + item.po_qty"></div>
                                </div>
                            </template>
                            <input type="hidden" name="items" :value="JSON.stringify(items)">
                            @error('items') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-input" rows="2">{{ old('notes') }}</textarea>
                        @error('notes') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="card bg-gray-50 border-0">
                        <div class="card-body !p-4">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Ringkasan</h3>
                            <div class="text-sm text-gray-500">Total item: <span class="font-medium text-gray-800" x-text="items.length"></span></div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary flex-1">Simpan</button>
                        <a href="{{ route('goods-receives.index') }}" class="btn-ghost">Batal</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function grForm() {
        return {
            poId: '{{ old('purchase_order_id') }}',
            items: [],
            loadPOItems() {
                if (!this.poId) { this.items = []; return; }
                fetch(`/purchase-orders/${this.poId}/items`).then(r => r.json()).then(data => {
                    this.items = data.map(i => ({ product_id: i.product_id, product_name: i.product?.name || i.product_name, quantity: i.quantity, po_qty: i.quantity }));
                }).catch(() => { this.items = []; });
            }
        }
    }
</script>
@endpush
@endsection
