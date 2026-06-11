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
        Schema::create('employee_finance_profiles', function (Blueprint $table) {

            $table->id();
            // cascadeOnDelete ensures if hard-purged, profiles drop, but softDeletes on Employee shields this
            $table->foreignId('employee_id')->unique()->constrained('employees')->cascadeOnDelete();
            $table->decimal('base_salary', 15, 2);
            $table->string('bank_account_number');
            $table->string('tax_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
            Schema::dropIfExists('employee_finance_profiles');
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
