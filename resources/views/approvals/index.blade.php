@extends('layouts.main')

@section('title', 'Approval')

@section('actions')
    <a href="{{ route('approvals.config') }}" class="btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Konfigurasi
    </a>
@endsection

@section('content')
    {{-- Tabs --}}
    <div class="card mb-6">
        <div class="flex gap-1 p-1 bg-gray-50 rounded-xl">
            <a href="{{ route('approvals.index', ['type' => 'all']) }}" class="flex-1 px-4 py-2 text-sm font-medium rounded-lg text-center transition-colors {{ $type === 'all' ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Semua
                @if(array_sum($pendingCount) > 0)
                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded-full">{{ array_sum($pendingCount) }}</span>
                @endif
            </a>
            <a href="{{ route('approvals.index', ['type' => 'transaction']) }}" class="flex-1 px-4 py-2 text-sm font-medium rounded-lg text-center transition-colors {{ $type === 'transaction' ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Transaksi
                @if(($pendingCount['transaction'] ?? 0) > 0)
                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded-full">{{ $pendingCount['transaction'] }}</span>
                @endif
            </a>
            <a href="{{ route('approvals.index', ['type' => 'invoice']) }}" class="flex-1 px-4 py-2 text-sm font-medium rounded-lg text-center transition-colors {{ $type === 'invoice' ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Invoice
                @if(($pendingCount['invoice'] ?? 0) > 0)
                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded-full">{{ $pendingCount['invoice'] }}</span>
                @endif
            </a>
            <a href="{{ route('approvals.index', ['type' => 'debt']) }}" class="flex-1 px-4 py-2 text-sm font-medium rounded-lg text-center transition-colors {{ $type === 'debt' ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Hutang/Piutang
                @if(($pendingCount['debt'] ?? 0) > 0)
                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded-full">{{ $pendingCount['debt'] }}</span>
                @endif
            </a>
            <a href="{{ route('approvals.index', ['type' => 'budget']) }}" class="flex-1 px-4 py-2 text-sm font-medium rounded-lg text-center transition-colors {{ $type === 'budget' ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Anggaran
                @if(($pendingCount['budget'] ?? 0) > 0)
                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded-full">{{ $pendingCount['budget'] }}</span>
                @endif
            </a>
            <a href="{{ route('approvals.index', ['type' => 'stock_opname']) }}" class="flex-1 px-4 py-2 text-sm font-medium rounded-lg text-center transition-colors {{ $type === 'stock_opname' ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Stock Opname
                @if(($pendingCount['stock_opname'] ?? 0) > 0)
                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded-full">{{ $pendingCount['stock_opname'] }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- Pending Transactions --}}
    @if(in_array($type, ['all', 'transaction']))
    <div class="card mb-6">
        <div class="card-header">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                </div>
                <h3 class="card-title">Transaksi</h3>
            </div>
            @if($pendingTransactions->count() > 0)
            <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">{{ $pendingTransactions->total() }} pending</span>
            @endif
        </div>
        <div>
            @if($pendingTransactions->count() > 0)
                @foreach($pendingTransactions as $t)
                <div class="border-b border-gray-50 last:border-b-0" x-data="{ show: false }">
                    <div class="flex items-center justify-between px-5 py-4 hover:bg-orange-50/50 transition-colors cursor-pointer" @click="show = !show">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $t->type === 'income' ? 'bg-emerald-50' : 'bg-red-50' }}">
                                @if($t->type === 'income')
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                @else
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $t->category?->name }}</p>
                                    @php
                                        $pendingLevels = $t->approvals->where('status', 'pending')->pluck('level')->sort();
                                    @endphp
                                    @foreach($pendingLevels as $lvl)
                                    <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded">L{{ $lvl }}</span>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Rp {{ number_format($t->amount, 0, ',', '.') }} &middot; {{ $t->user?->name }} &middot; {{ $t->transaction_date->format('d M Y') }}
                                </p>
                                @if($t->description)<p class="text-xs text-gray-400 mt-0.5 truncate">{{ $t->description }}</p>@endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-3">
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-amber-50 text-amber-600 rounded-full">Pending</span>
                            <svg class="w-4 h-4 text-gray-300 transition-transform" :class="show && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    <div x-show="show" x-cloak x-collapse>
                        <div class="px-5 pb-4 pt-0">
                            <div class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-gray-100">
                                <form action="{{ route('approvals.approve', $t) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="approvable_type" value="{{ get_class($t) }}">
                                    <input type="hidden" name="approvable_id" value="{{ $t->id }}">
                                    <input type="text" name="notes" placeholder="Catatan (opsional)" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 mb-2">
                                    <button type="button" @click="$store.confirm.ask('Setujui Transaksi', 'Setujui transaksi ini?', { confirmText: 'Ya, setujui', confirmClass: 'btn-success', action: () => $el.closest('form').submit() })" class="w-full btn-success btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Setujui
                                    </button>
                                </form>
                                <form action="{{ route('approvals.reject', $t) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="approvable_type" value="{{ get_class($t) }}">
                                    <input type="hidden" name="approvable_id" value="{{ $t->id }}">
                                    <input type="text" name="notes" placeholder="Alasan penolakan*" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-300 mb-2">
                                    <button type="button" @click="$store.confirm.ask('Tolak Transaksi', 'Yakin ingin menolak transaksi ini?', { confirmText: 'Ya, tolak', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="w-full btn-danger btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($pendingTransactions->hasPages())
                <div class="px-5 py-3 border-t border-gray-50">
                    {{ $pendingTransactions->links() }}
                </div>
                @endif
            @else
                <div class="card-body">
                    <div class="flex flex-col items-center justify-center py-8">
                        <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center mb-3">
                            <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-800">Tidak ada transaksi pending</p>
                        <p class="text-xs text-gray-400 mt-1">Semua transaksi sudah diproses.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Pending Invoices --}}
    @if(in_array($type, ['all', 'invoice']))
    <div class="card mb-6">
        <div class="card-header">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <h3 class="card-title">Invoice</h3>
            </div>
            @if($pendingInvoices->count() > 0)
            <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">{{ $pendingInvoices->total() }} pending</span>
            @endif
        </div>
        <div>
            @if($pendingInvoices->count() > 0)
                @foreach($pendingInvoices as $inv)
                <div class="border-b border-gray-50 last:border-b-0" x-data="{ show: false }">
                    <div class="flex items-center justify-between px-5 py-4 hover:bg-orange-50/50 transition-colors cursor-pointer" @click="show = !show">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-blue-50">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $inv->invoice_number }}</p>
                                    @php
                                        $invPendingLevels = $inv->approvals->where('status', 'pending')->pluck('level')->sort();
                                    @endphp
                                    @foreach($invPendingLevels as $lvl)
                                    <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded">L{{ $lvl }}</span>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $inv->customer_name }} &middot; Rp {{ number_format($inv->total, 0, ',', '.') }} &middot; {{ $inv->user?->name }} &middot; {{ $inv->created_at->format('d M Y') }}
                                </p>
                                @if($inv->notes)<p class="text-xs text-gray-400 mt-0.5 truncate">{{ $inv->notes }}</p>@endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-3">
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-amber-50 text-amber-600 rounded-full">Pending</span>
                            <svg class="w-4 h-4 text-gray-300 transition-transform" :class="show && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    <div x-show="show" x-cloak x-collapse>
                        <div class="px-5 pb-4 pt-0">
                            <div class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-gray-100">
                                <form action="{{ route('approvals.approve', $inv) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="approvable_type" value="{{ get_class($inv) }}">
                                    <input type="hidden" name="approvable_id" value="{{ $inv->id }}">
                                    <input type="text" name="notes" placeholder="Catatan (opsional)" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 mb-2">
                                    <button type="button" @click="$store.confirm.ask('Setujui Invoice', 'Setujui invoice ini?', { confirmText: 'Ya, setujui', confirmClass: 'btn-success', action: () => $el.closest('form').submit() })" class="w-full btn-success btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Setujui
                                    </button>
                                </form>
                                <form action="{{ route('approvals.reject', $inv) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="approvable_type" value="{{ get_class($inv) }}">
                                    <input type="hidden" name="approvable_id" value="{{ $inv->id }}">
                                    <input type="text" name="notes" placeholder="Alasan penolakan*" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-300 mb-2">
                                    <button type="button" @click="$store.confirm.ask('Tolak Invoice', 'Yakin ingin menolak invoice ini?', { confirmText: 'Ya, tolak', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="w-full btn-danger btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($pendingInvoices->hasPages())
                <div class="px-5 py-3 border-t border-gray-50">
                    {{ $pendingInvoices->links() }}
                </div>
                @endif
            @else
                <div class="card-body">
                    <div class="flex flex-col items-center justify-center py-8">
                        <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center mb-3">
                            <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-800">Tidak ada invoice pending</p>
                        <p class="text-xs text-gray-400 mt-1">Semua invoice sudah diproses.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Pending Debts --}}
    @if(in_array($type, ['all', 'debt']))
    <div class="card mb-6">
        <div class="card-header">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m0 0v9m0 0h-2.25m0 0h-2.25"/></svg>
                </div>
                <h3 class="card-title">Hutang/Piutang</h3>
            </div>
            @if($pendingDebts->count() > 0)
            <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">{{ $pendingDebts->total() }} pending</span>
            @endif
        </div>
        <div>
            @if($pendingDebts->count() > 0)
                @foreach($pendingDebts as $d)
                <div class="border-b border-gray-50 last:border-b-0" x-data="{ show: false }">
                    <div class="flex items-center justify-between px-5 py-4 hover:bg-orange-50/50 transition-colors cursor-pointer" @click="show = !show">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $d->type === 'receivable' ? 'bg-emerald-50' : 'bg-red-50' }}">
                                @if($d->type === 'receivable')
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $d->contact_name }}</p>
                                    @php
                                        $debtPendingLevels = $d->approvals->where('status', 'pending')->pluck('level')->sort();
                                    @endphp
                                    @foreach($debtPendingLevels as $lvl)
                                    <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded">L{{ $lvl }}</span>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Rp {{ number_format($d->amount, 0, ',', '.') }} &middot; {{ $d->type === 'receivable' ? 'Piutang' : 'Hutang' }}
                                </p>
                                @if($d->description)<p class="text-xs text-gray-400 mt-0.5 truncate">{{ $d->description }}</p>@endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-3">
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-amber-50 text-amber-600 rounded-full">Pending</span>
                            <svg class="w-4 h-4 text-gray-300 transition-transform" :class="show && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    <div x-show="show" x-cloak x-collapse>
                        <div class="px-5 pb-4 pt-0">
                            <div class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-gray-100">
                                <form action="{{ route('approvals.approve', $d) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="approvable_type" value="{{ get_class($d) }}">
                                    <input type="hidden" name="approvable_id" value="{{ $d->id }}">
                                    <input type="text" name="notes" placeholder="Catatan (opsional)" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 mb-2">
                                    <button type="button" @click="$store.confirm.ask('Setujui Hutang/Piutang', 'Setujui hutang/piutang ini?', { confirmText: 'Ya, setujui', confirmClass: 'btn-success', action: () => $el.closest('form').submit() })" class="w-full btn-success btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Setujui
                                    </button>
                                </form>
                                <form action="{{ route('approvals.reject', $d) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="approvable_type" value="{{ get_class($d) }}">
                                    <input type="hidden" name="approvable_id" value="{{ $d->id }}">
                                    <input type="text" name="notes" placeholder="Alasan penolakan*" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-300 mb-2">
                                    <button type="button" @click="$store.confirm.ask('Tolak Hutang/Piutang', 'Yakin ingin menolak hutang/piutang ini?', { confirmText: 'Ya, tolak', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="w-full btn-danger btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($pendingDebts->hasPages())
                <div class="px-5 py-3 border-t border-gray-50">
                    {{ $pendingDebts->links() }}
                </div>
                @endif
            @else
                <div class="card-body">
                    <div class="flex flex-col items-center justify-center py-8">
                        <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center mb-3">
                            <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-800">Tidak ada hutang/piutang pending</p>
                        <p class="text-xs text-gray-400 mt-1">Semua hutang/piutang sudah diproses.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Pending Stock Opnames --}}
    @if(in_array($type, ['all', 'stock_opname']))
    <div class="card mb-6">
        <div class="card-header">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <h3 class="card-title">Stock Opname</h3>
            </div>
            @if($pendingStockOpnames->count() > 0)
            <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">{{ $pendingStockOpnames->total() }} pending</span>
            @endif
        </div>
        <div>
            @if($pendingStockOpnames->count() > 0)
                @foreach($pendingStockOpnames as $so)
                <div class="border-b border-gray-50 last:border-b-0" x-data="{ show: false }">
                    <div class="flex items-center justify-between px-5 py-4 hover:bg-orange-50/50 transition-colors cursor-pointer" @click="show = !show">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-indigo-50">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $so->warehouse?->name ?? '-' }}</p>
                                    @php
                                        $soPendingLevels = $so->approvals->where('status', 'pending')->pluck('level')->sort();
                                    @endphp
                                    @foreach($soPendingLevels as $lvl)
                                    <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded">L{{ $lvl }}</span>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $so->createdBy?->name }} &middot; {{ optional($so->opname_date)->format('d M Y') }}
                                </p>
                                @if($so->notes)<p class="text-xs text-gray-400 mt-0.5 truncate">{{ $so->notes }}</p>@endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-3">
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-amber-50 text-amber-600 rounded-full">Pending</span>
                            <svg class="w-4 h-4 text-gray-300 transition-transform" :class="show && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    <div x-show="show" x-cloak x-collapse>
                        <div class="px-5 pb-4 pt-0">
                            <div class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-gray-100">
                                <form action="{{ route('approvals.approve', $so) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="approvable_type" value="{{ get_class($so) }}">
                                    <input type="hidden" name="approvable_id" value="{{ $so->id }}">
                                    <input type="text" name="notes" placeholder="Catatan (opsional)" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 mb-2">
                                    <button type="button" @click="$store.confirm.ask('Setujui Stock Opname', 'Setujui stock opname ini?', { confirmText: 'Ya, setujui', confirmClass: 'btn-success', action: () => $el.closest('form').submit() })" class="w-full btn-success btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Setujui
                                    </button>
                                </form>
                                <form action="{{ route('approvals.reject', $so) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="approvable_type" value="{{ get_class($so) }}">
                                    <input type="hidden" name="approvable_id" value="{{ $so->id }}">
                                    <input type="text" name="notes" placeholder="Alasan penolakan*" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-300 mb-2">
                                    <button type="button" @click="$store.confirm.ask('Tolak Stock Opname', 'Yakin ingin menolak stock opname ini?', { confirmText: 'Ya, tolak', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="w-full btn-danger btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($pendingStockOpnames->hasPages())
                <div class="px-5 py-3 border-t border-gray-50">
                    {{ $pendingStockOpnames->links() }}
                </div>
                @endif
            @else
                <div class="card-body">
                    <div class="flex flex-col items-center justify-center py-8">
                        <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center mb-3">
                            <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-800">Tidak ada stock opname pending</p>
                        <p class="text-xs text-gray-400 mt-1">Semua stock opname sudah diproses.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Pending Budgets --}}
    @if(in_array($type, ['all', 'budget']))
    <div class="card mb-6">
        <div class="card-header">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="card-title">Anggaran</h3>
            </div>
            @if($pendingBudgets->count() > 0)
            <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">{{ $pendingBudgets->total() }} pending</span>
            @endif
        </div>
        <div>
            @if($pendingBudgets->count() > 0)
                @foreach($pendingBudgets as $b)
                <div class="border-b border-gray-50 last:border-b-0" x-data="{ show: false }">
                    <div class="flex items-center justify-between px-5 py-4 hover:bg-orange-50/50 transition-colors cursor-pointer" @click="show = !show">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $b->category?->name ?? 'Semua Kategori' }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs font-medium text-gray-500">{{ $b->period === 'monthly' ? 'Bulanan' : 'Tahunan' }}</span>
                                    <span class="text-xs text-gray-300">|</span>
                                    <span class="text-xs font-semibold text-gray-600">
                                        Rp {{ number_format($b->amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 ml-3">
                            @php
                                $levels = $b->approvals->where('status', 'pending')->pluck('level')->unique()->sort()->map(fn($l) => 'L' . $l)->implode(', ');
                            @endphp
                            @if($levels)
                            <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">{{ $levels }}</span>
                            @endif
                            <svg class="w-4 h-4 text-gray-300 transition-transform" :class="show && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    <div x-show="show" x-cloak x-collapse>
                        <div class="px-5 pb-4 pt-0">
                            <div class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-gray-100">
                                <form action="{{ route('approvals.approve', $b) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="approvable_type" value="{{ get_class($b) }}">
                                    <input type="hidden" name="approvable_id" value="{{ $b->id }}">
                                    <input type="text" name="notes" placeholder="Catatan (opsional)" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 mb-2">
                                    <button type="button" @click="$store.confirm.ask('Setujui Anggaran', 'Setujui anggaran ini?', { confirmText: 'Ya, setujui', confirmClass: 'btn-success', action: () => $el.closest('form').submit() })" class="w-full btn-success btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Setujui
                                    </button>
                                </form>
                                <form action="{{ route('approvals.reject', $b) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="approvable_type" value="{{ get_class($b) }}">
                                    <input type="hidden" name="approvable_id" value="{{ $b->id }}">
                                    <input type="text" name="notes" placeholder="Alasan penolakan*" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-300 mb-2">
                                    <button type="button" @click="$store.confirm.ask('Tolak Anggaran', 'Yakin ingin menolak anggaran ini?', { confirmText: 'Ya, tolak', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="w-full btn-danger btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($pendingBudgets->hasPages())
                <div class="px-5 py-3 border-t border-gray-50">
                    {{ $pendingBudgets->links() }}
                </div>
                @endif
            @else
                <div class="card-body">
                    <div class="flex flex-col items-center justify-center py-8">
                        <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center mb-3">
                            <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-800">Tidak ada anggaran pending</p>
                        <p class="text-xs text-gray-400 mt-1">Semua anggaran sudah diproses.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- History --}}
    @if($history->count() > 0)
    <div class="card">
        <div class="card-header">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="card-title">Riwayat Approval</h3>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Approver</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Catatan</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($history as $item)
                        @php $itemClass = class_basename($item); @endphp
                        @foreach($item->approvals as $a)
                        <tr class="hover:bg-orange-50/30 transition-colors">
                            <td class="px-5 py-3">
                                @if($item instanceof \App\Models\Transaction)
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-purple-50 text-purple-600 rounded-full">Transaksi</span>
                                @elseif($item instanceof \App\Models\Invoice)
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-blue-50 text-blue-600 rounded-full">Invoice</span>
                                @elseif($item instanceof \App\Models\Debt)
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-amber-50 text-amber-600 rounded-full">Hutang/Piutang</span>
                                @elseif($item instanceof \App\Models\Budget)
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-600 rounded-full">Anggaran</span>
                                @elseif($item instanceof \App\Models\StockOpname)
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-indigo-50 text-indigo-600 rounded-full">Stock Opname</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 font-medium text-gray-900">
                                @if($item instanceof \App\Models\Transaction)
                                    {{ $item->category?->name ?? '-' }}
                                @elseif($item instanceof \App\Models\Invoice)
                                    {{ $item->invoice_number }}
                                @elseif($item instanceof \App\Models\Debt)
                                    {{ $item->contact_name }}
                                @elseif($item instanceof \App\Models\Budget)
                                    {{ $item->category?->name ?? 'Semua Kategori' }}
                                @elseif($item instanceof \App\Models\StockOpname)
                                    {{ $item->warehouse?->name ?? '-' }}
                                @endif
                            </td>
                            <td class="px-5 py-3 font-medium text-gray-700">
                                @if($item instanceof \App\Models\Transaction)
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                @elseif($item instanceof \App\Models\Invoice)
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                @elseif($item instanceof \App\Models\Debt)
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                @elseif($item instanceof \App\Models\Budget)
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                @elseif($item instanceof \App\Models\StockOpname)
                                    -
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $a->approver?->name ?? '-' }}</td>
                            <td class="px-5 py-3">
                                @if($a->status === 'approved')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-600 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                    Disetujui
                                </span>
                                @elseif($a->status === 'pending')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-amber-50 text-amber-600 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                    Pending
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-red-50 text-red-600 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                    Ditolak
                                </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-400 max-w-[160px] truncate">{{ $a->notes ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-400 text-xs">{{ $a->approved_at ? \Carbon\Carbon::parse($a->approved_at)->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection
