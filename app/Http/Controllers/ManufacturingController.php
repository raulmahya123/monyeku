<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use App\Models\BomMaterial;
use App\Models\WorkOrder;
use App\Models\WorkOrderMaterial;
use App\Models\Product;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManufacturingController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function indexBoms(Request $request)
    {
        $companyId = $this->getCompanyId();

        $boms = Bom::where('company_id', $companyId)
            ->with('product')
            ->when($request->search, fn($q) => $q->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            }))
            ->orderBy('name')
            ->paginate(20);

        return view('boms.index', compact('boms'));
    }

    public function storeBom(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:boms,code,NULL,id,company_id,' . $companyId,
            'name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:products,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
            'materials.*.waste_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $bom = Bom::create([
            'company_id' => $companyId,
            'product_id' => $validated['product_id'],
            'code' => $validated['code'],
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'],
            'is_active' => true,
        ]);

        foreach ($validated['materials'] as $material) {
            BomMaterial::create([
                'bom_id' => $bom->id,
                'material_id' => $material['material_id'],
                'quantity' => $material['quantity'],
                'waste_percentage' => $material['waste_percentage'] ?? 0,
            ]);
        }

        return redirect()->route('boms.index')->with('success', 'BOM berhasil dibuat.');
    }

    public function editBom(Bom $bom)
    {
        $this->authorizeCompany($bom);

        $companyId = $this->getCompanyId();
        $products = Product::where('company_id', $companyId)->where('is_active', true)->orderBy('name')->get();
        $bom->load('bomMaterials.material');

        return view('boms.edit', compact('bom', 'products'));
    }

    public function updateBom(Request $request, Bom $bom)
    {
        $this->authorizeCompany($bom);
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:boms,code,' . $bom->id . ',id,company_id,' . $companyId,
            'name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:products,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
            'materials.*.waste_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $bom->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'],
        ]);

        $bom->bomMaterials()->delete();

        foreach ($validated['materials'] as $material) {
            BomMaterial::create([
                'bom_id' => $bom->id,
                'material_id' => $material['material_id'],
                'quantity' => $material['quantity'],
                'waste_percentage' => $material['waste_percentage'] ?? 0,
            ]);
        }

        return redirect()->route('boms.index')->with('success', 'BOM berhasil diperbarui.');
    }

    public function destroyBom(Bom $bom)
    {
        $this->authorizeCompany($bom);

        if ($bom->workOrders()->exists()) {
            return redirect()->back()->with('error', 'BOM memiliki work order, tidak dapat dihapus.');
        }

        $bom->bomMaterials()->delete();
        $bom->delete();

        return redirect()->route('boms.index')->with('success', 'BOM berhasil dihapus.');
    }

    public function indexWorkOrders(Request $request)
    {
        $companyId = $this->getCompanyId();

        $workOrders = WorkOrder::where('company_id', $companyId)
            ->with(['product', 'bom', 'createdBy'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('work-orders.index', compact('workOrders'));
    }

    public function storeWorkOrder(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'order_number' => 'required|string|max:50|unique:work_orders,order_number,NULL,id,company_id,' . $companyId,
            'product_id' => 'required|exists:products,id',
            'bom_id' => 'required|exists:boms,id',
            'quantity' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $bom = Bom::with('bomMaterials')->findOrFail($validated['bom_id']);
        $this->authorizeCompany($bom);

        $wo = WorkOrder::create([
            'company_id' => $companyId,
            'order_number' => $validated['order_number'],
            'product_id' => $validated['product_id'],
            'bom_id' => $validated['bom_id'],
            'quantity' => $validated['quantity'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'draft',
            'produced_qty' => 0,
            'scrap_qty' => 0,
            'notes' => $validated['notes'],
            'created_by' => Auth::id(),
        ]);

        foreach ($bom->bomMaterials as $material) {
            $requiredQty = ($material->quantity / $bom->quantity) * $validated['quantity'];
            if ($material->waste_percentage > 0) {
                $requiredQty += $requiredQty * ($material->waste_percentage / 100);
            }

            WorkOrderMaterial::create([
                'work_order_id' => $wo->id,
                'product_id' => $material->material_id,
                'required_qty' => $requiredQty,
                'used_qty' => 0,
            ]);
        }

        return redirect()->route('work-orders.index')->with('success', 'Work order berhasil dibuat.');
    }

    public function completeWorkOrder(Request $request, WorkOrder $workOrder)
    {
        $this->authorizeCompany($workOrder);

        if ($workOrder->status === 'completed') {
            return redirect()->back()->with('error', 'Work order sudah selesai.');
        }

        $validated = $request->validate([
            'produced_qty' => 'required|numeric|min:0.01',
            'scrap_qty' => 'nullable|numeric|min:0',
            'materials' => 'required|array',
            'materials.*.used_qty' => 'required|numeric|min:0',
        ]);

        $companyId = $this->getCompanyId();
        $warehouseId = $workOrder->product->warehouses()->first()?->id;

        DB::transaction(function () use ($workOrder, $validated, $companyId, $warehouseId) {
            $scrapQty = $validated['scrap_qty'] ?? 0;

            $workOrder->update([
                'status' => 'completed',
                'produced_qty' => $validated['produced_qty'],
                'scrap_qty' => $scrapQty,
            ]);

            foreach ($validated['materials'] as $id => $data) {
                $wom = WorkOrderMaterial::findOrFail($id);
                $wom->update(['used_qty' => $data['used_qty']]);

                if ($data['used_qty'] > 0 && $warehouseId) {
                    $product = Product::findOrFail($wom->product_id);
                    $product->warehouses()->syncWithoutDetaching([
                        $warehouseId => ['stock' => DB::raw('COALESCE(stock, 0) - ' . $data['used_qty'])],
                    ]);

                    StockMutation::create([
                        'company_id' => $companyId,
                        'product_id' => $wom->product_id,
                        'warehouse_id' => $warehouseId,
                        'type' => 'out',
                        'quantity' => $data['used_qty'],
                        'price' => 0,
                        'reference_type' => 'work_order',
                        'reference_id' => $workOrder->id,
                        'notes' => 'Material usage for WO: ' . $workOrder->order_number,
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            if ($validated['produced_qty'] > 0 && $warehouseId) {
                $product = Product::findOrFail($workOrder->product_id);
                $product->warehouses()->syncWithoutDetaching([
                    $warehouseId => ['stock' => DB::raw('COALESCE(stock, 0) + ' . $validated['produced_qty'])],
                ]);

                StockMutation::create([
                    'company_id' => $companyId,
                    'product_id' => $workOrder->product_id,
                    'warehouse_id' => $warehouseId,
                    'type' => 'in',
                    'quantity' => $validated['produced_qty'],
                    'price' => 0,
                    'reference_type' => 'work_order',
                    'reference_id' => $workOrder->id,
                    'notes' => 'Production output for WO: ' . $workOrder->order_number,
                    'user_id' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('work-orders.index')->with('success', 'Work order berhasil diselesaikan.');
    }

    private function authorizeCompany($model)
    {
        if ($model->company_id !== $this->getCompanyId()) {
            abort(403);
        }
    }
}
