@extends('layouts.main')

@section('title', 'Supplier')

@section('subtitle')
    Kelola data supplier untuk transaksi pembelian.
@endsection

@section('actions')
    <a href="{{ route('suppliers.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Supplier
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($suppliers->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $supplier)
                    <tr>
                        <td class="text-sm text-gray-600">{{ $supplier->code }}</td>
                        <td class="text-sm font-medium text-gray-800">{{ $supplier->name }}</td>
                        <td class="text-sm text-gray-600">{{ $supplier->contact_person }}</td>
                        <td class="text-sm text-gray-600">{{ $supplier->phone }}</td>
                        <td>
                            @if($supplier->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus Supplier', 'Yakin ingin menghapus supplier ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
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
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Supplier</div>
            <div class="empty-state-desc">Tambahkan supplier untuk memulai transaksi pembelian.</div>
            <a href="{{ route('suppliers.create') }}" class="btn-primary mt-4">Tambah Supplier</a>
        </div>
        @endif
    </div>
</div>
@endsection
