<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\ApprovalService;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
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
        $query = Invoice::where('company_id', $companyId);

        if ($request->filled('status')) {
            if ($request->status === 'pending_approval') {
                $query->where('approval_status', 'pending');
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('invoice_number', 'like', '%' . $request->search . '%');
            });
        }

        $invoices = $query->orderBy('issue_date', 'desc')->paginate(config('moneyku.pagination', 15));

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('invoices.create');
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $items = collect($validated['items'])->map(function ($item) {
            $item['total'] = $item['quantity'] * $item['price'];
            return $item;
        });

        $subtotal = $items->sum('total');
        $total = $subtotal + $validated['tax'];

        $approvalConfig = $this->approvalService->getConfig('invoice', $companyId, null, $total);

        $shouldSkip = $approvalConfig && $this->approvalService->shouldSkipApproval($approvalConfig, Auth::user(), $companyId);

        if ($this->approvalService->requiresApproval($approvalConfig) && !$shouldSkip) {
            $approvalStatus = 'pending';
            $status = 'unpaid';
        } else {
            $approvalStatus = 'approved';
            $status = 'unpaid';
        }

        $invoice = Invoice::create([
            'company_id' => $companyId,
            'user_id' => Auth::id(),
            'invoice_number' => Invoice::generateNumber($companyId),
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'],
            'items' => $items->toArray(),
            'subtotal' => $subtotal,
            'tax' => $validated['tax'],
            'total' => $total,
            'status' => $status,
            'approval_status' => $approvalStatus,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'notes' => $validated['notes'],
        ]);

        if ($approvalStatus === 'pending') {
            $this->approvalService->createApprovals($invoice, $approvalConfig, $companyId, $total);
        }

        $message = $approvalStatus === 'pending'
            ? 'Invoice menunggu persetujuan sebelum dikirim.'
            : 'Invoice berhasil dibuat.';

        return redirect()->route('invoices.show', $invoice)->with('success', $message);
    }

    public function show(Invoice $invoice)
    {
        $this->authorizeAccess($invoice);
        $invoice->load(['approvals.approver']);
        return view('invoices.show', compact('invoice'));
    }

    public function exportPdf(Invoice $invoice)
    {
        $companyId = Auth::user()->current_company_id;
        if ($invoice->company_id !== $companyId) abort(403);

        $pdf = Pdf::loadView('pdfs.invoice', compact('invoice'));
        return $pdf->download('INVOICE-' . $invoice->invoice_number . '.pdf');
    }

    public function edit(Invoice $invoice)
    {
        $this->authorizeAccess($invoice);

        if ($invoice->status === 'paid') {
            return redirect()->back()->with('error', 'Invoice yang sudah lunas tidak dapat diedit.');
        }

        if ($invoice->approval_status === 'pending') {
            return redirect()->back()->with('error', 'Invoice yang menunggu approval tidak dapat diedit.');
        }

        return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorizeAccess($invoice);

        if ($invoice->status === 'paid') {
            return redirect()->back()->with('error', 'Invoice yang sudah lunas tidak dapat diedit.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:unpaid,paid,cancelled',
        ]);

        $items = collect($validated['items'])->map(function ($item) {
            $item['total'] = $item['quantity'] * $item['price'];
            return $item;
        });

        $subtotal = $items->sum('total');
        $total = $subtotal + $validated['tax'];

        $data = [
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'],
            'items' => $items->toArray(),
            'subtotal' => $subtotal,
            'tax' => $validated['tax'],
            'total' => $total,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'notes' => $validated['notes'],
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'paid' && !$invoice->paid_at) {
            $data['paid_at'] = now();
        }

        $invoice->update($data);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice berhasil diperbarui.');
    }

    public function markPaid(Invoice $invoice)
    {
        $this->authorizeAccess($invoice);
        $invoice->update(['status' => 'paid', 'paid_at' => now()]);

        $this->accountingService->createFromInvoice($invoice);

        return redirect()->back()->with('success', 'Invoice ditandai sebagai lunas.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorizeAccess($invoice);
        $invoice->update(['status' => 'cancelled']);

        return redirect()->route('invoices.index')->with('success', 'Invoice dibatalkan.');
    }

    private function authorizeAccess(Invoice $invoice)
    {
        if ($invoice->company_id !== $this->getCompanyId()) {
            abort(403);
        }
    }
}
