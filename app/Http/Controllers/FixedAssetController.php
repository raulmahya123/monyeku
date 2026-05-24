<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\DepreciationSchedule;
use App\Models\Category;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FixedAssetController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();

        $assets = FixedAsset::where('company_id', $companyId)
            ->with('category')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            }))
            ->orderBy('purchase_date', 'desc')
            ->paginate(20);

        return view('fixed-assets.index', compact('assets'));
    }

    public function create()
    {
        $companyId = $this->getCompanyId();
        $categories = Category::where('company_id', $companyId)->orderBy('name')->get();
        $coas = Coa::where('company_id', $companyId)->active()->orderBy('code')->get();

        return view('fixed-assets.create', compact('categories', 'coas'));
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:fixed_assets,code,NULL,id,company_id,' . $companyId,
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'residual_value' => 'required|numeric|min:0',
            'useful_life' => 'required|integer|min:1',
            'depreciation_method' => 'required|in:straight_line,double_declining,sum_of_years',
            'depreciation_start_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'coa_id' => 'nullable|exists:chart_of_accounts,id',
            'depreciation_coa_id' => 'nullable|exists:chart_of_accounts,id',
            'accumulation_coa_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        $validated['company_id'] = $companyId;
        $validated['book_value'] = $validated['purchase_price'];
        $validated['accumulated_depreciation'] = 0;
        $validated['status'] = 'active';

        FixedAsset::create($validated);

        return redirect()->route('fixed-assets.index')->with('success', 'Aset tetap berhasil dibuat.');
    }

    public function edit(FixedAsset $fixedAsset)
    {
        $this->authorizeCompany($fixedAsset);
        $companyId = $this->getCompanyId();
        $categories = Category::where('company_id', $companyId)->orderBy('name')->get();
        $coas = Coa::where('company_id', $companyId)->active()->orderBy('code')->get();

        return view('fixed-assets.edit', compact('fixedAsset', 'categories', 'coas'));
    }

    public function update(Request $request, FixedAsset $fixedAsset)
    {
        $this->authorizeCompany($fixedAsset);
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:fixed_assets,code,' . $fixedAsset->id . ',id,company_id,' . $companyId,
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'residual_value' => 'required|numeric|min:0',
            'useful_life' => 'required|integer|min:1',
            'depreciation_method' => 'required|in:straight_line,double_declining,sum_of_years',
            'depreciation_start_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'coa_id' => 'nullable|exists:chart_of_accounts,id',
            'depreciation_coa_id' => 'nullable|exists:chart_of_accounts,id',
            'accumulation_coa_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        $fixedAsset->update($validated);

        return redirect()->route('fixed-assets.index')->with('success', 'Aset tetap berhasil diperbarui.');
    }

    public function destroy(FixedAsset $fixedAsset)
    {
        $this->authorizeCompany($fixedAsset);

        if ($fixedAsset->depreciationSchedules()->exists()) {
            return redirect()->back()->with('error', 'Aset memiliki jadwal penyusutan, tidak dapat dihapus.');
        }

        $fixedAsset->update(['status' => 'disposed', 'disposal_date' => now()]);

        return redirect()->route('fixed-assets.index')->with('success', 'Aset tetap berhasil dinonaktifkan.');
    }

    public function calculateDepreciation(FixedAsset $asset)
    {
        $this->authorizeCompany($asset);

        if ($asset->status !== 'active') {
            return redirect()->back()->with('error', 'Hanya aset aktif yang dapat dihitung penyusutannya.');
        }

        $companyId = $this->getCompanyId();
        $startDate = $asset->depreciation_start_date ?? $asset->purchase_date;
        $existingPeriods = $asset->depreciationSchedules()->count();
        $remainingMonths = $asset->useful_life - $existingPeriods;

        if ($remainingMonths <= 0) {
            return redirect()->back()->with('error', 'Masa manfaat aset sudah habis.');
        }

        DB::transaction(function () use ($asset, $companyId, $startDate, $existingPeriods, $remainingMonths) {
            for ($i = 1; $i <= $remainingMonths; $i++) {
                $period = $existingPeriods + $i;
                $scheduleDate = $startDate->copy()->addMonths($period - 1);

                $depreciationAmount = $asset->calculateMonthlyDepreciation();
                if ($depreciationAmount <= 0) break;

                $prevAccumulated = $asset->depreciationSchedules()
                    ->where('period', '<', $period)
                    ->sum('depreciation_amount');

                $accumulated = $prevAccumulated + $depreciationAmount;
                $bookValue = max(0, $asset->purchase_price - $accumulated);

                DepreciationSchedule::create([
                    'fixed_asset_id' => $asset->id,
                    'period' => $period,
                    'schedule_date' => $scheduleDate,
                    'depreciation_amount' => $depreciationAmount,
                    'accumulated_depreciation' => $accumulated,
                    'book_value' => $bookValue,
                    'is_journalized' => false,
                ]);

                if ($bookValue <= $asset->residual_value) break;
            }

            $totalDepreciation = $asset->depreciationSchedules()->sum('depreciation_amount');
            $asset->update([
                'accumulated_depreciation' => $totalDepreciation,
                'book_value' => max(0, $asset->purchase_price - $totalDepreciation),
            ]);
        });

        return redirect()->route('fixed-assets.depreciation', $asset)
            ->with('success', 'Jadwal penyusutan berhasil dibuat.');
    }

    public function showDepreciation(FixedAsset $asset)
    {
        $this->authorizeCompany($asset);

        $schedules = $asset->depreciationSchedules()
            ->orderBy('period')
            ->paginate(60);

        return view('fixed-assets.depreciation', compact('asset', 'schedules'));
    }

    private function authorizeCompany($model)
    {
        if ($model->company_id !== $this->getCompanyId()) {
            abort(403);
        }
    }
}
