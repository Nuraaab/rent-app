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
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['active', 'pending'])->default('active');
            $table->unsignedBigInteger('approved_by')->nullable(); // Admin who approved the request
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            // Unique constraint to prevent duplicate memberships
            $table->unique(['group_id', 'user_id']);
            
            // Indexes for better performance
            $table->index('group_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
