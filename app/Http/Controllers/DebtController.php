<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Services\AccountingService;
use App\Traits\Auditable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebtController extends Controller
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

    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        $query = Debt::where('company_id', $companyId);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'pending_approval') {
                $query->where('approval_status', 'pending');
            } else {
                $query->where('status', $request->status);
            }
        }

        $debts = $query->orderBy('due_date')->paginate(config('moneyku.pagination', 15));

        $totalReceivable = Debt::where('company_id', $companyId)
            ->where('type', 'receivable')->whereIn('status', ['active', 'overdue'])
            ->sum('remaining');

        $totalPayable = Debt::where('company_id', $companyId)
            ->where('type', 'payable')->whereIn('status', ['active', 'overdue'])
            ->sum('remaining');

        return view('debts.index', compact('debts', 'totalReceivable', 'totalPayable'));
    }

    public function create()
    {
        return view('debts.create');
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'type' => 'required|in:receivable,payable',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'description' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['company_id'] = $companyId;
        $validated['paid_amount'] = 0;
        $validated['remaining'] = $validated['amount'];

        $approvalConfig = $this->approvalService->getConfig('debt', $companyId, null, $validated['amount']);

        $shouldSkip = $approvalConfig && $this->approvalService->shouldSkipApproval($approvalConfig, Auth::user(), $companyId);

        if ($this->approvalService->requiresApproval($approvalConfig) && !$shouldSkip) {
            $validated['status'] = 'active';
            $validated['approval_status'] = 'pending';
        } else {
            $validated['status'] = 'active';
            $validated['approval_status'] = 'approved';
        }

        $debt = Debt::create($validated);

        if ($validated['approval_status'] === 'pending') {
            $this->approvalService->createApprovals($debt, $approvalConfig, $companyId, $validated['amount']);
        }

        $label = $validated['type'] === 'receivable' ? 'Piutang' : 'Hutang';
        $message = $validated['approval_status'] === 'pending'
            ? "{$label} menunggu persetujuan."
            : "{$label} berhasil dicatat.";

        return redirect()->route('debts.index')->with('success', $message);
    }

    public function edit(Debt $debt)
    {
        $this->authorizeAccess($debt);
        return view('debts.edit', compact('debt'));
    }

    public function update(Request $request, Debt $debt)
    {
        $this->authorizeAccess($debt);

        $validated = $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'description' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $validated['remaining'] = $validated['amount'] - $validated['paid_amount'];

        if ($validated['remaining'] <= 0) {
            $validated['status'] = 'paid';
        } elseif (now()->startOfDay()->gt(\Carbon\Carbon::parse($validated['due_date']))) {
            $validated['status'] = 'overdue';
        } else {
            $validated['status'] = 'active';
        }

        $debt->update($validated);

        return redirect()->route('debts.index')->with('success', 'Data hutang/piutang berhasil diperbarui.');
    }

    public function pay(Request $request, Debt $debt)
    {
        $this->authorizeAccess($debt);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0|max:' . $debt->remaining,
        ]);

        $newPaid = $debt->paid_amount + $validated['amount'];
        $newRemaining = $debt->amount - $newPaid;

        $data = [
            'paid_amount' => $newPaid,
            'remaining' => $newRemaining,
        ];

        if ($newRemaining <= 0) {
            $data['status'] = 'paid';
        }

        $debt->update($data);

        $this->accountingService->createFromDebt($debt);

        $label = $debt->type === 'receivable' ? 'Pembayaran piutang' : 'Pembayaran hutang';
        return redirect()->route('debts.index')->with('success', "{$label} berhasil dicatat.");
    }

    public function destroy(Debt $debt)
    {
        $this->authorizeAccess($debt);
        $debt->delete();

        return redirect()->route('debts.index')->with('success', 'Data berhasil dihapus.');
    }

    private function authorizeAccess(Debt $debt)
    {
        if ($debt->company_id !== $this->getCompanyId()) {
            abort(403);
        }
    }
}
