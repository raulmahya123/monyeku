@extends('layouts.main')

@section('title', 'Anggaran')

@section('content')
{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="card px-5 py-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Terpakai</p>
                <p class="text-xl font-bold text-orange-600">Rp {{ number_format($budgets->sum('spent'), 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
    <div class="card px-5 py-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Sisa Anggaran</p>
                <p class="text-xl font-bold text-emerald-600">Rp {{ number_format($budgets->sum('remaining'), 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Add Budget Form --}}
<div class="card mb-6">
    <div class="card-header">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <h3 class="card-title">Tambah Anggaran Baru</h3>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('budgets.store') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Kategori</label>
                    <select name="category_id" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Periode</label>
                    <select name="period" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                        <option value="monthly">Bulanan</option>
                        <option value="yearly">Tahunan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Jumlah</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                        <input type="number" name="amount" placeholder="0" required min="0" class="w-full pl-9 pr-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Alert %</label>
                    <input type="number" name="notification_threshold" value="80" min="1" max="100" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Budget List --}}
@if($budgets->count() > 0)
<div class="grid grid-cols-1 gap-4">
    @foreach($budgets as $budget)
    @php
        $pct = min($budget->percentage, 100);
        $barColor = $budget->percentage > 80 ? 'bg-red-500' : ($budget->percentage > 50 ? 'bg-orange-400' : 'bg-emerald-400');
        $textColor = $budget->percentage > 80 ? 'text-red-600' : ($budget->percentage > 50 ? 'text-orange-600' : 'text-emerald-600');
        $bgColor = $budget->percentage > 80 ? 'bg-red-50' : ($budget->percentage > 50 ? 'bg-orange-50' : 'bg-emerald-50');
    @endphp
    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-gray-900">{{ $budget->category?->name ?? 'Semua Kategori' }}</h3>
                    <span class="text-xs {{ $bgColor }} {{ $textColor }} px-2 py-0.5 rounded-full font-medium">{{ $budget->percentage }}%</span>
                </div>
                <div class="flex items-center gap-2">
                    @if($budget->approval_status === 'pending')
                    <span class="badge-pending">Pending</span>
                    @elseif($budget->approval_status === 'rejected')
                    <span class="badge-rejected">Ditolak</span>
                    @endif
                    <span class="text-xs text-gray-400">
                        {{ $budget->period === 'monthly' ? 'Bulan ' . \Carbon\Carbon::create()->month(intval($budget->month ?? now()->month))->format('F') : 'Tahun ' . ($budget->year ?? now()->year) }}
                    </span>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 {{ $barColor }}" style="width: {{ $pct }}%"></div>
            </div>

            <div class="flex items-center justify-between mt-3 text-sm">
                <span class="text-gray-500">
                    <span class="font-medium text-gray-700">Rp {{ number_format($budget->spent, 0, ',', '.') }}</span>
                    <span class="text-gray-400"> / Rp {{ number_format($budget->amount, 0, ',', '.') }}</span>
                </span>
                <span class="font-medium {{ $budget->remaining >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    @if($budget->remaining >= 0)
                    Sisa: Rp {{ number_format($budget->remaining, 0, ',', '.') }}
                    @else
                    Over: Rp {{ number_format(abs($budget->remaining), 0, ',', '.') }}
                    @endif
                </span>
            </div>

            <div class="flex justify-end mt-3 pt-3 border-t border-gray-100">
                <form action="{{ route('budgets.destroy', $budget) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="button" @click="$store.confirm.ask('Hapus Anggaran', 'Hapus anggaran ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-red-200 text-red-500 hover:bg-red-50 hover:text-red-600 text-xs font-medium rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="card">
    <div class="flex flex-col items-center justify-center py-14 px-5">
        <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-base font-semibold text-gray-800">Belum ada anggaran</p>
        <p class="text-sm text-gray-400 mt-1.5">Tetapkan batas pengeluaran per kategori untuk mengontrol keuangan.</p>
    </div>
</div>
@endif
@endsection
