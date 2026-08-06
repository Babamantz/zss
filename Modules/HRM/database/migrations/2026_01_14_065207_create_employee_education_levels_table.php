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
        Schema::create('employee_education_levels', function (Blueprint $table) {
            $table->id();
            $table->string('course_name', 60)->nullable();
            $table->string('certificate_name', 60)->nullable();
            $table->string('holder_certificate_no', 60)->nullable();
            $table->string('certificate_file', 100)->nullable();
            $table->string('certificate_name_id')->nullable()->constrained('education_levels')->onDelete('set null');
            $table->foreignId('employee_id')->constrained('employees', 'id')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_education_levels');
    }
};
