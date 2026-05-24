<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Currencies
        Schema::table('transactions', fn(Blueprint $t) => $t->foreignId('currency_id')->nullable()->constrained()->nullOnDelete());
        Schema::table('invoices', fn(Blueprint $t) => $t->foreignId('currency_id')->nullable()->constrained()->nullOnDelete());
        Schema::table('debts', fn(Blueprint $t) => $t->foreignId('currency_id')->nullable()->constrained()->nullOnDelete());
        Schema::table('purchase_orders', fn(Blueprint $t) => $t->foreignId('currency_id')->nullable()->constrained()->nullOnDelete());
        Schema::table('sales_orders', fn(Blueprint $t) => $t->foreignId('currency_id')->nullable()->constrained()->nullOnDelete());
        Schema::table('quotations', fn(Blueprint $t) => $t->foreignId('currency_id')->nullable()->constrained()->nullOnDelete());

        // Tax
        Schema::table('transactions', fn(Blueprint $t) => $t->foreignId('tax_id')->nullable()->constrained('taxes')->nullOnDelete());
        Schema::table('invoices', fn(Blueprint $t) => $t->foreignId('tax_id')->nullable()->constrained('taxes')->nullOnDelete());
        Schema::table('purchase_orders', fn(Blueprint $t) => $t->foreignId('tax_id')->nullable()->constrained('taxes')->nullOnDelete());
        Schema::table('quotations', fn(Blueprint $t) => $t->foreignId('tax_id')->nullable()->constrained('taxes')->nullOnDelete());
        Schema::table('sales_orders', fn(Blueprint $t) => $t->foreignId('tax_id')->nullable()->constrained('taxes')->nullOnDelete());

        // Branches
        Schema::table('users', fn(Blueprint $t) => $t->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete());
        Schema::table('transactions', fn(Blueprint $t) => $t->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete());
        Schema::table('invoices', fn(Blueprint $t) => $t->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete());
        Schema::table('debts', fn(Blueprint $t) => $t->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete());
        Schema::table('journals', fn(Blueprint $t) => $t->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete());
        Schema::table('purchase_orders', fn(Blueprint $t) => $t->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete());
        Schema::table('sales_orders', fn(Blueprint $t) => $t->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete());
        Schema::table('quotations', fn(Blueprint $t) => $t->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete());
        Schema::table('fixed_assets', fn(Blueprint $t) => $t->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete());

        // Exchange rate on transactions
        Schema::table('transactions', fn(Blueprint $t) => $t->decimal('exchange_rate', 18, 4)->default(1));
        Schema::table('invoices', fn(Blueprint $t) => $t->decimal('exchange_rate', 18, 4)->default(1));
        Schema::table('debts', fn(Blueprint $t) => $t->decimal('exchange_rate', 18, 4)->default(1));
    }

    public function down(): void
    {
        $cols = ['branch_id', 'currency_id', 'tax_id', 'exchange_rate'];
        foreach (['transactions', 'invoices', 'debts', 'journals', 'users', 'purchase_orders', 'sales_orders', 'quotations', 'fixed_assets'] as $table) {
            foreach ($cols as $col) {
                if (Schema::hasColumn($table, $col)) {
                    Schema::table($table, fn(Blueprint $t) => $t->dropColumn($col));
                }
            }
        }
    }
};
