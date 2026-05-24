@extends('layouts.main')

@section('title', 'Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @php $user = Auth::user(); @endphp
    @php
        $totalTx = \App\Models\Transaction::where('user_id', Auth::id())->count();
        $totalIncome = \App\Models\Transaction::where('user_id', Auth::id())->where('type', 'income')->sum('amount');
        $totalExpense = \App\Models\Transaction::where('user_id', Auth::id())->where('type', 'expense')->sum('amount');
        $memberSince = $user->created_at;
        $daysAgo = $memberSince->diffInDays(now());
    @endphp

    {{-- Profile Header with Cover --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="h-28 sm:h-36 bg-gradient-to-r from-orange-400 to-orange-500 relative">
            <div class="absolute inset-0 bg-white/10"></div>
        </div>
        <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row sm:items-end sm:gap-6 -mt-10 sm:-mt-12">
                <div class="relative">
                    @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover shadow-lg border-4 border-white">
                    @else
                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-orange-500 rounded-2xl flex items-center justify-center shadow-lg border-4 border-white">
                        <span class="text-white font-bold text-3xl sm:text-4xl">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    @endif
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-400 border-2 border-white rounded-full"></div>
                </div>
                <div class="mt-4 sm:mt-0 sm:pb-1 flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1">
                                <p class="text-sm text-gray-500 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    {{ $user->email }}
                                </p>
                                @if($user->phone)
                                <p class="text-sm text-gray-500 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $user->phone }}
                                </p>
                                @endif
                                @if($user->birth_date)
                                <p class="text-sm text-gray-500 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $user->birth_date->format('d M Y') }}
                                </p>
                                @endif
                                @if($user->gender)
                                <p class="text-sm text-gray-500 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    {{ $user->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                                </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200 capitalize">{{ $user->role }}</span>
                            @if($user->currentCompany)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                {{ $user->currentCompany->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                    @if($user->address)
                    <p class="text-sm text-gray-400 mt-2 flex items-start gap-1.5 max-w-2xl">
                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $user->address }}
                    </p>
                    @endif
                    @if($user->bio)
                    <p class="text-sm text-gray-400 mt-1 max-w-2xl">{{ $user->bio }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-gray-200 transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalTx }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Total Transaksi</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-emerald-200 transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                    <p class="text-xs text-emerald-600 mt-0.5">Total Pemasukan</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-red-200 transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                    <p class="text-xs text-red-600 mt-0.5">Total Pengeluaran</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-orange-200 transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <p class="text-lg font-bold text-orange-600">{{ $memberSince->format('M Y') }}</p>
                    <p class="text-xs text-orange-500 mt-0.5">{{ $daysAgo }} hari bergabung</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Left Column --}}
        <div class="lg:col-span-3 space-y-6">

            {{-- Informasi Profil --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <h3 class="text-base font-semibold text-gray-900">Informasi Profil</h3>
                    </div>
                    <span class="text-xs text-gray-400">Lengkapi data diri Anda</span>
                </div>
                <div class="px-6 py-5">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Perusahaan Saya --}}
            @php
                $myCompanies = Auth::user()->companies()->withPivot('role', 'is_active')->get();
            @endphp
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <h3 class="text-base font-semibold text-gray-900">Perusahaan Saya</h3>
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-orange-50 text-orange-600 text-[10px] font-bold">{{ $myCompanies->count() }}</span>
                    </div>
                    <a href="{{ route('companies.index') }}" class="text-xs font-medium text-orange-600 hover:text-orange-700 flex items-center gap-1">
                        Kelola
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="px-6 py-5">
                    @if($myCompanies->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($myCompanies as $cmp)
                        @php $isActive = $cmp->id === Auth::user()->current_company_id; @endphp
                        <div class="group flex items-center gap-3 p-3.5 rounded-xl border transition-all {{ $isActive ? 'border-orange-200 bg-orange-50/50 shadow-sm' : 'border-gray-100 bg-gray-50/30 hover:bg-gray-50 hover:border-gray-200' }}">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold shrink-0 transition-colors {{ $isActive ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600 group-hover:bg-gray-200' }}">
                                {{ substr($cmp->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $cmp->name }}</p>
                                    @if($isActive)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold capitalize tracking-wide
                                        {{ $cmp->pivot->role === 'owner' ? 'bg-purple-50 text-purple-700 border border-purple-200' : '' }}
                                        {{ $cmp->pivot->role === 'admin' ? 'bg-orange-50 text-orange-700 border border-orange-200' : '' }}
                                        {{ $cmp->pivot->role === 'staff' ? 'bg-gray-100 text-gray-600 border border-gray-200' : '' }}">
                                        {{ $cmp->pivot->role }}
                                    </span>
                                    @if(!$isActive)
                                    <span class="text-[10px] text-gray-400">{{ $cmp->pivot->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    @endif
                                </div>
                            </div>
                            @if(!$isActive)
                            <form action="{{ route('companies.switch', $cmp) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-orange-600 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100">Aktifkan</button>
                            </form>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                        </div>
                        <p class="text-sm text-gray-400">Belum tergabung di perusahaan manapun.</p>
                        <a href="{{ route('companies.create') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700 mt-2 inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Buat Perusahaan Baru
                        </a>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Right Column --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Profile Completion --}}
            @php
                $fields = [
                    'name' => (bool) $user->name,
                    'email' => (bool) $user->email,
                    'phone' => (bool) $user->phone,
                    'birth_date' => (bool) $user->birth_date,
                    'gender' => (bool) $user->gender,
                    'address' => (bool) $user->address,
                    'bio' => (bool) $user->bio,
                    'avatar' => (bool) $user->avatar,
                ];
                $completed = collect($fields)->filter(fn($v) => $v)->count();
                $total = collect($fields)->count();
                $percent = round(($completed / $total) * 100);
            @endphp
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h3 class="text-base font-semibold text-gray-900">Kelengkapan Profil</h3>
                        </div>
                        <span class="text-xs font-bold {{ $percent === 100 ? 'text-emerald-600' : 'text-orange-600' }}">{{ $percent }}%</span>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700 ease-out {{ $percent === 100 ? 'bg-emerald-500' : 'bg-orange-500' }}" style="width: {{ $percent }}%"></div>
                    </div>
                    <div class="mt-4 space-y-2.5">
                        @foreach($fields as $key => $val)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 capitalize">{{ 
                                [
                                    'name' => 'Nama Lengkap',
                                    'email' => 'Email',
                                    'phone' => 'No. Telepon',
                                    'birth_date' => 'Tanggal Lahir',
                                    'gender' => 'Jenis Kelamin',
                                    'address' => 'Alamat',
                                    'bio' => 'Bio',
                                    'avatar' => 'Foto Profil',
                                ][$key] ?? $key 
                            }}</span>
                            @if($val)
                            <span class="inline-flex items-center gap-1 text-emerald-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Lengkap
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Belum
                            </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Perbarui Password --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <h3 class="text-base font-semibold text-gray-900">Keamanan</h3>
                    </div>
                </div>
                <div class="px-6 py-5">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Hapus Akun --}}
            <div class="bg-white rounded-xl border border-red-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-red-50">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        <h3 class="text-base font-semibold text-red-600">Zona Berbahaya</h3>
                    </div>
                </div>
                <div class="px-6 py-5">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
