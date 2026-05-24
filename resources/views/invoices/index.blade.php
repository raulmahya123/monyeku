@extends('layouts.main')

@section('title', 'Invoice')

@section('actions')
    <a href="{{ route('invoices.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Invoice Baru
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('invoices.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 flex flex-col sm:flex-row gap-3">
                <div class="sm:w-48">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>Menunggu Approval</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="flex-1">
                    <input type="text" name="search" placeholder="Cari invoice..." value="{{ request('search') }}" class="form-input">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary btn-sm">Cari</button>
                @if(request('status') || request('search'))
                <a href="{{ route('invoices.index') }}" class="btn-secondary btn-sm">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card mt-5">
    <div class="card-body p-0">
        @if($invoices->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Jatuh Tempo</th>
                        <th class="text-right">Total</th>
                        <th>Status</th>
                        <th>Approval</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $inv)
                    <tr>
                        <td class="font-medium">{{ $inv->invoice_number }}</td>
                        <td>{{ $inv->customer_name }}</td>
                        <td>{{ $inv->issue_date->format('d/m/Y') }}</td>
                        <td>{{ $inv->due_date->format('d/m/Y') }}</td>
                        <td class="text-right font-semibold">Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                        <td>
                            @if($inv->status === 'paid')
                            <span class="badge-paid">Lunas</span>
                            @elseif($inv->status === 'unpaid')
                            <span class="badge-unpaid">Belum Dibayar</span>
                            @elseif($inv->status === 'overdue')
                            <span class="badge-overdue">Overdue</span>
                            @else
                            <span class="badge-cancelled">Dibatalkan</span>
                            @endif
                        </td>
                        <td>
                            @if($inv->approval_status === 'pending')
                            <span class="badge-pending">Pending</span>
                            @elseif($inv->approval_status === 'rejected')
                            <span class="badge-rejected">Ditolak</span>
                            @else
                            <span class="badge-approved">Disetujui</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('invoices.show', $inv) }}" class="btn-ghost btn-sm">Detail</a>
                            <a href="{{ route('invoices.pdf', $inv) }}" class="btn-ghost btn-sm" target="_blank">PDF</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="empty-state-title">Belum ada invoice</div>
            <div class="empty-state-desc">Buat invoice pertama untuk mulai menagih pelanggan.</div>
            <a href="{{ route('invoices.create') }}" class="btn-primary">Buat Invoice</a>
        </div>
        @endif
    </div>
    @if($invoices->hasPages())
    <div class="card-footer">
        {{ $invoices->links() }}
    </div>
    @endif
</div>
@endsection
