<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserRoleController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    private function authorizeCompany($model)
    {
        if ($model->company_id !== $this->getCompanyId()) {
            abort(403);
        }
    }

    public function index()
    {
        $companyId = $this->getCompanyId();
        $company = \App\Models\Company::findOrFail($companyId);
        $users = $company->users()->withPivot('role', 'is_active')->get();

        return view('roles.index', compact('company', 'users'));
    }

    public function update(Request $request, User $user)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'role' => 'required|in:owner,admin,staff',
            'is_active' => 'boolean',
        ]);

        $user->companies()->updateExistingPivot($companyId, [
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('roles.index')->with('success', 'Role user berhasil diperbarui.');
    }

    public function remove(User $user)
    {
        $companyId = $this->getCompanyId();

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus diri sendiri.');
        }

        $user->companies()->detach($companyId);

        return redirect()->route('roles.index')->with('success', 'User berhasil dihapus dari perusahaan.');
    }
}
