<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MoneyKu') }} @hasSection('title') - @yield('title') @endif</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50" style="font-family: 'Poppins', sans-serif;" x-data="{ sideOpen: false, sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' }" x-init="$watch('sidebarCollapsed', val => localStorage.setItem('sidebarCollapsed', val))">

@auth

<div x-show="sideOpen" x-cloak @@click="sideOpen = false" class="fixed inset-0 z-30 bg-black/20 lg:hidden"></div>

<aside class="sidebar" x-bind:class="sidebarCollapsed ? 'w-16 lg:w-16' : 'w-60 lg:w-60'" :class="sideOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" x-cloak>
    <div class="sidebar-logo" x-bind:class="sidebarCollapsed ? 'justify-center px-0' : 'px-5'">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <div class="flex items-center justify-center bg-orange-500 shadow-sm w-9 h-9 rounded-xl shrink-0">
                <span class="text-base font-bold text-white">M</span>
            </div>
            <span x-show="!sidebarCollapsed" class="text-base font-bold text-orange-600 whitespace-nowrap">MoneyKu</span>
        </a>
    </div>

    @php
        $cc = Auth::user()->current_company_id ? Auth::user()->companies()->find(Auth::user()->current_company_id) : null;
        $uc = Auth::user()->companies;
    @endphp
    @if($uc->count() > 0)
    <div class="px-3 pt-4 pb-2" x-data="{ cOpen: false }" x-bind:class="sidebarCollapsed ? 'px-1' : 'px-3'">
        <div class="relative" @@click.away="cOpen = false">
            <button @@click="cOpen = !cOpen" class="flex items-center w-full company-badge" x-bind:class="sidebarCollapsed ? 'justify-center px-0' : 'px-3'">
                <div class="text-xs text-orange-700 bg-orange-100 company-logo shrink-0">{{ substr(($cc?$cc->name:$uc->first()->name),0,1) }}</div>
                <span x-show="!sidebarCollapsed" class="flex-1 ml-2 text-sm font-medium text-left truncate">{{ $cc?->name ?? $uc->first()->name }}</span>
                <svg x-show="!sidebarCollapsed" class="w-4 h-4 text-gray-400 transition-transform shrink-0" :class="cOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="cOpen" x-cloak @@click.away="cOpen = false" class="dropdown w-full mt-1.5" x-bind:class="sidebarCollapsed ? 'left-0 min-w-[200px]' : ''">
                @foreach($uc as $c)
                <form method="POST" action="{{ route('companies.switch', $c) }}">
                    @csrf
                    <button type="submit" class="dropdown-item {{ $c->id === ($cc?$cc->id:$uc->first()->id) ? 'text-orange-600 bg-orange-50' : '' }}">
                        <div class="company-logo bg-gray-100 text-gray-600 w-7 h-7 text-[11px]">{{ substr($c->name,0,1) }}</div>
                        <div class="flex-1 text-left">
                            <div class="text-sm font-medium">{{ $c->name }}</div>
                            <div class="text-xs text-gray-400 capitalize">{{ $c->pivot->role }}</div>
                        </div>
                    </button>
                </form>
                @endforeach
                <div class="dropdown-divider"></div>
                <a href="{{ route('companies.index') }}" class="font-medium text-orange-600 dropdown-item">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Kelola Perusahaan
                </a>
            </div>
        </div>
    </div>
    @endif

    @php
    $nav = [
        ['l'=>'Dashboard','r'=>'dashboard','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>'],
        ['l'=>'Transaksi','r'=>'transactions.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>'],
        ['l'=>'Berulang','r'=>'recurring.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>'],
        ['l'=>'Kategori','r'=>'categories.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 6h.008v.008H6V6z"/></svg>','s'=>true],
        ['l'=>'Approval','r'=>'approvals.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'],
        ['l'=>'Anggaran','r'=>'budgets.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'],
        ['l'=>'Pengguna','r'=>'roles.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>','s'=>true],
        ['l'=>'Chart of Account','r'=>'coa.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>'],
        ['l'=>'Jurnal Umum','r'=>'journals.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>'],
        ['l'=>'Buku Besar','r'=>'journals.ledger','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>'],
        ['l'=>'Invoice','r'=>'invoices.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>'],
        ['l'=>'Hutang','r'=>'debts.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m0 0v9m0 0h-2.25m0 0h-2.25"/></svg>','s'=>true],
        ['l'=>'Supplier','r'=>'suppliers.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/></svg>'],
        ['l'=>'Pelanggan','r'=>'customers.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>'],
        ['l'=>'Produk','r'=>'products.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>'],
        ['l'=>'Gudang','r'=>'warehouses.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m16.5-18v18M3 6.75h3M3 9.75h3M3 12.75h3m6-6h3m-3 3h3m-3 3h3"/></svg>'],
        ['l'=>'Stock Opname','r'=>'stock-opnames.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>'],
        ['l'=>'Pembelian','r'=>'purchase-orders.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>'],
        ['l'=>'Penjualan','r'=>'sales-orders.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>'],
        ['l'=>'Aset Tetap','r'=>'fixed-assets.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>'],
        ['l'=>'Manufaktur','r'=>'work-orders.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>'],
        ['l'=>'Periode Akuntansi','r'=>'accounting-periods.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>'],
        ['l'=>'Rekening Bank','r'=>'bank-accounts.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 3h6m-3-3h3"/></svg>'],
        ['l'=>'Rekonsiliasi Bank','r'=>'bank-reconciliations.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>'],
        ['l'=>'Audit Trail','r'=>'audit-trails.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>','s'=>true],
        ['l'=>'Laporan','r'=>'reports.index','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>','s'=>true],
        ['l'=>'Neraca','r'=>'reports.balance-sheet','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/></svg>'],
        ['l'=>'Laba Rugi','r'=>'reports.income-statement','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>'],
        ['l'=>'Arus Kas','r'=>'reports.cash-flow','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>'],
        ['l'=>'', 'r'=>'', 'i'=>'', 's'=>true],
        ['l'=>'Stok','r'=>'reports.stock','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>'],
        ['l'=>'Pembelian','r'=>'reports.purchases','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>'],
        ['l'=>'Penjualan','r'=>'reports.sales','i'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>'],
    ];
    $cur = request()->route()?->getName() ?? '';
    $activeMenuLabel = 'Dashboard';
    foreach ($nav as $n) {
        $prefix = explode('.', $n['r'])[0] . '.';
        if ($cur === $n['r'] || str_starts_with($cur, $prefix)) {
            $activeMenuLabel = $n['l'];
            break;
        }
    }
    @endphp

    <nav class="sidebar-nav">
        @foreach($nav as $n)
            @if(empty($n['r']))
                @if(isset($n['s']))<div class="sidebar-divider"></div>@endif
                @continue
            @endif

            @php $a = $cur === $n['r'] || str_starts_with($cur, explode('.',$n['r'])[0].'.'); @endphp
            <a href="{{ route($n['r']) }}" class="sidebar-link" x-bind:class="sidebarCollapsed ? 'justify-center px-0' : ''" x-cloak>
                <span class="sidebar-link-icon">{!! $n['i'] !!}</span>
                <span x-show="!sidebarCollapsed">{{ $n['l'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="sidebar-footer" x-bind:class="sidebarCollapsed ? 'px-1' : 'px-3'">
        <a href="{{ route('companies.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-400 hover:text-orange-600 hover:bg-orange-50 transition-all" x-bind:class="sidebarCollapsed ? 'justify-center px-2' : ''">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span x-show="!sidebarCollapsed">Kelola Perusahaan</span>
        </a>
    </div>
</aside>

<div class="flex flex-col min-h-screen transition-all duration-200" x-bind:class="sidebarCollapsed ? 'lg:ml-16' : 'lg:ml-60'">
    <nav class="px-5 py-3 lg:px-7 border-b border-gray-100 bg-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <button @@click="sidebarCollapsed = !sidebarCollapsed" class="hidden btn-icon lg:flex" x-bind:title="sidebarCollapsed ? 'Perluas sidebar' : 'Ciutkan sidebar'">
                    <svg x-show="!sidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5"/></svg>
                    <svg x-show="sidebarCollapsed" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5"/></svg>
                </button>
                <button @@click="sideOpen = true" class="btn-icon -ml-1.5 lg:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>
            </div>
            <div class="flex items-center gap-1.5 sm:gap-2.5">

            @php
                $nc = 0; $pns = collect();
                if ($cc) {
                    $dbNotifs = Auth::user()->notifications()->limit(5)->get();
                    $nc = \App\Models\Transaction::where('company_id', $cc->id)
                        ->whereHas('approvals', fn($q) => $q->where('approver_id', Auth::id())->where('status','pending'))
                        ->where('status','pending')->count()
                        + \App\Models\Invoice::where('company_id', $cc->id)
                        ->whereHas('approvals', fn($q) => $q->where('approver_id', Auth::id())->where('status','pending'))
                        ->where('approval_status','pending')->count()
                        + \App\Models\Debt::where('company_id', $cc->id)
                        ->whereHas('approvals', fn($q) => $q->where('approver_id', Auth::id())->where('status','pending'))
                        ->where('approval_status','pending')->count()
                        + \App\Models\Budget::where('company_id', $cc->id)
                        ->whereHas('approvals', fn($q) => $q->where('approver_id', Auth::id())->where('status','pending'))
                        ->where('approval_status','pending')->count();
                    $pns = \App\Models\Transaction::with(['user','category'])
                        ->where('company_id', $cc->id)
                        ->whereHas('approvals', fn($q) => $q->where('approver_id', Auth::id())->where('status','pending'))
                        ->where('status','pending')->limit(3)->get();
                }
            @endphp

            <div class="relative" x-data="{ nOpen: false }" @@click.away="nOpen = false">
                <button @@click="nOpen = !nOpen" class="relative btn-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                    @if($nc > 0)
                    <span class="notif-dot">{{ $nc }}</span>
                    @endif
                </button>
                <div x-show="nOpen" x-cloak @@click.away="nOpen = false" class="dropdown w-80 mt-1.5 right-0">
                    <div class="dropdown-header">Notifikasi <span class="font-normal text-gray-400 normal-case">· {{ $nc }} pending</span></div>
                    @if(count($dbNotifs ?? []) > 0)
                    @foreach($dbNotifs as $dbn)
                    @php $data = $dbn->data; @endphp
                    <a href="{{ $data['url'] ?? route('approvals.index') }}" class="flex items-start gap-3 px-4 py-3 transition-colors border-b hover:bg-orange-50 border-gray-50">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800">{{ $data['message'] ?? 'Approval diperlukan' }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ \Carbon\Carbon::parse($dbn->created_at)->diffForHumans() }}</p>
                        </div>
                    </a>
                    @endforeach
                    @endif
                    @forelse($pns as $n)
                    <a href="{{ route('approvals.index') }}" class="flex items-start gap-3 px-4 py-3 transition-colors border-b hover:bg-orange-50 border-gray-50">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800">Rp {{ number_format($n->amount,0,',','.') }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $n->user?->name }} · {{ $n->category?->name }}</p>
                        </div>
                    </a>
                    @empty
                        @if(count($dbNotifs ?? []) == 0)
                        <div class="px-4 py-10 text-sm text-center text-gray-400">Tidak ada notifikasi</div>
                        @endif
                    @endforelse
                    @if($nc > 0)
                    <div class="border-t border-gray-100">
                        <a href="{{ route('approvals.index') }}" class="justify-center text-sm font-medium text-orange-600 dropdown-item">Lihat Semua</a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="relative" x-data="{ pOpen: false }" @@click.away="pOpen = false">
                <button @@click="pOpen = !pOpen" class="flex items-center gap-2.5 pl-2.5 pr-3.5 py-1.5 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                    <div class="flex items-center justify-center w-8 h-8 bg-orange-500 shadow-sm rounded-xl">
                        <span class="text-xs font-bold text-white">{{ substr(Auth::user()->name,0,1) }}</span>
                    </div>
                    <div class="hidden text-sm leading-tight text-left sm:block">
                        <p class="font-semibold text-gray-700">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 capitalize">{{ Auth::user()->role }}</p>
                    </div>
                    <svg class="hidden w-4 h-4 text-gray-400 sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="pOpen" x-cloak @@click.away="pOpen = false" class="dropdown right-0 w-56 mt-1.5">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        Profile
                    </a>
                    <a href="{{ route('companies.index') }}" class="dropdown-item">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                        Perusahaan
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="button" @click="$store.confirm.ask('Logout', 'Yakin ingin keluar?', { confirmText: 'Ya, logout', confirmClass: 'btn-danger', action: () => $el.closest('form').submit() })" class="text-red-600 dropdown-item hover:text-red-700 hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </nav>

    <header class="px-5 pt-4 lg:px-7">
        <div class="rounded-3xl bg-gradient-to-r from-orange-500 via-orange-500 to-orange-600 px-6 py-6 lg:px-10 lg:py-8 text-white shadow-lg">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/20 text-orange-50 text-sm font-semibold tracking-wide">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 13h3l2-6 4 12 2-6h5"/></svg>
                        PUSAT {{ \Illuminate\Support\Str::upper($activeMenuLabel) }}
                    </div>
                    <h1 class="mt-4 text-2xl lg:text-3xl font-extrabold tracking-tight">Ringkasan {{ $activeMenuLabel }}</h1>
                    @hasSection('subtitle')
                    <p class="mt-3 text-sm lg:text-lg max-w-3xl text-orange-50/90 leading-relaxed">@yield('subtitle')</p>
                    @else
                    <p class="mt-3 text-sm lg:text-lg max-w-3xl text-orange-50/90 leading-relaxed">Selamat datang kembali, berikut informasi organisasi Anda hari ini.</p>
                    @endif
                </div>

                @hasSection('actions')
                <div class="flex flex-wrap gap-3 lg:justify-end [&>a]:inline-flex [&>a]:items-center [&>a]:rounded-2xl [&>a]:bg-white [&>a]:px-6 [&>a]:py-3 [&>a]:text-base [&>a]:font-bold [&>a]:text-orange-600 [&>a]:no-underline [&>a]:shadow-sm [&>a]:hover:bg-orange-50 [&>button]:inline-flex [&>button]:items-center [&>button]:rounded-2xl [&>button]:bg-white [&>button]:px-6 [&>button]:py-3 [&>button]:text-base [&>button]:font-bold [&>button]:text-orange-600 [&>button]:shadow-sm [&_.btn-primary]:bg-white [&_.btn-primary]:text-orange-600 [&_.btn-primary]:border-0 [&_.btn-primary]:shadow-sm [&_.btn-sm]:px-6 [&_.btn-sm]:py-3 [&_.btn-sm]:text-base">
                    @yield('actions')
                </div>
                @endif
            </div>
        </div>

    </header>

    @if(session('success'))
    <div x-init="$store.toast.success('{{ session('success') }}')" hidden></div>
    @endif
    @if(session('error'))
    <div x-init="$store.toast.error('{{ session('error') }}')" hidden></div>
    @endif

    {{-- Toast Container --}}
    <div
        x-data
        class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 w-80"
        style="pointer-events: none;"
    >
        <template x-for="item in $store.toast.items" :key="item.id">
            <div
                x-show="item"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0 opacity-100"
                x-transition:leave-end="translate-x-full opacity-0"
                class="flex items-start gap-3 px-4 py-3 rounded-xl shadow-lg cursor-pointer"
                style="pointer-events: auto;"
                :class="{
                    'bg-emerald-50 border border-emerald-200 text-emerald-700': item.type === 'success',
                    'bg-red-50 border border-red-200 text-red-600': item.type === 'error',
                    'bg-amber-50 border border-amber-200 text-amber-700': item.type === 'warning',
                }"
                x-on:click="$store.toast.remove(item.id)"
            >
                <template x-if="item.type === 'success'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="item.type === 'error'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </template>
                <template x-if="item.type === 'warning'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </template>
                <div class="text-sm font-medium" x-text="item.message"></div>
            </div>
        </template>
    </div>

    {{-- Global Confirm Modal --}}
    <x-modal name="global-confirm" :max-width="'md'">
        <div class="p-6" x-data>
            <div class="flex items-start gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shrink-0 flex items-center justify-center"
                    :class="$store.confirm.confirmClass === 'btn-success' ? 'bg-emerald-100' : 'bg-red-100'">
                    <svg class="w-5 h-5"
                        :class="$store.confirm.confirmClass === 'btn-success' ? 'text-emerald-600' : 'text-red-600'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800" x-text="$store.confirm.title"></h3>
                    <p class="text-sm text-gray-500 mt-1" x-text="$store.confirm.message"></p>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button x-on:click="$store.confirm.cancel()" type="button" class="btn-ghost btn-sm" x-text="$store.confirm.confirmText === 'Ya, setujui' ? 'Batal' : 'Batal'">Batal</button>
                <button
                    x-on:click="$store.confirm.confirm()"
                    type="button"
                    class="btn-sm"
                    :class="$store.confirm.confirmClass"
                    x-text="$store.confirm.confirmText"
                >Ya, lanjutkan</button>
            </div>
        </div>
    </x-modal>

    <main class="flex-1 px-5 py-6 lg:px-7">
        @yield('content')
    </main>

    <footer class="px-5 py-4 text-xs text-center text-gray-400 border-t border-gray-100 lg:px-7">
        &copy; {{ date('Y') }} MoneyKu. All rights reserved.
    </footer>
</div>

@endauth

@guest
<main class="flex-1 px-5 py-6 lg:px-7">
    @yield('content')
</main>
@endguest

@stack('scripts')
</body>
</html>
