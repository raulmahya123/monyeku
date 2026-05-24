@extends('layouts.main')

@section('title', 'Delivery Order Baru')

@section('subtitle')
    Buat pengiriman barang untuk sales order.
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('delivery-orders.store') }}" x-data="doForm()">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="form-group">
                            <label class="form-label">No. DO</label>
                            <input type="text" name="delivery_number" class="form-input" value="{{ old('delivery_number') }}" required>
                            @error('delivery_number') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">SO</label>
                            <select name="sales_order_id" class="form-input" required x-model="soId" @change="loadSOItems">
                                <option value="">Pilih SO</option>
                                @foreach($salesOrders as $so)
                                <option value="{{ $so->id }}" {{ old('sales_order_id') == $so->id ? 'selected' : '' }}>{{ $so->order_number }}</option>
                                @endforeach
                            </select>
                            @error('sales_order_id') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Kirim</label>
                            <input type="date" name="delivery_date" class="form-input" value="{{ old('delivery_date', date('Y-m-d')) }}" required>
                            @error('delivery_date') <p class="form-error">{{ $message }}</p> @enderror
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
                                        <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" min="0" placeholder="Kirim" class="form-input text-sm text-center">
                                    </div>
                                    <div class="w-20 shrink-0 pt-2 text-sm text-gray-500 text-center" x-text="'SO: ' + item.so_qty"></div>
                                </div>
                            </template>
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
                            <div class="text-sm text-gray-500">Total item: <span class="font-medium text-gray-800" x-text="items.length"></span></div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary flex-1">Simpan</button>
                        <a href="{{ route('delivery-orders.index') }}" class="btn-ghost">Batal</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function doForm() {
        return {
            soId: '{{ old('sales_order_id') }}',
            items: [],
            loadSOItems() {
                if (!this.soId) { this.items = []; return; }
                fetch(`/sales-orders/${this.soId}/items`).then(r => r.json()).then(data => {
                    this.items = data.map(i => ({ product_id: i.product_id, product_name: i.product?.name || i.product_name, quantity: i.quantity, so_qty: i.quantity }));
                }).catch(() => { this.items = []; });
            }
        }
    }
</script>
@endpush
@endsection
