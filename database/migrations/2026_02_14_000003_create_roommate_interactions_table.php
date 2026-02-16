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
        Schema::create('roommate_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('target_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['like', 'pass', 'super_like'])->default('like');
            $table->timestamp('matched_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'target_user_id', 'type'], 'roommate_interactions_unique');
            $table->index(['target_user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roommate_interactions');
    }
};

