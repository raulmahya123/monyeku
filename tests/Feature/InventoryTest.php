<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\StockMutation;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
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

    public function test_create_warehouse(): void
    {
        $response = $this->post(route('warehouses.store'), [
            'code' => 'WH-001',
            'name' => 'Main Warehouse',
            'address' => 'Jl. Test No. 1',
            'phone' => '021-123456',
        ]);

        $response->assertRedirect(route('warehouses.index'));
        $this->assertDatabaseHas('warehouses', [
            'company_id' => $this->company->id,
            'code' => 'WH-001',
            'name' => 'Main Warehouse',
        ]);
    }

    public function test_create_product(): void
    {
        $warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'code' => 'WH-001',
            'name' => 'Main Warehouse',
        ]);

        $category = Category::where('company_id', $this->company->id)->first();

        $response = $this->post(route('products.store'), [
            'code' => 'PRD-001',
            'name' => 'Test Product',
            'category_id' => $category->id,
            'unit' => 'pcs',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock_min' => 5,
            'stock_max' => 100,
            'type' => 'product',
            'initial_stock' => 50,
            'warehouse_id' => $warehouse->id,
        ]);

        $response->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'company_id' => $this->company->id,
            'code' => 'PRD-001',
            'name' => 'Test Product',
        ]);

        $product = Product::where('code', 'PRD-001')->first();

        $this->assertDatabaseHas('stock_mutations', [
            'company_id' => $this->company->id,
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'in',
            'quantity' => 50,
        ]);

        $this->assertDatabaseHas('product_warehouse', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'stock' => 50,
        ]);
    }

    public function test_view_products(): void
    {
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
    }

    public function test_view_warehouses(): void
    {
        $response = $this->get(route('warehouses.index'));

        $response->assertStatus(200);
    }

    public function test_stock_card(): void
    {
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

        $product->warehouses()->syncWithoutDetaching([
            $warehouse->id => ['stock' => 25, 'avg_cost' => 10000],
        ]);

        StockMutation::create([
            'company_id' => $this->company->id,
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'in',
            'quantity' => 25,
            'price' => 10000,
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('products.stock-card', $product));

        $this->assertDatabaseHas('stock_mutations', [
            'company_id' => $this->company->id,
            'product_id' => $product->id,
            'quantity' => 25,
        ]);
    }
}
