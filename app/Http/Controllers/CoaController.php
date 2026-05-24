<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoaController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function index()
    {
        $companyId = $this->getCompanyId();
        $groups = Coa::where('company_id', $companyId)->whereNull('parent_id')
            ->orderBy('code')->get();
        $accounts = Coa::where('company_id', $companyId)
            ->orderBy('code')->get();

        $totalAsset = Coa::where('company_id', $companyId)->where('type', 'asset')
            ->get()->sum(fn($c) => $c->balance);
        $totalLiability = Coa::where('company_id', $companyId)->where('type', 'liability')
            ->get()->sum(fn($c) => $c->balance);
        $totalEquity = Coa::where('company_id', $companyId)->where('type', 'equity')
            ->get()->sum(fn($c) => $c->balance);
        $totalIncome = Coa::where('company_id', $companyId)->where('type', 'income')
            ->get()->sum(fn($c) => $c->balance);
        $totalExpense = Coa::where('company_id', $companyId)->where('type', 'expense')
            ->get()->sum(fn($c) => $c->balance);

        return view('coa.index', compact(
            'groups', 'accounts',
            'totalAsset', 'totalLiability', 'totalEquity',
            'totalIncome', 'totalExpense'
        ));
    }

    public function create()
    {
        $companyId = $this->getCompanyId();
        $groups = Coa::where('company_id', $companyId)->whereNull('parent_id')
            ->orderBy('code')->get();
        $types = [
            'asset' => 'Aset',
            'liability' => 'Kewajiban',
            'equity' => 'Modal',
            'income' => 'Pendapatan',
            'expense' => 'Beban',
        ];
        $balances = ['debit' => 'Debit', 'credit' => 'Kredit'];
        return view('coa.create', compact('groups', 'types', 'balances'));
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:chart_of_accounts,code,NULL,id,company_id,' . $companyId,
            'name' => 'required|string|max:200',
            'type' => 'required|in:asset,liability,equity,income,expense',
            'normal_balance' => 'required|in:debit,credit',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['company_id'] = $companyId;

        Coa::create($validated);

        return redirect()->route('coa.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(Coa $coa)
    {
        if ($coa->company_id !== $this->getCompanyId()) abort(403);

        $companyId = $this->getCompanyId();
        $groups = Coa::where('company_id', $companyId)->whereNull('parent_id')
            ->where('id', '!=', $coa->id)->orderBy('code')->get();
        $types = [
            'asset' => 'Aset',
            'liability' => 'Kewajiban',
            'equity' => 'Modal',
            'income' => 'Pendapatan',
            'expense' => 'Beban',
        ];
        $balances = ['debit' => 'Debit', 'credit' => 'Kredit'];
        return view('coa.edit', compact('coa', 'groups', 'types', 'balances'));
    }

    public function update(Request $request, Coa $coa)
    {
        if ($coa->company_id !== $this->getCompanyId()) abort(403);

        $companyId = $this->getCompanyId();
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:chart_of_accounts,code,' . $coa->id . ',id,company_id,' . $companyId,
            'name' => 'required|string|max:200',
            'type' => 'required|in:asset,liability,equity,income,expense',
            'normal_balance' => 'required|in:debit,credit',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string|max:500',
        ]);

        $coa->update($validated);

        return redirect()->route('coa.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(Coa $coa)
    {
        if ($coa->company_id !== $this->getCompanyId()) abort(403);

        if ($coa->children()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus grup yang memiliki sub-akun.');
        }

        $coa->delete();
        return redirect()->route('coa.index')->with('success', 'Akun berhasil dihapus.');
    }
}
