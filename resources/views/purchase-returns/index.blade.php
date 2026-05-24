@extends('layouts.main')

@section('title', 'Retur Pembelian')

@section('subtitle')
    Kelola retur pembelian ke supplier.
@endsection

@section('actions')
    <a href="{{ route('purchase-returns.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Retur
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($purchaseReturns->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Retur</th>
                        <th>No. PO</th>
                        <th>Supplier</th>
                        <th>Tgl. Retur</th>
                        <th class="text-right">Total</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseReturns as $retur)
                    <tr>
                        <td class="text-sm font-mono text-gray-600">{{ $retur->return_number }}</td>
                        <td class="text-sm text-gray-800">{{ $retur->purchaseOrder?->order_number }}</td>
                        <td class="text-sm text-gray-600">{{ $retur->supplier?->name }}</td>
                        <td class="text-sm text-gray-600">{{ $retur->return_date->format('d/m/Y') }}</td>
                        <td class="text-right text-sm font-semibold text-gray-800">Rp {{ number_format($retur->total, 0, ',', '.') }}</td>
                        <td>
                            @if($retur->status === 'approved')
                            <span class="badge badge-success">Disetujui</span>
                            @elseif($retur->status === 'rejected')
                            <span class="badge badge-danger">Ditolak</span>
                            @else
                            <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('purchase-returns.show', $retur) }}" class="btn-ghost btn-sm">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Retur Pembelian</div>
            <div class="empty-state-desc">Catat retur barang yang dikembalikan ke supplier.</div>
            <a href="{{ route('purchase-returns.create') }}" class="btn-primary mt-4">Tambah Retur</a>
        </div>
        @endif
    </div>
</div>
@endsection
