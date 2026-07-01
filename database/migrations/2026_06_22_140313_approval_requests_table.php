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
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_module_id')->constrained('chain_modules');
            $table->morphs('approvable');              // polymorphic — attach to any model
            $table->string('reference_no', 60)->unique();   // auto-generated
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('status', 30)->default('Pending'); // Pending|Approved|Rejected|Completed
            $table->unsignedTinyInteger('current_level')->default(1);
            $table->unsignedTinyInteger('total_levels');
            $table->json('payload')->nullable();
            $table->timestamp('submitted_at');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to_user')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'current_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_requests');
    }
};
