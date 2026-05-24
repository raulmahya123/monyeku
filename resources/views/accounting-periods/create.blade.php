@extends('layouts.main')

@section('title', 'Periode Baru')

@section('subtitle')
    Buat periode akuntansi baru. Pastikan tanggal tidak tumpang tindih dengan periode yang sudah ada.
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('accounting-periods.store') }}">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Nama Periode</label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" placeholder="Contoh: Januari 2026" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" id="start_date" name="start_date" class="form-input" value="{{ old('start_date') }}" required>
                        @error('start_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" id="end_date" name="end_date" class="form-input" value="{{ old('end_date') }}" required>
                        @error('end_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn-primary">Simpan</button>
                    <a href="{{ route('accounting-periods.index') }}" class="btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
