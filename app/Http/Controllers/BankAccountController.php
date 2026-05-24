<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Coa;
use App\Models\JournalLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function index()
    {
        $companyId = $this->getCompanyId();
        $accounts = BankAccount::where('company_id', $companyId)
            ->orderBy('bank_name')
            ->get();
        return view('bank-accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('bank-accounts.create');
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50|unique:bank_accounts,account_number,NULL,id,company_id,' . $companyId,
            'account_name' => 'required|string|max:200',
            'currency' => 'required|string|max:10',
            'opening_balance' => 'required|numeric',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['company_id'] = $companyId;
        $validated['is_active'] = $request->boolean('is_active');

        BankAccount::create($validated);

        return redirect()->route('bank-accounts.index')->with('success', 'Rekening bank berhasil ditambahkan.');
    }

    public function edit(BankAccount $bankAccount)
    {
        if ($bankAccount->company_id !== $this->getCompanyId()) abort(403);
        return view('bank-accounts.edit', compact('bankAccount'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        if ($bankAccount->company_id !== $this->getCompanyId()) abort(403);

        $companyId = $this->getCompanyId();
        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50|unique:bank_accounts,account_number,' . $bankAccount->id . ',id,company_id,' . $companyId,
            'account_name' => 'required|string|max:200',
            'currency' => 'required|string|max:10',
            'opening_balance' => 'required|numeric',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $bankAccount->update($validated);

        return redirect()->route('bank-accounts.index')->with('success', 'Rekening bank berhasil diperbarui.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        if ($bankAccount->company_id !== $this->getCompanyId()) abort(403);
        $bankAccount->delete();
        return redirect()->route('bank-accounts.index')->with('success', 'Rekening bank berhasil dihapus.');
    }

    public function balance(BankAccount $bankAccount)
    {
        if ($bankAccount->company_id !== Auth::user()->current_company_id) abort(403);

        $coaCash = Coa::where('company_id', $bankAccount->company_id)
            ->where('code', '1-1200')->first()
            ?? Coa::where('company_id', $bankAccount->company_id)->where('type', 'asset')->first();

        $balance = $bankAccount->opening_balance;
        if ($coaCash) {
            $balance += JournalLine::where('coa_id', $coaCash->id)
                ->whereHas('journal', fn($q) => $q->where('company_id', $bankAccount->company_id))
                ->sum(\DB::raw('debit - credit'));
        }

        return response()->json(['balance' => $balance]);
    }
}
