@extends('layouts.main')

@section('title', 'DO ' . $deliveryOrder->delivery_number)

@section('content')
<div class="flex items-center gap-3 mb-5">
    <a href="{{ route('delivery-orders.index') }}" class="btn-icon-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 class="text-lg font-bold text-gray-800">DO {{ $deliveryOrder->delivery_number }}</h2>
        <p class="text-sm text-gray-400">Detail delivery order</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase mb-3">Informasi</h3>
                <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                    <div class="flex justify-between"><span class="text-sm text-gray-500">No. SO</span><span class="text-sm font-medium">{{ $deliveryOrder->salesOrder?->order_number }}</span></div>
                    <div class="flex justify-between"><span class="text-sm text-gray-500">Pelanggan</span><span class="text-sm font-medium">{{ $deliveryOrder->customer?->name ?? $deliveryOrder->customer_name }}</span></div>
                    <div class="flex justify-between"><span class="text-sm text-gray-500">Tgl. Kirim</span><span class="text-sm font-medium">{{ $deliveryOrder->delivery_date->format('d/m/Y') }}</span></div>
                </div>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase mb-3">Status</h3>
                <div class="bg-gray-50 rounded-xl p-4">
                    @if($deliveryOrder->status === 'delivered')
                    <span class="badge badge-success">Terkirim</span>
                    @elseif($deliveryOrder->status === 'cancelled')
                    <span class="badge badge-danger">Dibatalkan</span>
                    @else
                    <span class="badge badge-warning">Pending</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-6">
            <h3 class="text-xs font-semibold text-gray-400 uppercase mb-3">Item</h3>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-center">Qty Kirim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveryOrder->items as $item)
                        <tr>
                            <td>{{ $item->product?->name ?? $item->product_name }}</td>
                            <td class="text-center font-medium">{{ $item->quantity }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-gray-400 py-8">Tidak ada item</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="flex gap-3 mt-6">
    <a href="{{ route('sales-returns.create', ['delivery_order_id' => $deliveryOrder->id]) }}" class="btn-ghost">Retur</a>
    <a href="{{ route('delivery-orders.index') }}" class="btn-ghost">Kembali</a>
</div>
@endsection
