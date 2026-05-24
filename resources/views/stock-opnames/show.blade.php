@extends('layouts.main')

@section('title', 'Detail Stock Opname')

@section('subtitle')
    Detail stock opname dan daftar produk yang diopname.
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-3">
            <div>
                <p class="text-sm text-gray-500">Tanggal Opname</p>
                <p class="text-sm font-medium text-gray-800">{{ $stockOpname->opname_date->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Gudang</p>
                <p class="text-sm font-medium text-gray-800">{{ $stockOpname->warehouse->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <div>
                    @if($stockOpname->status === 'draft')
                    <span class="badge badge-warning">Draft</span>
                    @else
                    <span class="badge badge-success">Completed</span>
                    @endif
                </div>
            </div>
            <div>
                <p class="text-sm text-gray-500">Dibuat Oleh</p>
                <p class="text-sm font-medium text-gray-800">{{ $stockOpname->createdBy->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Dibuat Pada</p>
                <p class="text-sm font-medium text-gray-800">{{ $stockOpname->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        @if($stockOpname->notes)
        <div class="mb-6">
            <p class="text-sm text-gray-500">Catatan</p>
            <p class="text-sm text-gray-800">{{ $stockOpname->notes }}</p>
        </div>
        @endif

        <h3 class="mb-4 text-lg font-bold text-gray-800">Daftar Produk</h3>

        <div class="table-wrap border rounded-lg">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-10">No</th>
                        <th>Produk</th>
                        <th class="text-center">Stok Sistem</th>
                        <th class="text-center">Stok Fisik</th>
                        <th class="text-center">Selisih</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockOpname->items as $index => $item)
                    <tr>
                        <td class="text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td>
                            <div class="text-sm font-medium text-gray-800">{{ $item->product->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400 font-mono">{{ $item->product->code ?? '-' }}</div>
                        </td>
                        <td class="text-sm text-center text-gray-600">{{ number_format($item->system_qty, 2) }}</td>
                        <td class="text-sm text-center text-gray-800 font-medium">{{ number_format($item->physical_qty, 2) }}</td>
                        <td class="text-sm text-center font-medium {{ $item->difference >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $item->difference >= 0 ? '+' : '' }}{{ number_format($item->difference, 2) }}
                        </td>
                        <td class="text-sm text-gray-600">{{ $item->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('stock-opnames.index') }}" class="btn-ghost btn-sm">Kembali</a>
            @if($stockOpname->status === 'draft')
            <form action="{{ route('stock-opnames.complete', $stockOpname) }}" method="POST">
                @csrf
                <button type="button" @click="$store.confirm.ask('Selesaikan Stock Opname', 'Yakin ingin menyelesaikan stock opname ini? Stok akan disesuaikan berdasarkan data fisik yang dimasukkan.', { confirmText: 'Ya, selesaikan', confirmClass: 'btn-success', action: () => $el.closest('form').submit() })" class="btn-success btn-sm">Selesaikan</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
