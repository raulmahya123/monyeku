<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function index()
    {
        $companyId = $this->getCompanyId();
        $customers = Customer::where('company_id', $companyId)
            ->orderBy('name')->get();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:customers,code,NULL,id,company_id,' . $companyId,
            'name' => 'required|string|max:200',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:200',
            'address' => 'nullable|string|max:500',
            'npwp' => 'nullable|string|max:30',
            'contact_person' => 'nullable|string|max:200',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $companyId;

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        if ($customer->company_id !== $this->getCompanyId()) abort(403);

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        if ($customer->company_id !== $this->getCompanyId()) abort(403);

        $companyId = $this->getCompanyId();
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:customers,code,' . $customer->id . ',id,company_id,' . $companyId,
            'name' => 'required|string|max:200',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:200',
            'address' => 'nullable|string|max:500',
            'npwp' => 'nullable|string|max:30',
            'contact_person' => 'nullable|string|max:200',
            'is_active' => 'boolean',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->company_id !== $this->getCompanyId()) abort(403);

        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus.');
    }
}
