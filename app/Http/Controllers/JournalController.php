<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\JournalLine;
use App\Models\Coa;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    private AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index(Request $request)
    {
        $companyId = Auth::user()->current_company_id;

        $query = Journal::with(['lines.coa', 'creator'])
            ->where('company_id', $companyId);

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $journals = $query->orderBy('date', 'desc')->orderBy('id', 'desc')
            ->paginate(config('moneyku.pagination', 15));

        return view('journals.index', compact('journals'));
    }

    public function create()
    {
        $companyId = Auth::user()->current_company_id;
        $accounts = Coa::where('company_id', $companyId)
            ->orderBy('code')->get();
        return view('journals.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->current_company_id;

        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'lines' => 'required|array|min:2',
            'lines.*.coa_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'lines.*.description' => 'nullable|string|max:500',
        ]);

        $totalDebit = 0;
        $totalCredit = 0;
        foreach ($validated['lines'] as $line) {
            $debit = floatval($line['debit'] ?? 0);
            $credit = floatval($line['credit'] ?? 0);
            if ($debit > 0 && $credit > 0) {
                return redirect()->back()->with('error', 'Satu baris tidak bisa memiliki debit dan credit sekaligus.')->withInput();
            }
            if ($debit == 0 && $credit == 0) {
                return redirect()->back()->with('error', 'Setiap baris harus memiliki debit atau credit.')->withInput();
            }
            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return redirect()->back()->with('error', 'Total debit (' . number_format($totalDebit, 0, ',', '.') . ') harus sama dengan total credit (' . number_format($totalCredit, 0, ',', '.') . ').')->withInput();
        }

        $journal = DB::transaction(function () use ($companyId, $validated) {
            $journal = Journal::create([
                'company_id' => $companyId,
                'reference_type' => null,
                'reference_id' => null,
                'date' => $validated['date'],
                'description' => $validated['description'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['lines'] as $line) {
                $journal->lines()->create([
                    'coa_id' => $line['coa_id'],
                    'debit' => floatval($line['debit'] ?? 0),
                    'credit' => floatval($line['credit'] ?? 0),
                    'description' => $line['description'] ?? null,
                ]);
            }

            return $journal;
        });

        return redirect()->route('journals.show', $journal)->with('success', 'Jurnal manual berhasil dibuat.');
    }

    public function show(Journal $journal)
    {
        if ($journal->company_id !== Auth::user()->current_company_id) abort(403);
        $journal->load(['lines.coa', 'creator']);
        return view('journals.show', compact('journal'));
    }

    public function ledger(Request $request)
    {
        $companyId = Auth::user()->current_company_id;

        $coaId = $request->get('coa_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $accounts = Coa::where('company_id', $companyId)
            ->orderBy('code')->get();

        $lines = collect();
        $selectedAccount = null;

        if ($coaId) {
            $selectedAccount = Coa::find($coaId);
            $lines = JournalLine::with(['journal.creator'])
                ->whereHas('journal', function ($q) use ($companyId, $startDate, $endDate) {
                    $q->where('company_id', $companyId)
                      ->whereBetween('date', [$startDate, $endDate]);
                })
                ->where('coa_id', $coaId)
                ->orderBy('journal.date')
                ->orderBy('journal.id')
                ->get()
                ->map(function ($line) {
                    $line->journal_date = $line->journal->date;
                    $line->journal_description = $line->journal->description;
                    return $line;
                });
        }

        $openingBalance = 0;
        $runningBalance = $openingBalance;

        if ($selectedAccount && $lines->isNotEmpty()) {
            $normalDebit = $selectedAccount->normal_balance === 'debit';
            $lines = $lines->map(function ($line) use ($normalDebit, &$runningBalance) {
                $line->running_balance = $runningBalance + ($normalDebit ? $line->debit - $line->credit : $line->credit - $line->debit);
                $runningBalance = $line->running_balance;
                return $line;
            });
        }

        return view('journals.ledger', compact('accounts', 'selectedAccount', 'lines', 'startDate', 'endDate', 'coaId', 'runningBalance'));
    }

    public function balanceSheet(Request $request)
    {
        $companyId = Auth::user()->current_company_id;
        $endDate = $request->get('end_date', now()->toDateString());
        $data = $this->accountingService->getBalanceSheet($companyId, $endDate);
        return view('reports.balance-sheet', array_merge($data, ['endDate' => $endDate]));
    }

    public function incomeStatement(Request $request)
    {
        $companyId = Auth::user()->current_company_id;
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $data = $this->accountingService->getIncomeStatement($companyId, $startDate, $endDate);
        return view('reports.income-statement', array_merge($data, compact('startDate', 'endDate')));
    }

    public function cashFlow(Request $request)
    {
        $companyId = Auth::user()->current_company_id;
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $data = $this->accountingService->getCashFlow($companyId, $startDate, $endDate);
        return view('reports.cash-flow', array_merge($data, compact('startDate', 'endDate')));
    }
}
