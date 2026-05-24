@extends('layouts.main')

@section('title', 'Mata Uang')

@section('subtitle')
    Kelola mata uang yang digunakan dalam transaksi.
@endsection

@section('actions')
    <a href="{{ route('currencies.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Mata Uang
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($currencies->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Simbol</th>
                        <th class="text-right">Kurs</th>
                        <th>Dasar</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($currencies as $currency)
                    <tr>
                        <td class="text-sm text-gray-600">{{ $currency->code }}</td>
                        <td class="text-sm font-medium text-gray-800">{{ $currency->name }}</td>
                        <td class="text-sm text-gray-600">{{ $currency->symbol }}</td>
                        <td class="text-right text-sm font-semibold text-gray-800">{{ number_format($currency->exchange_rate, 4, ',', '.') }}</td>
                        <td>
                            @if($currency->is_base)
                                <span class="badge badge-success">Ya</span>
                            @else
                                <span class="badge badge-default">Tidak</span>
                            @endif
                        </td>
                        <td>
                            @if($currency->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('currencies.edit', $currency) }}" class="btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('currencies.destroy', $currency) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus Mata Uang', 'Yakin ingin menghapus mata uang ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
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
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Mata Uang</div>
            <div class="empty-state-desc">Tambahkan mata uang yang digunakan dalam transaksi.</div>
            <a href="{{ route('currencies.create') }}" class="btn-primary mt-4">Tambah Mata Uang</a>
        </div>
        @endif
    </div>
</div>
@endsection
