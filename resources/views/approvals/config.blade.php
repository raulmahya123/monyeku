@extends('layouts.main')

@section('title', 'Konfigurasi Approval')

@php
$companyUsers = Auth::user()->companies()->find(Auth::user()->current_company_id)?->users ?? collect();
$userRoles = ['admin', 'owner'];
@endphp

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('approvals.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 text-gray-400 hover:text-orange-500 hover:border-orange-200 hover:bg-orange-50 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 class="text-lg font-bold text-gray-800">Konfigurasi Approval</h2>
        <p class="text-xs text-gray-400">Atur aturan persetujuan berdasarkan tipe, kategori, dan nominal</p>
    </div>
</div>

{{-- Type Tabs --}}
@php
$types = [
    'transaction' => ['label' => 'Transaksi', 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>'],
    'invoice' => ['label' => 'Invoice', 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>'],
    'debt' => ['label' => 'Hutang/Piutang', 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'],
    'budget' => ['label' => 'Anggaran', 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'],
];
@endphp
<div class="flex gap-1 p-1 mb-6 bg-gray-100 rounded-xl overflow-x-auto">
    @foreach($types as $key => $t)
    <a href="{{ route('approvals.config', ['type' => $key]) }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium whitespace-nowrap transition-all
       {{ $type === $key ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
        {!! $t['icon'] !!}
        {{ $t['label'] }}
    </a>
    @endforeach
</div>

{{-- Add Rule Form --}}
<div class="card mb-6">
    <div class="card-header">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <h3 class="card-title">Tambah Aturan {{ $types[$type]['label'] ?? 'Approval' }}</h3>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('approvals.config.store') }}" x-data="{
            l2: false, l3: false, mode: 'sequential',
            effectiveFrom: '', effectiveUntil: '',
            hasL2Min: false, l2MinAmount: '',
            hasL3Min: false, l3MinAmount: '',
        }">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Kategori</label>
                    <select name="category_id" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Min. Nominal</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                        <input type="number" name="min_amount" value="0" min="0" class="w-full pl-9 pr-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Maks. Nominal</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                        <input type="number" name="max_amount" min="0" class="w-full pl-9 pr-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all" placeholder="Tanpa batas">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Deadline (jam)</label>
                    <input type="number" name="deadline_hours" value="72" min="1" max="720" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all" placeholder="Default: 72">
                </div>
            </div>

            {{-- Advanced Options Collapse --}}
            <div class="mt-4" x-data="{ advOpen: false }">
                <button type="button" @@click="advOpen = !advOpen" class="inline-flex items-center gap-2 text-sm font-medium text-orange-600 hover:text-orange-700 transition-colors">
                    <svg class="w-4 h-4 transition-transform" :class="advOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    Opsi Lanjutan
                </button>
                <div x-show="advOpen" x-cloak x-collapse class="mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-xl">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Mode Approval</label>
                            <select name="approval_mode" x-model="mode" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                                <option value="sequential">Sequential (semua wajib)</option>
                                <option value="parallel">Parallel (siapa cepat)</option>
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Parallel: cukup 1 approval per level</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tugaskan ke User</label>
                            <select name="assigned_to" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                                <option value="">Semua dengan Role sesuai</option>
                                @foreach($companyUsers as $cu)
                                <option value="{{ $cu->id }}">{{ $cu->name }} ({{ $cu->pivot->role ?? $cu->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Skip Role</label>
                            <select name="skip_role" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                                <option value="">Jangan skip</option>
                                @foreach($userRoles as $ur)
                                <option value="{{ $ur }}">{{ ucfirst($ur) }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">User dengan role ini auto-approved</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tgl. Aktif</label>
                            <input type="date" name="effective_from" x-model="effectiveFrom" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tgl. Berakhir</label>
                            <input type="date" name="effective_until" x-model="effectiveUntil" x-bind:min="effectiveFrom" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 mt-5">
                                <input type="checkbox" x-model="hasL2Min" class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                                <span class="text-xs font-semibold text-gray-600">Min nominal Level 2</span>
                            </label>
                            <div x-show="hasL2Min" x-cloak>
                                <div class="relative mt-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                    <input type="number" name="level_2_min_amount" x-model="l2MinAmount" min="0" class="w-full pl-9 pr-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="flex items-center gap-2 mt-5">
                                <input type="checkbox" x-model="hasL3Min" class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                                <span class="text-xs font-semibold text-gray-600">Min nominal Level 3</span>
                            </label>
                            <div x-show="hasL3Min" x-cloak>
                                <div class="relative mt-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                    <input type="number" name="level_3_min_amount" x-model="l3MinAmount" min="0" class="w-full pl-9 pr-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Level Approval</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 border border-orange-200 bg-orange-50/50 rounded-xl">
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="requires_level_1" value="1" checked disabled class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Level 1 <span class="text-xs text-orange-500 font-normal">(wajib)</span></p>
                                <select name="level_1_role" class="mt-2 w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                                    <option value="admin">Admin</option>
                                    <option value="owner">Owner</option>
                                </select>
                            </div>
                        </label>
                        <input type="hidden" name="requires_level_1" value="1">
                    </div>

                    <div class="p-4 border border-gray-200 rounded-xl" :class="l2 ? 'border-orange-200 bg-orange-50/50' : ''">
                        <label class="flex items-start gap-3">
                            <input type="checkbox" name="requires_level_2" value="1" x-model="l2" class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400 mt-0.5">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Level 2</p>
                                <p class="text-xs text-gray-500 mt-0.5">Opsional</p>
                                <select name="level_2_role" x-bind:disabled="!l2" class="mt-2 w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all" x-bind:class="!l2 ? 'opacity-50' : ''">
                                    <option value="admin">Admin</option>
                                    <option value="owner">Owner</option>
                                </select>
                            </div>
                        </label>
                    </div>

                    <div class="p-4 border border-gray-200 rounded-xl" :class="l3 ? 'border-orange-200 bg-orange-50/50' : ''">
                        <label class="flex items-start gap-3">
                            <input type="checkbox" name="requires_level_3" value="1" x-model="l3" class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400 mt-0.5">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Level 3</p>
                                <p class="text-xs text-gray-500 mt-0.5">Opsional</p>
                                <select name="level_3_role" x-bind:disabled="!l3" class="mt-2 w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all" x-bind:class="!l3 ? 'opacity-50' : ''">
                                    <option value="admin">Admin</option>
                                    <option value="owner">Owner</option>
                                </select>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-5 pt-5 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Aturan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Rules List --}}
<div class="card">
    <div class="card-header">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <h3 class="card-title">Daftar Aturan {{ $types[$type]['label'] ?? '' }}</h3>
        </div>
        <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full">{{ $configs->count() }} aturan</span>
    </div>

    <div>
        @if($configs->count() > 0)
            @foreach($configs as $c)
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50 last:border-b-0 hover:bg-orange-50/30 transition-colors">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="text-sm font-semibold text-gray-900">{{ $c->category?->name ?? 'Semua Kategori' }}</p>
                        <span class="text-xs text-gray-400">—</span>
                        <span class="text-sm font-medium text-orange-600">Rp {{ number_format($c->min_amount, 0, ',', '.') }}</span>
                        @if($c->max_amount)
                        <span class="text-xs text-gray-400">s.d.</span>
                        <span class="text-sm font-medium text-orange-600">Rp {{ number_format($c->max_amount, 0, ',', '.') }}</span>
                        @else
                        <span class="text-xs text-gray-400">+</span>
                        @endif
                        @if($c->deadline_hours)
                        <span class="text-xs text-gray-400 ml-1">· {{ $c->deadline_hours }} jam</span>
                        @endif
                        @if($c->approval_mode === 'parallel')
                        <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold bg-amber-100 text-amber-700 rounded">Parallel</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                        @foreach($c->levels as $lvl)
                        @php
                            $role = $c->getRoleForLevel($lvl);
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold bg-orange-100 text-orange-700 rounded">
                            L{{ $lvl }}
                            <span class="text-orange-500 font-medium">{{ $role }}</span>
                        </span>
                        @endforeach
                        @if($c->assigned_to)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold bg-purple-100 text-purple-700 rounded">
                            @ {{ $c->assignedUser?->name ?? 'User #'.$c->assigned_to }}
                        </span>
                        @endif
                        @if($c->skip_role)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold bg-gray-100 text-gray-600 rounded">
                            Skip: {{ ucfirst($c->skip_role) }}
                        </span>
                        @endif
                        @if($c->effective_from || $c->effective_until)
                        <span class="text-[10px] text-gray-400">
                            @if($c->effective_from) {{ \Carbon\Carbon::parse($c->effective_from)->format('d/m/Y') }} @endif
                            @if($c->effective_from && $c->effective_until) — @endif
                            @if($c->effective_until) {{ \Carbon\Carbon::parse($c->effective_until)->format('d/m/Y') }} @endif
                        </span>
                        @endif
                    </div>
                </div>
                <form action="{{ route('approvals.config.destroy', $c) }}" method="POST" class="shrink-0 ml-3">
                    @csrf @method('DELETE')
                    <button type="button" @click="$store.confirm.ask('Hapus Aturan', 'Hapus aturan approval ini?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-red-200 text-red-500 hover:bg-red-50 hover:text-red-600 text-xs font-medium rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus
                    </button>
                </form>
            </div>
            @endforeach
        @else
            <div class="flex flex-col items-center justify-center py-12 px-5">
                <div class="w-14 h-14 bg-gray-50 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-800">Belum Ada Aturan</p>
                <p class="text-xs text-gray-400 mt-1 text-center max-w-xs">Tidak ada aturan approval untuk {{ strtolower($types[$type]['label']) }}. Semua {{ strtolower($types[$type]['label']) }} akan langsung tervalidasi.</p>
            </div>
        @endif
    </div>
</div>
@endsection
