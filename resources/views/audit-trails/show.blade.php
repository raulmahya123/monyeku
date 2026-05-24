@extends('layouts.main')

@section('title', 'Detail Audit Trail')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('audit-trails.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-orange-600 mb-4">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    <div class="card">
        <div class="card-header">
            <h3 class="text-base font-semibold text-gray-800">Detail Audit Trail</h3>
        </div>
        <div class="card-body">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wider">Waktu</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800">{{ $auditTrail->created_at->format('d/m/Y H:i:s') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wider">User</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800">{{ $auditTrail->user?->name ?? 'System' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wider">Event</dt>
                    <dd class="mt-1">
                        @php
                        $eventLabels = ['created' => 'Dibuat', 'updated' => 'Diubah', 'deleted' => 'Dihapus'];
                        $eventColors = ['created' => 'badge-success', 'updated' => 'badge-warning', 'deleted' => 'badge-danger'];
                        @endphp
                        <span class="badge {{ $eventColors[$auditTrail->event] ?? 'badge-info' }}">{{ $eventLabels[$auditTrail->event] ?? $auditTrail->event }}</span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wider">IP Address</dt>
                    <dd class="mt-1 text-sm font-mono text-gray-600">{{ $auditTrail->ip_address ?? '-' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wider">Tipe</dt>
                    <dd class="mt-1 text-sm text-gray-800">{{ str_replace('App\Models\\', '', $auditTrail->auditable_type) }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium text-gray-400 uppercase tracking-wider">ID Referensi</dt>
                    <dd class="mt-1 text-sm font-mono text-gray-600">{{ $auditTrail->auditable_id }}</dd>
                </div>
            </dl>

            @if($auditTrail->old_values || $auditTrail->new_values)
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                @if($auditTrail->old_values)
                <div>
                    <h4 class="text-xs font-medium text-red-500 uppercase tracking-wider mb-2">Nilai Lama</h4>
                    <div class="bg-red-50 rounded-xl p-4 text-sm text-red-800 font-mono whitespace-pre-wrap break-words">
                        {{ json_encode($auditTrail->old_values, JSON_PRETTY_PRINT) }}
                    </div>
                </div>
                @endif
                @if($auditTrail->new_values)
                <div>
                    <h4 class="text-xs font-medium text-emerald-500 uppercase tracking-wider mb-2">Nilai Baru</h4>
                    <div class="bg-emerald-50 rounded-xl p-4 text-sm text-emerald-800 font-mono whitespace-pre-wrap break-words">
                        {{ json_encode($auditTrail->new_values, JSON_PRETTY_PRINT) }}
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
