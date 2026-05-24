<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMutation;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();

        $period = $request->get('period', 'monthly');
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $startDate = match ($period) {
            'daily' => now()->startOfDay(),
            'monthly' => now()->setYear($year)->setMonth($month)->startOfMonth(),
            'yearly' => now()->setYear($year)->startOfYear(),
            'custom' => \Carbon\Carbon::parse($request->start_date),
            default => now()->startOfMonth(),
        };

        $endDate = match ($period) {
            'daily' => now()->endOfDay(),
            'monthly' => now()->setYear($year)->setMonth($month)->endOfMonth(),
            'yearly' => now()->setYear($year)->endOfYear(),
            'custom' => \Carbon\Carbon::parse($request->end_date),
            default => now()->endOfMonth(),
        };

        $transactions = Transaction::with(['category', 'user'])
            ->where('company_id', $companyId)
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date')
            ->get();

        $summary = [
            'total_income' => $transactions->where('type', 'income')->sum('amount'),
            'total_expense' => $transactions->where('type', 'expense')->sum('amount'),
            'net' => $transactions->where('type', 'income')->sum('amount') - $transactions->where('type', 'expense')->sum('amount'),
            'count' => $transactions->count(),
        ];

        $byCategory = $transactions->groupBy('category_id')->map(function ($items) {
            $category = $items->first()->category;
            return [
                'category' => $category?->name ?? 'Tanpa Kategori',
                'type' => $category?->type ?? 'unknown',
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        })->sortByDesc('total');

        $dailySummary = $transactions->groupBy(function ($t) {
            return $t->transaction_date->format('Y-m-d');
        })->map(function ($items, $date) {
            return [
                'date' => $date,
                'income' => $items->where('type', 'income')->sum('amount'),
                'expense' => $items->where('type', 'expense')->sum('amount'),
                'net' => $items->where('type', 'income')->sum('amount') - $items->where('type', 'expense')->sum('amount'),
            ];
        })->sortBy('date');

        return view('reports.index', compact(
            'transactions', 'summary', 'byCategory', 'dailySummary',
            'period', 'year', 'month', 'startDate', 'endDate'
        ));
    }

    public function exportPdf(Request $request)
    {
        $companyId = $this->getCompanyId();
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $type = $request->get('type', 'all');

        $query = Transaction::with(['category', 'user'])
            ->where('company_id', $companyId)
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $transactions = $query->orderBy('transaction_date')->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $net = $income - $expense;

        $html = view('reports.pdf', compact(
            'transactions', 'income', 'expense', 'net', 'startDate', 'endDate'
        ))->render();

        if (class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            return $pdf->download('laporan-keuangan-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.pdf');
        }

        return response()->streamDownload(function () use ($html) {
            echo $html;
        }, 'laporan-keuangan-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.html', [
            'Content-Type' => 'text/html',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $companyId = $this->getCompanyId();
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $type = $request->get('type', 'all');

        $query = Transaction::with(['category', 'user'])
            ->where('company_id', $companyId)
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $transactions = $query->orderBy('transaction_date')->get();

        $filename = 'laporan-keuangan-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Tipe', 'Kategori', 'Deskripsi', 'Metode', 'Jumlah', 'Status']);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->transaction_date->format('d/m/Y'),
                    $t->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    $t->category?->name ?? '-',
                    $t->description ?? '-',
                    $t->payment_method === 'cash' ? 'Kas' : 'Bank',
                    number_format($t->amount, 0, ',', '.'),
                    $t->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function stockReport(Request $request)
    {
        $companyId = $this->getCompanyId();

        $products = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->with(['category', 'warehouses'])
            ->when($request->search, fn($q) => $q->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('code', 'like', '%'.$request->search.'%');
            }))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->orderBy('name')
            ->paginate(20);

        $categories = \App\Models\Category::where('company_id', $companyId)->orderBy('name')->get();
        $totalStockValue = Product::where('company_id', $companyId)->where('is_active', true)
            ->get()->sum(fn($p) => $p->stock * $p->purchase_price);

        return view('reports.stock', compact('products', 'categories', 'totalStockValue'));
    }

    public function purchaseReport(Request $request)
    {
        $companyId = $this->getCompanyId();

        $orders = PurchaseOrder::where('company_id', $companyId)
            ->with(['supplier', 'items.product'])
            ->when($request->date_from, fn($q) => $q->whereDate('order_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('order_date', '<=', $request->date_to))
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('order_date', 'desc')
            ->paginate(20);

        $suppliers = \App\Models\Supplier::where('company_id', $companyId)->orderBy('name')->get();
        $totalAmount = $orders->sum(fn($o) => $o->items->sum(fn($i) => $i->quantity * $i->price));

        return view('reports.purchases', compact('orders', 'suppliers', 'totalAmount'));
    }

    public function salesReport(Request $request)
    {
        $companyId = $this->getCompanyId();

        $orders = SalesOrder::where('company_id', $companyId)
            ->with(['customer', 'items.product'])
            ->when($request->date_from, fn($q) => $q->whereDate('order_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('order_date', '<=', $request->date_to))
            ->when($request->customer_id, fn($q) => $q->where('customer_id', $request->customer_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('order_date', 'desc')
            ->paginate(20);

        $customers = \App\Models\Customer::where('company_id', $companyId)->orderBy('name')->get();
        $totalAmount = $orders->sum(fn($o) => $o->items->sum(fn($i) => $i->quantity * $i->price));

        return view('reports.sales', compact('orders', 'customers', 'totalAmount'));
    }

    public function exportStockPdf(Request $request)
    {
        $companyId = $this->getCompanyId();
        $products = Product::where('company_id', $companyId)->where('is_active', true)
            ->with('category')->orderBy('name')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.stock-pdf', compact('products'));
        return $pdf->download('laporan-stok.pdf');
    }
}
