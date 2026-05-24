<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        $incomeCategories = ['Gaji', 'Penjualan', 'Investasi', 'Freelance', 'Bunga Bank', 'Lain-lain'];
        $expenseCategories = ['Bahan Baku', 'Listrik', 'Air', 'Marketing', 'Gaji Karyawan', 'Sewa', 'Transportasi', 'Makanan', 'Perlengkapan', 'Pajak', 'Internet', 'Lain-lain'];

        foreach ($companies as $company) {
            foreach ($incomeCategories as $name) {
                Category::create([
                    'company_id' => $company->id,
                    'name' => $name,
                    'type' => 'income',
                    'is_active' => true,
                ]);
            }

            foreach ($expenseCategories as $name) {
                Category::create([
                    'company_id' => $company->id,
                    'name' => $name,
                    'type' => 'expense',
                    'is_active' => true,
                ]);
            }
        }
    }
}