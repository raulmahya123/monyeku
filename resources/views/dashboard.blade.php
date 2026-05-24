@extends('layouts.main')

@section('title', 'Dashboard')

@section('actions')
    <a href="{{ route('transactions.create') }}" class="btn-primary btn-sm">+ Transaksi Baru</a>
@endsection

@section('content')
    @if(!Auth::user()->current_company_id)
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <h3 class="empty-state-title">Selamat Datang di MoneyKu!</h3>
        <p class="empty-state-desc">Buat perusahaan terlebih dahulu untuk mulai mencatat keuangan.</p>
        <a href="{{ route('companies.create') }}" class="btn-primary">Buat Perusahaan</a>
    </div>
    @else
    @php
        $netBalance = $monthIncome - $monthExpense;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <span class="stat-label">Pemasukan Hari Ini</span>
                <div class="stat-icon" style="background: #ecfdf5;">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value text-emerald-600">Rp {{ number_format($todayIncome, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <span class="stat-label">Pengeluaran Hari Ini</span>
                <div class="stat-icon" style="background: #fef2f2;">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value text-red-600">Rp {{ number_format($todayExpense, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <span class="stat-label">Pemasukan Bulan Ini</span>
                <div class="stat-icon" style="background: #ecfdf5;">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value text-emerald-600">Rp {{ number_format($monthIncome, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <span class="stat-label">Pengeluaran Bulan Ini</span>
                <div class="stat-icon" style="background: #fef2f2;">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value text-red-600">Rp {{ number_format($monthExpense, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Arus Kas Bulan Ini</h3>
                </div>
                <div class="card-body">
                    <canvas id="cashflowChart" height="220"></canvas>
                </div>
            </div>
        </div>
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ringkasan Bulanan</h3>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                </div>
                                <span class="text-sm font-medium text-emerald-700">Total Pemasukan</span>
                            </div>
                            <span class="text-sm font-bold text-emerald-700">Rp {{ number_format($monthIncome, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-xl bg-red-50 border border-red-100">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                                </div>
                                <span class="text-sm font-medium text-red-700">Total Pengeluaran</span>
                            </div>
                            <span class="text-sm font-bold text-red-700">Rp {{ number_format($monthExpense, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="flex items-center justify-between p-4 rounded-xl {{ $netBalance >= 0 ? 'bg-orange-50 border border-orange-100' : 'bg-red-50 border border-red-100' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg {{ $netBalance >= 0 ? 'bg-orange-100' : 'bg-red-100' }} flex items-center justify-center">
                                <svg class="w-4 h-4 {{ $netBalance >= 0 ? 'text-orange-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <span class="text-sm font-medium {{ $netBalance >= 0 ? 'text-orange-700' : 'text-red-700' }}">Saldo Bersih</span>
                                <p class="text-xs {{ $netBalance >= 0 ? 'text-orange-500' : 'text-red-500' }}">Bulan ini</p>
                            </div>
                        </div>
                        <span class="text-base font-bold {{ $netBalance >= 0 ? 'text-orange-700' : 'text-red-700' }}">Rp {{ number_format($netBalance, 0, ',', '.') }}</span>
                    </div>

                    @php
                        $pendingTotal = $pendingApprovals;
                        $hasPendingTx = $pendingTxCount ?? 0;
                        $hasPendingInv = $pendingInvCount ?? 0;
                        $hasPendingDebt = $pendingDebtCount ?? 0;
                        $hasPendingBudget = $pendingBudgetCount ?? 0;
                    @endphp
                    @if($pendingTotal > 0)
                    <div class="mt-3 p-4 rounded-xl bg-amber-50 border border-amber-200">
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-amber-800">{{ $pendingTotal }} item menunggu approval</span>
                                <div class="flex gap-2 mt-1 text-xs text-amber-600">
                                    @if($hasPendingTx > 0)<span>{{ $hasPendingTx }} transaksi</span>@endif
                                    @if($hasPendingInv > 0)<span>{{ $hasPendingInv }} invoice</span>@endif
                                    @if($hasPendingDebt > 0)<span>{{ $hasPendingDebt }} hutang/piutang</span>@endif
                                    @if($hasPendingBudget > 0)<span>{{ $hasPendingBudget }} anggaran</span>@endif
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('approvals.index') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium ml-9">Lihat &rarr;</a>
                    </div>
                    @endif

                    @if($budgetAlerts->count() > 0)
                    <div class="mt-3 p-4 rounded-xl bg-orange-50 border border-orange-200">
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <div class="w-7 h-7 rounded-lg bg-orange-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-orange-800">{{ $budgetAlerts->count() }} anggaran mendekati batas</span>
                        </div>
                        <a href="{{ route('budgets.index') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium ml-9">Lihat &rarr;</a>
                    </div>
                    @endif

                    @if($budgetOverspent->count() > 0)
                    <div class="mt-3 p-4 rounded-xl bg-red-50 border border-red-200">
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <div class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-red-800">{{ $budgetOverspent->count() }} anggaran melebihi batas</span>
                        </div>
                        <a href="{{ route('budgets.index') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium ml-9">Lihat &rarr;</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Summary -->
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Produk</h3>
                    <svg class="w-8 h-8 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                </div>
                <div class="text-3xl font-bold text-gray-800">{{ number_format($totalProducts, 0, ',', '.') }}</div>
                <p class="text-sm text-gray-400 mt-1">Total produk aktif</p>
                <a href="{{ route('products.index') }}" class="inline-block mt-3 text-sm font-medium text-orange-600 hover:text-orange-700">Kelola Produk →</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Gudang</h3>
                    <svg class="w-8 h-8 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m16.5-18v18M3 6.75h3M3 9.75h3M3 12.75h3m6-6h3m-3 3h3m-3 3h3"/></svg>
                </div>
                <div class="text-3xl font-bold text-gray-800">{{ number_format($totalWarehouses, 0, ',', '.') }}</div>
                <p class="text-sm text-gray-400 mt-1">Total gudang aktif</p>
                <a href="{{ route('warehouses.index') }}" class="inline-block mt-3 text-sm font-medium text-orange-600 hover:text-orange-700">Kelola Gudang →</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Stok Menipis</h3>
                    <svg class="w-8 h-8 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                @if($lowStockProducts->count() > 0)
                <div class="text-3xl font-bold text-red-600">{{ $lowStockProducts->count() }}</div>
                <p class="text-sm text-gray-400 mt-1">Produk dengan stok di bawah minimum</p>
                <div class="mt-3 space-y-2">
                    @foreach($lowStockProducts as $p)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-700 truncate">{{ $p->name }}</span>
                        <span class="font-semibold text-red-600">{{ number_format($p->stock, 0) }} / {{ number_format($p->stock_min, 0) }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-3xl font-bold text-emerald-600">0</div>
                <p class="text-sm text-gray-400 mt-1">Semua stok aman</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Stock Mutations -->
    <div class="card mt-5">
        <div class="card-header">
            <h3 class="text-base font-semibold text-gray-800">Mutasi Stok Terbaru</h3>
            <a href="{{ route('products.index') }}" class="text-sm font-medium text-orange-600 hover:text-orange-700">Lihat Semua →</a>
        </div>
        <div class="card-body p-0">
            @if($recentMutations->count() > 0)
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Gudang</th>
                            <th>Tipe</th>
                            <th class="text-right">Qty</th>
                            <th>Keterangan</th>
                            <th>User</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentMutations as $m)
                        <tr>
                            <td class="text-sm font-medium text-gray-800">{{ $m->product?->name }}</td>
                            <td class="text-sm text-gray-600">{{ $m->warehouse?->name }}</td>
                            <td>
                                @if($m->type === 'in')
                                <span class="badge badge-success">Masuk</span>
                                @elseif($m->type === 'out')
                                <span class="badge badge-danger">Keluar</span>
                                @else
                                <span class="badge badge-warning">{{ $m->type }}</span>
                                @endif
                            </td>
                            <td class="text-right text-sm font-semibold">{{ number_format($m->quantity, 0) }}</td>
                            <td class="text-sm text-gray-600 max-w-[200px] truncate">{{ $m->notes }}</td>
                            <td class="text-sm text-gray-600">{{ $m->user?->name }}</td>
                            <td class="text-sm text-gray-400">{{ $m->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-title">Belum Ada Mutasi</div>
                <div class="empty-state-desc">Mutasi stok akan muncul setelah ada transaksi.</div>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Transaksi Terbaru</h3>
                <a href="{{ route('transactions.index') }}" class="btn-ghost btn-sm">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if($recentTransactions->count() > 0)
                    @foreach($recentTransactions as $t)
                    <div class="flex items-center justify-between px-5 py-4 hover:bg-orange-50 transition-colors border-b border-gray-50 last:border-b-0">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $t->type === 'income' ? 'bg-emerald-100' : 'bg-red-100' }}">
                                @if($t->type === 'income')
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                @else
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                                </svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $t->category?->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $t->description ? Str::limit($t->description, 30) : $t->transaction_date->format('d M Y') }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold {{ $t->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $t->type === 'income' ? '+' : '-' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
                        </span>
                    </div>
                    @endforeach
                @else
                <div class="px-5 py-12 text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <p class="text-sm text-gray-500">Belum ada transaksi bulan ini.</p>
                </div>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kategori Terboros</h3>
            </div>
            <div class="card-body p-0">
                @if($topCategories->count() > 0)
                    @foreach($topCategories as $cat)
                    @php
                        $max = $topCategories->first()?->total ?? 1;
                        $pct = min(($cat->total / $max) * 100, 100);
                    @endphp
                    <div class="px-5 py-4 border-b border-gray-50 last:border-b-0">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">{{ $cat->category?->name ?? '-' }}</span>
                            <span class="text-sm font-semibold text-gray-700">Rp {{ number_format($cat->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar-orange" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="px-5 py-12 text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m0 0v9m0 0h-2.25m0 0h-2.25"/></svg>
                    </div>
                    <p class="text-sm text-gray-500">Belum ada data pengeluaran.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('cashflowChart');
            if (!ctx) return;

            const labels = @json($monthlyData->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M')));
            const income = @json($monthlyData->pluck('income'));
            const expense = @json($monthlyData->pluck('expense'));

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pemasukan',
                        data: income,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }, {
                        label: 'Pengeluaran',
                        data: expense,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { drawBorder: false },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                },
                                font: { size: 11 }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        });
    </script>
    @endpush
@endsection
