@extends('layouts.main')

@section('title', 'Invoice Baru')

@section('content')
<div class="flex items-center gap-3 mb-5">
    <a href="{{ route('invoices.index') }}" class="btn-icon-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 class="text-lg font-bold text-gray-800">Invoice Baru</h2>
        <p class="text-sm text-gray-400">Buat invoice untuk penagihan ke pelanggan</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('invoices.store') }}" x-data="invoiceForm()">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-5">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Informasi Pelanggan</h3>
                        <div class="space-y-4">
                            <div class="form-group">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="form-input" placeholder="Nama pelanggan">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="form-label">Telepon (opsional)</label>
                                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="form-input" placeholder="Nomor telepon">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email (opsional)</label>
                                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="form-input" placeholder="Email pelanggan">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Item Invoice</h3>
                        <div class="space-y-3">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex gap-3 items-start p-3 bg-gray-50 rounded-xl">
                                    <div class="flex-1 min-w-0">
                                        <input type="text" x-model="item.description" :name="`items[${index}][description]`" required placeholder="Deskripsi item" class="form-input text-sm">
                                    </div>
                                    <div class="w-20 shrink-0">
                                        <input type="number" x-model="item.quantity" :name="`items[${index}][quantity]`" required min="1" placeholder="Qty" class="form-input text-sm text-center" @input="calcTotal">
                                    </div>
                                    <div class="w-28 shrink-0">
                                        <input type="number" x-model="item.price" :name="`items[${index}][price]`" required min="0" placeholder="Harga" class="form-input text-sm text-right" @input="calcTotal">
                                    </div>
                                    <div class="w-28 shrink-0 pt-2.5 text-sm text-right font-semibold text-gray-800" x-text="'Rp ' + formatNumber(item.quantity * item.price)"></div>
                                    <button type="button" @click="removeItem(index)" class="p-2 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors mt-0.5" x-show="items.length > 1">
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
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="notes" rows="3" class="form-input" placeholder="Catatan untuk invoice...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Tanggal & Status</h3>
                        <div class="space-y-4">
                            <div class="form-group">
                                <label class="form-label">Tanggal Invoice</label>
                                <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jatuh Tempo</label>
                                <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="unpaid" {{ old('status', 'unpaid') === 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                                    <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-gray-50 border-0">
                        <div class="card-body !p-4">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Ringkasan</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Subtotal</span>
                                    <span class="font-medium text-gray-800" x-text="'Rp ' + formatNumber(subtotal)"></span>
                                </div>
                                <div class="flex justify-between text-sm items-center">
                                    <span class="text-gray-500">Pajak</span>
                                    <input type="number" name="tax" x-model="tax" min="0" class="form-input w-28 text-sm text-right" @input="calcTotal" placeholder="0">
                                </div>
                                <div class="flex justify-between text-base font-bold text-gray-800 border-t border-gray-200 pt-2 mt-2">
                                    <span>Total</span>
                                    <span x-text="'Rp ' + formatNumber(total)"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary flex-1">Buat Invoice</button>
                        <a href="{{ route('invoices.index') }}" class="btn-secondary">Batal</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function invoiceForm() {
        return {
            items: [{ description: '', quantity: 1, price: 0 }],
            tax: 0,
            subtotal: 0,
            total: 0,
            addItem() {
                this.items.push({ description: '', quantity: 1, price: 0 });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                    this.calcTotal();
                }
            },
            calcTotal() {
                this.subtotal = this.items.reduce((sum, item) => sum + (parseInt(item.quantity) || 0) * (parseFloat(item.price) || 0), 0);
                this.total = this.subtotal + (parseFloat(this.tax) || 0);
            },
            formatNumber(num) {
                return num.toLocaleString('id-ID');
            }
        }
    }
</script>
@endpush
@endsection
