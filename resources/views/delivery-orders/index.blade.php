@extends('layouts.main')

@section('title', 'Delivery Order')

@section('subtitle')
    Kelola pengiriman barang ke pelanggan.
@endsection

@section('actions')
    <a href="{{ route('delivery-orders.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah DO
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($deliveryOrders->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. DO</th>
                        <th>No. SO</th>
                        <th>Pelanggan</th>
                        <th>Tgl. Kirim</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveryOrders as $do)
                    <tr>
                        <td class="text-sm font-mono text-gray-600">{{ $do->delivery_number }}</td>
                        <td class="text-sm text-gray-800">{{ $do->salesOrder?->order_number }}</td>
                        <td class="text-sm text-gray-600">{{ $do->customer?->name ?? $do->customer_name }}</td>
                        <td class="text-sm text-gray-600">{{ $do->delivery_date->format('d/m/Y') }}</td>
                        <td>
                            @if($do->status === 'delivered')
                            <span class="badge badge-success">Terkirim</span>
                            @elseif($do->status === 'cancelled')
                            <span class="badge badge-danger">Dibatalkan</span>
                            @else
                            <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('delivery-orders.show', $do) }}" class="btn-ghost btn-sm">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Delivery Order</div>
            <div class="empty-state-desc">Buat pengiriman barang untuk sales order.</div>
            <a href="{{ route('delivery-orders.create') }}" class="btn-primary mt-4">Tambah DO</a>
        </div>
        @endif
    </div>
</div>
@endsection
