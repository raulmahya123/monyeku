@extends('layouts.main')

@section('title', 'Quotation')

@section('subtitle')
    Kelola penawaran harga untuk pelanggan.
@endsection

@section('actions')
        <button class="btn-primary btn-sm" disabled>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Quotation
    </button>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($quotations->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Quotation</th>
                        <th>Pelanggan</th>
                        <th>Tgl. Quotation</th>
                        <th>Berlaku Sampai</th>
                        <th class="text-right">Total</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotations as $q)
                    <tr>
                        <td class="text-sm font-mono text-gray-600">{{ $q->quotation_number }}</td>
                        <td class="text-sm text-gray-800">{{ $q->customer?->name ?? $q->customer_name }}</td>
                        <td class="text-sm text-gray-600">{{ $q->quotation_date->format('d/m/Y') }}</td>
                        <td class="text-sm text-gray-600">{{ $q->valid_until?->format('d/m/Y') }}</td>
                        <td class="text-right text-sm font-semibold text-gray-800">Rp {{ number_format($q->total, 0, ',', '.') }}</td>
                        <td>
                            @if($q->status === 'approved')
                            <span class="badge badge-success">Disetujui</span>
                            @elseif($q->status === 'rejected')
                            <span class="badge badge-danger">Ditolak</span>
                            @elseif($q->status === 'expired')
                            <span class="badge badge-warning">Kadaluarsa</span>
                            @else
                            <span class="badge badge-info">Draft</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('quotations.show', $q) }}" class="btn-ghost btn-sm">Detail</a>
                            <a href="{{ route('quotations.pdf', $q) }}" class="btn-ghost btn-sm" target="_blank">PDF</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            </div>
            <div class="empty-state-title">Belum Ada Quotation</div>
            <div class="empty-state-desc">Buat penawaran harga untuk pelanggan.</div>
            <span class="btn-primary mt-4 opacity-50 cursor-not-allowed">Tambah Quotation</span>
        </div>
        @endif
    </div>
</div>
@endsection
