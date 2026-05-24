<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved')->after('status');
            $table->timestamp('approved_at')->nullable()->after('approval_status');
        });

        Schema::table('debts', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved')->after('status');
            $table->timestamp('approved_at')->nullable()->after('approval_status');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->timestamp('approval_deadline_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'approved_at']);
        });

        Schema::table('debts', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'approved_at']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['approval_deadline_at']);
        });
    }
};
