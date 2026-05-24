@extends('layouts.main')

@section('title', 'Penerimaan Barang')

@section('subtitle')
    Kelola penerimaan barang dari supplier.
@endsection

@section('actions')
    <a href="{{ route('goods-receives.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Terima Barang
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($goodsReceives->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Terima</th>
                        <th>No. PO</th>
                        <th>Gudang</th>
                        <th>Tgl. Terima</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($goodsReceives as $gr)
                    <tr>
                        <td class="text-sm font-mono text-gray-600">{{ $gr->receive_number }}</td>
                        <td class="text-sm text-gray-800">{{ $gr->purchaseOrder?->order_number }}</td>
                        <td class="text-sm text-gray-600">{{ $gr->warehouse?->name }}</td>
                        <td class="text-sm text-gray-600">{{ $gr->received_date->format('d/m/Y') }}</td>
                        <td>
                            @if($gr->status === 'completed')
                            <span class="badge badge-success">Selesai</span>
                            @elseif($gr->status === 'partial')
                            <span class="badge badge-warning">Sebagian</span>
                            @else
                            <span class="badge badge-info">Draft</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('goods-receives.show', $gr) }}" class="btn-ghost btn-sm">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Penerimaan</div>
            <div class="empty-state-desc">Catat penerimaan barang dari PO yang sudah dikirim.</div>
            <a href="{{ route('goods-receives.create') }}" class="btn-primary mt-4">Terima Barang</a>
        </div>
        @endif
    </div>
</div>
@endsection
