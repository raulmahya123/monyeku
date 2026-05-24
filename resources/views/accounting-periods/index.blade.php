@extends('layouts.main')

@section('title', 'Periode Akuntansi')

@section('subtitle')
    Kelola periode akuntansi perusahaan. Periode yang ditutup tidak dapat diubah transaksinya.
@endsection

@section('actions')
    <a href="{{ route('accounting-periods.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Periode Baru
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($periods->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Periode</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Akhir</th>
                        <th>Status</th>
                        <th>Ditutup Oleh</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($periods as $period)
                    <tr>
                        <td class="text-sm font-medium text-gray-800">{{ $period->name }}</td>
                        <td class="text-sm text-gray-600">{{ $period->start_date->format('d/m/Y') }}</td>
                        <td class="text-sm text-gray-600">{{ $period->end_date->format('d/m/Y') }}</td>
                        <td>
                            @if($period->is_closed)
                                <span class="badge badge-danger">Ditutup</span>
                            @else
                                <span class="badge badge-success">Terbuka</span>
                            @endif
                        </td>
                        <td class="text-sm text-gray-600">
                            @if($period->closed_by && $period->closedBy)
                                {{ $period->closedBy->name }}<br>
                                <span class="text-xs text-gray-400">{{ $period->closed_at?->format('d/m/Y H:i') }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex gap-1 justify-end">
                                @if(!$period->is_closed)
                                <form action="{{ route('accounting-periods.close', $period) }}" method="POST">
                                    @csrf
                                    <button type="button" @click="$store.confirm.ask('Tutup Periode', 'Yakin ingin menutup periode {{ $period->name }}? Transaksi dalam periode ini tidak bisa diubah lagi.', { confirmText: 'Ya, tutup', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-ghost btn-sm text-amber-600 hover:bg-amber-50">Tutup</button>
                                </form>
                                @else
                                <form action="{{ route('accounting-periods.open', $period) }}" method="POST">
                                    @csrf
                                    <button type="button" @click="$store.confirm.ask('Buka Periode', 'Yakin ingin membuka kembali periode {{ $period->name }}?', { confirmText: 'Ya, buka', confirmClass: 'btn-success', action: () => $el.closest('form').submit() })" class="btn-ghost btn-sm text-emerald-600 hover:bg-emerald-50">Buka</button>
                                </form>
                                @endif
                                <form action="{{ route('accounting-periods.destroy', $period) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" @click="$store.confirm.ask('Hapus Periode', 'Yakin ingin menghapus periode {{ $period->name }}?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">
            {{ $periods->links() }}
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Periode</div>
            <div class="empty-state-desc">Buat periode akuntansi pertama untuk mulai mengatur siklus akuntansi.</div>
            <a href="{{ route('accounting-periods.create') }}" class="btn-primary mt-4">Buat Periode</a>
        </div>
        @endif
    </div>
</div>
@endsection
