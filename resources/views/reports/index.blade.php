@extends('layouts.main')

@section('title', 'Laporan Keuangan')

@section('content')
{{-- Period Filter --}}
<div class="card mb-6">
    <div class="card-header">
        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4v16h18V4H3zm2 4h14M7 4v3m10-3v3"/></svg>
        </div>
        <h3 class="card-title">Filter Periode</h3>
    </div>
    <div class="card-body">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Periode</label>
                <select name="period" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all" form="reportForm" x-on:change="document.getElementById('reportForm').submit()">
                    <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Kustom</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tahun</label>
                <select name="year" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all" form="reportForm">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div x-data="{ period: '{{ $period }}' }">
                <div x-show="period === 'monthly'">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Bulan</label>
                    <select name="month" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all" form="reportForm">
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $m)
                        <option value="{{ $i + 1 }}" {{ $month == $i + 1 ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div x-data="{ period: '{{ $period }}' }">
                <div x-show="period === 'custom'" class="flex gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Dari</label>
                        <input type="date" name="start_date" value="{{ request('start_date', $startDate instanceof \Carbon\Carbon ? $startDate->format('Y-m-d') : '') }}" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all" form="reportForm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sampai</label>
                        <input type="date" name="end_date" value="{{ request('end_date', $endDate instanceof \Carbon\Carbon ? $endDate->format('Y-m-d') : '') }}" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all" form="reportForm">
                    </div>
                </div>
            </div>
            <div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors" form="reportForm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Tampilkan
                </button>
            </div>
        </div>
        <form id="reportForm" method="GET" action="{{ route('reports.index') }}"></form>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Pemasukan</p>
                <p class="text-xl font-bold text-emerald-600">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
    <div class="card px-5 py-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Pengeluaran</p>
                <p class="text-xl font-bold text-red-600">Rp {{ number_format($summary['total_expense'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
    <div class="card px-5 py-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 {{ $summary['net'] >= 0 ? 'bg-orange-50' : 'bg-red-50' }} rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 {{ $summary['net'] >= 0 ? 'text-orange-500' : 'text-red-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Saldo Bersih</p>
                <p class="text-xl font-bold {{ $summary['net'] >= 0 ? 'text-orange-600' : 'text-red-600' }}">
                    Rp {{ number_format($summary['net'], 0, ',', '.') }}
                </p>
                <span class="text-xs text-gray-400">{{ $summary['count'] }} transaksi</span>
            </div>
        </div>
    </div>
</div>

@if($transactions->count() > 0)
{{-- Category Summary + Chart --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Category Summary --}}
<div class="card">
    <div class="card-header">
        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
        </div>
        <h3 class="card-title">Ringkasan per Kategori</h3>
    </div>
    <div class="card-body p-0">
        <div class="divide-y divide-gray-50">
            @foreach($byCategory as $item)
            <div class="flex items-center justify-between px-5 py-3 hover:bg-orange-50/30 transition-colors">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full {{ $item['type'] === 'income' ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                    <span class="text-sm text-gray-700">{{ $item['category'] }}</span>
                    <span class="text-xs text-gray-400">({{ $item['count'] }}x)</span>
                </div>
                <span class="text-sm font-semibold {{ $item['type'] === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                    Rp {{ number_format($item['total'], 0, ',', '.') }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Chart --}}
    <div class="card">
        <div class="card-header">
            <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
            </div>
            <h3 class="card-title">Grafik Keuangan</h3>
        </div>
        <div class="card-body">
            <canvas id="reportChart" height="140"></canvas>
        </div>
    </div>
</div>

{{-- Daily Summary --}}
<div class="card mb-6">
    <div class="card-header">
        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <h3 class="card-title">Ringkasan Harian</h3>
    </div>
    <div class="card-body p-0">
    <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-50 bg-gray-50/50 sticky top-0">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemasukan</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengeluaran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($dailySummary as $day)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-5 py-3 text-gray-600">{{ \Carbon\Carbon::parse($day['date'])->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-right text-emerald-600 font-medium">+Rp {{ number_format($day['income'], 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-right text-red-600 font-medium">-Rp {{ number_format($day['expense'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>
</div>

{{-- Transaction Detail --}}
<div class="card mb-6">
    <div class="card-header">
        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
        </div>
        <h3 class="card-title">Detail Transaksi</h3>
    </div>
    <div class="card-body p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-50 bg-gray-50/50">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($transactions as $t)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-5 py-3 text-gray-600">{{ $t->transaction_date->format('d/m/Y') }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full {{ $t->type === 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $t->type === 'income' ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                            {{ $t->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-700">{{ $t->category?->name ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-400 max-w-[200px] truncate">{{ $t->description ?? '-' }}</td>
                    <td class="px-5 py-3 text-right font-semibold {{ $t->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                        Rp {{ number_format($t->amount, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>
</div>

{{-- Export Buttons --}}
<div class="flex gap-3">
    <form action="{{ route('reports.export-pdf') }}" method="GET" target="_blank">
        <input type="hidden" name="start_date" value="{{ $startDate instanceof \Carbon\Carbon ? $startDate->format('Y-m-d') : '' }}">
        <input type="hidden" name="end_date" value="{{ $endDate instanceof \Carbon\Carbon ? $endDate->format('Y-m-d') : '' }}">
        <input type="hidden" name="type" value="all">
        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export PDF
        </button>
    </form>
    <form action="{{ route('reports.export-excel') }}" method="GET">
        <input type="hidden" name="start_date" value="{{ $startDate instanceof \Carbon\Carbon ? $startDate->format('Y-m-d') : '' }}">
        <input type="hidden" name="end_date" value="{{ $endDate instanceof \Carbon\Carbon ? $endDate->format('Y-m-d') : '' }}">
        <input type="hidden" name="type" value="all">
        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Excel
        </button>
    </form>
</div>
@else
<div class="card">
    <div class="flex flex-col items-center justify-center py-14 px-5">
        <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <p class="text-base font-semibold text-gray-800">Tidak ada transaksi</p>
        <p class="text-sm text-gray-400 mt-1.5">Tidak ada transaksi untuk periode ini.</p>
    </div>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('reportChart');
        if (!ctx) return;

        const labels = @json($dailySummary->keys()->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M')));
        const income = @json($dailySummary->pluck('income'));
        const expense = @json($dailySummary->pluck('expense'));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pemasukan',
                    data: income,
                    backgroundColor: '#34d399',
                    hoverBackgroundColor: '#10b981',
                    borderRadius: 6,
                }, {
                    label: 'Pengeluaran',
                    data: expense,
                    backgroundColor: '#fb923c',
                    hoverBackgroundColor: '#f97316',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 16,
                            font: { size: 12 }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => 'Rp ' + v.toLocaleString('id-ID')
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.04)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
