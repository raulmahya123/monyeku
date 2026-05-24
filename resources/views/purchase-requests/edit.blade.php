@extends('layouts.main')

@section('title', 'Edit Purchase Request')

@section('subtitle')
    Ubah data purchase request.
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('purchase-requests.update', $purchaseRequest) }}">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">No. PR</label>
                        <input type="text" name="request_number" class="form-input" value="{{ old('request_number', $purchaseRequest->request_number) }}" required>
                        @error('request_number') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-input" required>
                            <option value="">Pilih supplier</option>
                            @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ old('supplier_id', $purchaseRequest->supplier_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Tanggal Diminta</label>
                        <input type="date" name="request_date" class="form-input" value="{{ old('request_date', $purchaseRequest->request_date?->format('Y-m-d')) }}" required>
                        @error('request_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Harapan</label>
                        <input type="date" name="expected_date" class="form-input" value="{{ old('expected_date', $purchaseRequest->expected_date?->format('Y-m-d')) }}">
                        @error('expected_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-input" rows="2">{{ old('notes', $purchaseRequest->notes) }}</textarea>
                    @error('notes') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn-primary">Simpan</button>
                    <a href="{{ route('purchase-requests.index') }}" class="btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
