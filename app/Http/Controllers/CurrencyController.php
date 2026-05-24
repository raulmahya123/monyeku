<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurrencyController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function index()
    {
        $companyId = $this->getCompanyId();
        $currencies = Currency::where('company_id', $companyId)
            ->orderBy('code')->get();

        return view('currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('currencies.create');
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:currencies,code,NULL,id,company_id,' . $companyId,
            'name' => 'required|string|max:100',
            'symbol' => 'nullable|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'is_base' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $companyId;

        if (!empty($validated['is_base'])) {
            Currency::where('company_id', $companyId)->update(['is_base' => false]);
        }

        Currency::create($validated);

        return redirect()->route('currencies.index')->with('success', 'Currency berhasil ditambahkan.');
    }

    public function edit(Currency $currency)
    {
        if ($currency->company_id !== $this->getCompanyId()) abort(403);

        return view('currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        if ($currency->company_id !== $this->getCompanyId()) abort(403);

        $companyId = $this->getCompanyId();
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:currencies,code,' . $currency->id . ',id,company_id,' . $companyId,
            'name' => 'required|string|max:100',
            'symbol' => 'nullable|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'is_base' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['is_base'])) {
            Currency::where('company_id', $companyId)->where('id', '!=', $currency->id)->update(['is_base' => false]);
        }

        $currency->update($validated);

        return redirect()->route('currencies.index')->with('success', 'Currency berhasil diperbarui.');
    }

    public function destroy(Currency $currency)
    {
        if ($currency->company_id !== $this->getCompanyId()) abort(403);

        $currency->delete();
        return redirect()->route('currencies.index')->with('success', 'Currency berhasil dihapus.');
    }
}
