<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SellingController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function indexQuotations(Request $request)
    {
        $companyId = $this->getCompanyId();

        $quotations = Quotation::where('company_id', $companyId)
            ->with(['customer', 'createdBy'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('quotation_date', 'desc')
            ->paginate(20);

        return view('quotations.index', compact('quotations'));
    }

    public function storeQuotation(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'quotation_number' => 'required|string|max:50|unique:quotations,quotation_number,NULL,id,company_id,' . $companyId,
            'customer_id' => 'required|exists:customers,id',
            'quotation_date' => 'required|date',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $subtotal = collect($validated['items'])->sum(fn($i) => $i['quantity'] * $i['price']);

        $quotation = Quotation::create([
            'company_id' => $companyId,
            'quotation_number' => $validated['quotation_number'],
            'customer_id' => $validated['customer_id'],
            'quotation_date' => $validated['quotation_date'],
            'valid_until' => $validated['valid_until'],
            'status' => 'draft',
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $subtotal,
            'notes' => $validated['notes'],
            'terms' => $validated['terms'],
            'created_by' => Auth::id(),
        ]);

        foreach ($validated['items'] as $item) {
            $total = $item['quantity'] * $item['price'];
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'product_id' => $item['product_id'],
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $total,
            ]);
        }

        return redirect()->route('quotations.index')->with('success', 'Quotation berhasil dibuat.');
    }

    public function convertToOrder(Quotation $quotation)
    {
        $this->authorizeCompany($quotation);

        if ($quotation->status !== 'approved') {
            return redirect()->back()->with('error', 'Quotation harus berstatus approved.');
        }

        $companyId = $this->getCompanyId();

        DB::transaction(function () use ($quotation, $companyId) {
            $orderNumber = 'SO-' . $quotation->quotation_number;

            $order = SalesOrder::create([
                'company_id' => $companyId,
                'order_number' => $orderNumber,
                'customer_id' => $quotation->customer_id,
                'quotation_id' => $quotation->id,
                'order_date' => now(),
                'status' => 'draft',
                'subtotal' => $quotation->subtotal,
                'tax' => $quotation->tax,
                'total' => $quotation->total,
                'notes' => $quotation->notes,
                'created_by' => Auth::id(),
            ]);

            foreach ($quotation->items as $qi) {
                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'product_id' => $qi->product_id,
                    'description' => $qi->description,
                    'quantity' => $qi->quantity,
                    'price' => $qi->price,
                    'total' => $qi->total,
                    'delivered_qty' => 0,
                ]);
            }

            $quotation->update(['status' => 'converted']);
        });

        return redirect()->route('sales-orders.index')->with('success', 'Quotation berhasil dikonversi ke sales order.');
    }

    public function exportQuotationPdf(Request $request, $quotation)
    {
        $companyId = $this->getCompanyId();
        $quotation = Quotation::with(['customer', 'items.product', 'currency'])
            ->where('company_id', $companyId)
            ->findOrFail($quotation);

        $pdf = Pdf::loadView('pdfs.quotation', compact('quotation'));
        return $pdf->download('QUOTATION-' . $quotation->quotation_number . '.pdf');
    }

    public function exportSalesOrderPdf(Request $request, $salesOrder)
    {
        $companyId = $this->getCompanyId();
        $order = SalesOrder::with(['customer', 'items.product', 'currency'])
            ->where('company_id', $companyId)
            ->findOrFail($salesOrder);

        $pdf = Pdf::loadView('pdfs.sales-order', compact('order'));
        return $pdf->download('SO-' . $order->order_number . '.pdf');
    }

    public function indexOrders(Request $request)
    {
        $companyId = $this->getCompanyId();

        $orders = SalesOrder::where('company_id', $companyId)
            ->with(['customer', 'createdBy'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('order_date', 'desc')
            ->paginate(20);

        return view('sales-orders.index', compact('orders'));
    }

    public function storeOrder(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'order_number' => 'required|string|max:50|unique:sales_orders,order_number,NULL,id,company_id,' . $companyId,
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $subtotal = collect($validated['items'])->sum(fn($i) => $i['quantity'] * $i['price']);

        $order = SalesOrder::create([
            'company_id' => $companyId,
            'order_number' => $validated['order_number'],
            'customer_id' => $validated['customer_id'],
            'order_date' => $validated['order_date'],
            'expected_date' => $validated['expected_date'],
            'status' => 'draft',
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $subtotal,
            'notes' => $validated['notes'],
            'created_by' => Auth::id(),
        ]);

        foreach ($validated['items'] as $item) {
            $total = $item['quantity'] * $item['price'];
            SalesOrderItem::create([
                'sales_order_id' => $order->id,
                'product_id' => $item['product_id'],
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $total,
                'delivered_qty' => 0,
            ]);
        }

        return redirect()->route('sales-orders.index')->with('success', 'Sales order berhasil dibuat.');
    }

    public function showOrder(SalesOrder $salesOrder)
    {
        $this->authorizeCompany($salesOrder);
        $salesOrder->load(['customer', 'items.product', 'deliveryOrders']);

        return view('sales-orders.show', compact('salesOrder'));
    }

    public function deliver(DeliveryOrder $do)
    {
        $this->authorizeCompany($do);
        $do->load(['salesOrder.items.product', 'items']);

        return view('delivery-orders.deliver', compact('do'));
    }

    public function storeDelivery(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'delivery_number' => 'required|string|max:50|unique:delivery_orders,delivery_number,NULL,id,company_id,' . $companyId,
            'sales_order_id' => 'required|exists:sales_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'delivery_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sales_order_item_id' => 'required|exists:sales_order_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $so = SalesOrder::with('items')->findOrFail($validated['sales_order_id']);
        $this->authorizeCompany($so);

        DB::transaction(function () use ($validated, $companyId, $so) {
            $do = DeliveryOrder::create([
                'company_id' => $companyId,
                'delivery_number' => $validated['delivery_number'],
                'sales_order_id' => $validated['sales_order_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'delivery_date' => $validated['delivery_date'],
                'status' => 'completed',
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                DeliveryOrderItem::create([
                    'delivery_order_id' => $do->id,
                    'sales_order_item_id' => $item['sales_order_item_id'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);

                $soItem = SalesOrderItem::findOrFail($item['sales_order_item_id']);
                $soItem->increment('delivered_qty', $item['quantity']);

                $product = Product::findOrFail($item['product_id']);
                $product->warehouses()->syncWithoutDetaching([
                    $validated['warehouse_id'] => [
                        'stock' => DB::raw('COALESCE(stock, 0) - ' . $item['quantity']),
                    ],
                ]);

                StockMutation::create([
                    'company_id' => $companyId,
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $validated['warehouse_id'],
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'price' => $soItem->price,
                    'reference_type' => 'delivery_order',
                    'reference_id' => $do->id,
                    'notes' => 'Delivery SO: ' . $so->order_number,
                    'user_id' => Auth::id(),
                ]);
            }

            $allDelivered = $so->items->every(fn($i) => $i->delivered_qty >= $i->quantity);
            $so->update(['status' => $allDelivered ? 'delivered' : 'partial']);
        });

        return redirect()->route('delivery-orders.index')->with('success', 'Pengiriman barang berhasil.');
    }

    public function indexDeliveries(Request $request)
    {
        $companyId = $this->getCompanyId();

        $deliveries = DeliveryOrder::where('company_id', $companyId)
            ->with(['salesOrder.customer', 'warehouse', 'createdBy'])
            ->orderBy('delivery_date', 'desc')
            ->paginate(20);

        return view('delivery-orders.index', compact('deliveries'));
    }

    public function indexReturns(Request $request)
    {
        $companyId = $this->getCompanyId();

        $returns = SalesReturn::where('company_id', $companyId)
            ->with(['deliveryOrder.salesOrder.customer', 'createdBy'])
            ->orderBy('return_date', 'desc')
            ->paginate(20);

        return view('sales-returns.index', compact('returns'));
    }

    public function storeReturn(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'return_number' => 'required|string|max:50|unique:sales_returns,return_number,NULL,id,company_id,' . $companyId,
            'delivery_order_id' => 'required|exists:delivery_orders,id',
            'return_date' => 'required|date',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string',
        ]);

        $do = DeliveryOrder::with('salesOrder')->findOrFail($validated['delivery_order_id']);
        $this->authorizeCompany($do);

        DB::transaction(function () use ($validated, $companyId, $do) {
            $return = SalesReturn::create([
                'company_id' => $companyId,
                'return_number' => $validated['return_number'],
                'delivery_order_id' => $validated['delivery_order_id'],
                'return_date' => $validated['return_date'],
                'status' => 'completed',
                'reason' => $validated['reason'],
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                $total = $item['quantity'] * $item['price'];
                SalesReturnItem::create([
                    'sales_return_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $total,
                    'reason' => $item['reason'] ?? null,
                ]);

                $product = Product::findOrFail($item['product_id']);
                $warehouseId = $do->warehouse_id;
                $product->warehouses()->syncWithoutDetaching([
                    $warehouseId => ['stock' => DB::raw('COALESCE(stock, 0) + ' . $item['quantity'])],
                ]);

                StockMutation::create([
                    'company_id' => $companyId,
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $warehouseId,
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'reference_type' => 'sales_return',
                    'reference_id' => $return->id,
                    'notes' => 'Return from customer: ' . $do->delivery_number,
                    'user_id' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('sales-returns.index')->with('success', 'Retur penjualan berhasil.');
    }

    private function authorizeCompany($model)
    {
        if ($model->company_id !== $this->getCompanyId()) {
            abort(403);
        }
    }
}
