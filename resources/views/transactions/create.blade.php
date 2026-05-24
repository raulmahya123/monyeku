@extends('layouts.main')

@section('title', 'Transaksi Baru')

@section('content')
    <div class="page-header">
        <a href="{{ route('transactions.index') }}" class="btn-ghost btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="page-title">Transaksi Baru</h1>
    </div>

    <div class="card max-w-2xl">
        <div class="card-body">
            <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data" x-data="{ type: '{{ old('type', 'expense') }}' }">
                @csrf

                <div class="form-group">
                    <label class="form-label">Tipe Transaksi</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label @click="type = 'income'" :class="type === 'income' ? 'border-orange-400 bg-orange-50 ring-2 ring-orange-200' : 'border-gray-200 hover:border-orange-300'" class="flex items-center p-4 border rounded-xl cursor-pointer transition-all">
                            <input type="radio" name="type" value="income" x-model="type" class="sr-only">
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center" :class="type === 'income' ? 'border-orange-500' : 'border-gray-300'">
                                <div x-show="type === 'income'" class="w-2.5 h-2.5 rounded-full bg-orange-500"></div>
                            </div>
                            <div class="ms-3">
                                <div class="text-sm font-semibold text-gray-800">Pemasukan</div>
                                <div class="text-xs text-gray-500">Gaji, Penjualan, dividen</div>
                            </div>
                        </label>
                        <label @click="type = 'expense'" :class="type === 'expense' ? 'border-orange-400 bg-orange-50 ring-2 ring-orange-200' : 'border-gray-200 hover:border-orange-300'" class="flex items-center p-4 border rounded-xl cursor-pointer transition-all">
                            <input type="radio" name="type" value="expense" x-model="type" class="sr-only">
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center" :class="type === 'expense' ? 'border-orange-500' : 'border-gray-300'">
                                <div x-show="type === 'expense'" class="w-2.5 h-2.5 rounded-full bg-orange-500"></div>
                            </div>
                            <div class="ms-3">
                                <div class="text-sm font-semibold text-gray-800">Pengeluaran</div>
                                <div class="text-xs text-gray-500">Belanja, tagihan, transport</div>
                            </div>
                        </label>
                    </div>
                    @error('type') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" required class="form-select">
                        <option value="">Pilih kategori</option>
                        <optgroup label="Pemasukan">
                            @foreach($incomeCategories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Pengeluaran">
                            @foreach($expenseCategories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                    @error('category_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Jumlah</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                            <input type="number" name="amount" value="{{ old('amount') }}" required min="0" step="0.01" class="form-input w-full pl-10" placeholder="0">
                        </div>
                        @error('amount') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="payment_method" required class="form-select">
                            <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Kas</option>
                            <option value="bank" {{ old('payment_method') === 'bank' ? 'selected' : '' }}>Bank</option>
                            <option value="qris" {{ old('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                            <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                        @error('payment_method') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required class="form-input w-full">
                        @error('transaction_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Nota</label>
                        <input type="text" name="nota_number" value="{{ old('nota_number') }}" class="form-input w-full" placeholder="INV-001 / QRIS-xxx">
                        <p class="form-hint">Opsional</p>
                        @error('nota_number') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea name="description" rows="3" class="form-textarea w-full" placeholder="Deskripsi transaksi...">{{ old('description') }}</textarea>
                    <p class="form-hint">Opsional</p>
                    @error('description') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Lampiran</label>
                    <input type="file" name="attachments[]" multiple accept="image/*" class="form-input w-full text-sm">
                    <p class="form-hint">Upload foto struk atau nota. Maks 5MB per file.</p>
                    @error('attachments.*') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="btn-primary">Simpan</button>
                    <a href="{{ route('transactions.index') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
