@extends('layouts.main')

@section('title', 'Work Order')

@section('subtitle')
    Kelola work order untuk produksi.
@endsection

@section('actions')
    <a href="{{ route('work-orders.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah WO
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($workOrders->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. WO</th>
                        <th>Produk</th>
                        <th class="text-center">Qty</th>
                        <th>Tgl. Mulai</th>
                        <th class="text-center">Produksi</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workOrders as $wo)
                    <tr>
                        <td class="text-sm font-mono text-gray-600">{{ $wo->order_number }}</td>
                        <td class="text-sm font-medium text-gray-800">{{ $wo->product?->name }}</td>
                        <td class="text-center text-sm text-gray-600">{{ $wo->quantity }}</td>
                        <td class="text-sm text-gray-600">{{ $wo->start_date->format('d/m/Y') }}</td>
                        <td class="text-center text-sm font-semibold text-gray-800">{{ $wo->produced_qty ?? 0 }}</td>
                        <td>
                            @if($wo->status === 'completed')
                            <span class="badge badge-success">Selesai</span>
                            @elseif($wo->status === 'in_progress')
                            <span class="badge badge-warning">Proses</span>
                            @elseif($wo->status === 'cancelled')
                            <span class="badge badge-danger">Dibatalkan</span>
                            @else
                            <span class="badge badge-info">Draft</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('work-orders.show', $wo) }}" class="btn-ghost btn-sm">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17l-5.646 5.646a1.5 1.5 0 01-2.122 0l-.47-.47a1.5 1.5 0 010-2.122l5.646-5.646m4.302 4.303l5.646-5.646a1.5 1.5 0 000-2.122l-.47-.47a1.5 1.5 0 00-2.122 0l-5.646 5.646m-2.122-2.122l5.646-5.646a1.5 1.5 0 000-2.122l-.47-.47a1.5 1.5 0 00-2.122 0L9.3 11.03"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Work Order</div>
            <div class="empty-state-desc">Buat work order untuk memulai produksi.</div>
            <a href="{{ route('work-orders.create') }}" class="btn-primary mt-4">Tambah WO</a>
        </div>
        @endif
    </div>
</div>
@endsection
