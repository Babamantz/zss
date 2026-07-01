<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_module_id')->constrained('chain_modules')->cascadeOnDelete();
            $table->unsignedTinyInteger('level_no');        // 1, 2, 3...
            $table->string('level_name', 80);               // e.g. "Officer Review"
            $table->unsignedInteger('order');               // sort order
            $table->boolean('is_active')->default(true);
            $table->boolean('can_reject')->default(true);
            $table->boolean('can_return')->default(true);
            $table->boolean('is_final')->default(false);
            $table->json('conditions')->nullable();
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['chain_module_id', 'level_no']);
            $table->index(['chain_module_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_steps');
    }
};
