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
        Schema::create('user_interactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // User who gave the interaction
            $table->unsignedBigInteger('target_user_id'); // User who received the interaction
            $table->enum('type', ['like', 'nudge']); // Interaction type
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('target_user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Prevent duplicate interactions
            $table->unique(['user_id', 'target_user_id', 'type']);
            
            $table->index('user_id');
            $table->index('target_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_interactions');
    }
};
