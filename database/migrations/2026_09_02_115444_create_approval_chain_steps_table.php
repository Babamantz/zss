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
        Schema::create('approval_chain_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_chain_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('order'); // 1, 2, 3...
            $table->string('name'); // e.g. "HOD Review", "Finance Approval"
            $table->string('approver_type'); // 'role' | 'user' | 'department_head'
            $table->string('approver_value'); // role name, user id, etc.
            $table->boolean('requires_all')->default(false); // all approvers in step must approve, or just one
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_chain_steps');
    }
};
