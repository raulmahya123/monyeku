<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function indexProducts(Request $request)
    {
        $companyId = $this->getCompanyId();

        $products = Product::where('company_id', $companyId)
            ->with(['category', 'warehouses'])
            ->when($request->search, fn($q) => $q->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            }))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->orderBy('name')
            ->paginate(20);

        $warehouses = Warehouse::where('company_id', $companyId)->where('is_active', true)->get();

        return view('products.index', compact('products', 'warehouses'));
    }

    public function createProduct()
    {
        $companyId = $this->getCompanyId();
        $categories = Category::where('company_id', $companyId)->orderBy('name')->get();
        $warehouses = Warehouse::where('company_id', $companyId)->where('is_active', true)->get();

        return view('products.create', compact('categories', 'warehouses'));
    }

    public function storeProduct(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:products,code,NULL,id,company_id,' . $companyId,
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_min' => 'nullable|integer|min:0',
            'stock_max' => 'nullable|integer|min:0',
            'type' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:100',
            'initial_stock' => 'nullable|numeric|min:0',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        $validated['company_id'] = $companyId;
        $validated['is_active'] = true;

        $product = Product::create($validated);

        if (!empty($validated['initial_stock']) && $validated['initial_stock'] > 0 && !empty($validated['warehouse_id'])) {
            $warehouse = Warehouse::findOrFail($validated['warehouse_id']);
            $this->authorizeCompany($warehouse);

            $product->warehouses()->syncWithoutDetaching([
                $validated['warehouse_id'] => ['stock' => $validated['initial_stock'], 'avg_cost' => $validated['purchase_price']],
            ]);

            StockMutation::create([
                'company_id' => $companyId,
                'product_id' => $product->id,
                'warehouse_id' => $validated['warehouse_id'],
                'type' => 'in',
                'quantity' => $validated['initial_stock'],
                'price' => $validated['purchase_price'],
                'reference_type' => 'initial_stock',
                'reference_id' => $product->id,
                'notes' => 'Initial stock',
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil dibuat.');
    }

    public function editProduct(Product $product)
    {
        $this->authorizeCompany($product);

        $companyId = $this->getCompanyId();
        $categories = Category::where('company_id', $companyId)->orderBy('name')->get();
        $warehouses = Warehouse::where('company_id', $companyId)->where('is_active', true)->get();
        $product->load('warehouses');

        return view('products.edit', compact('product', 'categories', 'warehouses'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $this->authorizeCompany($product);
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:products,code,' . $product->id . ',id,company_id,' . $companyId,
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_min' => 'nullable|integer|min:0',
            'stock_max' => 'nullable|integer|min:0',
            'type' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:100',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroyProduct(Product $product)
    {
        $this->authorizeCompany($product);

        if ($product->stockMutations()->exists()) {
            return redirect()->back()->with('error', 'Produk memiliki riwayat mutasi, tidak dapat dihapus.');
        }

        $product->update(['is_active' => false]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil dinonaktifkan.');
    }

    public function indexWarehouses()
    {
        $companyId = $this->getCompanyId();
        $warehouses = Warehouse::where('company_id', $companyId)->orderBy('name')->get();

        return view('warehouses.index', compact('warehouses'));
    }

    public function createWarehouse()
    {
        return view('warehouses.create');
    }

    public function storeWarehouse(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:warehouses,code,NULL,id,company_id,' . $companyId,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
        ]);

        $validated['company_id'] = $companyId;
        $validated['is_active'] = true;

        Warehouse::create($validated);

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil dibuat.');
    }

    public function editWarehouse(Warehouse $warehouse)
    {
        $this->authorizeCompany($warehouse);
        return view('warehouses.edit', compact('warehouse'));
    }

    public function updateWarehouse(Request $request, Warehouse $warehouse)
    {
        $this->authorizeCompany($warehouse);
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:warehouses,code,' . $warehouse->id . ',id,company_id,' . $companyId,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
        ]);

        $warehouse->update($validated);

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroyWarehouse(Warehouse $warehouse)
    {
        $this->authorizeCompany($warehouse);

        if ($warehouse->stockMutations()->exists()) {
            return redirect()->back()->with('error', 'Gudang memiliki riwayat mutasi, tidak dapat dihapus.');
        }

        $warehouse->update(['is_active' => false]);

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil dinonaktifkan.');
    }

    public function stockCard(Request $request, Product $product)
    {
        $this->authorizeCompany($product);
        $companyId = $this->getCompanyId();

        $mutations = StockMutation::where('product_id', $product->id)
            ->with('warehouse')
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $warehouses = Warehouse::where('company_id', $companyId)->where('is_active', true)->get();

        return view('products.stock-card', compact('product', 'mutations', 'warehouses'));
    }

    private function authorizeCompany($model)
    {
        if ($model->company_id !== $this->getCompanyId()) {
            abort(403);
        }
    }
}
