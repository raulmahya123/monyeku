@extends('layouts.main')

@section('title', 'Edit Transaksi Berulang')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('recurring.index') }}" class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 hover:text-orange-500 hover:border-orange-200 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h1 class="text-lg font-semibold text-gray-900">Edit Transaksi Berulang</h1>
        <p class="text-sm text-gray-500">Ubah transaksi otomatis</p>
    </div>
</div>

<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">
        <form method="POST" action="{{ route('recurring.update', $recurring) }}">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all {{ ($recurring->type === 'income' || old('type') === 'income') ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-emerald-300 hover:bg-emerald-50/50' }}">
                        <input type="radio" name="type" value="income" {{ ($recurring->type === 'income' || old('type') === 'income') ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500" required>
                        <div>
                            <span class="text-sm font-medium text-gray-700">Pemasukan</span>
                            <p class="text-xs text-gray-400">Pendapatan berulang</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all {{ ($recurring->type === 'expense' || old('type') === 'expense') ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-red-300 hover:bg-red-50/50' }}">
                        <input type="radio" name="type" value="expense" {{ ($recurring->type === 'expense' || old('type') === 'expense') ? 'checked' : '' }} class="text-red-600 focus:ring-red-500" required>
                        <div>
                            <span class="text-sm font-medium text-gray-700">Pengeluaran</span>
                            <p class="text-xs text-gray-400">Biaya berulang</p>
                        </div>
                    </label>
                </div>
                @error('type') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori</label>
                    <select name="category_id" required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all bg-white">
                        <option value="">Pilih Kategori</option>
                        <optgroup label="Pemasukan">
                            @foreach($categories->where('type', 'income') as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $recurring->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Pengeluaran">
                            @foreach($categories->where('type', 'expense') as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $recurring->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                    @error('category_id') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                        <input type="number" name="amount" value="{{ old('amount', $recurring->amount) }}" required
                            class="w-full px-3.5 py-2.5 pl-10 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all placeholder:text-gray-400"
                            placeholder="0">
                    </div>
                    @error('amount') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="2"
                    class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all placeholder:text-gray-400"
                    placeholder="e.g. Sewa gedung bulanan">{{ old('description', $recurring->description) }}</textarea>
                @error('description') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Frekuensi</label>
                    <select name="frequency" required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all bg-white">
                        <option value="monthly" {{ old('frequency', $recurring->frequency) === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="yearly" {{ old('frequency', $recurring->frequency) === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                    </select>
                    @error('frequency') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Hari Eksekusi</label>
                    <input type="number" name="day_of_month" value="{{ old('day_of_month', $recurring->day_of_month ?? 1) }}" min="1" max="31"
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all placeholder:text-gray-400"
                        placeholder="1-31">
                    @error('day_of_month') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ old('start_date', $recurring->start_date->format('Y-m-d')) }}" required
                    class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all">
                @error('start_date') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('recurring.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
        </div>
    </div>
</div>
@endsection
