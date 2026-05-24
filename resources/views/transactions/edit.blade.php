@extends('layouts.main')

@section('title', 'Edit Transaksi')

@section('content')
    <div class="page-header">
        <a href="{{ route('transactions.index') }}" class="btn-ghost btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="page-title">Edit Transaksi</h1>
    </div>

    <div class="card max-w-2xl">
        <div class="card-body">
            <form method="POST" action="{{ route('transactions.update', $transaction) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Tipe Transaksi</label>
                    <div>
                        <span class="{{ $transaction->type === 'income' ? 'badge-income' : 'badge-expense' }} text-sm px-4 py-2">
                            {{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                        </span>
                        <p class="form-hint mt-1">Tipe transaksi tidak dapat diubah</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" required class="form-select">
                        <option value="">Pilih kategori</option>
                        <optgroup label="Pemasukan">
                            @foreach($incomeCategories as $cat)
                            <option value="{{ $cat->id }}" {{ $transaction->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Pengeluaran">
                            @foreach($expenseCategories as $cat)
                            <option value="{{ $cat->id }}" {{ $transaction->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                            <input type="number" name="amount" value="{{ old('amount', $transaction->amount) }}" required min="0" step="0.01" class="form-input w-full pl-10" placeholder="0">
                        </div>
                        @error('amount') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="payment_method" required class="form-select">
                            <option value="cash" {{ old('payment_method', $transaction->payment_method) === 'cash' ? 'selected' : '' }}>Kas</option>
                            <option value="bank" {{ old('payment_method', $transaction->payment_method) === 'bank' ? 'selected' : '' }}>Bank</option>
                            <option value="qris" {{ old('payment_method', $transaction->payment_method) === 'qris' ? 'selected' : '' }}>QRIS</option>
                            <option value="transfer" {{ old('payment_method', $transaction->payment_method) === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                        @error('payment_method') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" required class="form-input w-full">
                        @error('transaction_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Nota</label>
                        <input type="text" name="nota_number" value="{{ old('nota_number', $transaction->nota_number) }}" class="form-input w-full" placeholder="INV-001 / QRIS-xxx">
                        <p class="form-hint">Opsional</p>
                        @error('nota_number') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea name="description" rows="3" class="form-textarea w-full" placeholder="Deskripsi transaksi...">{{ old('description', $transaction->description) }}</textarea>
                    @error('description') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                @if($transaction->attachments->count() > 0)
                <div class="form-group">
                    <label class="form-label">Lampiran Saat Ini</label>
                    <div class="grid grid-cols-4 sm:grid-cols-5 gap-2">
                        @foreach($transaction->attachments as $att)
                        <a href="{{ Storage::url($att->file_path) }}" target="_blank" class="group relative block aspect-square rounded-lg overflow-hidden bg-gray-100 border border-gray-200 hover:border-orange-300 transition-colors">
                            <img src="{{ Storage::url($att->file_path) }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <label class="form-label">Tambah Lampiran</label>
                    <input type="file" name="attachments[]" multiple accept="image/*" class="form-input w-full">
                    <p class="form-hint">Upload foto struk atau nota. Maks 5MB per file. Format: JPG/PNG</p>
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
