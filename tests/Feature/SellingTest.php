<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellingTest extends TestCase
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

    public function test_create_quotation(): void
    {
        $customer = Customer::create([
            'company_id' => $this->company->id,
            'code' => 'CUS-001',
            'name' => 'PT Customer',
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

        $response = $this->post(route('quotations.store'), [
            'quotation_number' => 'Q-2024-001',
            'customer_id' => $customer->id,
            'quotation_date' => '2024-01-15',
            'valid_until' => '2024-02-15',
            'notes' => 'Quotation note',
            'terms' => 'Payment 30 days',
            'items' => [
                [
                    'product_id' => $product->id,
                    'description' => 'Test Product - Qty 10',
                    'quantity' => 10,
                    'price' => 15000,
                ],
            ],
        ]);

        $response->assertRedirect(route('quotations.index'));

        $this->assertDatabaseHas('quotations', [
            'company_id' => $this->company->id,
            'quotation_number' => 'Q-2024-001',
            'subtotal' => 150000,
            'total' => 150000,
        ]);

        $this->assertDatabaseHas('quotation_items', [
            'product_id' => $product->id,
            'quantity' => 10,
            'price' => 15000,
            'total' => 150000,
        ]);
    }

    public function test_create_sales_order(): void
    {
        $customer = Customer::create([
            'company_id' => $this->company->id,
            'code' => 'CUS-001',
            'name' => 'PT Customer',
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

        $response = $this->post(route('sales-orders.store'), [
            'order_number' => 'SO-2024-001',
            'customer_id' => $customer->id,
            'order_date' => '2024-01-15',
            'expected_date' => '2024-01-30',
            'notes' => 'Sales order note',
            'items' => [
                [
                    'product_id' => $product->id,
                    'description' => 'Test Product - Qty 5',
                    'quantity' => 5,
                    'price' => 15000,
                ],
            ],
        ]);

        $response->assertRedirect(route('sales-orders.index'));

        $this->assertDatabaseHas('sales_orders', [
            'company_id' => $this->company->id,
            'order_number' => 'SO-2024-001',
            'subtotal' => 75000,
            'total' => 75000,
        ]);

        $this->assertDatabaseHas('sales_order_items', [
            'product_id' => $product->id,
            'quantity' => 5,
            'price' => 15000,
            'total' => 75000,
        ]);
    }

    public function test_view_quotations(): void
    {
        $response = $this->get(route('quotations.index'));

        $response->assertStatus(200);
    }

    public function test_view_sales_orders(): void
    {
        $response = $this->get(route('sales-orders.index'));

        $response->assertStatus(200);
    }
}
