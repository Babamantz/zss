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

        Schema::create('payroll_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('pay_period_id')->constrained('pay_periods');
            $table->decimal('total_gross', 15, 2);
            $table->decimal('total_deductions', 15, 2);
            $table->decimal('net_pay', 15, 2);
            $table->timestamp('processed_at')->nullable();

            // Audit Trails (Who approved/processed this specific employee's final payroll run)
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['employee_id', 'pay_period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
            Schema::dropIfExists('payroll_entries');
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
