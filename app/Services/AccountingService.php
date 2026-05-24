<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\Journal;
use App\Models\JournalLine;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\Debt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AccountingService
{
    /**
     * Auto-create journal entry from a Transaction.
     * Income: Debit Kas/Bank (COA by payment_method), Credit Pendapatan by category mapping
     * Expense: Debit Beban by category mapping, Credit Kas/Bank (COA by payment_method)
     */
    public function createFromTransaction(Transaction $transaction): Journal
    {
        return DB::transaction(function () use ($transaction) {
            $coaCash = $this->getCashCoa($transaction->company_id, $transaction->payment_method);
            $coaRevenue = $this->getCategoryCoa($transaction);

            $journal = Journal::create([
                'company_id' => $transaction->company_id,
                'reference_type' => Transaction::class,
                'reference_id' => $transaction->id,
                'date' => $transaction->transaction_date,
                'description' => $transaction->description ?? ($transaction->type === 'income' ? 'Pendapatan' : 'Beban') . ' - ' . ($transaction->category?->name ?? ''),
                'created_by' => $transaction->user_id,
            ]);

            if ($transaction->type === 'income') {
                // Debit Kas, Credit Pendapatan
                $journal->lines()->createMany([
                    ['coa_id' => $coaCash->id, 'debit' => $transaction->amount, 'credit' => 0, 'description' => $transaction->description],
                    ['coa_id' => $coaRevenue->id, 'debit' => 0, 'credit' => $transaction->amount, 'description' => $transaction->description],
                ]);
            } else {
                // Debit Beban, Credit Kas
                $journal->lines()->createMany([
                    ['coa_id' => $coaRevenue->id, 'debit' => $transaction->amount, 'credit' => 0, 'description' => $transaction->description],
                    ['coa_id' => $coaCash->id, 'debit' => 0, 'credit' => $transaction->amount, 'description' => $transaction->description],
                ]);
            }

            return $journal;
        });
    }

    /**
     * Auto-create journal entry from an Invoice (when marked paid).
     * Debit Kas/Bank, Credit Pendapatan Usaha
     */
    public function createFromInvoice(Invoice $invoice): Journal
    {
        return DB::transaction(function () use ($invoice) {
            $coaCash = Coa::where('company_id', $invoice->company_id)
                ->where('code', '1-1100')->first() ?? Coa::where('company_id', $invoice->company_id)->where('type', 'asset')->first();
            $coaRevenue = Coa::where('company_id', $invoice->company_id)
                ->where('code', '4-1000')->first() ?? Coa::where('company_id', $invoice->company_id)->where('type', 'income')->first();

            $journal = Journal::create([
                'company_id' => $invoice->company_id,
                'reference_type' => Invoice::class,
                'reference_id' => $invoice->id,
                'date' => $invoice->paid_at?->toDateString() ?? now()->toDateString(),
                'description' => 'Pembayaran Invoice ' . $invoice->invoice_number . ' - ' . $invoice->customer_name,
                'created_by' => Auth::id(),
            ]);

            $journal->lines()->createMany([
                ['coa_id' => $coaCash->id, 'debit' => $invoice->total, 'credit' => 0, 'description' => 'Penerimaan dari ' . $invoice->customer_name],
                ['coa_id' => $coaRevenue->id, 'debit' => 0, 'credit' => $invoice->total, 'description' => 'Pendapatan Invoice ' . $invoice->invoice_number],
            ]);

            return $journal;
        });
    }

    /**
     * Auto-create journal entry from a Debt payment.
     * Receivable: Debit Kas, Credit Piutang Usaha
     * Payable: Debit Hutang Usaha, Credit Kas
     */
    public function createFromDebt(Debt $debt): Journal
    {
        return DB::transaction(function () use ($debt) {
            $coaCash = Coa::where('company_id', $debt->company_id)
                ->where('code', '1-1100')->first() ?? Coa::where('company_id', $debt->company_id)->where('type', 'asset')->first();
            $coaReceivable = Coa::where('company_id', $debt->company_id)
                ->where('code', '1-1300')->first();
            $coaPayable = Coa::where('company_id', $debt->company_id)
                ->where('code', '2-1100')->first();

            $description = $debt->type === 'receivable'
                ? 'Penerimaan Piutang dari ' . $debt->contact_name
                : 'Pembayaran Hutang ke ' . $debt->contact_name;

            $journal = Journal::create([
                'company_id' => $debt->company_id,
                'reference_type' => Debt::class,
                'reference_id' => $debt->id,
                'date' => now()->toDateString(),
                'description' => $description,
                'created_by' => Auth::id(),
            ]);

            if ($debt->type === 'receivable') {
                // Debit Kas, Credit Piutang
                $journal->lines()->createMany([
                    ['coa_id' => $coaCash->id, 'debit' => $debt->amount, 'credit' => 0, 'description' => $description],
                    ['coa_id' => $coaReceivable->id, 'debit' => 0, 'credit' => $debt->amount, 'description' => $description],
                ]);
            } else {
                // Debit Hutang, Credit Kas
                $journal->lines()->createMany([
                    ['coa_id' => $coaPayable->id, 'debit' => $debt->amount, 'credit' => 0, 'description' => $description],
                    ['coa_id' => $coaCash->id, 'debit' => 0, 'credit' => $debt->amount, 'description' => $description],
                ]);
            }

            return $journal;
        });
    }

    /**
     * Get balance sheet data.
     * Returns assets, liabilities, and equity with balances.
     */
    public function getBalanceSheet(int $companyId, ?string $endDate = null): array
    {
        $endDate = $endDate ?? now()->toDateString();

        $assets = Coa::where('company_id', $companyId)->where('type', 'asset')->whereNull('parent_id')
            ->with(['children' => function ($q) use ($companyId, $endDate) {
                $q->where('company_id', $companyId);
            }])->get()->map(function ($group) {
                $group->balance = $group->children->sum(fn($c) => $c->balance);
                return $group;
            });

        $liabilities = Coa::where('company_id', $companyId)->where('type', 'liability')->whereNull('parent_id')
            ->with(['children' => function ($q) use ($companyId, $endDate) {
                $q->where('company_id', $companyId);
            }])->get()->map(function ($group) {
                $group->balance = $group->children->sum(fn($c) => $c->balance);
                return $group;
            });

        $equity = Coa::where('company_id', $companyId)->where('type', 'equity')->whereNull('parent_id')
            ->with(['children' => function ($q) use ($companyId, $endDate) {
                $q->where('company_id', $companyId);
            }])->get()->map(function ($group) {
                $group->balance = $group->children->sum(fn($c) => $c->balance);
                return $group;
            });

        // Calculate net income from income - expense
        $incomeTotal = Coa::where('company_id', $companyId)->where('type', 'income')
            ->get()->sum(fn($c) => $c->balance);
        $expenseTotal = Coa::where('company_id', $companyId)->where('type', 'expense')
            ->get()->sum(fn($c) => $c->balance);
        $netIncome = $incomeTotal - $expenseTotal;

        return compact('assets', 'liabilities', 'equity', 'netIncome');
    }

    /**
     * Get income statement data.
     */
    public function getIncomeStatement(int $companyId, ?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? now()->toDateString();

        $incomeByGroup = Coa::where('company_id', $companyId)->where('type', 'income')->whereNull('parent_id')
            ->with(['children' => function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            }])->get()->map(function ($group) {
                $group->balance = $group->children->sum(fn($c) => $c->balance);
                return $group;
            });

        $expenseByGroup = Coa::where('company_id', $companyId)->where('type', 'expense')->whereNull('parent_id')
            ->with(['children' => function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            }])->get()->map(function ($group) {
                $group->balance = $group->children->sum(fn($c) => $c->balance);
                return $group;
            });

        $totalIncome = $incomeByGroup->sum('balance');
        $totalExpense = $expenseByGroup->sum('balance');
        $netIncome = $totalIncome - $totalExpense;

        return compact('incomeByGroup', 'expenseByGroup', 'totalIncome', 'totalExpense', 'netIncome');
    }

    /**
     * Get cash flow statement (direct method) per company.
     */
    public function getCashFlow(int $companyId, string $startDate, string $endDate): array
    {
        $cashCoaIds = Coa::where('company_id', $companyId)
            ->whereIn('code', ['1-1100', '1-1200'])
            ->pluck('id');

        $lines = JournalLine::whereHas('journal', function ($q) use ($companyId, $startDate, $endDate) {
            $q->where('company_id', $companyId)
              ->whereBetween('date', [$startDate, $endDate]);
        })
        ->whereIn('coa_id', $cashCoaIds)
        ->with('journal')
        ->get();

        $totalCashIn = $lines->sum('debit');
        $totalCashOut = $lines->sum('credit');
        $netCashFlow = $totalCashIn - $totalCashOut;

        // Operating: cash from revenue COAs (code 4-xxxx)
        $revenueCoaIds = Coa::where('company_id', $companyId)
            ->where('code', 'like', '4-%')
            ->pluck('id');
        $operatingIn = JournalLine::whereHas('journal', function ($q) use ($companyId, $startDate, $endDate) {
            $q->where('company_id', $companyId)
              ->whereBetween('date', [$startDate, $endDate]);
        })
        ->whereIn('coa_id', $revenueCoaIds)
        ->sum('credit');

        $expenseCoaIds = Coa::where('company_id', $companyId)
            ->where('code', 'like', '5-%')
            ->pluck('id');
        $operatingOut = JournalLine::whereHas('journal', function ($q) use ($companyId, $startDate, $endDate) {
            $q->where('company_id', $companyId)
              ->whereBetween('date', [$startDate, $endDate]);
        })
        ->whereIn('coa_id', $expenseCoaIds)
        ->sum('debit');

        $netOperating = $operatingIn - $operatingOut;

        // Investing: COA code 1-2xxx (fixed assets)
        $investCoaIds = Coa::where('company_id', $companyId)
            ->where('code', 'like', '1-2%')
            ->pluck('id');
        $investOut = JournalLine::whereHas('journal', function ($q) use ($companyId, $startDate, $endDate) {
            $q->where('company_id', $companyId)
              ->whereBetween('date', [$startDate, $endDate]);
        })
        ->whereIn('coa_id', $investCoaIds)
        ->sum('debit');
        $investIn = JournalLine::whereHas('journal', function ($q) use ($companyId, $startDate, $endDate) {
            $q->where('company_id', $companyId)
              ->whereBetween('date', [$startDate, $endDate]);
        })
        ->whereIn('coa_id', $investCoaIds)
        ->sum('credit');
        $netInvesting = $investIn - $investOut;

        // Financing: COA code 2-xxxx (liabilities) & 3-xxxx (equity)
        $finCoaIds = Coa::where('company_id', $companyId)
            ->where(function ($q) {
                $q->where('code', 'like', '2-%')
                  ->orWhere('code', 'like', '3-%');
            })
            ->pluck('id');
        $finIn = JournalLine::whereHas('journal', function ($q) use ($companyId, $startDate, $endDate) {
            $q->where('company_id', $companyId)
              ->whereBetween('date', [$startDate, $endDate]);
        })
        ->whereIn('coa_id', $finCoaIds)
        ->sum('credit');
        $finOut = JournalLine::whereHas('journal', function ($q) use ($companyId, $startDate, $endDate) {
            $q->where('company_id', $companyId)
              ->whereBetween('date', [$startDate, $endDate]);
        })
        ->whereIn('coa_id', $finCoaIds)
        ->sum('debit');
        $netFinancing = $finIn - $finOut;

        return compact(
            'totalCashIn', 'totalCashOut', 'netCashFlow',
            'operatingIn', 'operatingOut', 'netOperating',
            'investIn', 'investOut', 'netInvesting',
            'finIn', 'finOut', 'netFinancing'
        );
    }

    private function getCashCoa(int $companyId, string $paymentMethod): Coa
    {
        $codeMap = [
            'cash' => '1-1100',
            'bank' => '1-1200',
            'transfer' => '1-1200',
            'qris' => '1-1200',
        ];
        $code = $codeMap[$paymentMethod] ?? '1-1100';
        return Coa::where('company_id', $companyId)->where('code', $code)->first()
            ?? Coa::where('company_id', $companyId)->where('type', 'asset')->first();
    }

    private function getCategoryCoa(Transaction $transaction): Coa
    {
        $category = $transaction->category;
        $companyId = $transaction->company_id;
        $coaCode = '5-1900'; // default: Beban Lain-lain

        if (!$category) {
            $coaCode = $transaction->type === 'income' ? '4-1000' : '5-1900';
        } else {
            $nameMap = [
                'Gaji' => '5-1000', 'Sewa' => '5-1100',
                'Listrik' => '5-1200', 'Air' => '5-1200',
                'Telepon' => '5-1300', 'Internet' => '5-1300',
                'Transportasi' => '5-1400', 'Transport' => '5-1400',
                'Perlengkapan' => '5-1500', 'Alat Tulis' => '5-1500', 'ATK' => '5-1500',
                'Pemasaran' => '5-1700', 'Iklan' => '5-1700', 'Promosi' => '5-1700',
                'Makan' => '5-1800', 'Minum' => '5-1800',
                'Pendapatan' => '4-1000', 'Penjualan' => '4-1000', 'Jasa' => '4-1000',
                'Lain' => '5-1900',
            ];

            foreach ($nameMap as $keyword => $c) {
                if (stripos($category->name, $keyword) !== false) {
                    $coaCode = $c;
                    break;
                }
            }

            if ($transaction->type === 'income' && $coaCode === '5-1900') {
                $coaCode = '4-1000';
            }
        }

        return Coa::where('company_id', $companyId)->where('code', $coaCode)->first()
            ?? Coa::where('company_id', $companyId)->where('type', $transaction->type === 'income' ? 'income' : 'expense')->first();
    }
}
