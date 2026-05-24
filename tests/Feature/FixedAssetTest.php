<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coa;
use App\Models\Company;
use App\Models\FixedAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FixedAssetTest extends TestCase
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

    public function test_create_fixed_asset(): void
    {
        $coa = Coa::create([
            'company_id' => $this->company->id,
            'code' => '1-1100',
            'name' => 'Kas',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        $category = Category::where('company_id', $this->company->id)->first();

        $response = $this->post(route('fixed-assets.store'), [
            'code' => 'FA-001',
            'name' => 'Mesin Produksi',
            'category_id' => $category->id,
            'purchase_date' => '2024-01-01',
            'purchase_price' => 50000000,
            'residual_value' => 5000000,
            'useful_life' => 60,
            'depreciation_method' => 'straight_line',
            'depreciation_start_date' => '2024-02-01',
            'location' => 'Gudang A',
            'description' => 'Mesin produksi utama',
            'coa_id' => $coa->id,
        ]);

        $response->assertRedirect(route('fixed-assets.index'));

        $this->assertDatabaseHas('fixed_assets', [
            'company_id' => $this->company->id,
            'code' => 'FA-001',
            'name' => 'Mesin Produksi',
            'purchase_price' => 50000000,
            'book_value' => 50000000,
            'accumulated_depreciation' => 0,
        ]);
    }

    public function test_view_fixed_assets(): void
    {
        $response = $this->get(route('fixed-assets.index'));

        $response->assertStatus(200);
    }
}
