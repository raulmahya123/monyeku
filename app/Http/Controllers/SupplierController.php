<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function index()
    {
        $companyId = $this->getCompanyId();
        $suppliers = Supplier::where('company_id', $companyId)
            ->orderBy('name')->get();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:suppliers,code,NULL,id,company_id,' . $companyId,
            'name' => 'required|string|max:200',
            'contact_person' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:200',
            'address' => 'nullable|string|max:500',
            'npwp' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $companyId;

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        if ($supplier->company_id !== $this->getCompanyId()) abort(403);

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        if ($supplier->company_id !== $this->getCompanyId()) abort(403);

        $companyId = $this->getCompanyId();
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:suppliers,code,' . $supplier->id . ',id,company_id,' . $companyId,
            'name' => 'required|string|max:200',
            'contact_person' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:200',
            'address' => 'nullable|string|max:500',
            'npwp' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->company_id !== $this->getCompanyId()) abort(403);

        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
