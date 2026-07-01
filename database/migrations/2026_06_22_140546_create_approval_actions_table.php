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
        Schema::create('approval_actions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('approval_request_id')
                ->constrained('approval_requests')
                ->cascadeOnDelete();
            $table->foreignId('flow_level_id')
                ->constrained('approval_steps')
                ->restrictOnDelete();
            $table->unsignedTinyInteger('level_no');
            $table->string('status', 20);   // Pending|Approved|Rejected|Returned
            $table->foreignId('assigned_to')
                ->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('actioned_by')
                ->nullable()->constrained('users')->nullOnDelete();
            $table->text('comments')->nullable();
            $table->string('rejection_reason', 500)->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('device')->nullable();
            $table->timestamp('actioned_at')->nullable();
            $table->timestamps();
            $table->index(['approval_request_id', 'level_no']);
            $table->index(['assigned_to', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_actions');
    }
};
