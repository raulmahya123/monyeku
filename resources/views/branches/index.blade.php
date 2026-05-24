@extends('layouts.main')

@section('title', 'Cabang')

@section('subtitle')
    Kelola data cabang perusahaan.
@endsection

@section('actions')
    <a href="{{ route('branches.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Cabang
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($branches->count() > 0)
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
                    @foreach($branches as $branch)
                    <tr>
                        <td class="text-sm text-gray-600">{{ $branch->code }}</td>
                        <td class="text-sm font-medium text-gray-800">{{ $branch->name }}</td>
                        <td class="text-sm text-gray-600 max-w-xs truncate">{{ $branch->address }}</td>
                        <td class="text-sm text-gray-600">{{ $branch->phone }}</td>
                        <td>
                            @if($branch->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('branches.edit', $branch) }}" class="btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('branches.destroy', $branch) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus Cabang', 'Yakin ingin menghapus cabang ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
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
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Cabang</div>
            <div class="empty-state-desc">Tambahkan cabang perusahaan.</div>
            <a href="{{ route('branches.create') }}" class="btn-primary mt-4">Tambah Cabang</a>
        </div>
        @endif
    </div>
</div>
@endsection
