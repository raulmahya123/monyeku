<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approval_configs', function (Blueprint $table) {
            $table->enum('approval_mode', ['sequential', 'parallel'])->default('sequential')->after('max_amount');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete()->after('deadline_hours');
            $table->date('effective_from')->nullable()->after('assigned_to');
            $table->date('effective_until')->nullable()->after('effective_from');
            $table->string('skip_role')->nullable()->after('effective_until');
            $table->decimal('level_2_min_amount', 18, 2)->nullable()->after('skip_role');
            $table->decimal('level_3_min_amount', 18, 2)->nullable()->after('level_2_min_amount');
        });
    }

    public function down(): void
    {
        Schema::table('approval_configs', function (Blueprint $table) {
            $table->dropColumn([
                'approval_mode', 'assigned_to', 'effective_from', 'effective_until',
                'skip_role', 'level_2_min_amount', 'level_3_min_amount',
            ]);
        });
    }
};
