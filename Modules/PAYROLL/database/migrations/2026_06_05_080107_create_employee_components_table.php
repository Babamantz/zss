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
        Schema::create('employee_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('component_id')->constrained('salary_components')->cascadeOnDelete();
            $table->decimal('custom_amount', 15, 2);
            $table->string('calculation_type',100)->default('fixed');// ['fixed', 'percentage'])->default('fixed');
            $table->decimal('percentage_value', 8, 4)->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->date('ends_at')->nullable();


            // Audit Trails
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'is_recurring', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('employee_components');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
