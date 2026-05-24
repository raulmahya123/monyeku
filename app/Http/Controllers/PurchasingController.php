<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceive;
use App\Models\GoodsReceiveItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchasingController extends Controller
{
    private function getCompanyId()
    {
        return Auth::user()->current_company_id;
    }

    public function indexRequests(Request $request)
    {
        $companyId = $this->getCompanyId();

        $requests = PurchaseRequest::where('company_id', $companyId)
            ->with(['supplier', 'createdBy', 'items.product'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('request_date', 'desc')
            ->paginate(20);

        return view('purchase-requests.index', compact('requests'));
    }

    public function storeRequest(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'request_number' => 'required|string|max:50|unique:purchase_requests,request_number,NULL,id,company_id,' . $companyId,
            'supplier_id' => 'nullable|exists:suppliers,id',
            'request_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.estimated_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        $pr = PurchaseRequest::create([
            'company_id' => $companyId,
            'request_number' => $validated['request_number'],
            'supplier_id' => $validated['supplier_id'],
            'request_date' => $validated['request_date'],
            'expected_date' => $validated['expected_date'],
            'status' => 'draft',
            'notes' => $validated['notes'],
            'created_by' => Auth::id(),
        ]);

        foreach ($validated['items'] as $item) {
            PurchaseRequestItem::create([
                'purchase_request_id' => $pr->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'estimated_price' => $item['estimated_price'] ?? 0,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        return redirect()->route('purchase-requests.index')->with('success', 'Purchase request berhasil dibuat.');
    }

    public function updateRequestStatus(Request $request, PurchaseRequest $purchaseRequest)
    {
        $this->authorizeCompany($purchaseRequest);

        $validated = $request->validate([
            'status' => 'required|in:draft,approved,rejected,ordered',
        ]);

        $purchaseRequest->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Status berhasil diperbarui.');
    }

    public function indexOrders(Request $request)
    {
        $companyId = $this->getCompanyId();

        $orders = PurchaseOrder::where('company_id', $companyId)
            ->with(['supplier', 'warehouse', 'createdBy'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('order_date', 'desc')
            ->paginate(20);

        return view('purchase-orders.index', compact('orders'));
    }

    public function storeOrder(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'order_number' => 'required|string|max:50|unique:purchase_orders,order_number,NULL,id,company_id,' . $companyId,
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        $subtotal = collect($validated['items'])->sum(fn($i) => $i['quantity'] * $i['price']);

        $po = PurchaseOrder::create([
            'company_id' => $companyId,
            'order_number' => $validated['order_number'],
            'supplier_id' => $validated['supplier_id'],
            'purchase_request_id' => $validated['purchase_request_id'] ?? null,
            'order_date' => $validated['order_date'],
            'expected_date' => $validated['expected_date'],
            'warehouse_id' => $validated['warehouse_id'],
            'status' => 'draft',
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $subtotal,
            'notes' => $validated['notes'],
            'created_by' => Auth::id(),
        ]);

        foreach ($validated['items'] as $item) {
            $total = $item['quantity'] * $item['price'];
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $total,
                'received_qty' => 0,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        if (!empty($validated['purchase_request_id'])) {
            $pr = PurchaseRequest::find($validated['purchase_request_id']);
            if ($pr && $pr->company_id === $companyId) {
                $pr->update(['status' => 'ordered']);
            }
        }

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase order berhasil dibuat.');
    }

    public function showOrder(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeCompany($purchaseOrder);
        $purchaseOrder->load(['supplier', 'warehouse', 'items.product', 'goodsReceives']);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function exportOrderPdf(Request $request, $purchaseOrder)
    {
        $companyId = $this->getCompanyId();
        $order = PurchaseOrder::with(['supplier', 'items.product', 'currency'])
            ->where('company_id', $companyId)
            ->findOrFail($purchaseOrder);

        $pdf = Pdf::loadView('pdfs.purchase-order', compact('order'));
        return $pdf->download('PO-' . $order->order_number . '.pdf');
    }

    public function receive(GoodsReceive $receive)
    {
        $this->authorizeCompany($receive);
        $receive->load(['purchaseOrder.items.product', 'items']);

        return view('goods-receives.receive', compact('receive'));
    }

    public function storeReceive(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'receive_number' => 'required|string|max:50|unique:goods_receives,receive_number,NULL,id,company_id,' . $companyId,
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'received_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.accepted_qty' => 'required|numeric|min:0',
            'items.*.rejected_qty' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        $po = PurchaseOrder::with('items')->findOrFail($validated['purchase_order_id']);
        $this->authorizeCompany($po);

        DB::transaction(function () use ($validated, $companyId, $po) {
            $receive = GoodsReceive::create([
                'company_id' => $companyId,
                'receive_number' => $validated['receive_number'],
                'purchase_order_id' => $validated['purchase_order_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'received_date' => $validated['received_date'],
                'status' => 'completed',
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                GoodsReceiveItem::create([
                    'goods_receive_id' => $receive->id,
                    'purchase_order_item_id' => $item['purchase_order_item_id'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'accepted_qty' => $item['accepted_qty'],
                    'rejected_qty' => $item['rejected_qty'],
                    'notes' => $item['notes'] ?? null,
                ]);

                $poItem = PurchaseOrderItem::findOrFail($item['purchase_order_item_id']);
                $poItem->increment('received_qty', $item['accepted_qty']);

                if ($item['accepted_qty'] > 0) {
                    $product = Product::findOrFail($item['product_id']);
                    $product->warehouses()->syncWithoutDetaching([
                        $validated['warehouse_id'] => [
                            'stock' => DB::raw('COALESCE(stock, 0) + ' . $item['accepted_qty']),
                            'avg_cost' => $item['price'] ?? 0,
                        ],
                    ]);

                    StockMutation::create([
                        'company_id' => $companyId,
                        'product_id' => $item['product_id'],
                        'warehouse_id' => $validated['warehouse_id'],
                        'type' => 'in',
                        'quantity' => $item['accepted_qty'],
                        'price' => $poItem->price,
                        'reference_type' => 'goods_receive',
                        'reference_id' => $receive->id,
                        'notes' => 'Receiving PO: ' . $po->order_number,
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            $allReceived = $po->items->every(fn($i) => $i->received_qty >= $i->quantity);
            if ($allReceived) {
                $po->update(['status' => 'received']);
            } else {
                $po->update(['status' => 'partial']);
            }
        });

        return redirect()->route('goods-receives.index')->with('success', 'Penerimaan barang berhasil.');
    }

    public function indexReceives(Request $request)
    {
        $companyId = $this->getCompanyId();

        $receives = GoodsReceive::where('company_id', $companyId)
            ->with(['purchaseOrder.supplier', 'warehouse', 'createdBy'])
            ->orderBy('received_date', 'desc')
            ->paginate(20);

        return view('goods-receives.index', compact('receives'));
    }

    public function indexReturns(Request $request)
    {
        $companyId = $this->getCompanyId();

        $returns = PurchaseReturn::where('company_id', $companyId)
            ->with(['purchaseOrder.supplier', 'createdBy'])
            ->orderBy('return_date', 'desc')
            ->paginate(20);

        return view('purchase-returns.index', compact('returns'));
    }

    public function storeReturn(Request $request)
    {
        $companyId = $this->getCompanyId();

        $validated = $request->validate([
            'return_number' => 'required|string|max:50|unique:purchase_returns,return_number,NULL,id,company_id,' . $companyId,
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'return_date' => 'required|date',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string',
        ]);

        $po = PurchaseOrder::findOrFail($validated['purchase_order_id']);
        $this->authorizeCompany($po);

        DB::transaction(function () use ($validated, $companyId, $po) {
            $return = PurchaseReturn::create([
                'company_id' => $companyId,
                'return_number' => $validated['return_number'],
                'purchase_order_id' => $validated['purchase_order_id'],
                'return_date' => $validated['return_date'],
                'status' => 'completed',
                'reason' => $validated['reason'],
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                $total = $item['quantity'] * $item['price'];
                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $total,
                    'reason' => $item['reason'] ?? null,
                ]);

                $product = Product::findOrFail($item['product_id']);
                $warehouseId = $po->warehouse_id;
                $product->warehouses()->syncWithoutDetaching([
                    $warehouseId => ['stock' => DB::raw('COALESCE(stock, 0) - ' . $item['quantity'])],
                ]);

                StockMutation::create([
                    'company_id' => $companyId,
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $warehouseId,
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'reference_type' => 'purchase_return',
                    'reference_id' => $return->id,
                    'notes' => 'Return to supplier: ' . $po->order_number,
                    'user_id' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('purchase-returns.index')->with('success', 'Retur pembelian berhasil.');
    }

    private function authorizeCompany($model)
    {
        if ($model->company_id !== $this->getCompanyId()) {
            abort(403);
        }
    }
}
