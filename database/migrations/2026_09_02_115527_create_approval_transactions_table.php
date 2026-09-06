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
        Schema::create('approval_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_chain_id')->constrained();
            $table->morphs('approvable'); // approvable_type, approvable_id — polymorphic
            $table->foreignId('current_step_id')->nullable()->constrained('approval_chain_steps');
            $table->string('status')->default('pending'); // pending|approved|rejected|cancelled
            $table->foreignId('initiated_by')->constrained('users');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_transactions');
    }
};
