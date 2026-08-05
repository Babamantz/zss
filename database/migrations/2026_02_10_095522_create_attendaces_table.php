
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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Creator
            $table->string('title')->nullable();
            $table->string('heading')->nullable();
            $table->boolean('is_swahili')->default(false);
            $table->integer('number_of_rows')->default(10); // Number of attendee rows
            $table->string('attendance_path', 255)->nullable();
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->foreignId('division_id')->nullable()->constrained('units')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('attendances');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
