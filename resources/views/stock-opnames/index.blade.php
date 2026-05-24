@extends('layouts.main')

@section('title', 'Stock Opname')

@section('subtitle')
    Kelola stock opname (stock taking) untuk mencocokkan stok fisik dengan sistem.
@endsection

@section('actions')
    <a href="{{ route('stock-opnames.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Buat Stock Opname
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($stockOpnames->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Gudang</th>
                        <th>Status</th>
                        <th class="text-center">Items</th>
                        <th>Dibuat Oleh</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockOpnames as $so)
                    <tr>
                        <td class="text-sm text-gray-600">{{ $so->opname_date->format('d/m/Y') }}</td>
                        <td class="text-sm font-medium text-gray-800">{{ $so->warehouse->name }}</td>
                        <td>
                            @if($so->status === 'draft')
                            <span class="badge badge-warning">Draft</span>
                            @else
                            <span class="badge badge-success">Completed</span>
                            @endif
                        </td>
                        <td class="text-sm text-center text-gray-600">{{ $so->items_count }}</td>
                        <td class="text-sm text-gray-600">{{ $so->createdBy->name ?? '-' }}</td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('stock-opnames.show', $so) }}" class="btn-ghost btn-sm">Detail</a>
                                @if($so->status === 'draft')
                                <form action="{{ route('stock-opnames.complete', $so) }}" method="POST">
                                    @csrf
                                    <button type="button" @click="$store.confirm.ask('Selesaikan Stock Opname', 'Yakin ingin menyelesaikan stock opname ini? Stok akan disesuaikan.', { confirmText: 'Ya, selesaikan', confirmClass: 'btn-success', action: () => $el.closest('form').submit() })" class="btn-success btn-sm">Selesaikan</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $stockOpnames->links() }}
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Stock Opname</div>
            <div class="empty-state-desc">Buat stock opname untuk mencocokkan stok fisik dengan sistem.</div>
            <a href="{{ route('stock-opnames.create') }}" class="btn-primary mt-4">Buat Stock Opname</a>
        </div>
        @endif
    </div>
</div>
@endsection
