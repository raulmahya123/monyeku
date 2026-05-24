@extends('layouts.main')

@section('title', 'Purchase Request')

@section('subtitle')
    Kelola permintaan pembelian barang dan jasa.
@endsection

@section('actions')
    <a href="{{ route('purchase-requests.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah PR
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($purchaseRequests->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. PR</th>
                        <th>Supplier</th>
                        <th>Tgl. Diminta</th>
                        <th>Tgl. Harapan</th>
                        <th>Status</th>
                        <th>Dibuat Oleh</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseRequests as $pr)
                    <tr>
                        <td class="text-sm font-mono text-gray-600">{{ $pr->request_number }}</td>
                        <td class="text-sm text-gray-800">{{ $pr->supplier?->name }}</td>
                        <td class="text-sm text-gray-600">{{ $pr->request_date->format('d/m/Y') }}</td>
                        <td class="text-sm text-gray-600">{{ $pr->expected_date?->format('d/m/Y') }}</td>
                        <td>
                            @if($pr->status === 'approved')
                            <span class="badge badge-success">Disetujui</span>
                            @elseif($pr->status === 'rejected')
                            <span class="badge badge-danger">Ditolak</span>
                            @else
                            <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                        <td class="text-sm text-gray-600">{{ $pr->createdBy?->name }}</td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('purchase-requests.edit', $pr) }}" class="btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('purchase-requests.destroy', $pr) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus PR', 'Yakin ingin menghapus purchase request ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
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
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Purchase Request</div>
            <div class="empty-state-desc">Buat permintaan pembelian untuk memulai proses pengadaan.</div>
            <a href="{{ route('purchase-requests.create') }}" class="btn-primary mt-4">Tambah PR</a>
        </div>
        @endif
    </div>
</div>
@endsection
