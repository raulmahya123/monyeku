@extends('layouts.main')

@section('title', 'Detail Rekonsiliasi Bank')

@section('subtitle')
    Perbandingan saldo bank dengan catatan sistem untuk periode {{ $reconciliation->period }}.
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="stat-card bg-blue-50 border border-blue-200">
        <p class="stat-label text-blue-700">Saldo Bank</p>
        <p class="stat-value text-blue-800">Rp {{ number_format($reconciliation->closing_balance, 0, ',', '.') }}</p>
    </div>
    <div class="stat-card bg-emerald-50 border border-emerald-200">
        <p class="stat-label text-emerald-700">Saldo Sistem</p>
        <p class="stat-value text-emerald-800">Rp {{ number_format($reconciliation->system_balance, 0, ',', '.') }}</p>
    </div>
    <div class="stat-card {{ abs($reconciliation->difference) < 1 ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }}">
        <p class="stat-label {{ abs($reconciliation->difference) < 1 ? 'text-emerald-700' : 'text-red-700' }}">Selisih</p>
        <p class="stat-value {{ abs($reconciliation->difference) < 1 ? 'text-emerald-800' : 'text-red-800' }}">Rp {{ number_format($reconciliation->difference, 0, ',', '.') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card">
        <div class="card-header">
            <h3 class="text-base font-bold text-gray-800">Data Rekonsiliasi</h3>
        </div>
        <div class="card-body space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Bank</span><span class="font-medium">{{ $reconciliation->bankAccount?->bank_name }} - {{ $reconciliation->bankAccount?->account_number }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Periode</span><span class="font-medium">{{ $reconciliation->period }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Tanggal Mutasi</span><span class="font-medium">{{ $reconciliation->statement_date->format('d/m/Y') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Saldo Awal</span><span class="font-medium">Rp {{ number_format($reconciliation->opening_balance, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Status</span>
                <span>@if($reconciliation->status === 'completed')<span class="badge badge-success">Selesai</span>@else<span class="badge badge-warning">Draft</span>@endif</span>
            </div>
            <div class="flex justify-between"><span class="text-gray-500">Dibuat Oleh</span><span class="font-medium">{{ $reconciliation->creator?->name }}</span></div>
            @if($reconciliation->completed_at)
            <div class="flex justify-between"><span class="text-gray-500">Selesai Pada</span><span class="font-medium">{{ $reconciliation->completed_at->format('d/m/Y H:i') }}</span></div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="text-base font-bold text-gray-800">Mutasi Sistem (Kas/Bank)</h3>
        </div>
        <div class="card-body p-0">
            @if(count($systemEntries) > 0)
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($systemEntries as $entry)
                        <tr>
                            <td class="text-sm text-gray-600">{{ $entry['date'] }}</td>
                            <td class="text-sm text-gray-800">{{ $entry['description'] }}</td>
                            <td class="text-sm text-emerald-600 font-medium">{{ $entry['debit'] > 0 ? 'Rp ' . number_format($entry['debit'], 0, ',', '.') : '-' }}</td>
                            <td class="text-sm text-red-600 font-medium">{{ $entry['credit'] > 0 ? 'Rp ' . number_format($entry['credit'], 0, ',', '.') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state py-8">
                <div class="empty-state-title text-sm">Tidak ada mutasi sistem untuk periode ini</div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($reconciliation->statement_lines)
<div class="card mt-6">
    <div class="card-header">
        <h3 class="text-base font-bold text-gray-800">Mutasi Bank (Import)</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reconciliation->statement_lines as $line)
                    <tr>
                        <td class="text-sm text-gray-600">{{ $line['date'] ?? '-' }}</td>
                        <td class="text-sm text-gray-800">{{ $line['description'] ?? '-' }}</td>
                        <td class="text-sm font-medium {{ ($line['amount'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            Rp {{ number_format(abs($line['amount'] ?? 0), 0, ',', '.') }}
                            {{ ($line['amount'] ?? 0) >= 0 ? 'Masuk' : 'Keluar' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="flex gap-3 mt-6">
    @if($reconciliation->status === 'draft')
    <form action="{{ route('bank-reconciliations.complete', $reconciliation) }}" method="POST">
        @csrf
        <button type="button" @click="$store.confirm.ask('Kompletasi', 'Tandai rekonsiliasi ini sebagai selesai?', { confirmText: 'Ya, selesai', confirmClass: 'btn-success', action: () => $el.closest('form').submit() })" class="btn-success">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Kompletasi Rekonsiliasi
        </button>
    </form>
    @endif
    <a href="{{ route('bank-reconciliations.index') }}" class="btn-ghost">Kembali</a>
</div>
@endsection
