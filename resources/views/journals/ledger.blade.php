@extends('layouts.main')

@section('title', 'Buku Besar')

@section('actions')
    <a href="{{ route('journals.index') }}" class="btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
        Jurnal Umum
    </a>
@endsection

@section('content')
<div class="flex items-center gap-3 mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-800">Buku Besar</h2>
        <p class="text-xs text-gray-400">Riwayat mutasi per akun dengan saldo berjalan</p>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-6">
    <div class="card-header">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4v16h18V4H3zm2 4h14M7 4v3m10-3v3"/></svg>
            </div>
            <h3 class="card-title">Filter</h3>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('journals.ledger') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Akun</label>
                <select name="coa_id" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                    <option value="">Pilih Akun</option>
                    @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" {{ $coaId == $acc->id ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
            </div>
            <div>
                <button type="submit" class="btn-primary btn-sm w-full justify-center">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

@if($selectedAccount)
<div class="card mb-6">
    <div class="card-header">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
            </div>
            <div>
                <h3 class="card-title">{{ $selectedAccount->code }} - {{ $selectedAccount->name }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ ucfirst($selectedAccount->type) }} · Normal {{ ucfirst($selectedAccount->normal_balance) }}</p>
            </div>
        </div>
        <span class="text-sm font-bold {{ $runningBalance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
            Saldo: Rp {{ number_format($runningBalance, 0, ',', '.') }}
        </span>
    </div>

    @if($lines->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-50 bg-gray-50/50">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Debit</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Credit</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Saldo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <tr class="text-gray-400 italic">
                    <td class="px-5 py-3" colspan="4">Saldo awal periode</td>
                    <td class="px-5 py-3 text-right font-medium text-gray-500">Rp 0</td>
                </tr>
                @foreach($lines as $line)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-5 py-3 text-gray-500">{{ \Carbon\Carbon::parse($line->journal_date)->format('d/m/Y') }}</td>
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">{{ $line->journal_description }}</p>
                        @if($line->description)
                        <p class="text-xs text-gray-400">{{ $line->description }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right text-gray-700">
                        @if($line->debit > 0)
                        Rp {{ number_format($line->debit, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right text-gray-700">
                        @if($line->credit > 0)
                        Rp {{ number_format($line->credit, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right font-medium {{ $line->running_balance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        Rp {{ number_format($line->running_balance, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-gray-50 font-semibold">
                    <td colspan="2" class="px-5 py-3 text-xs text-gray-600 uppercase tracking-wider">Total Mutasi</td>
                    <td class="px-5 py-3 text-right text-gray-800">Rp {{ number_format($lines->sum('debit'), 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-right text-gray-800">Rp {{ number_format($lines->sum('credit'), 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-right {{ $runningBalance >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                        Rp {{ number_format($runningBalance, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <div class="card-body">
        <div class="flex flex-col items-center justify-center py-10">
            <div class="w-14 h-14 bg-gray-50 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4v16h18V4H3zm2 4h14M7 4v3m10-3v3"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-800">Tidak ada mutasi</p>
            <p class="text-xs text-gray-400 mt-1">Tidak ada transaksi untuk akun ini pada periode tersebut.</p>
        </div>
    </div>
    @endif
</div>
@else
<div class="card">
    <div class="card-body">
        <div class="flex flex-col items-center justify-center py-14">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
            </div>
            <p class="text-base font-semibold text-gray-800">Pilih Akun</p>
            <p class="text-sm text-gray-400 mt-1.5 max-w-sm text-center">Pilih akun dan periode untuk melihat mutasi dan saldo berjalan.</p>
        </div>
    </div>
</div>
@endif
@endsection
