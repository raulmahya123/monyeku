@extends('layouts.main')

@section('title', 'Rekonsiliasi Bank Baru')

@section('subtitle')
    Masukkan saldo awal dan akhir dari mutasi bank Anda. Sistem akan menghitung selisih dengan catatan internal.
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('bank-reconciliations.store') }}">
                @csrf

                <div class="form-group">
                    <label for="bank_account_id" class="form-label">Rekening Bank</label>
                    <select id="bank_account_id" name="bank_account_id" class="form-input" required>
                        <option value="">Pilih Rekening</option>
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ old('bank_account_id') == $acc->id ? 'selected' : '' }}>
                            {{ $acc->bank_name }} - {{ $acc->account_number }} ({{ $acc->account_name }})
                        </option>
                        @endforeach
                    </select>
                    @error('bank_account_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="period" class="form-label">Periode (Bulan-Tahun)</label>
                        <input type="month" id="period" name="period" class="form-input" value="{{ old('period', date('Y-m')) }}" required>
                        @error('period') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="statement_date" class="form-label">Tanggal Mutasi</label>
                        <input type="date" id="statement_date" name="statement_date" class="form-input" value="{{ old('statement_date', date('Y-m-d')) }}" required>
                        @error('statement_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="opening_balance" class="form-label">Saldo Awal (Bank)</label>
                        <input type="number" id="opening_balance" name="opening_balance" class="form-input" value="{{ old('opening_balance', 0) }}" step="0.01" required>
                        @error('opening_balance') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="closing_balance" class="form-label">Saldo Akhir (Bank)</label>
                        <input type="number" id="closing_balance" name="closing_balance" class="form-input" value="{{ old('closing_balance', 0) }}" step="0.01" required>
                        @error('closing_balance') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="statement_lines" class="form-label">Mutasi Bank (JSON, opsional)</label>
                    <textarea id="statement_lines" name="statement_lines" class="form-input" rows="5" placeholder='[{"date":"2026-01-15","description":"Setoran Tunai","amount":500000},{"date":"2026-01-20","description":"Transfer Pembayaran","amount":-250000}]'>{{ old('statement_lines') }}</textarea>
                    @error('statement_lines') <p class="form-error">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">Format JSON array dengan field: date, description, amount (positif = masuk, negatif = keluar)</p>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn-primary">Buat Rekonsiliasi</button>
                    <a href="{{ route('bank-reconciliations.index') }}" class="btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('bank_account_id')?.addEventListener('change', function() {
    fetch('/bank-accounts/' + this.value + '/balance')
        .then(r => r.json())
        .then(d => { if (d.balance !== undefined) document.getElementById('opening_balance').value = d.balance; })
        .catch(() => {});
});
</script>
@endpush
