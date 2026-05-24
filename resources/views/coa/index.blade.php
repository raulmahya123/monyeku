@extends('layouts.main')

@section('title', 'Chart of Account')

@section('actions')
    <a href="{{ route('coa.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Akun Baru
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="stat-card bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-emerald-100">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z"/></svg>
            </div>
            <div>
                <p class="stat-label">Total Aset</p>
                <p class="stat-value text-emerald-700">Rp {{ number_format($totalAsset, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-blue-100">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="stat-label">Total Kewajiban</p>
                <p class="stat-value text-blue-700">Rp {{ number_format($totalLiability, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-violet-100">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <p class="stat-label">Total Ekuitas</p>
                <p class="stat-value text-violet-700">Rp {{ number_format($totalEquity, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-emerald-100">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <p class="stat-label">Total Pendapatan</p>
                <p class="stat-value text-emerald-700">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-red-100">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
            </div>
            <div>
                <p class="stat-label">Total Beban</p>
                <p class="stat-value text-red-700">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($groups->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Akun</th>
                        <th>Tipe</th>
                        <th>Saldo Normal</th>
                        <th class="text-right">Saldo</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groups as $group)
                    <tr class="font-semibold bg-gray-50/50">
                        <td class="text-sm text-gray-800">{{ $group->code }}</td>
                        <td class="text-sm text-gray-800">{{ $group->name }}</td>
                        <td>
                            <span class="badge {{ $group->type === 'asset' ? 'badge-income' : ($group->type === 'liability' ? 'badge-bank' : ($group->type === 'equity' ? 'badge-role-admin' : ($group->type === 'income' ? 'badge-cash' : 'badge-expense'))) }}">
                                {{ $group->type === 'asset' ? 'Aset' : ($group->type === 'liability' ? 'Kewajiban' : ($group->type === 'equity' ? 'Ekuitas' : ($group->type === 'income' ? 'Pendapatan' : 'Beban'))) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $group->normal_balance === 'debit' ? 'badge-income' : 'badge-expense' }}">
                                {{ $group->normal_balance === 'debit' ? 'Debit' : 'Kredit' }}
                            </span>
                        </td>
                        <td class="text-right text-sm font-semibold text-gray-800">Rp {{ number_format($group->balance ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('coa.edit', $group) }}" class="btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('coa.destroy', $group) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus Akun', 'Yakin ingin menghapus akun ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @foreach($group->children as $child)
                    <tr class="hover:bg-orange-50/30 transition-colors">
                        <td class="text-sm text-gray-500 pl-10">{{ $child->code }}</td>
                        <td class="text-sm text-gray-700 pl-10">{{ $child->name }}</td>
                        <td>
                            <span class="badge {{ $child->type === 'asset' ? 'badge-income' : ($child->type === 'liability' ? 'badge-bank' : ($child->type === 'equity' ? 'badge-role-admin' : ($child->type === 'income' ? 'badge-cash' : 'badge-expense'))) }}">
                                {{ $child->type === 'asset' ? 'Aset' : ($child->type === 'liability' ? 'Kewajiban' : ($child->type === 'equity' ? 'Ekuitas' : ($child->type === 'income' ? 'Pendapatan' : 'Beban'))) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $child->normal_balance === 'debit' ? 'badge-income' : 'badge-expense' }}">
                                {{ $child->normal_balance === 'debit' ? 'Debit' : 'Kredit' }}
                            </span>
                        </td>
                        <td class="text-right text-sm text-gray-700">Rp {{ number_format($child->balance ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('coa.edit', $child) }}" class="btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('coa.destroy', $child) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus Akun', 'Yakin ingin menghapus akun ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Akun</div>
            <div class="empty-state-desc">Buat akun pertama untuk memulai chart of account.</div>
            <a href="{{ route('coa.create') }}" class="btn-primary mt-4">Buat Akun</a>
        </div>
        @endif
    </div>
</div>
@endsection
