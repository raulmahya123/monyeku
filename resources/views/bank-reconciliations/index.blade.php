@extends('layouts.main')

@section('title', 'Rekonsiliasi Bank')

@section('subtitle')
    Cocokkan saldo bank Anda dengan catatan sistem untuk memastikan keakuratan data keuangan.
@endsection

@section('actions')
    <a href="{{ route('bank-reconciliations.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Rekonsiliasi Baru
    </a>
    <a href="{{ route('bank-accounts.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 3h6m-3-3h3"/></svg>
        Rekening Bank
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-blue-100">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 3h6m-3-3h3"/></svg>
            </div>
            <div>
                <p class="stat-label">Total Rekening</p>
                <p class="stat-value text-blue-700">{{ $accounts->count() }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-emerald-100">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="stat-label">Selesai</p>
                <p class="stat-value text-emerald-700">{{ $reconciliations->where('status', 'completed')->count() }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-amber-100">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="stat-label">Draft</p>
                <p class="stat-value text-amber-700">{{ $reconciliations->where('status', 'draft')->count() }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-violet-100">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="stat-label">Selisih</p>
                <p class="stat-value text-violet-700">Rp {{ number_format($reconciliations->where('status', 'draft')->sum('difference'), 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($reconciliations->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Rekening Bank</th>
                        <th>Saldo Bank</th>
                        <th>Saldo Sistem</th>
                        <th>Selisih</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reconciliations as $rec)
                    <tr>
                        <td class="text-sm font-medium text-gray-800">{{ $rec->period }}</td>
                        <td class="text-sm text-gray-600">{{ $rec->bankAccount?->bank_name }} - {{ $rec->bankAccount?->account_number }}</td>
                        <td class="text-sm text-gray-800 font-medium">Rp {{ number_format($rec->closing_balance, 0, ',', '.') }}</td>
                        <td class="text-sm text-gray-800 font-medium">Rp {{ number_format($rec->system_balance, 0, ',', '.') }}</td>
                        <td class="text-sm font-medium {{ abs($rec->difference) < 1 ? 'text-emerald-600' : 'text-red-600' }}">
                            Rp {{ number_format($rec->difference, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($rec->status === 'completed')
                                <span class="badge badge-success">Selesai</span>
                            @else
                                <span class="badge badge-warning">Draft</span>
                            @endif
                        </td>
                        <td class="text-sm text-gray-500">{{ $rec->creator?->name }}</td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('bank-reconciliations.show', $rec) }}" class="btn-ghost btn-sm">Detail</a>
                                @if($rec->status === 'draft')
                                <form action="{{ route('bank-reconciliations.complete', $rec) }}" method="POST">
                                    @csrf
                                    <button type="button" @click="$store.confirm.ask('Kompletasi Rekonsiliasi', 'Tandai rekonsiliasi ini sebagai selesai?', { confirmText: 'Ya, selesai', confirmClass: 'btn-success', action: () => $el.closest('form').submit() })" class="btn-ghost btn-sm text-emerald-600 hover:bg-emerald-50">Selesai</button>
                                </form>
                                <form action="{{ route('bank-reconciliations.destroy', $rec) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus Rekonsiliasi', 'Yakin ingin menghapus?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">
            {{ $reconciliations->links() }}
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 3h6m-3-3h3"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Rekonsiliasi</div>
            <div class="empty-state-desc">Buat rekonsiliasi bank pertama dengan mengimpor mutasi bank Anda.</div>
            <a href="{{ route('bank-reconciliations.create') }}" class="btn-primary mt-4">Rekonsiliasi Baru</a>
        </div>
        @endif
    </div>
</div>
@endsection
