@extends('layouts.main')

@section('title', 'SO ' . $salesOrder->order_number)

@section('content')
<div class="flex items-center gap-3 mb-5">
    <a href="{{ route('sales-orders.index') }}" class="btn-icon-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 class="text-lg font-bold text-gray-800">SO {{ $salesOrder->order_number }}</h2>
        <p class="text-sm text-gray-400">Detail sales order</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="flex flex-col sm:flex-row items-start justify-between gap-4 mb-6 pb-6 border-b border-gray-100">
            <div>
                <div class="text-sm text-gray-400 mb-1">Total SO</div>
                <div class="text-3xl font-bold text-gray-800">Rp {{ number_format($salesOrder->total, 0, ',', '.') }}</div>
            </div>
            <div>
                @if($salesOrder->status === 'completed')
                <span class="badge badge-success text-sm px-4 py-1.5">Selesai</span>
                @elseif($salesOrder->status === 'cancelled')
                <span class="badge badge-danger text-sm px-4 py-1.5">Dibatalkan</span>
                @elseif($salesOrder->status === 'delivered')
                <span class="badge badge-info text-sm px-4 py-1.5">Dikirim</span>
                @else
                <span class="badge badge-warning text-sm px-4 py-1.5">Pending</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase mb-3">Pelanggan</h3>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="font-semibold text-gray-800">{{ $salesOrder->customer?->name ?? $salesOrder->customer_name }}</p>
                </div>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase mb-3">Informasi</h3>
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><span class="text-xs text-gray-400">Tgl. Pesan</span><p class="text-sm font-medium mt-0.5">{{ $salesOrder->order_date->format('d/m/Y') }}</p></div>
                    </div>
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
                            <th class="text-center">Qty</th>
                            <th class="text-right">Harga</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesOrder->items as $item)
                        <tr>
                            <td>{{ $item->product?->name ?? $item->product_name }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-right font-medium">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-gray-400 py-8">Tidak ada item</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="flex gap-3 mt-6">
    @if($salesOrder->status !== 'completed' && $salesOrder->status !== 'cancelled')
    <a href="{{ route('delivery-orders.create', ['sales_order_id' => $salesOrder->id]) }}" class="btn-primary">Buat DO</a>
    @endif
    <a href="{{ route('sales-orders.index') }}" class="btn-ghost">Kembali</a>
</div>
@endsection
