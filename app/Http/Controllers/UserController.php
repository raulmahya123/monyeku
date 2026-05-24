<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    private function authorizeAdmin()
    {
        $user = Auth::user();
        $companyId = $this->getCompanyId();
        $role = $user->companies()->where('company_id', $companyId)->first()?->pivot->role ?? $user->role;
        if (!in_array($role, ['owner', 'admin'])) {
            abort(403);
        }
    }

    private function getAccessibleCompanies()
    {
        return Auth::user()->companies()->wherePivot('is_active', true)->get();
    }

    public function index()
    {
        $this->authorizeAdmin();
        $companyId = $this->getCompanyId();
        $company = Company::findOrFail($companyId);

        $users = $company->users()->withPivot('role', 'is_active')->get();

        return view('users.index', compact('users', 'company'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        $companies = $this->getAccessibleCompanies();

        return view('users.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,staff',
            'company_ids' => 'required|array|min:1',
            'company_ids.*' => 'exists:companies,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        $user->role = $validated['role'];
        $user->save();

        foreach ($validated['company_ids'] as $companyId) {
            $user->companies()->attach($companyId, [
                'role' => $validated['role'],
                'is_active' => true,
            ]);
        }

        $user->update(['current_company_id' => $validated['company_ids'][0]]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan ke ' . count($validated['company_ids']) . ' perusahaan.');
    }

    public function edit(User $user)
    {
        $this->authorizeAdmin();

        $companies = $this->getAccessibleCompanies();
        $userCompanyIds = $user->companies()->pluck('companies.id')->toArray();

        $pivot = $user->companies()->where('company_id', $this->getCompanyId())->first()?->pivot;

        return view('users.edit', compact('user', 'pivot', 'companies', 'userCompanyIds'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();
        $companyId = $this->getCompanyId();

        $pivot = $user->companies()->where('company_id', $companyId)->first()?->pivot;
        if (!$pivot) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => 'required|in:admin,staff',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
            'company_ids' => 'required|array|min:1',
            'company_ids.*' => 'exists:companies,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $existingIds = $user->companies()->pluck('companies.id')->toArray();
        $newIds = $validated['company_ids'];

        $toDetach = array_diff($existingIds, $newIds);
        $toAttach = array_diff($newIds, $existingIds);

        if (!empty($toDetach)) {
            $user->companies()->detach($toDetach);
        }

        foreach ($toAttach as $cid) {
            $user->companies()->attach($cid, [
                'role' => $validated['role'],
                'is_active' => $request->boolean('is_active', true),
            ]);
        }

        $user->companies()->updateExistingPivot($companyId, [
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        $currentCompanyId = $user->current_company_id;
        $updatedIds = $user->companies()->pluck('companies.id')->toArray();
        if (!in_array($currentCompanyId, $updatedIds)) {
            $user->update(['current_company_id' => !empty($updatedIds) ? $updatedIds[0] : null]);
        }

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();
        $companyId = $this->getCompanyId();

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus diri sendiri.');
        }

        $company = Company::findOrFail($companyId);
        $ownerCount = $company->users()->wherePivot('role', 'owner')->count();

        $pivot = $user->companies()->where('company_id', $companyId)->first()?->pivot;
        if ($pivot && $pivot->role === 'owner' && $ownerCount <= 1) {
            return redirect()->back()->with('error', 'Setidaknya harus ada satu owner.');
        }

        $user->companies()->detach($companyId);

        if ($user->current_company_id == $companyId) {
            $remaining = $user->companies()->pluck('companies.id')->toArray();
            $user->update(['current_company_id' => !empty($remaining) ? $remaining[0] : null]);
        }

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus dari perusahaan.');
    }
}
