<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\ApprovalService;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    private ApprovalService $approvalService;
    private AccountingService $accountingService;

    public function __construct(ApprovalService $approvalService, AccountingService $accountingService)
    {
        $this->approvalService = $approvalService;
        $this->accountingService = $accountingService;
    }

    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function index()
    {
        $companyId = $this->getCompanyId();
        $now = now();

        $budgets = Budget::with('category')
            ->where('company_id', $companyId)
            ->where('year', $now->year)
            ->where(function ($q) use ($now) {
                $q->where('period', 'yearly')
                  ->orWhere(function ($q2) use ($now) {
                      $q2->where('period', 'monthly')->where('month', $now->month);
                  });
            })
            ->get()
            ->map(function ($budget) use ($now) {
                $query = Transaction::where('company_id', $budget->company_id)
                    ->where('type', 'expense')
                    ->where('status', 'approved');

                if ($budget->category_id) {
                    $query->where('category_id', $budget->category_id);
                }

                if ($budget->period === 'monthly') {
                    $query->whereMonth('transaction_date', $now->month)
                          ->whereYear('transaction_date', $now->year);
                } else {
                    $query->whereYear('transaction_date', $now->year);
                }

                $budget->spent = $query->sum('amount');
                $budget->remaining = $budget->amount - $budget->spent;
                $budget->percentage = $budget->amount > 0
                    ? round(($budget->spent / $budget->amount) * 100, 1)
                    : 0;

                return $budget;
            });

        $categories = Category::where('company_id', $companyId)
            ->where('type', 'expense')
            ->where('is_active', true)
            ->get();

        return view('budgets.index', compact('budgets', 'categories'));
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'period' => 'required|in:monthly,yearly',
            'amount' => 'required|numeric|min:0',
            'notification_threshold' => 'required|integer|min:1|max:100',
        ]);

        $now = now();
        $validated['company_id'] = $companyId;
        $validated['month'] = $validated['period'] === 'monthly' ? $now->month : null;
        $validated['year'] = $now->year;
        $validated['spent'] = 0;

        $exists = Budget::where('company_id', $companyId)
            ->where('category_id', $validated['category_id'])
            ->where('period', $validated['period'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Anggaran untuk periode ini sudah ada.');
        }

        $approvalConfig = $this->approvalService->getConfig(
            'budget', $companyId, $validated['category_id'], $validated['amount']
        );

        $shouldSkip = $approvalConfig && $this->approvalService->shouldSkipApproval($approvalConfig, Auth::user(), $companyId);

        if ($this->approvalService->requiresApproval($approvalConfig) && !$shouldSkip) {
            $validated['approval_status'] = 'pending';
        } else {
            $validated['approval_status'] = 'approved';
        }

        $budget = Budget::create($validated);

        if ($validated['approval_status'] === 'pending') {
            $this->approvalService->createApprovals($budget, $approvalConfig, $companyId, $validated['amount']);
        }

        $message = $validated['approval_status'] === 'pending'
            ? 'Anggaran menunggu persetujuan.'
            : 'Anggaran berhasil dibuat.';

        return redirect()->route('budgets.index')->with('success', $message);
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorizeAccess($budget);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'notification_threshold' => 'required|integer|min:1|max:100',
        ]);

        $budget->update($validated);

        return redirect()->route('budgets.index')->with('success', 'Anggaran berhasil diperbarui.');
    }

    public function destroy(Budget $budget)
    {
        $this->authorizeAccess($budget);
        $budget->delete();

        return redirect()->route('budgets.index')->with('success', 'Anggaran berhasil dihapus.');
    }

    private function authorizeAccess(Budget $budget)
    {
        if ($budget->company_id !== $this->getCompanyId()) {
            abort(403);
        }
    }
}
