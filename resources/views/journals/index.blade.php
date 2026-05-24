@extends('layouts.main')

@section('title', 'Jurnal Umum')

@section('actions')
    <a href="{{ route('journals.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Buat Jurnal
    </a>
    <a href="{{ route('reports.balance-sheet') }}" class="btn-primary btn-sm">Neraca</a>
    <a href="{{ route('reports.income-statement') }}" class="btn-primary btn-sm">Laba Rugi</a>
@endsection

@section('content')
<div class="card mb-5">
    <div class="card-body">
        <form method="GET" action="{{ route('journals.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="form-group">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input text-sm">
            </div>
            <div class="form-group">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input text-sm">
            </div>
            <div class="form-group">
                <label class="form-label">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input text-sm" placeholder="Cari jurnal...">
            </div>
            <div class="form-group flex items-end gap-2">
                <button type="submit" class="btn-primary btn-sm">Filter</button>
                <a href="{{ route('journals.index') }}" class="btn-ghost btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($journals->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <th>Ref</th>
                        <th class="text-right">Jumlah Debit</th>
                        <th class="text-right">Jumlah Kredit</th>
                        <th>Dibuat Oleh</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalDebit = 0;
                        $totalKredit = 0;
                    @endphp
                    @foreach($journals as $j)
                    @php
                        $totalDebit += $j->total_debit;
                        $totalKredit += $j->total_kredit;
                    @endphp
                    <tr class="hover:bg-orange-50/30 transition-colors">
                        <td class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($j->date)->format('d/m/Y') }}</td>
                        <td class="text-sm text-gray-700 max-w-[200px] truncate">{{ $j->description }}</td>
                        <td>
                            @if($j->reference_type)
                            <span class="badge {{ $j->reference_type === 'invoice' ? 'badge-paid' : 'badge-pending' }}">
                                {{ Str::ucfirst($j->reference_type) }} #{{ $j->reference_id }}
                            </span>
                            @else
                            <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="text-right text-sm text-gray-700">Rp {{ number_format($j->total_debit, 0, ',', '.') }}</td>
                        <td class="text-right text-sm text-gray-700">Rp {{ number_format($j->total_kredit, 0, ',', '.') }}</td>
                        <td class="text-sm text-gray-600">{{ $j->user?->name ?? '-' }}</td>
                        <td class="text-right">
                            <a href="{{ route('journals.show', $j) }}" class="btn-ghost btn-sm">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50/80 border-t-2 border-gray-200">
                    <tr>
                        <th colspan="3" class="text-right px-5 py-3.5 text-sm font-semibold text-gray-800">Total</th>
                        <th class="text-right px-5 py-3.5 text-sm font-semibold text-gray-800">Rp {{ number_format($totalDebit, 0, ',', '.') }}</th>
                        <th class="text-right px-5 py-3.5 text-sm font-semibold text-gray-800">Rp {{ number_format($totalKredit, 0, ',', '.') }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Jurnal</div>
            <div class="empty-state-desc">Belum ada transaksi jurnal untuk ditampilkan.</div>
        </div>
        @endif
    </div>
    @if($journals->hasPages())
    <div class="card-footer">
        {{ $journals->links() }}
    </div>
    @endif
</div>
@endsection
