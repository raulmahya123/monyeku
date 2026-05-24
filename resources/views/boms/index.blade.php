@extends('layouts.main')

@section('title', 'Bill of Materials')

@section('subtitle')
    Kelola bill of materials untuk produksi.
@endsection

@section('actions')
    <a href="{{ route('boms.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah BOM
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($boms->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Produk</th>
                        <th class="text-center">Qty</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($boms as $bom)
                    <tr>
                        <td class="text-sm font-mono text-gray-600">{{ $bom->code }}</td>
                        <td class="text-sm font-medium text-gray-800">{{ $bom->name }}</td>
                        <td class="text-sm text-gray-800">{{ $bom->product?->name }}</td>
                        <td class="text-center text-sm text-gray-600">{{ $bom->quantity }}</td>
                        <td>
                            @if($bom->is_active)
                            <span class="badge badge-success">Aktif</span>
                            @else
                            <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('boms.edit', $bom) }}" class="btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('boms.destroy', $bom) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus BOM', 'Yakin ingin menghapus BOM ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
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
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada BOM</div>
            <div class="empty-state-desc">Buat bill of materials untuk produksi.</div>
            <a href="{{ route('boms.create') }}" class="btn-primary mt-4">Tambah BOM</a>
        </div>
        @endif
    </div>
</div>
@endsection
