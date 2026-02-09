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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nida_no', 50)->nullable();
            $table->string('nida_file', 100)->nullable();
            $table->string('photo_file', 100)->nullable();
            $table->string('zan_id_no', 50)->nullable();
            $table->string('zan_id_file', 100)->nullable();
            $table->string('birth_certificate_file', 100)->nullable();
            $table->string('employment_contract_file', 100)->nullable();
            $table->string('opf_number', 50)->nullable();
            $table->string('health_insurance_no', 50)->nullable();
            $table->string('designation', 50)->nullable();
            $table->string('social_security_no', 50)->nullable();
            $table->string('bank_account_no', 50)->nullable();
            $table->string('disability', 10); //['no', 'yes'])->default('no');
            $table->string('is_active', 10); //['active', 'in-active'])->default('active');
            $table->string('education', 30); //length: ['certificate', 'Advance Diploma', 'diploma', 'Bachelor', 'Master', 'Phd'])->default('Bachelor');
            $table->string('gender', 10); //['male', 'female'])->default('male');
            $table->string('marital_status', 10); // ['married', 'single', 'divorced'])->default('single');
            $table->string('phone_number', 15)->nullable()->index();
            $table->date('dob')->nullable(); // Added nullable()
            $table->date('hired_date')->nullable(); //add 
            $table->date('retiring_date')->nullable();
            $table->boolean('hr_registered')->default(false);
            $table->boolean('is_officer')->default(true);
            $table->boolean('is_manager')->default(false);
            $table->boolean('is_director')->default(false);
            $table->boolean('is_director_general')->default(false);
            $table->boolean('is_coordinator')->default(false);

            // Foreign keys (Added nullable to avoid constraints issues)
            $table->foreignId('bank_name_id')->nullable()->constrained('banks')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
