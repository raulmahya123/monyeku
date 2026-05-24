@extends('layouts.main')

@section('title', 'Hutang & Piutang')

@section('actions')
    <a href="{{ route('debts.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Catat Baru
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="stat-card">
        <div class="flex items-center justify-between mb-1">
            <span class="stat-label">Total Piutang</span>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
        </div>
        <div class="stat-value text-emerald-600">Rp {{ number_format($totalReceivable, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between mb-1">
            <span class="stat-label">Total Hutang</span>
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
        <div class="stat-value text-red-500">Rp {{ number_format($totalPayable, 0, ',', '.') }}</div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('debts.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 flex flex-col sm:flex-row gap-3">
                <div class="sm:w-40">
                    <select name="type" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="receivable" {{ request('type') === 'receivable' ? 'selected' : '' }}>Piutang</option>
                        <option value="payable" {{ request('type') === 'payable' ? 'selected' : '' }}>Hutang</option>
                    </select>
                </div>
                <div class="sm:w-40">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>Menunggu Approval</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <div class="flex-1">
                    <input type="text" name="search" placeholder="Cari kontak..." value="{{ request('search') }}" class="form-input">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary btn-sm">Cari</button>
                @if(request('type') || request('status') || request('search'))
                <a href="{{ route('debts.index') }}" class="btn-secondary btn-sm">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card mt-5">
    <div class="card-body p-0">
        @if($debts->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tipe</th>
                        <th>Kontak</th>
                        <th class="text-right">Jumlah</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Approval</th>
                        <th>Catatan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($debts as $debt)
                    <tr>
                        <td>
                            <span class="{{ $debt->type === 'receivable' ? 'badge-income' : 'badge-expense' }}">
                                {{ $debt->type === 'receivable' ? 'Piutang' : 'Hutang' }}
                            </span>
                        </td>
                        <td>
                            <p class="font-medium text-gray-900">{{ $debt->contact_name }}</p>
                            @if($debt->contact_phone)<p class="text-xs text-gray-500">{{ $debt->contact_phone }}</p>@endif
                        </td>
                        <td class="text-right font-semibold">Rp {{ number_format($debt->amount, 0, ',', '.') }}</td>
                        <td>{{ $debt->due_date->format('d/m/Y') }}</td>
                        <td>
                            @if($debt->status === 'paid')
                            <span class="badge-paid">Lunas</span>
                            @elseif($debt->status === 'overdue')
                            <span class="badge-overdue">Overdue</span>
                            @else
                            <span class="badge-active">Aktif</span>
                            @endif
                        </td>
                        <td>
                            @if($debt->approval_status === 'pending')
                            <span class="badge-pending">Pending</span>
                            @elseif($debt->approval_status === 'rejected')
                            <span class="badge-rejected">Ditolak</span>
                            @else
                            <span class="badge-approved">Disetujui</span>
                            @endif
                        </td>
                        <td class="text-sm text-gray-500 max-w-[160px] truncate">{{ $debt->description }}</td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                @if($debt->status !== 'paid')
                                <form action="{{ route('debts.mark-paid', $debt) }}" method="POST">
                                    @csrf
                                    <button type="button" @click="$store.confirm.ask('Bayar Hutang/Piutang', 'Tandai hutang/piutang ini sebagai lunas?', { confirmText: 'Ya, bayar', confirmClass: 'btn-success', action: () => $el.closest('form').submit() })" class="btn-success btn-sm">Bayar</button>
                                </form>
                                @endif
                                <a href="{{ route('debts.edit', $debt) }}" class="btn-secondary btn-sm">Edit</a>
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
                <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div class="empty-state-title">Belum ada data hutang/piutang</div>
            <div class="empty-state-desc">Catat transaksi hutang atau piutang untuk mulai melacak.</div>
            <a href="{{ route('debts.create') }}" class="btn-primary">Catat Baru</a>
        </div>
        @endif
    </div>
    @if($debts->hasPages())
    <div class="card-footer">
        {{ $debts->links() }}
    </div>
    @endif
</div>
@endsection
