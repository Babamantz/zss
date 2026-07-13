<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')->nullable()->constrained('components')->cascadeOnDelete();
            $table->decimal('percentage', 4, 2)->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('type')->default('Earning'); // Earning | Deduction
            $table->boolean('is_global')->default(false);
            $table->boolean('is_active')->default(false); // default false as you noted
            $table->string('calculation_type', 50)->default('fixed'); // fixed | percentage
            $table->decimal('percentage_value', 8, 4)->nullable();

            // NULL = applies to ALL employment types. Set = restricted to that type.
            $table->foreignId('applies_to')->nullable()->constrained('employment_types')->nullOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index('is_global');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('salary_components');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
