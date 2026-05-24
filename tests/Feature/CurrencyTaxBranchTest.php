<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyTaxBranchTest extends TestCase
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

    public function test_create_currency(): void
    {
        $response = $this->post(route('currencies.store'), [
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 15500,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('currencies.index'));
        $this->assertDatabaseHas('currencies', [
            'company_id' => $this->company->id,
            'code' => 'USD',
            'name' => 'US Dollar',
        ]);
    }

    public function test_create_tax(): void
    {
        $response = $this->post(route('taxes.store'), [
            'code' => 'PPN',
            'name' => 'Pajak Pertambahan Nilai',
            'rate' => 11,
            'type' => 'ppn',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('taxes.index'));
        $this->assertDatabaseHas('taxes', [
            'company_id' => $this->company->id,
            'code' => 'PPN',
            'name' => 'Pajak Pertambahan Nilai',
            'rate' => 11,
            'type' => 'ppn',
        ]);
    }

    public function test_create_branch(): void
    {
        $response = $this->post(route('branches.store'), [
            'code' => 'BDG',
            'name' => 'Cabang Bandung',
            'address' => 'Jl. Asia Afrika No. 1',
            'phone' => '022-123456',
            'email' => 'bandung@company.com',
        ]);

        $response->assertRedirect(route('branches.index'));
        $this->assertDatabaseHas('branches', [
            'company_id' => $this->company->id,
            'code' => 'BDG',
            'name' => 'Cabang Bandung',
        ]);
    }

    public function test_view_currencies(): void
    {
        $response = $this->get(route('currencies.index'));

        $response->assertStatus(200);
    }

    public function test_view_taxes(): void
    {
        $response = $this->get(route('taxes.index'));

        $response->assertStatus(200);
    }

    public function test_view_branches(): void
    {
        $response = $this->get(route('branches.index'));

        $response->assertStatus(200);
    }
}
