@extends('layouts.main')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="flex items-center gap-3 mb-5">
    <a href="{{ route('invoices.index') }}" class="btn-icon-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 class="text-lg font-bold text-gray-800">Invoice {{ $invoice->invoice_number }}</h2>
        <p class="text-sm text-gray-400">Detail lengkap invoice</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-gray-100">
            <div>
                <div class="text-sm text-gray-400 mb-1">Total Tagihan</div>
                <div class="text-3xl font-bold text-gray-800">
                    Rp {{ number_format($invoice->total, 0, ',', '.') }}
                </div>
            </div>
            <div>
                @if($invoice->status === 'paid')
                <span class="badge-paid text-sm px-4 py-1.5">Lunas {{ $invoice->paid_at ? $invoice->paid_at->format('d/m/Y') : '' }}</span>
                @elseif($invoice->status === 'unpaid')
                <span class="badge-unpaid text-sm px-4 py-1.5">Belum Dibayar</span>
                @elseif($invoice->status === 'overdue')
                <span class="badge-overdue text-sm px-4 py-1.5">Overdue</span>
                @else
                <span class="badge-cancelled text-sm px-4 py-1.5">Dibatalkan</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Pelanggan</h3>
                <div class="bg-gray-50 rounded-xl p-4 space-y-1.5">
                    <p class="font-semibold text-gray-800">{{ $invoice->customer_name }}</p>
                    @if($invoice->customer_phone)
                    <p class="text-sm text-gray-500 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $invoice->customer_phone }}
                    </p>
                    @endif
                    @if($invoice->customer_email)
                    <p class="text-sm text-gray-500 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        {{ $invoice->customer_email }}
                    </p>
                    @endif
                </div>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Informasi Invoice</h3>
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-gray-400">Tanggal Invoice</span>
                            <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $invoice->issue_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400">Jatuh Tempo</span>
                            <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $invoice->due_date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-6">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Item Invoice</h3>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Deskripsi</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Harga</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->items as $item)
                        <tr>
                            <td>{{ $item['description'] }}</td>
                            <td class="text-center">{{ $item['quantity'] }}</td>
                            <td class="text-right">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                            <td class="text-right font-medium">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-400 py-8">Tidak ada item</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-4">
                <div class="w-full sm:w-72 space-y-2">
                    <div class="flex justify-between text-sm py-1.5">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium text-gray-800">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm py-1.5">
                        <span class="text-gray-500">Pajak</span>
                        <span class="font-medium text-gray-800">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-base font-bold text-gray-800 border-t border-gray-200 pt-3 mt-1">
                        <span>Grand Total</span>
                        <span>Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($invoice->notes)
        <div class="border-t border-gray-100 pt-6 mt-6">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Catatan</h3>
            <p class="text-sm text-gray-600 bg-gray-50 rounded-xl p-4">{{ $invoice->notes }}</p>
        </div>
        @endif
    </div>
</div>

@if($invoice->approvals->count() > 0)
<div class="card mt-6">
    <div class="card-header">
        <h3 class="card-title">Riwayat Approval</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Level</th>
                        <th>Approver</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->approvals->sortBy('level') as $ap)
                    <tr>
                        <td>
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold
                                {{ $ap->level === 1 ? 'bg-orange-100 text-orange-700' : ($ap->level === 2 ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                                L{{ $ap->level }}
                            </span>
                        </td>
                        <td class="font-medium text-gray-800">{{ $ap->approver?->name ?? '-' }}</td>
                        <td>
                            @if($ap->status === 'approved')
                            <span class="badge-approved">Disetujui</span>
                            @elseif($ap->status === 'rejected')
                            <span class="badge-rejected">Ditolak</span>
                            @else
                            <span class="badge-pending">Pending</span>
                            @endif
                        </td>
                        <td class="text-sm text-gray-500 max-w-[200px]">{{ $ap->notes ?? '-' }}</td>
                        <td class="text-sm text-gray-500">{{ $ap->approved_at ? $ap->approved_at->format('d/m/Y H:i') : ($ap->created_at->format('d/m/Y H:i')) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="flex gap-3 mt-6">
    @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
    <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST">
        @csrf
        <button type="button" @click="$store.confirm.ask('Tandai Lunas', 'Tandai invoice ini sebagai lunas? Tindakan ini tidak dapat dibatalkan.', { confirmText: 'Ya, tandai lunas', confirmClass: 'btn-success', action: () => $el.closest('form').submit() })" class="btn-success">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Tandai Lunas
        </button>
    </form>
    <a href="{{ route('invoices.edit', $invoice) }}" class="btn-secondary">Edit</a>
    @endif
    <a href="{{ route('invoices.index') }}" class="btn-ghost">Kembali</a>
</div>
@endsection
