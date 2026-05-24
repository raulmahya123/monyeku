@extends('layouts.main')

@section('title', 'Produk')

@section('subtitle')
    Kelola data produk, material, dan jasa perusahaan.
@endsection

@section('actions')
    <a href="{{ route('products.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Produk
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($products->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th class="text-right">Stok</th>
                        <th class="text-right">Harga Jual</th>
                        <th>Tipe</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td class="text-sm font-mono text-gray-600">{{ $product->code }}</td>
                        <td class="text-sm font-medium text-gray-800">{{ $product->name }}</td>
                        <td class="text-sm text-gray-600">{{ $product->category?->name }}</td>
                        <td class="text-sm text-gray-600">{{ $product->unit }}</td>
                        <td class="text-right text-sm font-semibold text-gray-800">{{ number_format($product->stock, 0, ',', '.') }}</td>
                        <td class="text-right text-sm font-semibold text-gray-800">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td>
                            @if($product->type === 'product')
                            <span class="badge badge-success">Produk</span>
                            @elseif($product->type === 'material')
                            <span class="badge badge-warning">Material</span>
                            @else
                            <span class="badge badge-info">Jasa</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('products.edit', $product) }}" class="btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus Produk', 'Yakin ingin menghapus produk ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
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
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Produk</div>
            <div class="empty-state-desc">Tambahkan produk, material, atau jasa untuk memulai.</div>
            <a href="{{ route('products.create') }}" class="btn-primary mt-4">Tambah Produk</a>
        </div>
        @endif
    </div>
</div>
@endsection
