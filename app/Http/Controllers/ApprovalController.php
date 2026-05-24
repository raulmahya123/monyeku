<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\ApprovalConfig;
use App\Models\Budget;
use App\Models\Invoice;
use App\Models\Debt;
use App\Models\StockOpname;
use App\Models\Transaction;
use App\Models\Category;
use App\Services\ApprovalService;
use App\Traits\Auditable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    private ApprovalService $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    public function index(Request $request)
    {
        $companyId = Auth::user()->current_company_id;
        $type = $request->get('type', 'transaction');

        $pendingTransactions = collect();
        $pendingInvoices = collect();
        $pendingDebts = collect();
        $pendingBudgets = collect();
        $pendingStockOpnames = collect();

        if (in_array($type, ['transaction', 'all'])) {
            $pendingTransactions = Transaction::with(['category', 'user', 'approvals.approver'])
                ->where('company_id', $companyId)
                ->where('status', 'pending')
                ->orderBy('transaction_date', 'desc')
                ->paginate(config('moneyku.pagination', 15));
        }

        if (in_array($type, ['invoice', 'all'])) {
            $pendingInvoices = Invoice::with(['user', 'approvals.approver'])
                ->where('company_id', $companyId)
                ->where('approval_status', 'pending')
                ->orderBy('created_at', 'desc')
                ->paginate(config('moneyku.pagination', 15));
        }

        if (in_array($type, ['debt', 'all'])) {
            $pendingDebts = Debt::with(['approvals.approver'])
                ->where('company_id', $companyId)
                ->where('approval_status', 'pending')
                ->orderBy('created_at', 'desc')
                ->paginate(config('moneyku.pagination', 15));
        }

        if (in_array($type, ['budget', 'all'])) {
            $pendingBudgets = Budget::with(['category', 'approvals.approver'])
                ->where('company_id', $companyId)
                ->where('approval_status', 'pending')
                ->orderBy('created_at', 'desc')
                ->paginate(config('moneyku.pagination', 15));
        }

        if (in_array($type, ['stock_opname', 'all'])) {
            $pendingStockOpnames = StockOpname::with(['warehouse', 'createdBy', 'approvals.approver'])
                ->where('company_id', $companyId)
                ->where('approval_status', 'pending')
                ->orderBy('opname_date', 'desc')
                ->paginate(config('moneyku.pagination', 15));
        }

        $history = collect();

        if ($type === 'all') {
            $txHistory = Transaction::with(['category', 'user', 'approvals.approver'])
                ->where('company_id', $companyId)
                ->whereIn('status', ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->limit(10)->get();

            $invHistory = Invoice::with(['user', 'approvals.approver'])
                ->where('company_id', $companyId)
                ->whereIn('approval_status', ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->limit(10)->get();

            $debtHistory = Debt::with(['approvals.approver'])
                ->where('company_id', $companyId)
                ->whereIn('approval_status', ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->limit(10)->get();

            $budgetHistory = Budget::with(['category', 'approvals.approver'])
                ->where('company_id', $companyId)
                ->whereIn('approval_status', ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->limit(10)->get();

            $soHistory = StockOpname::with(['approvals.approver'])
                ->where('company_id', $companyId)
                ->whereIn('approval_status', ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->limit(10)->get();

            $history = $txHistory->concat($invHistory)->concat($debtHistory)->concat($budgetHistory)->concat($soHistory)->sortByDesc('updated_at')->take(20);
        } else {
            $historyModel = match ($type) {
                'invoice' => Invoice::class,
                'debt' => Debt::class,
                'stock_opname' => StockOpname::class,
                default => Transaction::class,
            };
            $statusField = $type === 'transaction' ? 'status' : 'approval_status';

            $history = $historyModel::with(['user', 'approvals.approver'])
                ->where('company_id', $companyId)
                ->whereIn($statusField, ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->limit(20)->get();
        }

        $pendingCount = [
            'transaction' => Transaction::where('company_id', $companyId)->where('status', 'pending')->count(),
            'invoice' => Invoice::where('company_id', $companyId)->where('approval_status', 'pending')->count(),
            'debt' => Debt::where('company_id', $companyId)->where('approval_status', 'pending')->count(),
            'budget' => Budget::where('company_id', $companyId)->where('approval_status', 'pending')->count(),
            'stock_opname' => StockOpname::where('company_id', $companyId)->where('approval_status', 'pending')->count(),
        ];

        return view('approvals.index', compact(
            'type', 'pendingTransactions', 'pendingInvoices', 'pendingDebts',
            'pendingBudgets', 'pendingStockOpnames', 'history', 'pendingCount'
        ));
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'approvable_type' => 'required|string',
            'approvable_id' => 'required|integer',
            'notes' => 'nullable|string|max:500',
        ]);

        $approvable = $this->findApprovable($validated['approvable_type'], $validated['approvable_id']);
        if (!$approvable) abort(404);

        $this->authorizeCompany($approvable);

        $approval = Approval::where('approvable_type', $validated['approvable_type'])
            ->where('approvable_id', $validated['approvable_id'])
            ->where('approver_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $approval->update([
            'status' => 'approved',
            'notes' => $validated['notes'],
            'approved_at' => now(),
        ]);

        $statusField = $validated['approvable_type'] === Transaction::class ? 'status' : 'approval_status';

        $config = null;
        $approvableModel = $validated['approvable_type']::find($validated['approvable_id']);
        if ($approvableModel && method_exists($approvableModel, 'approvalConfig')) {
            $config = $approvableModel->approvalConfig;
        }

        if (!$config && $approvableModel && isset($approvableModel->company_id)) {
            $type = match ($validated['approvable_type']) {
                Transaction::class => 'transaction',
                Invoice::class => 'invoice',
                Debt::class => 'debt',
                Budget::class => 'budget',
                StockOpname::class => 'stock_opname',
                default => null,
            };
            if ($type) {
                $categoryId = $approvableModel->category_id ?? null;
                $amount = $approvableModel->amount ?? $approvableModel->total ?? 0;
                $config = $this->approvalService->getConfig($type, $approvableModel->company_id, $categoryId, $amount);
            }
        }

        $mode = $config?->approval_mode ?? 'sequential';

        $sameLevelPending = Approval::where('approvable_type', $validated['approvable_type'])
            ->where('approvable_id', $validated['approvable_id'])
            ->where('level', $approval->level)
            ->where('status', 'pending')
            ->exists();

        $levelComplete = $mode === 'parallel' || !$sameLevelPending;

        if (!$levelComplete) {
            return redirect()->back()->with('success', 'Level ' . $approval->level . ' disetujui. Menunggu approval lain di level yang sama.');
        }

        $higherPending = Approval::where('approvable_type', $validated['approvable_type'])
            ->where('approvable_id', $validated['approvable_id'])
            ->where('level', '>', $approval->level)
            ->where('status', 'pending')
            ->exists();

        if ($higherPending) {
            return redirect()->back()->with('success', 'Level ' . $approval->level . ' disetujui. Menunggu level berikutnya.');
        }

        $remainingPending = Approval::where('approvable_type', $validated['approvable_type'])
            ->where('approvable_id', $validated['approvable_id'])
            ->where('status', 'pending')
            ->exists();

        if (!$remainingPending) {
            $updateData = [$statusField => 'approved'];
            if ($validated['approvable_type'] !== Transaction::class) {
                $updateData['approved_at'] = now();
            }
            $approvable->update($updateData);

            try {
                $approvableClass = $validated['approvable_type'];
                if (in_array(Auditable::class, class_uses($approvableClass))) {
                    $approvableClass::logCustom('approved', $approvable, ['status' => $approvable->getOriginal($statusField)], ['status' => 'approved']);
                }
            } catch (\Exception $e) {}

            if ($validated['approvable_type'] === Budget::class) {
                $budget = Budget::find($validated['approvable_id']);
                if ($budget) {
                    $now = now();
                    $spent = \App\Models\Transaction::where('company_id', $budget->company_id)
                        ->where('type', 'expense')
                        ->where('status', 'approved');
                    if ($budget->category_id) {
                        $spent->where('category_id', $budget->category_id);
                    }
                    if ($budget->period === 'monthly') {
                        $spent->whereMonth('transaction_date', $now->month)->whereYear('transaction_date', $now->year);
                    } else {
                        $spent->whereYear('transaction_date', $now->year);
                    }
                    $budget->update(['spent' => $spent->sum('amount')]);
                }
            }
        }

        return redirect()->back()->with('success', 'Berhasil disetujui.');
    }

    public function reject(Request $request)
    {
        $validated = $request->validate([
            'approvable_type' => 'required|string',
            'approvable_id' => 'required|integer',
            'notes' => 'required|string|max:500',
        ]);

        $approvable = $this->findApprovable($validated['approvable_type'], $validated['approvable_id']);
        if (!$approvable) abort(404);

        $this->authorizeCompany($approvable);

        $approval = Approval::where('approvable_type', $validated['approvable_type'])
            ->where('approvable_id', $validated['approvable_id'])
            ->where('approver_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $approval->update([
            'status' => 'rejected',
            'notes' => $validated['notes'],
            'approved_at' => now(),
        ]);

        $statusField = $validated['approvable_type'] === Transaction::class ? 'status' : 'approval_status';
        $approvable->update([$statusField => 'rejected']);

        Approval::where('approvable_type', $validated['approvable_type'])
            ->where('approvable_id', $validated['approvable_id'])
            ->where('status', 'pending')
            ->update(['status' => 'rejected', 'notes' => 'Dibatalkan otomatis']);

        try {
            $approvableClass = $validated['approvable_type'];
            if (in_array(Auditable::class, class_uses($approvableClass))) {
                $approvableClass::logCustom('rejected', $approvable, ['status' => $approvable->getOriginal($statusField)], ['status' => 'rejected']);
            }
        } catch (\Exception $e) {}

        return redirect()->back()->with('success', 'Ditolak.');
    }

    public function config()
    {
        $companyId = Auth::user()->current_company_id;
        $type = request('type', 'transaction');

        $configs = ApprovalConfig::with('category')
            ->where('company_id', $companyId)
            ->where('type', $type)
            ->get();

        $categories = Category::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        return view('approvals.config', compact('configs', 'categories', 'type'));
    }

    public function configStore(Request $request)
    {
        $companyId = Auth::user()->current_company_id;

        $validated = $request->validate([
            'type' => 'required|in:transaction,invoice,debt,budget,stock_opname',
            'category_id' => 'nullable|exists:categories,id',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'requires_level_1' => 'boolean',
            'requires_level_2' => 'boolean',
            'requires_level_3' => 'boolean',
            'level_1_role' => 'required|in:admin,owner',
            'level_2_role' => 'required|in:admin,owner',
            'level_3_role' => 'nullable|in:admin,owner',
            'deadline_hours' => 'nullable|integer|min:1|max:720',
            'approval_mode' => 'nullable|in:sequential,parallel',
            'assigned_to' => 'nullable|exists:users,id',
            'effective_from' => 'nullable|date',
            'effective_until' => 'nullable|date|after_or_equal:effective_from',
            'skip_role' => 'nullable|in:admin,owner',
            'level_2_min_amount' => 'nullable|numeric|min:0',
            'level_3_min_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validated['effective_until'] ?? null && !($validated['effective_from'] ?? null)) {
            $validated['effective_from'] = now()->toDateString();
        }

        $validated['company_id'] = $companyId;
        $validated['is_active'] = true;

        ApprovalConfig::create($validated);

        return redirect()->back()->with('success', 'Aturan approval berhasil ditambahkan.');
    }

    public function configDestroy(ApprovalConfig $config)
    {
        if ($config->company_id !== Auth::user()->current_company_id) {
            abort(403);
        }

        $config->delete();

        return redirect()->back()->with('success', 'Aturan approval berhasil dihapus.');
    }

    private function findApprovable(string $type, int $id)
    {
        return match ($type) {
            Transaction::class => Transaction::find($id),
            Invoice::class => Invoice::find($id),
            Debt::class => Debt::find($id),
            Budget::class => Budget::find($id),
            StockOpname::class => StockOpname::find($id),
            default => null,
        };
    }

    private function authorizeCompany($approvable): void
    {
        $companyId = Auth::user()->current_company_id;
        if ($approvable->company_id !== $companyId) {
            abort(403);
        }
    }
}
