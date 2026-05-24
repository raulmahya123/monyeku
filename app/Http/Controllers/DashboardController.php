<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\Debt;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockMutation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->current_company_id;
        if (!$companyId) {
            return redirect()->route('companies.index');
        }

        $today = now()->format('Y-m-d');
        $monthStart = now()->startOfMonth()->format('Y-m-d');
        $monthEnd = now()->endOfMonth()->format('Y-m-d');

        $todayIncome = Transaction::where('company_id', $companyId)
            ->where('type', 'income')
            ->where('status', 'approved')
            ->whereDate('transaction_date', $today)
            ->sum('amount');

        $todayExpense = Transaction::where('company_id', $companyId)
            ->where('type', 'expense')
            ->where('status', 'approved')
            ->whereDate('transaction_date', $today)
            ->sum('amount');

        $monthIncome = Transaction::where('company_id', $companyId)
            ->where('type', 'income')
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $monthExpense = Transaction::where('company_id', $companyId)
            ->where('type', 'expense')
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $pendingTxCount = Transaction::where('company_id', $companyId)
            ->where('status', 'pending')->count();
        $pendingInvCount = Invoice::where('company_id', $companyId)
            ->where('approval_status', 'pending')->count();
        $pendingDebtCount = Debt::where('company_id', $companyId)
            ->where('approval_status', 'pending')->count();
        $pendingBudgetCount = Budget::where('company_id', $companyId)
            ->where('approval_status', 'pending')->count();
        $pendingApprovals = $pendingTxCount + $pendingInvCount + $pendingDebtCount + $pendingBudgetCount;

        $recentTransactions = Transaction::with(['category', 'user'])
            ->where('company_id', $companyId)
            ->orderBy('transaction_date', 'desc')
            ->limit(5)
            ->get();

        $monthlyData = Transaction::where('company_id', $companyId)
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->selectRaw("
                DATE(transaction_date) as date,
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
            ")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topCategories = Transaction::where('company_id', $companyId)
            ->where('type', 'expense')
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $budgets = Budget::with('category')
            ->where('company_id', $companyId)
            ->where('year', now()->year)
            ->get();

        $budgets->each(function ($b) {
            $query = Transaction::where('company_id', $b->company_id)
                ->where('type', 'expense')
                ->where('status', 'approved')
                ->whereYear('transaction_date', $b->year);

            if ($b->period === 'monthly') {
                $query->whereMonth('transaction_date', $b->month ?? now()->month);
            }
            if ($b->category_id) {
                $query->where('category_id', $b->category_id);
            }
            $b->spent = $query->sum('amount');
            $b->percentage = $b->amount > 0 ? round(($b->spent / $b->amount) * 100) : 0;
        });

        $budgetAlerts = $budgets->filter(fn($b) => $b->percentage >= $b->notification_threshold && $b->percentage <= 100);
        $budgetOverspent = $budgets->filter(fn($b) => $b->percentage > 100);

        $lowStockProducts = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->whereColumn('stock', '<', 'stock_min')
            ->whereNotNull('stock_min')
            ->where('stock_min', '>', 0)
            ->orderByRaw('CAST(stock AS SIGNED) - CAST(stock_min AS SIGNED) ASC')
            ->limit(5)
            ->get();

        $totalProducts = Product::where('company_id', $companyId)->where('is_active', true)->count();
        $totalWarehouses = Warehouse::where('company_id', $companyId)->where('is_active', true)->count();

        $recentMutations = StockMutation::where('company_id', $companyId)
            ->with(['product', 'warehouse', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'todayIncome', 'todayExpense',
            'monthIncome', 'monthExpense',
            'pendingApprovals', 'pendingTxCount', 'pendingInvCount', 'pendingDebtCount', 'pendingBudgetCount',
            'recentTransactions',
            'monthlyData', 'topCategories',
            'budgetAlerts', 'budgetOverspent',
            'lowStockProducts', 'totalProducts', 'totalWarehouses', 'recentMutations'
        ));
    }
}
