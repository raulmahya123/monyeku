@extends('layouts.main')

@section('title', 'Edit Akun')

@section('content')
<div class="page-header">
    <a href="{{ route('coa.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">Edit Akun</h1>
</div>

<div class="card max-w-2xl">
    <div class="card-body">
        <form method="POST" action="{{ route('coa.update', $coa) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Kode Akun</label>
                <input type="text" name="code" value="{{ old('code', $coa->code) }}" required class="form-input" placeholder="Mis: 1.1.1">
                @error('code') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nama Akun</label>
                <input type="text" name="name" value="{{ old('name', $coa->name) }}" required class="form-input" placeholder="Nama akun">
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tipe</label>
                <select name="type" required class="form-select">
                    <option value="">Pilih tipe</option>
                    <option value="asset" {{ old('type', $coa->type) === 'asset' ? 'selected' : '' }}>Aset</option>
                    <option value="liability" {{ old('type', $coa->type) === 'liability' ? 'selected' : '' }}>Kewajiban</option>
                    <option value="equity" {{ old('type', $coa->type) === 'equity' ? 'selected' : '' }}>Ekuitas</option>
                    <option value="income" {{ old('type', $coa->type) === 'income' ? 'selected' : '' }}>Pendapatan</option>
                    <option value="expense" {{ old('type', $coa->type) === 'expense' ? 'selected' : '' }}>Beban</option>
                </select>
                @error('type') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Saldo Normal</label>
                <select name="normal_balance" required class="form-select">
                    <option value="">Pilih saldo normal</option>
                    <option value="debit" {{ old('normal_balance', $coa->normal_balance) === 'debit' ? 'selected' : '' }}>Debit</option>
                    <option value="credit" {{ old('normal_balance', $coa->normal_balance) === 'credit' ? 'selected' : '' }}>Kredit</option>
                </select>
                @error('normal_balance') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Induk Akun <span class="text-gray-400 font-normal">(opsional)</span></label>
                <select name="parent_id" class="form-select">
                    <option value="">Tidak ada induk</option>
                    @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ old('parent_id', $coa->parent_id) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
                @error('parent_id') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi <span class="text-gray-400 font-normal">(opsional)</span></label>
                <textarea name="description" rows="3" class="form-textarea" placeholder="Deskripsi akun">{{ old('description', $coa->description) }}</textarea>
                @error('description') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('coa.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
