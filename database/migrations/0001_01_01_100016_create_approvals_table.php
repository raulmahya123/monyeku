<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->morphs('approvable');
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();
            $table->integer('level')->default(1);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->timestamps();

            $table->unique(['approvable_type', 'approvable_id', 'level', 'approver_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
