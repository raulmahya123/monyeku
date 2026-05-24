@extends('layouts.main')

@section('title', 'Transaksi Berulang')

@section('actions')
    <a href="{{ route('recurring.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Baru
    </a>
@endsection

@section('content')
    @if($recurrings->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($recurrings as $r)
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:border-orange-200 hover:shadow-sm transition-all">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $r->type === 'income' ? 'bg-emerald-100' : 'bg-red-100' }}">
                        @if($r->type === 'income')
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        @else
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $r->category?->name }}</h3>
                        <span class="text-xs text-gray-400">{{ ucfirst($r->frequency) }}</span>
                    </div>
                </div>
                @if($r->is_active)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                    Aktif
                </span>
                @else
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>
                @endif
            </div>

            <div class="text-lg font-bold {{ $r->type === 'income' ? 'text-emerald-600' : 'text-red-600' }} mb-3">
                Rp {{ number_format($r->amount, 0, ',', '.') }}
            </div>

            @if($r->description)
            <p class="text-xs text-gray-500 mb-2">{{ Str::limit($r->description, 60) }}</p>
            @endif

            <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 mb-4">
                <div class="bg-gray-50 rounded-lg p-2.5">
                    <span class="text-gray-400">Hari Eksekusi</span>
                    <p class="font-medium text-gray-700 mt-0.5">{{ $r->day_of_month ?? '-' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-2.5">
                    <span class="text-gray-400">Jatuh Tempo</span>
                    <p class="font-medium text-gray-700 mt-0.5">{{ $r->next_due_date?->format('d M Y') ?? '-' }}</p>
                </div>
            </div>

            <div class="flex gap-2 pt-3 border-t border-gray-100">
                <form action="{{ route('recurring.toggle', $r) }}" method="POST" class="inline">
                    @csrf
                    <button type="button" @click="$store.confirm.ask('Ubah Status', 'Ubah status transaksi berulang ini?', { confirmText: 'Ya, ubah', confirmClass: 'btn-warning', action: () => $el.closest('form').submit() })" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 hover:border-orange-300 transition-colors">
                        {{ $r->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
                <form action="{{ route('recurring.destroy', $r) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="button" @click="$store.confirm.ask('Hapus Transaksi Berulang', 'Hapus transaksi berulang ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-red-200 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors">Hapus</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-orange-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum ada transaksi berulang</h3>
        <p class="text-sm text-gray-500 mb-6">Atur transaksi otomatis untuk sewa, gaji, langganan.</p>
        <a href="{{ route('recurring.create') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors">Buat Transaksi Berulang</a>
    </div>
    @endif
@endsection
