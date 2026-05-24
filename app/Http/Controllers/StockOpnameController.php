<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockOpnameController extends Controller
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

    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();

        $stockOpnames = StockOpname::where('company_id', $companyId)
            ->with(['warehouse', 'createdBy'])
            ->withCount('items')
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->search, fn($q) => $q->where(function($q) use ($request) {
                $q->where('notes', 'like', '%' . $request->search . '%')
                  ->orWhereHas('warehouse', fn($w) => $w->where('name', 'like', '%' . $request->search . '%'));
            }))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $warehouses = Warehouse::where('company_id', $companyId)->where('is_active', true)->get();

        return view('stock-opnames.index', compact('stockOpnames', 'warehouses'));
    }

    public function create()
    {
        $companyId = $this->getCompanyId();
        $warehouses = Warehouse::where('company_id', $companyId)->where('is_active', true)->get();
        $products = Product::where('company_id', $companyId)->where('is_active', true)->orderBy('name')->get();

        return view('stock-opnames.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'opname_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'system_qty' => 'required|array',
            'system_qty.*' => 'numeric|min:0',
            'physical_qty' => 'required|array',
            'physical_qty.*' => 'numeric|min:0',
            'item_notes' => 'nullable|array',
            'item_notes.*' => 'nullable|string|max:500',
        ]);

        $warehouse = Warehouse::findOrFail($validated['warehouse_id']);
        $this->authorizeCompany($warehouse);

        $stockOpname = StockOpname::create([
            'company_id' => $companyId,
            'warehouse_id' => $validated['warehouse_id'],
            'opname_date' => $validated['opname_date'],
            'status' => 'draft',
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        foreach ($validated['product_ids'] as $index => $productId) {
            $product = Product::find($productId);
            $pivot = $product->warehouses()->where('warehouse_id', $validated['warehouse_id'])->first();
            $systemQty = $pivot ? $pivot->pivot->stock : 0;

            StockOpnameItem::create([
                'stock_opname_id' => $stockOpname->id,
                'product_id' => $productId,
                'system_qty' => $systemQty,
                'physical_qty' => $validated['physical_qty'][$index] ?? 0,
                'difference' => ($validated['physical_qty'][$index] ?? 0) - $systemQty,
                'notes' => $validated['item_notes'][$index] ?? null,
            ]);
        }

        return redirect()->route('stock-opnames.show', $stockOpname)
            ->with('success', 'Stock Opname berhasil dibuat.');
    }

    public function show(StockOpname $stockOpname)
    {
        $this->authorizeCompany($stockOpname);
        $stockOpname->load(['warehouse', 'createdBy', 'items.product']);

        return view('stock-opnames.show', compact('stockOpname'));
    }

    public function complete(StockOpname $stockOpname)
    {
        $this->authorizeCompany($stockOpname);

        if ($stockOpname->status !== 'draft') {
            return redirect()->back()->with('error', 'Stock Opname sudah selesai.');
        }

        $stockOpname->load('items.product');

        foreach ($stockOpname->items as $item) {
            $difference = $item->physical_qty - $item->system_qty;

            if ($difference != 0) {
                $product = $item->product;

                StockMutation::create([
                    'company_id' => $stockOpname->company_id,
                    'product_id' => $item->product_id,
                    'warehouse_id' => $stockOpname->warehouse_id,
                    'type' => 'opname',
                    'quantity' => $difference,
                    'price' => 0,
                    'reference_type' => 'stock_opname',
                    'reference_id' => $stockOpname->id,
                    'notes' => 'Stock opname adjustment: ' . ($item->notes ?? ''),
                    'user_id' => Auth::id(),
                ]);

                $product->warehouses()->updateExistingPivot($stockOpname->warehouse_id, [
                    'stock' => $item->physical_qty,
                ]);
            }
        }

        $stockOpname->update(['status' => 'completed']);

        return redirect()->route('stock-opnames.show', $stockOpname)
            ->with('success', 'Stock Opname berhasil diselesaikan.');
    }

    public function getProductStock(Request $request, Product $product)
    {
        $this->authorizeCompany($product);
        $stock = 0;
        if ($request->warehouse_id) {
            $pivot = $product->warehouses()->where('warehouse_id', $request->warehouse_id)->first();
            $stock = $pivot ? (float) $pivot->pivot->stock : 0;
        }
        return response()->json(['stock' => $stock]);
    }
}
