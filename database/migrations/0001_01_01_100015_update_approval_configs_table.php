<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approval_configs', function (Blueprint $table) {
            $table->string('type', 20)->default('transaction')->after('company_id');
            $table->boolean('requires_level_3')->default(false)->after('requires_level_2');
            $table->string('level_3_role', 10)->nullable()->after('level_2_role');
            $table->integer('deadline_hours')->nullable()->default(72)->after('level_3_role');
        });
    }

    public function down(): void
    {
        Schema::table('approval_configs', function (Blueprint $table) {
            $table->dropColumn(['type', 'requires_level_3', 'level_3_role', 'deadline_hours']);
        });
    }
};
