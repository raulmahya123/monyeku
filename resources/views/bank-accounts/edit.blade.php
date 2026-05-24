@extends('layouts.main')

@section('title', 'Edit Rekening Bank')

@section('subtitle')
    Ubah informasi rekening bank perusahaan.
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('bank-accounts.update', $bankAccount) }}">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="bank_name" class="form-label">Nama Bank</label>
                        <input type="text" id="bank_name" name="bank_name" class="form-input" value="{{ old('bank_name', $bankAccount->bank_name) }}" required>
                        @error('bank_name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="account_number" class="form-label">No. Rekening</label>
                        <input type="text" id="account_number" name="account_number" class="form-input" value="{{ old('account_number', $bankAccount->account_number) }}" required>
                        @error('account_number') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="account_name" class="form-label">Atas Nama</label>
                    <input type="text" id="account_name" name="account_name" class="form-input" value="{{ old('account_name', $bankAccount->account_name) }}" required>
                    @error('account_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="currency" class="form-label">Mata Uang</label>
                        <select id="currency" name="currency" class="form-input">
                            <option value="IDR" {{ (old('currency', $bankAccount->currency) === 'IDR') ? 'selected' : '' }}>IDR - Rupiah</option>
                            <option value="USD" {{ (old('currency', $bankAccount->currency) === 'USD') ? 'selected' : '' }}>USD - Dolar AS</option>
                            <option value="SGD" {{ (old('currency', $bankAccount->currency) === 'SGD') ? 'selected' : '' }}>SGD - Dolar Singapura</option>
                        </select>
                        @error('currency') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="opening_balance" class="form-label">Saldo Awal</label>
                        <input type="number" id="opening_balance" name="opening_balance" class="form-input" value="{{ old('opening_balance', $bankAccount->opening_balance) }}" step="0.01">
                        @error('opening_balance') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">Catatan</label>
                    <textarea id="notes" name="notes" class="form-input" rows="2">{{ old('notes', $bankAccount->notes) }}</textarea>
                    @error('notes') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-2 mt-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $bankAccount->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                    <label for="is_active" class="text-sm text-gray-700">Aktif</label>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn-primary">Simpan</button>
                    <a href="{{ route('bank-accounts.index') }}" class="btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
