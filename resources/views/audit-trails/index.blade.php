@extends('layouts.main')

@section('title', 'Audit Trail')

@section('subtitle')
    Riwayat perubahan data dalam sistem. Setiap perubahan tercatat untuk keperluan audit.
@endsection

@section('content')
<div class="card mb-5">
    <div class="card-body">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <select name="event" class="form-input">
                    <option value="">Semua Event</option>
                    @foreach($events as $ev)
                    <option value="{{ $ev }}" {{ request('event') === $ev ? 'selected' : '' }}>{{ ucfirst($ev) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="date" name="start_date" class="form-input" value="{{ request('start_date') }}" placeholder="Dari tanggal">
            </div>
            <div>
                <input type="date" name="end_date" class="form-input" value="{{ request('end_date') }}" placeholder="Sampai tanggal">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary btn-sm flex-1">Filter</button>
                <a href="{{ route('audit-trails.index') }}" class="btn-ghost btn-sm">Reset</a>
                <a href="{{ route('audit-trails.export-csv') }}" class="btn-secondary btn-sm">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($trails->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Tipe</th>
                        <th>ID</th>
                        <th>Detail</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trails as $trail)
                    <tr>
                        <td class="text-sm text-gray-600 whitespace-nowrap">{{ $trail->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-sm font-medium text-gray-800">{{ $trail->user?->name ?? 'System' }}</td>
                        <td>
                            @php
                                $eventStyles = [
                                    'created' => 'badge badge-success',
                                    'updated' => 'badge badge-warning',
                                    'deleted' => 'badge badge-danger',
                                    'approved' => 'badge badge-info',
                                    'rejected' => 'badge badge-danger',
                                    'marked_paid' => 'badge badge-cash',
                                ];
                            @endphp
                            <span class="{{ $eventStyles[$trail->event] ?? 'badge' }}">{{ ucfirst(str_replace('_', ' ', $trail->event)) }}</span>
                        </td>
                        <td class="text-sm text-gray-600">
                            @php
                                $class = explode('\\', $trail->auditable_type);
                                echo end($class);
                            @endphp
                        </td>
                        <td class="text-sm text-gray-600">#{{ $trail->auditable_id }}</td>
                        <td class="text-sm text-gray-600 max-w-xs truncate">
                            @if($trail->event === 'updated' && $trail->new_values)
                                @php $changes = []; @endphp
                                @foreach($trail->new_values as $k => $v)
                                    @if(!in_array($k, ['updated_at']))
                                        @php
                                            $old = $trail->old_values[$k] ?? '-';
                                            if(is_array($old)) $old = json_encode($old);
                                            if(is_array($v)) $v = json_encode($v);
                                        @endphp
                                        @if($old !== $v)
                                            @php $changes[] = "$k: $old → $v"; @endphp
                                        @endif
                                    @endif
                                @endforeach
                                {{ implode(', ', array_slice($changes, 0, 2)) }}
                                @if(count($changes) > 2) ... @endif
                            @elseif($trail->event === 'created' && $trail->new_values)
                                Data baru dibuat
                            @elseif($trail->event === 'deleted')
                                Data dihapus
                            @else
                                {{ ucfirst(str_replace('_', ' ', $trail->event)) }}
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('audit-trails.show', $trail) }}" class="text-orange-600 hover:text-orange-800 text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">
            {{ $trails->links() }}
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Aktivitas</div>
            <div class="empty-state-desc">Audit trail akan tercatat secara otomatis saat ada perubahan data.</div>
        </div>
        @endif
    </div>
</div>
@endsection
