@extends('layouts.main')

@section('title', 'Transaksi')

@section('actions')
    <a href="{{ route('transactions.create') }}" class="btn-primary btn-sm">+ Transaksi Baru</a>
@endsection

@section('content')
    <div class="card mb-5">
        <div class="card-body">
            <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="form-group">
                    <select name="type" class="form-select text-sm">
                        <option value="">Semua Tipe</option>
                        <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div class="form-group">
                    <select name="category_id" class="form-select text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <select name="status" class="form-select text-sm">
                        <option value="">Semua Status</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="form-group">
                    <div class="flex gap-2">
                        <div class="search-input-wrapper flex-1">
                            <svg class="search-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" value="{{ request('search') }}" class="search-input text-sm" placeholder="Cari transaksi...">
                        </div>
                        <button type="submit" class="btn-primary btn-sm">Filter</button>
                        <a href="{{ route('transactions.index') }}" class="btn-ghost btn-sm">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($transactions->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>Nota</th>
                            <th>Catatan</th>
                            <th>Metode</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $t)
                        <tr>
                            <td class="text-sm text-gray-600">{{ $t->transaction_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="{{ $t->type === 'income' ? 'badge-income' : 'badge-expense' }}">
                                    {{ $t->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                </span>
                            </td>
                            <td class="text-sm text-gray-700">{{ $t->category?->name ?? '-' }}</td>
                            <td class="text-sm font-mono text-gray-500">{{ $t->nota_number ?? '-' }}</td>
                            <td class="text-sm text-gray-500 max-w-[150px] truncate">{{ $t->description ?? '-' }}</td>
                            <td>
                                <span class="text-sm
                                    {{ $t->payment_method === 'cash' ? 'badge-cash' : '' }}
                                    {{ $t->payment_method === 'bank' ? 'badge-bank' : '' }}
                                    {{ $t->payment_method === 'qris' ? 'badge-qris' : '' }}
                                    {{ $t->payment_method === 'transfer' ? 'badge-transfer' : '' }}">
                                    {{ $t->payment_method === 'cash' ? 'Kas' : ($t->payment_method === 'bank' ? 'Bank' : ($t->payment_method === 'qris' ? 'QRIS' : 'Transfer')) }}
                                </span>
                            </td>
                            <td class="text-sm font-semibold text-right {{ $t->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                Rp {{ number_format($t->amount, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($t->status === 'approved')
                                <span class="badge-approved">Disetujui</span>
                                @elseif($t->status === 'pending')
                                <span class="badge-pending">Pending</span>
                                @else
                                <span class="badge-rejected">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex gap-1 items-center">
                                    <a href="{{ route('transactions.show', $t) }}" class="btn-icon-sm" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if($t->status === 'pending' && $t->user_id === Auth::id())
                                    <a href="{{ route('transactions.edit', $t) }}" class="btn-icon-sm" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @endif
                                    @if($t->attachments->count() > 0)
                                    <span class="relative" title="{{ $t->attachments->count() }} lampiran">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-orange-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">{{ $t->attachments->count() }}</span>
                                    </span>
                                    @endif
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
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <div class="empty-state-title">Belum Ada Transaksi</div>
                <div class="empty-state-desc">Mulai catat pemasukan dan pengeluaran untuk mengelola keuangan Anda.</div>
                <a href="{{ route('transactions.create') }}" class="btn-primary btn-sm mt-4">Catat Transaksi</a>
            </div>
            @endif
        </div>
        @if($transactions->hasPages())
        <div class="card-footer">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
@endsection
