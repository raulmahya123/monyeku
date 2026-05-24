@extends('layouts.main')

@section('title', 'Aset Tetap')

@section('subtitle')
    Kelola aset tetap perusahaan dan penyusutannya.
@endsection

@section('actions')
    <a href="{{ route('fixed-assets.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Aset
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($assets->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th class="text-right">Harga Beli</th>
                        <th class="text-right">Nilai Buku</th>
                        <th>Metode Depresiasi</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assets as $asset)
                    <tr>
                        <td class="text-sm font-mono text-gray-600">{{ $asset->code }}</td>
                        <td class="text-sm font-medium text-gray-800">{{ $asset->name }}</td>
                        <td class="text-right text-sm text-gray-800">Rp {{ number_format($asset->purchase_price, 0, ',', '.') }}</td>
                        <td class="text-right text-sm font-semibold text-gray-800">Rp {{ number_format($asset->book_value, 0, ',', '.') }}</td>
                        <td class="text-sm text-gray-600">{{ $asset->depreciation_method }}</td>
                        <td>
                            @if($asset->status === 'active')
                            <span class="badge badge-success">Aktif</span>
                            @elseif($asset->status === 'disposed')
                            <span class="badge badge-danger">Dijual</span>
                            @else
                            <span class="badge badge-warning">Draft</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('fixed-assets.edit', $asset) }}" class="btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('fixed-assets.destroy', $asset) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus Aset', 'Yakin ingin menghapus aset ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
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
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Aset Tetap</div>
            <div class="empty-state-desc">Tambahkan aset tetap perusahaan.</div>
            <a href="{{ route('fixed-assets.create') }}" class="btn-primary mt-4">Tambah Aset</a>
        </div>
        @endif
    </div>
</div>
@endsection
