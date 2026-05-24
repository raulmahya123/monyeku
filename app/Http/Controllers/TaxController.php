<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function index()
    {
        $companyId = $this->getCompanyId();
        $taxes = Tax::where('company_id', $companyId)
            ->orderBy('name')->get();

        return view('taxes.index', compact('taxes'));
    }

    public function create()
    {
        return view('taxes.create');
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:taxes,code,NULL,id,company_id,' . $companyId,
            'name' => 'required|string|max:200',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:ppn,pph',
            'account_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $companyId;

        Tax::create($validated);

        return redirect()->route('taxes.index')->with('success', 'Pajak berhasil ditambahkan.');
    }

    public function edit(Tax $tax)
    {
        if ($tax->company_id !== $this->getCompanyId()) abort(403);

        return view('taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        if ($tax->company_id !== $this->getCompanyId()) abort(403);

        $companyId = $this->getCompanyId();
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:taxes,code,' . $tax->id . ',id,company_id,' . $companyId,
            'name' => 'required|string|max:200',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:ppn,pph',
            'account_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $tax->update($validated);

        return redirect()->route('taxes.index')->with('success', 'Pajak berhasil diperbarui.');
    }

    public function destroy(Tax $tax)
    {
        if ($tax->company_id !== $this->getCompanyId()) abort(403);

        $tax->delete();
        return redirect()->route('taxes.index')->with('success', 'Pajak berhasil dihapus.');
    }
}
