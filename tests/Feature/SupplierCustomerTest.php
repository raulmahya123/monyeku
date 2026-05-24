<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCustomerTest extends TestCase
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

    public function test_create_supplier(): void
    {
        $response = $this->post(route('suppliers.store'), [
            'code' => 'SUP-001',
            'name' => 'PT Supplier Utama',
            'contact_person' => 'Budi',
            'phone' => '021-789012',
            'email' => 'budi@supplier.com',
            'address' => 'Jl. Supplier No. 1',
            'npwp' => '01.234.567.8-999.000',
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', [
            'company_id' => $this->company->id,
            'code' => 'SUP-001',
            'name' => 'PT Supplier Utama',
        ]);
    }

    public function test_create_customer(): void
    {
        $response = $this->post(route('customers.store'), [
            'code' => 'CUS-001',
            'name' => 'PT Customer Sejahtera',
            'phone' => '021-345678',
            'email' => 'contact@customer.com',
            'address' => 'Jl. Customer No. 1',
            'contact_person' => 'Ani',
        ]);

        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('customers', [
            'company_id' => $this->company->id,
            'code' => 'CUS-001',
            'name' => 'PT Customer Sejahtera',
        ]);
    }

    public function test_view_suppliers(): void
    {
        $response = $this->get(route('suppliers.index'));

        $response->assertStatus(200);
    }

    public function test_view_customers(): void
    {
        $response = $this->get(route('customers.index'));

        $response->assertStatus(200);
    }

    public function test_edit_supplier(): void
    {
        $supplier = Supplier::create([
            'company_id' => $this->company->id,
            'code' => 'SUP-001',
            'name' => 'PT Supplier Utama',
        ]);

        $response = $this->get(route('suppliers.edit', $supplier));

        $response->assertStatus(200);
    }
}
