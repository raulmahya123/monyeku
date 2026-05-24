<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchasingTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create(['name' => 'Test Co', 'slug' => 'test-co']);
        $this->user = User::factory()->create(['current_company_id' => $this->company->id]);
        $this->actingAs($this->user);
    }

    public function test_create_purchase_request(): void
    {
        $supplier = Supplier::create([
            'company_id' => $this->company->id,
            'code' => 'SUP-001',
            'name' => 'PT Supplier',
        ]);

        $product = Product::create([
            'company_id' => $this->company->id,
            'code' => 'PRD-001',
            'name' => 'Test Product',
            'unit' => 'pcs',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'type' => 'product',
        ]);

        $response = $this->post(route('purchase-requests.store'), [
            'request_number' => 'PR-2024-001',
            'supplier_id' => $supplier->id,
            'request_date' => '2024-01-15',
            'expected_date' => '2024-01-30',
            'notes' => 'Urgent request',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 100,
                    'estimated_price' => 9500,
                ],
            ],
        ]);

        $response->assertRedirect(route('purchase-requests.index'));

        $this->assertDatabaseHas('purchase_requests', [
            'company_id' => $this->company->id,
            'request_number' => 'PR-2024-001',
        ]);

        $this->assertDatabaseHas('purchase_request_items', [
            'product_id' => $product->id,
            'quantity' => 100,
        ]);
    }

    public function test_create_purchase_order(): void
    {
        $supplier = Supplier::create([
            'company_id' => $this->company->id,
            'code' => 'SUP-001',
            'name' => 'PT Supplier',
        ]);

        $warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'code' => 'WH-001',
            'name' => 'Main Warehouse',
        ]);

        $product = Product::create([
            'company_id' => $this->company->id,
            'code' => 'PRD-001',
            'name' => 'Test Product',
            'unit' => 'pcs',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'type' => 'product',
        ]);

        $response = $this->post(route('purchase-orders.store'), [
            'order_number' => 'PO-2024-001',
            'supplier_id' => $supplier->id,
            'order_date' => '2024-01-15',
            'expected_date' => '2024-01-30',
            'warehouse_id' => $warehouse->id,
            'notes' => 'Test order',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 50,
                    'price' => 10000,
                ],
            ],
        ]);

        $response->assertRedirect(route('purchase-orders.index'));

        $this->assertDatabaseHas('purchase_orders', [
            'company_id' => $this->company->id,
            'order_number' => 'PO-2024-001',
            'subtotal' => 500000,
            'total' => 500000,
        ]);

        $this->assertDatabaseHas('purchase_order_items', [
            'product_id' => $product->id,
            'quantity' => 50,
            'price' => 10000,
            'total' => 500000,
        ]);
    }

    public function test_view_purchase_orders(): void
    {
        $response = $this->get(route('purchase-orders.index'));

        $response->assertStatus(200);
    }
}
