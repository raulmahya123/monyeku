@extends('layouts.main')

@section('title', 'Catat Hutang/Piutang Baru')

@section('content')
<div class="flex items-center gap-3 mb-5">
    <a href="{{ route('debts.index') }}" class="btn-icon-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 class="text-lg font-bold text-gray-800">Catat Hutang/Piutang Baru</h2>
        <p class="text-sm text-gray-400">Lacak transaksi hutang atau piutang</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('debts.store') }}">
            @csrf

            <div class="form-group mb-6">
                <label class="form-label">Tipe</label>
                <div class="grid grid-cols-2 gap-3 mt-1">
                    <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50/50 transition-all hover:border-gray-300">
                        <input type="radio" name="type" value="receivable" {{ old('type') === 'receivable' ? 'checked' : '' }} class="sr-only" required>
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-gray-800">Piutang</span>
                            <p class="text-xs text-gray-400">Orang berhutang ke Anda</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer has-[:checked]:border-red-400 has-[:checked]:bg-red-50/50 transition-all hover:border-gray-300">
                        <input type="radio" name="type" value="payable" {{ old('type') === 'payable' ? 'checked' : '' }} class="sr-only" required>
                        <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-gray-800">Hutang</span>
                            <p class="text-xs text-gray-400">Anda berhutang ke orang</p>
                        </div>
                    </label>
                </div>
                @error('type') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div class="form-group">
                    <label class="form-label">Nama Kontak</label>
                    <input type="text" name="contact_name" value="{{ old('contact_name') }}" required class="form-input" placeholder="Nama orang/perusahaan">
                    @error('contact_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="amount" value="{{ old('amount') }}" required min="0" class="form-input" placeholder="Rp 0">
                    @error('amount') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="2" class="form-input" placeholder="Keterangan...">{{ old('description') }}</textarea>
                    @error('description') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Jatuh Tempo</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}" required class="form-input">
                    @error('due_date') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-3 pt-6 border-t border-gray-100">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('debts.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
