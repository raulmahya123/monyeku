<?php

namespace App\Http\Controllers;

use App\Models\RecurringTransaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecurringTransactionController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function index()
    {
        $companyId = $this->getCompanyId();
        $recurrings = RecurringTransaction::with(['category', 'user'])
            ->where('company_id', $companyId)
            ->orderBy('is_active', 'desc')
            ->orderBy('start_date')
            ->get()
            ->map(function ($item) {
                $item->next_due_date = $this->calculateNextDate($item);
                return $item;
            });

        return view('recurring.index', compact('recurrings'));
    }

    public function create()
    {
        $companyId = $this->getCompanyId();
        $categories = Category::where('company_id', $companyId)->where('is_active', true)->get();
        return view('recurring.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cash,bank',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $validated['company_id'] = $companyId;
        $validated['user_id'] = Auth::id();

        RecurringTransaction::create($validated);

        return redirect()->route('recurring.index')->with('success', 'Transaksi berulang berhasil dibuat.');
    }

    public function edit(RecurringTransaction $recurring)
    {
        $this->authorizeAccess($recurring);
        $companyId = $this->getCompanyId();
        $categories = Category::where('company_id', $companyId)->where('is_active', true)->get();

        return view('recurring.edit', compact('recurring', 'categories'));
    }

    public function update(Request $request, RecurringTransaction $recurring)
    {
        $this->authorizeAccess($recurring);

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cash,bank',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $recurring->update($validated);

        return redirect()->route('recurring.index')->with('success', 'Transaksi berulang berhasil diperbarui.');
    }

    public function toggle(RecurringTransaction $recurring)
    {
        $this->authorizeAccess($recurring);
        $recurring->update(['is_active' => !$recurring->is_active]);

        $status = $recurring->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Transaksi berulang berhasil {$status}.");
    }

    public function destroy(RecurringTransaction $recurring)
    {
        $this->authorizeAccess($recurring);
        $recurring->delete();

        return redirect()->route('recurring.index')->with('success', 'Transaksi berulang berhasil dihapus.');
    }

    private function calculateNextDate(RecurringTransaction $recurring)
    {
        $lastDate = $recurring->last_generated_date ?? $recurring->start_date;
        $base = \Carbon\Carbon::parse($lastDate);

        return match ($recurring->frequency) {
            'daily' => $base->addDay(),
            'weekly' => $base->addWeek(),
            'monthly' => $base->addMonth(),
            'yearly' => $base->addYear(),
            default => $base,
        };
    }

    private function authorizeAccess(RecurringTransaction $recurring)
    {
        if ($recurring->company_id !== $this->getCompanyId()) {
            abort(403);
        }
    }
}
