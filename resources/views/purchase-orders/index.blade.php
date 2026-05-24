@extends('layouts.main')

@section('title', 'Purchase Order')

@section('subtitle')
    Kelola pesanan pembelian ke supplier.
@endsection

@section('actions')
        <button class="btn-primary btn-sm" disabled>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah PO
    </button>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($orders->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. PO</th>
                        <th>Supplier</th>
                        <th>Tgl. Pesan</th>
                        <th class="text-right">Total</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $po)
                    <tr>
                        <td class="text-sm font-mono text-gray-600">{{ $po->order_number }}</td>
                        <td class="text-sm text-gray-800">{{ $po->supplier?->name }}</td>
                        <td class="text-sm text-gray-600">{{ $po->order_date->format('d/m/Y') }}</td>
                        <td class="text-right text-sm font-semibold text-gray-800">Rp {{ number_format($po->total, 0, ',', '.') }}</td>
                        <td>
                            @if($po->status === 'approved')
                            <span class="badge badge-success">Disetujui</span>
                            @elseif($po->status === 'received')
                            <span class="badge badge-info">Diterima</span>
                            @elseif($po->status === 'cancelled')
                            <span class="badge badge-danger">Dibatalkan</span>
                            @else
                            <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('purchase-orders.show', $po) }}" class="btn-ghost btn-sm">Detail</a>
                            <a href="{{ route('purchase-orders.pdf', $po) }}" class="btn-ghost btn-sm" target="_blank">PDF</a>
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
            <div class="empty-state-title">Belum Ada Purchase Order</div>
            <div class="empty-state-desc">Buat pesanan pembelian untuk memulai pengadaan.</div>
            <span class="btn-primary mt-4 opacity-50 cursor-not-allowed">Tambah PO</span>
        </div>
        @endif
    </div>
</div>
@endsection
