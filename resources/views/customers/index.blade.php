@extends('layouts.main')

@section('title', 'Pelanggan')

@section('subtitle')
    Kelola data pelanggan untuk transaksi penjualan.
@endsection

@section('actions')
    <a href="{{ route('customers.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Pelanggan
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($customers->count() > 0)
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
                    @foreach($customers as $customer)
                    <tr>
                        <td class="text-sm text-gray-600">{{ $customer->code }}</td>
                        <td class="text-sm font-medium text-gray-800">{{ $customer->name }}</td>
                        <td class="text-sm text-gray-600">{{ $customer->contact_person }}</td>
                        <td class="text-sm text-gray-600">{{ $customer->phone }}</td>
                        <td>
                            @if($customer->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('customers.edit', $customer) }}" class="btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus Pelanggan', 'Yakin ingin menghapus pelanggan ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
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
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Pelanggan</div>
            <div class="empty-state-desc">Tambahkan pelanggan untuk memulai transaksi penjualan.</div>
            <a href="{{ route('customers.create') }}" class="btn-primary mt-4">Tambah Pelanggan</a>
        </div>
        @endif
    </div>
</div>
@endsection
