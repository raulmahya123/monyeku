@extends('layouts.main')

@section('title', 'Edit Kategori')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('categories.index') }}" class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 hover:text-orange-500 hover:border-orange-200 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h1 class="text-lg font-semibold text-gray-900">Edit Kategori</h1>
        <p class="text-sm text-gray-500">Ubah kategori transaksi</p>
    </div>
</div>

<div class="max-w-lg">
    <div class="card">
        <div class="card-body">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                    class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all placeholder:text-gray-400"
                    placeholder="Nama kategori">
                @error('name') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all {{ ($category->type === 'income' || old('type') === 'income') ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-emerald-300 hover:bg-emerald-50/50' }}">
                        <input type="radio" name="type" value="income" {{ ($category->type === 'income' || old('type') === 'income') ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500" required>
                        <div>
                            <span class="text-sm font-medium text-gray-700">Pemasukan</span>
                            <p class="text-xs text-gray-400">Pendapatan, penjualan, dll</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all {{ ($category->type === 'expense' || old('type') === 'expense') ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-red-300 hover:bg-red-50/50' }}">
                        <input type="radio" name="type" value="expense" {{ ($category->type === 'expense' || old('type') === 'expense') ? 'checked' : '' }} class="text-red-600 focus:ring-red-500" required>
                        <div>
                            <span class="text-sm font-medium text-gray-700">Pengeluaran</span>
                            <p class="text-xs text-gray-400">Biaya, belanja, dll</p>
                        </div>
                    </label>
                </div>
                @error('type') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="2"
                    class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all placeholder:text-gray-400"
                    placeholder="Deskripsi kategori">{{ old('description', $category->description) }}</textarea>
                @error('description') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('categories.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
        </div>
    </div>
</div>
@endsection
