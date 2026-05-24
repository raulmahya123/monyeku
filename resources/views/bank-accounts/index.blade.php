@extends('layouts.main')

@section('title', 'Rekening Bank')

@section('subtitle')
    Kelola rekening bank perusahaan untuk rekonsiliasi dan pencatatan transaksi.
@endsection

@section('actions')
    <a href="{{ route('bank-accounts.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Rekening Baru
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($accounts->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Bank</th>
                        <th>No. Rekening</th>
                        <th>Atas Nama</th>
                        <th>Mata Uang</th>
                        <th>Saldo Awal</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $acc)
                    <tr>
                        <td class="text-sm font-medium text-gray-800">{{ $acc->bank_name }}</td>
                        <td class="text-sm text-gray-600">{{ $acc->account_number }}</td>
                        <td class="text-sm text-gray-600">{{ $acc->account_name }}</td>
                        <td class="text-sm text-gray-600">{{ $acc->currency }}</td>
                        <td class="text-sm font-semibold text-gray-800">Rp {{ number_format($acc->opening_balance, 0, ',', '.') }}</td>
                        <td>
                            @if($acc->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('bank-accounts.edit', $acc) }}" class="btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('bank-accounts.destroy', $acc) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus Rekening', 'Yakin ingin menghapus rekening bank ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 3h6m-3-3h3"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Rekening Bank</div>
            <div class="empty-state-desc">Tambahkan rekening bank untuk memulai rekonsiliasi.</div>
            <a href="{{ route('bank-accounts.create') }}" class="btn-primary mt-4">Tambah Rekening</a>
        </div>
        @endif
    </div>
</div>
@endsection
