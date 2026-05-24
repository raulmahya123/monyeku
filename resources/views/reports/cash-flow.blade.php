@extends('layouts.main')

@section('title', 'Laporan Arus Kas')

@section('subtitle')
    Laporan arus kas metode langsung (direct method) — periode {{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }}
@endsection

@section('content')
<div class="card mb-5">
    <div class="card-body">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="form-label text-xs">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-input" value="{{ $startDate }}">
            </div>
            <div>
                <label class="form-label text-xs">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-input" value="{{ $endDate }}">
            </div>
            <button type="submit" class="btn-primary btn-sm">Tampilkan</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="stat-card bg-emerald-50 border border-emerald-200">
        <p class="stat-label text-emerald-700">Total Penerimaan Kas</p>
        <p class="stat-value text-emerald-800">Rp {{ number_format($totalCashIn, 0, ',', '.') }}</p>
    </div>
    <div class="stat-card bg-red-50 border border-red-200">
        <p class="stat-label text-red-700">Total Pengeluaran Kas</p>
        <p class="stat-value text-red-800">Rp {{ number_format($totalCashOut, 0, ',', '.') }}</p>
    </div>
    <div class="stat-card {{ $netCashFlow >= 0 ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }}">
        <p class="stat-label {{ $netCashFlow >= 0 ? 'text-emerald-700' : 'text-red-700' }}">Arus Kas Bersih</p>
        <p class="stat-value {{ $netCashFlow >= 0 ? 'text-emerald-800' : 'text-red-800' }}">Rp {{ number_format($netCashFlow, 0, ',', '.') }}</p>
    </div>
</div>

<div class="space-y-4">
    {{-- Operating Activities --}}
    <div class="card">
        <div class="card-header">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800">Aktivitas Operasi</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Penerimaan dari Pelanggan</span>
                    <span class="font-semibold text-emerald-600">Rp {{ number_format($operatingIn, 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-gray-100"></div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Pembayaran ke Pemasok & Karyawan</span>
                    <span class="font-semibold text-red-600">(Rp {{ number_format($operatingOut, 0, ',', '.') }})</span>
                </div>
                <div class="border-t border-gray-100"></div>
                <div class="flex justify-between py-3 text-base font-bold {{ $netOperating >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                    <span>Kas Bersih dari Aktivitas Operasi</span>
                    <span>Rp {{ number_format(abs($netOperating), 0, ',', '.') }} {{ $netOperating >= 0 ? 'Masuk' : 'Keluar' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Investing Activities --}}
    <div class="card">
        <div class="card-header">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800">Aktivitas Investasi</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Penerimaan dari Investasi</span>
                    <span class="font-semibold text-emerald-600">Rp {{ number_format($investIn, 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-gray-100"></div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Pembelian Aset Tetap</span>
                    <span class="font-semibold text-red-600">(Rp {{ number_format($investOut, 0, ',', '.') }})</span>
                </div>
                <div class="border-t border-gray-100"></div>
                <div class="flex justify-between py-3 text-base font-bold {{ $netInvesting >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                    <span>Kas Bersih dari Aktivitas Investasi</span>
                    <span>Rp {{ number_format(abs($netInvesting), 0, ',', '.') }} {{ $netInvesting >= 0 ? 'Masuk' : 'Keluar' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Financing Activities --}}
    <div class="card">
        <div class="card-header">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m0 0v9m0 0h-2.25m0 0h-2.25"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-800">Aktivitas Pendanaan</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Penerimaan Pinjaman/Modal</span>
                    <span class="font-semibold text-emerald-600">Rp {{ number_format($finIn, 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-gray-100"></div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Pembayaran Pinjaman/Dividen</span>
                    <span class="font-semibold text-red-600">(Rp {{ number_format($finOut, 0, ',', '.') }})</span>
                </div>
                <div class="border-t border-gray-100"></div>
                <div class="flex justify-between py-3 text-base font-bold {{ $netFinancing >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                    <span>Kas Bersih dari Aktivitas Pendanaan</span>
                    <span>Rp {{ number_format(abs($netFinancing), 0, ',', '.') }} {{ $netFinancing >= 0 ? 'Masuk' : 'Keluar' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-extrabold text-gray-800">Kenaikan/Penurunan Kas Bersih</h3>
            <span class="text-xl font-extrabold {{ $netCashFlow >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                {{ $netCashFlow >= 0 ? '+' : '-' }} Rp {{ number_format(abs($netCashFlow), 0, ',', '.') }}
            </span>
        </div>
    </div>
</div>
@endsection
