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
            $table->string('type')->default('Earning'); //, ['Earning', 'Deduction']);
            $table->boolean('is_global')->default(false); //statutory 
            $table->boolean('is_active')->default(true); //make this false by default
            $table->string('calculation_type', 50)->default('fixed'); // ['fixed', 'percentage'])->default('fixed')->after('type');
            $table->decimal('percentage_value', 8, 4)->nullable(); // e.g. 7.5000
            // Employment type restriction
            $table->foreignId('employment_type_id')->nullable()->constrained('employment_types')->nullOnDelete();
            $table->string('applies_to', 100)->default('all'); //['all', 'permanent', 'non_permanent'])->default('all');


            // Audit Trails
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
