<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $defaultCoa = [
        // ASSET (1-xxx) — normal_balance: debit
        ['code' => '1-1000', 'name' => 'Aset Lancar', 'type' => 'asset', 'normal_balance' => 'debit', 'children' => [
            ['code' => '1-1100', 'name' => 'Kas', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1-1101', 'name' => 'Kas Kecil', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1-1200', 'name' => 'Bank BCA', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1-1201', 'name' => 'Bank Mandiri', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1-1202', 'name' => 'Bank Lainnya', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1-1300', 'name' => 'Piutang Usaha', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1-1400', 'name' => 'Perlengkapan', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1-1500', 'name' => 'Persediaan Barang', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1-1600', 'name' => 'Uang Muka', 'type' => 'asset', 'normal_balance' => 'debit'],
        ]],
        ['code' => '1-2000', 'name' => 'Aset Tetap', 'type' => 'asset', 'normal_balance' => 'debit', 'children' => [
            ['code' => '1-2100', 'name' => 'Peralatan', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1-2110', 'name' => 'Akum. Penyusutan Peralatan', 'type' => 'asset', 'normal_balance' => 'credit'],
            ['code' => '1-2200', 'name' => 'Kendaraan', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1-2210', 'name' => 'Akum. Penyusutan Kendaraan', 'type' => 'asset', 'normal_balance' => 'credit'],
            ['code' => '1-2300', 'name' => 'Bangunan', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1-2310', 'name' => 'Akum. Penyusutan Bangunan', 'type' => 'asset', 'normal_balance' => 'credit'],
        ]],
        // LIABILITY (2-xxx) — normal_balance: credit
        ['code' => '2-1000', 'name' => 'Kewajiban Lancar', 'type' => 'liability', 'normal_balance' => 'credit', 'children' => [
            ['code' => '2-1100', 'name' => 'Hutang Usaha', 'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '2-1200', 'name' => 'Hutang Bank', 'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '2-1300', 'name' => 'Hutang Pajak', 'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '2-1400', 'name' => 'Hutang Gaji', 'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '2-1500', 'name' => 'Pendapatan Diterima Dimuka', 'type' => 'liability', 'normal_balance' => 'credit'],
        ]],
        ['code' => '2-2000', 'name' => 'Kewajiban Jangka Panjang', 'type' => 'liability', 'normal_balance' => 'credit', 'children' => [
            ['code' => '2-2100', 'name' => 'Hutang Jangka Panjang', 'type' => 'liability', 'normal_balance' => 'credit'],
        ]],
        // EQUITY (3-xxx) — normal_balance: credit
        ['code' => '3-1000', 'name' => 'Modal', 'type' => 'equity', 'normal_balance' => 'credit', 'children' => [
            ['code' => '3-1100', 'name' => 'Modal Pemilik', 'type' => 'equity', 'normal_balance' => 'credit'],
            ['code' => '3-1200', 'name' => 'Prive', 'type' => 'equity', 'normal_balance' => 'debit'],
            ['code' => '3-1300', 'name' => 'Laba Ditahan', 'type' => 'equity', 'normal_balance' => 'credit'],
            ['code' => '3-1400', 'name' => 'Laba Tahun Berjalan', 'type' => 'equity', 'normal_balance' => 'credit'],
        ]],
        // INCOME (4-xxx) — normal_balance: credit
        ['code' => '4-0000', 'name' => 'Pendapatan', 'type' => 'income', 'normal_balance' => 'credit', 'children' => [
            ['code' => '4-1000', 'name' => 'Pendapatan Usaha', 'type' => 'income', 'normal_balance' => 'credit'],
            ['code' => '4-2000', 'name' => 'Pendapatan Lain-lain', 'type' => 'income', 'normal_balance' => 'credit'],
        ]],
        // EXPENSE (5-xxx) — normal_balance: debit
        ['code' => '5-0000', 'name' => 'Beban', 'type' => 'expense', 'normal_balance' => 'debit', 'children' => [
            ['code' => '5-1000', 'name' => 'Beban Gaji', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5-1100', 'name' => 'Beban Sewa', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5-1200', 'name' => 'Beban Listrik & Air', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5-1300', 'name' => 'Beban Telepon & Internet', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5-1400', 'name' => 'Beban Transportasi', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5-1500', 'name' => 'Beban Perlengkapan', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5-1600', 'name' => 'Beban Penyusutan', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5-1700', 'name' => 'Beban Pemasaran', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5-1800', 'name' => 'Beban Makan & Minum', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '5-1900', 'name' => 'Beban Lain-lain', 'type' => 'expense', 'normal_balance' => 'debit'],
        ]],
        // COGS (6-xxx) — normal_balance: debit
        ['code' => '6-0000', 'name' => 'Harga Pokok Penjualan', 'type' => 'expense', 'normal_balance' => 'debit', 'children' => [
            ['code' => '6-1000', 'name' => 'Pembelian Barang', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '6-1100', 'name' => 'Retur Pembelian', 'type' => 'expense', 'normal_balance' => 'credit'],
            ['code' => '6-1200', 'name' => 'Potongan Pembelian', 'type' => 'expense', 'normal_balance' => 'credit'],
        ]],
    ];

    public function up(): void
    {
        $companies = DB::table('companies')->get();

        foreach ($companies as $company) {
            $this->seedCoa($company->id);
        }
    }

    private function seedCoa(int $companyId): void
    {
        foreach ($this->defaultCoa as $group) {
            $parentId = DB::table('chart_of_accounts')->insertGetId([
                'company_id' => $companyId,
                'code' => $group['code'],
                'name' => $group['name'],
                'type' => $group['type'],
                'normal_balance' => $group['normal_balance'],
                'parent_id' => null,
                'description' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($group['children'] as $child) {
                DB::table('chart_of_accounts')->insert([
                    'company_id' => $companyId,
                    'code' => $child['code'],
                    'name' => $child['name'],
                    'type' => $child['type'],
                    'normal_balance' => $child['normal_balance'],
                    'parent_id' => $parentId,
                    'description' => null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('chart_of_accounts')->delete();
    }
};
