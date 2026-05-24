@extends('layouts.main')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="page-header">
        <a href="{{ route('transactions.index') }}" class="btn-ghost btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="page-title">Detail Transaksi</h1>
    </div>

    <div class="max-w-3xl space-y-5">
        <div class="card">
            <div class="card-body">
                <div class="flex items-start justify-between mb-5">
                    <div>
                        <span class="text-sm text-gray-400">Status</span>
                        <div class="mt-1">
                            @if($transaction->status === 'approved')
                            <span class="badge-approved">Disetujui</span>
                            @elseif($transaction->status === 'pending')
                            <span class="badge-pending">Pending</span>
                            @else
                            <span class="badge-rejected">Ditolak</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-gray-400">Jumlah</span>
                        <div class="text-xl font-bold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <span class="text-xs text-gray-400">Tipe</span>
                        <p class="text-sm font-medium text-gray-800 mt-0.5">
                            <span class="{{ $transaction->type === 'income' ? 'badge-income' : 'badge-expense' }}">
                                {{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400">Kategori</span>
                        <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $transaction->category?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400">Tanggal</span>
                        <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $transaction->transaction_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400">Metode Pembayaran</span>
                        <p class="text-sm font-medium text-gray-800 mt-0.5 capitalize">{{ $transaction->payment_method }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400">No. Nota</span>
                        <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $transaction->nota_number ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400">Oleh</span>
                        <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $transaction->user?->name ?? '-' }}</p>
                    </div>
                </div>

                @if($transaction->description)
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <span class="text-xs text-gray-400">Catatan</span>
                    <p class="text-sm text-gray-700 mt-1 whitespace-pre-wrap">{{ $transaction->description }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($transaction->attachments->count() > 0)
        <div class="card">
            <div class="card-header">
                <span class="card-title">Lampiran ({{ $transaction->attachments->count() }})</span>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($transaction->attachments as $att)
                    <a href="{{ Storage::url($att->file_path) }}" target="_blank" class="group relative block aspect-square rounded-lg overflow-hidden bg-gray-100 border border-gray-200 hover:border-orange-300 transition-colors">
                        <img src="{{ Storage::url($att->file_path) }}" alt="{{ $att->original_name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/50 to-transparent p-2">
                            <p class="text-[10px] text-white truncate">{{ $att->original_name }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($transaction->approvals->count() > 0)
        <div class="card">
            <div class="card-header">
                <span class="card-title">Riwayat Approval</span>
            </div>
            <div class="card-body p-0">
                <div class="table-wrap">
                    <table>
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
                            @foreach($transaction->approvals as $a)
                            <tr>
                                <td><span class="badge-level">L{{ $a->level }}</span></td>
                                <td class="text-sm text-gray-700">{{ $a->approver?->name ?? '-' }}</td>
                                <td>
                                    @if($a->status === 'approved')
                                    <span class="badge-approved">Disetujui</span>
                                    @elseif($a->status === 'pending')
                                    <span class="badge-pending">Pending</span>
                                    @else
                                    <span class="badge-rejected">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-sm text-gray-500 max-w-[200px] truncate">{{ $a->notes ?? '-' }}</td>
                                <td class="text-xs text-gray-400">{{ $a->approved_at ? \Carbon\Carbon::parse($a->approved_at)->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection
