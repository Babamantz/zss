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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('photo_file', 100)->nullable();
            $table->string('birth_certificate_file', 100)->nullable();
            $table->string('opf_number', 50)->nullable();
            $table->string('file_number', 50)->nullable();
            $table->boolean('is_disable')->default(false); //['no', 'yes'])->default('no');
            $table->string('is_active', 10); //['active', 'in-active'])->default('active');
            $table->string('education', 30); //length: ['certificate', 'Advance Diploma', 'diploma', 'Bachelor', 'Master', 'Phd'])->default('Bachelor');
            $table->string('gender', 10); //['male', 'female'])->default('male');
            $table->string('marital_status', 10); // ['married', 'single', 'divorced'])->default('single');
            $table->json('contacts')->nullable();
            $table->date('dob')->nullable(); // Added nullable()
            $table->date('hired_date')->nullable(); //add 
            $table->date('retiring_date')->nullable();
            $table->boolean('is_hr_registered')->default(false);
            $table->boolean('is_officer')->default(true);
            $table->boolean('is_permanent')->default(true);
            $table->foreignId('department_id')->nullable()->constrained('departments')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained('designations')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('employees');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
