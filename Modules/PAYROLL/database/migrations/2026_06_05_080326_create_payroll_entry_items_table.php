<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_entry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_entry_id')->constrained('payroll_entries')->cascadeOnDelete();
            $table->foreignId('component_id')->nullable()->constrained('components');
            $table->string('item_type', 100)->default('Earning'); // Retains textual string integrity over centuries of modifications
            $table->string('component_name_snapshot'); // Retains textual string integrity over centuries of modifications
            $table->decimal('finalized_amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('payroll_entry_items');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
