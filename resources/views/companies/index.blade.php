@extends('layouts.main')

@section('title', 'Perusahaan')

@section('actions')
    <a href="{{ route('companies.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Perusahaan Baru
    </a>
@endsection

@section('content')
    @if($companies->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($companies as $company)
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:border-orange-200 hover:shadow-sm transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                    <span class="text-orange-600 font-bold text-lg">{{ substr($company->name, 0, 1) }}</span>
                </div>
                @if($company->id === Auth::user()->current_company_id)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                    <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                    Aktif
                </span>
                @endif
            </div>
            <h3 class="font-semibold text-gray-900 mb-1">{{ $company->name }}</h3>
            @if($company->address)
            <p class="text-sm text-gray-500 mb-3">{{ Str::limit($company->address, 50) }}</p>
            @else
            <p class="text-sm text-gray-400 mb-3 italic">Tidak ada alamat</p>
            @endif
            <div class="flex items-center gap-3 text-xs text-gray-400 mb-4">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                    {{ $company->users()->count() }} pengguna
                </span>
                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    {{ $company->transactions()->count() }} transaksi
                </span>
            </div>
            <div class="flex gap-2 pt-3 border-t border-gray-100">
                @if($company->id !== Auth::user()->current_company_id)
                <form action="{{ route('companies.switch', $company) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors">Aktifkan</button>
                </form>
                @endif
                <a href="{{ route('companies.edit', $company) }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 hover:border-orange-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-orange-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum ada perusahaan</h3>
        <p class="text-sm text-gray-500 mb-6">Buat perusahaan pertama Anda untuk mulai mencatat keuangan.</p>
        <a href="{{ route('companies.create') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors">Buat Perusahaan</a>
    </div>
    @endif
@endsection
