<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankReconciliation;
use App\Models\Coa;
use App\Models\Journal;
use App\Models\JournalLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankReconciliationController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function index()
    {
        $companyId = $this->getCompanyId();
        $accounts = BankAccount::where('company_id', $companyId)->get();
        $reconciliations = BankReconciliation::with(['bankAccount', 'creator'])
            ->where('company_id', $companyId)
            ->orderBy('period', 'desc')
            ->paginate(config('moneyku.pagination', 15));
        return view('bank-reconciliations.index', compact('accounts', 'reconciliations'));
    }

    public function create()
    {
        $companyId = $this->getCompanyId();
        $accounts = BankAccount::where('company_id', $companyId)
            ->where('is_active', true)->get();
        return view('bank-reconciliations.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'period' => 'required|date_format:Y-m',
            'statement_date' => 'required|date',
            'opening_balance' => 'required|numeric|min:0',
            'closing_balance' => 'required|numeric|min:0',
            'statement_lines' => 'nullable|json',
        ]);

        $bankAccount = BankAccount::findOrFail($validated['bank_account_id']);
        if ($bankAccount->company_id !== $companyId) abort(403);

        $exists = BankReconciliation::where('company_id', $companyId)
            ->where('bank_account_id', $validated['bank_account_id'])
            ->where('period', $validated['period'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Rekonsiliasi untuk periode ini sudah ada.')->withInput();
        }

        $coaCash = Coa::where('company_id', $companyId)
            ->where('code', '1-1200')->first()
            ?? Coa::where('company_id', $companyId)->where('type', 'asset')->first();

        $systemBalance = JournalLine::whereHas('journal', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
        ->where('coa_id', $coaCash->id)
        ->whereHas('journal', function ($q) use ($validated) {
            $q->where('date', '<=', $validated['statement_date']);
        })
        ->sum(\DB::raw('debit - credit'));

        $difference = $validated['closing_balance'] - $systemBalance;

        $reconciliation = BankReconciliation::create([
            'company_id' => $companyId,
            'bank_account_id' => $validated['bank_account_id'],
            'period' => $validated['period'],
            'statement_date' => $validated['statement_date'],
            'opening_balance' => $validated['opening_balance'],
            'closing_balance' => $validated['closing_balance'],
            'system_balance' => $systemBalance,
            'difference' => $difference,
            'status' => 'draft',
            'statement_lines' => $validated['statement_lines'] ? json_decode($validated['statement_lines'], true) : null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('bank-reconciliations.show', $reconciliation)
            ->with('success', 'Rekonsiliasi bank berhasil dibuat.');
    }

    public function show(BankReconciliation $reconciliation)
    {
        if ($reconciliation->company_id !== $this->getCompanyId()) abort(403);
        $reconciliation->load(['bankAccount', 'creator']);

        $companyId = $this->getCompanyId();
        $coaCash = Coa::where('company_id', $companyId)->where('code', '1-1200')->first()
            ?? Coa::where('company_id', $companyId)->where('type', 'asset')->first();

        $systemEntries = [];
        if ($coaCash) {
            $systemEntries = JournalLine::with(['journal.creator'])
                ->where('coa_id', $coaCash->id)
                ->whereHas('journal', function ($q) use ($reconciliation) {
                    $q->where('company_id', $reconciliation->company_id)
                      ->whereBetween('date', [
                          $reconciliation->period . '-01',
                          $reconciliation->statement_date
                      ]);
                })
                ->orderBy('journal.date')
                ->get()
                ->map(fn($l) => [
                    'date' => $l->journal->date->format('Y-m-d'),
                    'description' => $l->journal->description,
                    'debit' => $l->debit,
                    'credit' => $l->credit,
                    'user' => $l->journal->creator?->name,
                ]);
        }

        return view('bank-reconciliations.show', compact('reconciliation', 'systemEntries'));
    }

    public function complete(BankReconciliation $reconciliation)
    {
        if ($reconciliation->company_id !== $this->getCompanyId()) abort(403);
        if ($reconciliation->status === 'completed') {
            return redirect()->back()->with('error', 'Rekonsiliasi sudah selesai.');
        }

        $reconciliation->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('bank-reconciliations.index')
            ->with('success', 'Rekonsiliasi bank berhasil dikompletasi.');
    }

    public function destroy(BankReconciliation $reconciliation)
    {
        if ($reconciliation->company_id !== $this->getCompanyId()) abort(403);
        $reconciliation->delete();
        return redirect()->route('bank-reconciliations.index')
            ->with('success', 'Rekonsiliasi bank berhasil dihapus.');
    }
}
