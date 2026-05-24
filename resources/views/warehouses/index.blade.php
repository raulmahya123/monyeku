@extends('layouts.main')

@section('title', 'Gudang')

@section('subtitle')
    Kelola data gudang dan lokasi penyimpanan.
@endsection

@section('actions')
    <a href="{{ route('warehouses.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Gudang
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($warehouses->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($warehouses as $wh)
                    <tr>
                        <td class="text-sm font-mono text-gray-600">{{ $wh->code }}</td>
                        <td class="text-sm font-medium text-gray-800">{{ $wh->name }}</td>
                        <td class="text-sm text-gray-600 max-w-[200px] truncate">{{ $wh->address }}</td>
                        <td class="text-sm text-gray-600">{{ $wh->phone }}</td>
                        <td>
                            @if($wh->is_active)
                            <span class="badge badge-success">Aktif</span>
                            @else
                            <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('warehouses.edit', $wh) }}" class="btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('warehouses.destroy', $wh) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus Gudang', 'Yakin ingin menghapus gudang ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
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
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Gudang</div>
            <div class="empty-state-desc">Tambahkan gudang untuk mengelola lokasi penyimpanan.</div>
            <a href="{{ route('warehouses.create') }}" class="btn-primary mt-4">Tambah Gudang</a>
        </div>
        @endif
    </div>
</div>
@endsection
