@extends('layouts.main')

@section('title', 'Laba Rugi')

@section('content')
<div class="card mb-6">
    <div class="card-header">
        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4v16h18V4H3zm2 4h14M7 4v3m10-3v3"/></svg>
        </div>
        <h3 class="card-title">Filter Periode</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.income-statement') }}" class="flex flex-col sm:flex-row gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="form-input">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="form-input">
            </div>
            <button type="submit" class="btn-primary btn-sm">Tampilkan</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card">
        <div class="card-header">
            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <h3 class="card-title">Pendapatan</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Akun</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalIncome = 0; @endphp
                        @foreach($incomes as $inc)
                        @php $totalIncome += $inc['balance']; @endphp
                        <tr class="hover:bg-orange-50/30 transition-colors">
                            <td class="text-sm text-gray-700">{{ $inc['name'] }}</td>
                            <td class="text-right text-sm text-emerald-600 font-medium">Rp {{ number_format($inc['balance'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50/80 border-t-2 border-gray-200">
                        <tr>
                            <th class="text-right px-5 py-3.5 text-sm font-semibold text-gray-800">Total Pendapatan</th>
                            <th class="text-right px-5 py-3.5 text-sm font-semibold text-emerald-700">Rp {{ number_format($totalIncome, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
            </div>
            <h3 class="card-title">Beban</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Akun</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalExpense = 0; @endphp
                        @foreach($expenses as $exp)
                        @php $totalExpense += $exp['balance']; @endphp
                        <tr class="hover:bg-orange-50/30 transition-colors">
                            <td class="text-sm text-gray-700">{{ $exp['name'] }}</td>
                            <td class="text-right text-sm text-red-600 font-medium">Rp {{ number_format($exp['balance'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50/80 border-t-2 border-gray-200">
                        <tr>
                            <th class="text-right px-5 py-3.5 text-sm font-semibold text-gray-800">Total Beban</th>
                            <th class="text-right px-5 py-3.5 text-sm font-semibold text-red-700">Rp {{ number_format($totalExpense, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mt-6">
    <div class="card-body">
        @php $netIncome = $totalIncome - $totalExpense; @endphp
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="text-center p-4 rounded-xl bg-emerald-50">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Pendapatan</p>
                <p class="text-lg font-bold text-emerald-700">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            </div>
            <div class="text-center p-4 rounded-xl bg-red-50">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Beban</p>
                <p class="text-lg font-bold text-red-700">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            </div>
            <div class="text-center p-4 rounded-xl {{ $netIncome >= 0 ? 'bg-emerald-50 border-2 border-emerald-300' : 'bg-red-50 border-2 border-red-300' }}">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Laba / Rugi Bersih</p>
                <p class="text-lg font-bold {{ $netIncome >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                    {{ $netIncome >= 0 ? 'Laba' : 'Rugi' }} Rp {{ number_format(abs($netIncome), 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
