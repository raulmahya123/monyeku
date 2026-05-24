<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Auth::user()->companies()->wherePivot('is_active', true)->get();
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);

        $company = Company::create($validated);

        Auth::user()->companies()->attach($company->id, ['role' => 'owner', 'is_active' => true]);
        Auth::user()->update(['current_company_id' => $company->id]);

        return redirect()->route('dashboard')->with('success', 'Perusahaan berhasil dibuat.');
    }

    public function edit(Company $company)
    {
        $this->authorizeAccess($company);
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $this->authorizeAccess($company);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $company->update($validated);

        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil diperbarui.');
    }

    public function switch(Company $company)
    {
        $user = Auth::user();
        $exists = $user->companies()->where('company_id', $company->id)->wherePivot('is_active', true)->exists();

        if (!$exists) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke perusahaan ini.');
        }

        $user->update(['current_company_id' => $company->id]);

        return redirect()->route('dashboard')->with('success', 'Beralih ke ' . $company->name);
    }

    private function authorizeAccess(Company $company)
    {
        $user = Auth::user();
        $pivot = $user->companies()->where('company_id', $company->id)->first()?->pivot;

        if (!$pivot || !$pivot->is_active || !in_array($pivot->role, ['owner', 'admin'])) {
            abort(403);
        }
    }
}
