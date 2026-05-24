@extends('layouts.main')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="card mb-6">
    <div class="card-header">
        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
        </div>
        <h3 class="card-title">Filter Penjualan</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.sales') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Pelanggan</label>
                <select name="customer_id" class="form-input">
                    <option value="">Semua</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Status</label>
                <select name="status" class="form-input">
                    <option value="">Semua</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Dikirim</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary btn-sm">Tampilkan</button>
                <a href="{{ route('reports.sales') }}" class="btn-ghost btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card mb-6">
    <div class="card-body">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Nilai Penjualan</p>
                <p class="text-xl font-bold text-emerald-600">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
                <span class="text-xs text-gray-400">{{ $orders->total() }} transaksi</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
        </div>
        <h3 class="card-title">Daftar Sales Order</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. SO</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th class="text-right">Item</th>
                        <th class="text-right">Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $o)
                    <tr>
                        <td class="text-sm font-medium text-gray-800">{{ $o->order_number }}</td>
                        <td class="text-sm text-gray-600">{{ $o->customer?->name ?? '-' }}</td>
                        <td class="text-sm text-gray-600">{{ $o->order_date->format('d/m/Y') }}</td>
                        <td class="text-right text-sm text-gray-600">{{ $o->items->count() }}</td>
                        <td class="text-right text-sm font-semibold text-gray-800">Rp {{ number_format($o->items->sum(fn($i) => $i->quantity * $i->price), 0, ',', '.') }}</td>
                        <td>
                            @if($o->status === 'draft')
                            <span class="badge badge-warning">Draft</span>
                            @elseif($o->status === 'approved')
                            <span class="badge badge-success">Disetujui</span>
                            @elseif($o->status === 'delivered')
                            <span class="badge badge-info">Dikirim</span>
                            @elseif($o->status === 'cancelled')
                            <span class="badge badge-danger">Dibatalkan</span>
                            @else
                            <span class="badge badge-warning">{{ $o->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400">Tidak ada sales order.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
    <div class="card-footer">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
