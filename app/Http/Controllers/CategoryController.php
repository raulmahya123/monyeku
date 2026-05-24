<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        $type = $request->get('type');

        $categories = Category::where('company_id', $companyId)
            ->when($type, fn($q) => $q->where('type', $type))
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories', 'type'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string',
        ]);

        $validated['company_id'] = $this->getCompanyId();

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dibuat.');
    }

    public function edit(Category $category)
    {
        $this->authorizeAccess($category);
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorizeAccess($category);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $this->authorizeAccess($category);

        if ($category->transactions()->exists()) {
            return redirect()->back()->with('error', 'Kategori memiliki transaksi, tidak dapat dihapus.');
        }

        $category->update(['is_active' => false]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dinonaktifkan.');
    }

    private function authorizeAccess(Category $category)
    {
        if ($category->company_id !== $this->getCompanyId()) {
            abort(403);
        }
    }
}
