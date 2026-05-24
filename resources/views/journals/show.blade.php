@extends('layouts.main')

@section('title', 'Detail Jurnal')

@section('content')
<div class="page-header">
    <a href="{{ route('journals.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">Detail Jurnal</h1>
</div>

<div class="max-w-3xl space-y-5">
    <div class="card">
        <div class="card-body">
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <span class="text-xs text-gray-400">Tanggal</span>
                    <p class="text-sm font-medium text-gray-800 mt-0.5">{{ \Carbon\Carbon::parse($journal->date)->format('d M Y') }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-400">Referensi</span>
                    <p class="text-sm font-medium text-gray-800 mt-0.5">
                        @if($journal->reference_type)
                        <span class="badge {{ $journal->reference_type === 'invoice' ? 'badge-paid' : 'badge-pending' }}">
                            {{ Str::ucfirst($journal->reference_type) }} #{{ $journal->reference_id }}
                        </span>
                        @else
                        -
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-xs text-gray-400">Dibuat Oleh</span>
                    <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $journal->user?->name ?? '-' }}</p>
                </div>
            </div>

            @if($journal->description)
            <div class="mt-5 pt-4 border-t border-gray-100">
                <span class="text-xs text-gray-400">Deskripsi</span>
                <p class="text-sm text-gray-700 mt-1">{{ $journal->description }}</p>
            </div>
            @endif

            @if($journal->notes)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="text-xs text-gray-400">Catatan</span>
                <p class="text-sm text-gray-700 mt-1 whitespace-pre-wrap">{{ $journal->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Garispemisah Jurnal</span>
        </div>
        <div class="card-body p-0">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode Akun</th>
                            <th>Nama Akun</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalDebit = 0;
                            $totalKredit = 0;
                        @endphp
                        @foreach($journal->lines as $line)
                        @php
                            $totalDebit += $line->debit;
                            $totalKredit += $line->credit;
                        @endphp
                        <tr class="hover:bg-orange-50/30 transition-colors">
                            <td class="text-sm font-mono text-gray-600">{{ $line->coa?->code ?? '-' }}</td>
                            <td class="text-sm text-gray-700">{{ $line->coa?->name ?? '-' }}</td>
                            <td class="text-right text-sm text-gray-700">{{ $line->debit > 0 ? 'Rp ' . number_format($line->debit, 0, ',', '.') : '-' }}</td>
                            <td class="text-right text-sm text-gray-700">{{ $line->credit > 0 ? 'Rp ' . number_format($line->credit, 0, ',', '.') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50/80 border-t-2 border-gray-200">
                        <tr>
                            <th colspan="2" class="text-right px-5 py-3.5 text-sm font-semibold text-gray-800">Total</th>
                            <th class="text-right px-5 py-3.5 text-sm font-semibold text-gray-800">Rp {{ number_format($totalDebit, 0, ',', '.') }}</th>
                            <th class="text-right px-5 py-3.5 text-sm font-semibold text-gray-800">Rp {{ number_format($totalKredit, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
