<?php

namespace App\Http\Controllers;

use App\Models\AccountingPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountingPeriodController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function index()
    {
        $companyId = $this->getCompanyId();
        $periods = AccountingPeriod::where('company_id', $companyId)
            ->orderBy('start_date', 'desc')
            ->paginate(config('moneyku.pagination', 15));
        return view('accounting-periods.index', compact('periods'));
    }

    public function create()
    {
        return view('accounting-periods.create');
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $overlap = AccountingPeriod::where('company_id', $companyId)
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                  ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                  ->orWhere(function ($q) use ($validated) {
                      $q->where('start_date', '<=', $validated['start_date'])
                        ->where('end_date', '>=', $validated['end_date']);
                  });
            })->exists();

        if ($overlap) {
            return redirect()->back()->with('error', 'Periode tumpang tindih dengan periode yang sudah ada.')->withInput();
        }

        AccountingPeriod::create([
            'company_id' => $companyId,
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        return redirect()->route('accounting-periods.index')->with('success', 'Periode akuntansi berhasil ditambahkan.');
    }

    public function close(AccountingPeriod $period)
    {
        if ($period->company_id !== $this->getCompanyId()) abort(403);
        if ($period->is_closed) {
            return redirect()->back()->with('error', 'Periode sudah ditutup.');
        }

        $period->update([
            'is_closed' => true,
            'closed_at' => now(),
            'closed_by' => Auth::id(),
        ]);

        return redirect()->route('accounting-periods.index')->with('success', 'Periode berhasil ditutup.');
    }

    public function open(AccountingPeriod $period)
    {
        if ($period->company_id !== $this->getCompanyId()) abort(403);
        if (!$period->is_closed) {
            return redirect()->back()->with('error', 'Periode sudah terbuka.');
        }

        $period->update([
            'is_closed' => false,
            'closed_at' => null,
            'closed_by' => null,
        ]);

        return redirect()->route('accounting-periods.index')->with('success', 'Periode berhasil dibuka kembali.');
    }

    public function destroy(AccountingPeriod $period)
    {
        if ($period->company_id !== $this->getCompanyId()) abort(403);

        $period->delete();
        return redirect()->route('accounting-periods.index')->with('success', 'Periode berhasil dihapus.');
    }

    public static function isPeriodClosed(int $companyId, string $date): bool
    {
        return AccountingPeriod::where('company_id', $companyId)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('is_closed', true)
            ->exists();
    }
}
