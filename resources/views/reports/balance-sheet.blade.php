@extends('layouts.main')

@section('title', 'Neraca')

@section('content')
<div class="card mb-6">
    <div class="card-header">
        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4v16h18V4H3zm2 4h14M7 4v3m10-3v3"/></svg>
        </div>
        <h3 class="card-title">Filter Periode</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.balance-sheet') }}" class="flex flex-col sm:flex-row gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="form-input">
            </div>
            <button type="submit" class="btn-primary btn-sm">Tampilkan</button>
        </form>
    </div>
</div>

<div class="card mb-6">
    <div class="card-body">
        @php
            $totalAset = 0;
            $totalKewajiban = 0;
            $totalEkuitas = 0;
            $netIncome = $totalIncomeBalance ?? 0;
        @endphp
        @foreach($aset as $a) @php $totalAset += $a['balance']; @endphp @endforeach
        @foreach($kewajiban as $k) @php $totalKewajiban += $k['balance']; @endphp @endforeach
        @foreach($ekuitas as $e) @php $totalEkuitas += $e['balance']; @endphp @endforeach
        @php $totalEkuitas += $netIncome; @endphp

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card bg-emerald-50">
                <p class="stat-label text-emerald-700 font-semibold">Total Aset</p>
                <p class="stat-value text-emerald-700">Rp {{ number_format($totalAset, 0, ',', '.') }}</p>
            </div>
            <div class="stat-card bg-blue-50">
                <p class="stat-label text-blue-700 font-semibold">Total Kewajiban</p>
                <p class="stat-value text-blue-700">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</p>
            </div>
            <div class="stat-card {{ $totalAset == ($totalKewajiban + $totalEkuitas - $netIncome) ? 'bg-violet-50' : 'bg-red-50' }}">
                <p class="stat-label {{ $totalAset == ($totalKewajiban + $totalEkuitas - $netIncome) ? 'text-violet-700' : 'text-red-700' }} font-semibold">Total Ekuitas</p>
                <p class="stat-value {{ $totalAset == ($totalKewajiban + $totalEkuitas - $netIncome) ? 'text-violet-700' : 'text-red-700' }}">Rp {{ number_format($totalEkuitas - $netIncome, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="mt-4 p-4 rounded-xl {{ $totalAset == ($totalKewajiban + $totalEkuitas) ? 'bg-emerald-50 border border-emerald-200' : 'bg-red-50 border border-red-200' }}">
            <p class="text-sm font-semibold {{ $totalAset == ($totalKewajiban + $totalEkuitas) ? 'text-emerald-700' : 'text-red-700' }}">
                {{ $totalAset == ($totalKewajiban + $totalEkuitas) ? 'Balance: Aset = Kewajiban + Ekuitas' : 'Tidak Balance: Selisih Rp ' . number_format(abs($totalAset - $totalKewajiban - $totalEkuitas), 0, ',', '.') }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Aset</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Akun</th>
                            <th class="text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aset as $a)
                        <tr class="hover:bg-orange-50/30 transition-colors">
                            <td class="text-sm text-gray-700">{{ $a['name'] }}</td>
                            <td class="text-right text-sm text-gray-700">Rp {{ number_format($a['balance'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50/80 border-t-2 border-gray-200">
                        <tr>
                            <th class="text-right px-5 py-3.5 text-sm font-semibold text-gray-800">Total Aset</th>
                            <th class="text-right px-5 py-3.5 text-sm font-semibold text-emerald-700">Rp {{ number_format($totalAset, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Kewajiban</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Akun</th>
                            <th class="text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kewajiban as $k)
                        <tr class="hover:bg-orange-50/30 transition-colors">
                            <td class="text-sm text-gray-700">{{ $k['name'] }}</td>
                            <td class="text-right text-sm text-gray-700">Rp {{ number_format($k['balance'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50/80 border-t-2 border-gray-200">
                        <tr>
                            <th class="text-right px-5 py-3.5 text-sm font-semibold text-gray-800">Total Kewajiban</th>
                            <th class="text-right px-5 py-3.5 text-sm font-semibold text-blue-700">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Modal / Ekuitas</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Akun</th>
                            <th class="text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ekuitas as $e)
                        <tr class="hover:bg-orange-50/30 transition-colors">
                            <td class="text-sm text-gray-700">{{ $e['name'] }}</td>
                            <td class="text-right text-sm text-gray-700">Rp {{ number_format($e['balance'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="bg-emerald-50/50 font-semibold">
                            <td class="text-sm text-emerald-700">Laba Berjalan</td>
                            <td class="text-right text-sm text-emerald-700">Rp {{ number_format($netIncome, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-50/80 border-t-2 border-gray-200">
                        <tr>
                            <th class="text-right px-5 py-3.5 text-sm font-semibold text-gray-800">Total Ekuitas</th>
                            <th class="text-right px-5 py-3.5 text-sm font-semibold text-violet-700">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mt-6">
    <div class="card-body">
        @php $balanceOk = ($totalAset == ($totalKewajiban + $totalEkuitas)); @endphp
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="text-center p-4 rounded-xl bg-emerald-50">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Aset</p>
                <p class="text-lg font-bold text-emerald-700">Rp {{ number_format($totalAset, 0, ',', '.') }}</p>
            </div>
            <div class="text-center p-4 rounded-xl bg-blue-50">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Kewajiban</p>
                <p class="text-lg font-bold text-blue-700">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</p>
            </div>
            <div class="text-center p-4 rounded-xl bg-violet-50">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Ekuitas</p>
                <p class="text-lg font-bold text-violet-700">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</p>
            </div>
            <div class="text-center p-4 rounded-xl {{ $balanceOk ? 'bg-emerald-50' : 'bg-red-50' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Balance Check</p>
                <p class="text-lg font-bold {{ $balanceOk ? 'text-emerald-700' : 'text-red-700' }}">{{ $balanceOk ? 'Seimbang' : 'Tidak Seimbang' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
