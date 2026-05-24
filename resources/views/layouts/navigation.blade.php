@php
    $menuItems = [
        'dashboard' => [
            'label' => 'Dashboard',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
            'route' => 'dashboard',
        ],
        'transactions' => [
            'label' => 'Transaksi',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>',
            'route' => 'transactions.index',
        ],
        'recurring' => [
            'label' => 'Berulang',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>',
            'route' => 'recurring.index',
        ],
        'categories' => [
            'label' => 'Kategori',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>',
            'route' => 'categories.index',
        ],
        'separator1' => ['separator' => true],
        'approvals' => [
            'label' => 'Approval',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'route' => 'approvals.index',
        ],
        'budgets' => [
            'label' => 'Anggaran',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'route' => 'budgets.index',
        ],
        'separator2' => ['separator' => true],
        'invoices' => [
            'label' => 'Invoice',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            'route' => 'invoices.index',
        ],
        'debts' => [
            'label' => 'Hutang & Piutang',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
            'route' => 'debts.index',
        ],
        'separator3' => ['separator' => true],
        'reports' => [
            'label' => 'Laporan',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            'route' => 'reports.index',
        ],
    ];

    $currentRoute = request()->route()?->getName() ?? 'dashboard';
@endphp

<aside class="fixed inset-y-0 left-0 z-30 flex flex-col w-64 transition-transform duration-200 ease-in-out transform -translate-x-full bg-white border-r border-gray-100 shadow-sm lg:translate-x-0" x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" x-cloak>
    {{-- Logo --}}
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-100 bg-gradient-to-r from-orange-500 to-orange-600">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/20 backdrop-blur-sm">
                <span class="text-sm font-bold text-white">M</span>
            </div>
            <span class="text-lg font-bold text-white">MoneyKu</span>
        </a>
        <button @@click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Company Switcher --}}
    @php
        $userCompanies = Auth::user()->companies;
    @endphp
    @if($userCompanies->count() > 0)
    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50" x-data="{ companyOpen: false }">
        <div class="relative" @@click.away="companyOpen = false">
            <button @@click="companyOpen = !companyOpen" class="w-full flex items-center gap-2.5 px-3 py-2 bg-white rounded-xl border border-gray-200 hover:border-orange-300 transition-all shadow-xs">
                <div class="flex items-center justify-center bg-orange-100 rounded-lg w-7 h-7 shrink-0">
                    <span class="text-xs font-bold text-orange-700">{{ substr(Auth::user()->current_company ? Auth::user()->current_company->name : $userCompanies->first()->name, 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0 text-left">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->current_company?->name ?? $userCompanies->first()->name }}</p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">{{ Auth::user()->current_company?->pivot?->role ?? $userCompanies->first()->pivot->role }}</p>
                </div>
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" :class="{'rotate-180': companyOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="companyOpen" x-cloak class="absolute left-0 right-0 z-10 mt-1 overflow-hidden bg-white border border-gray-200 shadow-lg top-full rounded-xl">
                @foreach($userCompanies as $company)
                <form method="POST" action="{{ route('companies.switch', $company) }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2.5 text-sm hover:bg-orange-50 transition-colors flex items-center gap-2.5 {{ $company->id === (Auth::user()->current_company_id ?? $userCompanies->first()->id) ? 'bg-orange-50 text-orange-700' : 'text-gray-700' }}">
                        <div class="flex items-center justify-center w-6 h-6 bg-gray-100 rounded-lg shrink-0">
                            <span class="text-xs font-bold text-gray-600">{{ substr($company->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <div class="font-medium">{{ $company->name }}</div>
                            <div class="text-[10px] text-gray-400">{{ $company->pivot->role }}</div>
                        </div>
                    </button>
                </form>
                @endforeach
                @if(Auth::user()->role === 'owner')
                <div class="border-t border-gray-100">
                    <a href="{{ route('companies.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm text-orange-600 hover:bg-orange-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Kelola Perusahaan
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Navigation Links --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
        @foreach($menuItems as $key => $item)
            @if(isset($item['separator']))
                @if(!$loop->first)
                <div class="my-3 border-t border-gray-100"></div>
                @endif
            @else
                @php
                    $isActive = $currentRoute === $item['route'] || str_starts_with($currentRoute, $key . '.');
                    $hasChildren = isset($item['children']);
                @endphp
                <a href="{{ route($item['route']) }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ $isActive ? 'bg-orange-50 text-orange-700 shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} group">
                    <span class="{{ $isActive ? 'text-orange-500' : 'text-gray-400 group-hover:text-gray-600' }} shrink-0">{!! $item['icon'] !!}</span>
                    <span>{{ $item['label'] }}</span>
                    @if($isActive)
                    <span class="ml-auto w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                    @endif
                </a>
            @endif
        @endforeach
    </nav>

    {{-- Bottom --}}
    <div class="px-3 py-3 border-t border-gray-100 bg-gray-50/50">
        <a href="{{ route('companies.create') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium text-orange-600 hover:bg-orange-50 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Perusahaan
        </a>
    </div>
</aside>
