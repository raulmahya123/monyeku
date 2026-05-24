<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\ApprovalConfig;
use App\Models\Approval;
use App\Services\ApprovalService;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
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
        $query = Transaction::with(['category', 'user', 'attachments'])
            ->where('company_id', $companyId);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('nota_number', 'like', '%' . $request->search . '%');
            });
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(config('moneyku.pagination', 15));

        $categories = Category::where('company_id', $companyId)->where('is_active', true)->get();

        return view('transactions.index', compact('transactions', 'categories'));
    }

    public function create()
    {
        $companyId = $this->getCompanyId();
        $incomeCategories = Category::where('company_id', $companyId)
            ->where('type', 'income')->where('is_active', true)->get();
        $expenseCategories = Category::where('company_id', $companyId)
            ->where('type', 'expense')->where('is_active', true)->get();

        return view('transactions.create', compact('incomeCategories', 'expenseCategories'));
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'nota_number' => 'nullable|string|max:100',
            'payment_method' => 'required|in:cash,bank,qris,transfer',
            'attachments' => 'nullable|array',
            'attachments.*' => 'image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $validated['company_id'] = $companyId;
        $validated['user_id'] = Auth::id();

        $approvalConfig = $this->approvalService->getConfig(
            'transaction', $companyId, $validated['category_id'], $validated['amount']
        );

        $shouldSkip = $approvalConfig && $this->approvalService->shouldSkipApproval($approvalConfig, Auth::user(), $companyId);

        if ($this->approvalService->requiresApproval($approvalConfig) && !$shouldSkip) {
            $validated['status'] = 'pending';
        } else {
            $validated['status'] = 'approved';
        }

        $transaction = Transaction::create($validated);

        if ($validated['status'] === 'pending') {
            $this->approvalService->createApprovals($transaction, $approvalConfig, $companyId, $validated['amount']);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/' . $transaction->id, 'public');
                $transaction->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        if ($validated['status'] === 'approved') {
            $this->accountingService->createFromTransaction($transaction);
        }

        $message = $validated['status'] === 'pending'
            ? 'Transaksi menunggu persetujuan.'
            : 'Transaksi berhasil dicatat.';

        return redirect()->route('transactions.index')->with('success', $message);
    }

    public function show(Transaction $transaction)
    {
        $this->authorizeAccess($transaction);
        $transaction->load(['category', 'user', 'attachments', 'approvals.approver']);

        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $this->authorizeAccess($transaction);

        $companyId = $this->getCompanyId();
        $incomeCategories = Category::where('company_id', $companyId)
            ->where('type', 'income')->where('is_active', true)->get();
        $expenseCategories = Category::where('company_id', $companyId)
            ->where('type', 'expense')->where('is_active', true)->get();

        return view('transactions.edit', compact('transaction', 'incomeCategories', 'expenseCategories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorizeAccess($transaction);

        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya transaksi pending yang bisa diedit.');
        }

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'nota_number' => 'nullable|string|max:100',
            'payment_method' => 'required|in:cash,bank,qris,transfer',
            'attachments' => 'nullable|array',
            'attachments.*' => 'image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $transaction->update($validated);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/' . $transaction->id, 'public');
                $transaction->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorizeAccess($transaction);

        if ($transaction->status === 'approved') {
            return redirect()->back()->with('error', 'Transaksi yang sudah disetujui tidak dapat dihapus.');
        }

        foreach ($transaction->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $transaction->attachments()->delete();
        $transaction->approvals()->delete();
        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    private function authorizeAccess(Transaction $transaction)
    {
        if ($transaction->company_id !== $this->getCompanyId()) {
            abort(403);
        }
    }
}
