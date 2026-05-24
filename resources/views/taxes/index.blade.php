@extends('layouts.main')

@section('title', 'Pajak')

@section('subtitle')
    Kelola tarif pajak untuk transaksi.
@endsection

@section('actions')
    <a href="{{ route('taxes.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Pajak
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($taxes->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th class="text-right">Tarif</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($taxes as $tax)
                    <tr>
                        <td class="text-sm text-gray-600">{{ $tax->code }}</td>
                        <td class="text-sm font-medium text-gray-800">{{ $tax->name }}</td>
                        <td class="text-right text-sm font-semibold text-gray-800">{{ number_format($tax->rate, 2) }}%</td>
                        <td>
                            <span class="badge {{ $tax->type === 'ppn' ? 'badge-income' : 'badge-expense' }}">
                                {{ strtoupper($tax->type) }}
                            </span>
                        </td>
                        <td>
                            @if($tax->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('taxes.edit', $tax) }}" class="btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('taxes.destroy', $tax) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus Pajak', 'Yakin ingin menghapus pajak ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
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
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Pajak</div>
            <div class="empty-state-desc">Tambahkan tarif pajak untuk transaksi.</div>
            <a href="{{ route('taxes.create') }}" class="btn-primary mt-4">Tambah Pajak</a>
        </div>
        @endif
    </div>
</div>
@endsection
