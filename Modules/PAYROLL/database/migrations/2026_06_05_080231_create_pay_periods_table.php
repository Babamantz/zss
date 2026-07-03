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
        Schema::create('pay_periods', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 30)->default('draft');
            $table->boolean('is_confirmed')->default(false);
            $table->decimal('total_gross',       15, 2)->default(0);
            $table->decimal('total_net',         15, 2)->default(0);
            $table->decimal('total_allowances',  15, 2)->default(0);
            $table->decimal('total_deductions',  15, 2)->default(0);
            $table->decimal('total_cost',        15, 2)->default(0); // gross + employer contributions
            $table->unsignedInteger('employee_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['start_date', 'end_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('pay_periods');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
