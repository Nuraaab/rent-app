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
        Schema::create('roommate_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->enum('gender_preference', ['any', 'male', 'female', 'other'])->default('any');
            $table->decimal('min_budget', 10, 2)->nullable();
            $table->decimal('max_budget', 10, 2)->nullable();
            $table->json('preferred_locations')->nullable();
            $table->json('lifestyle_preferences')->nullable();
            $table->json('amenity_preferences')->nullable();
            $table->enum('smoking_preference', ['any', 'smoker', 'non_smoker'])->default('any');
            $table->enum('pet_preference', ['any', 'pets_ok', 'no_pets'])->default('any');
            $table->string('sleep_schedule')->nullable();
            $table->date('move_in_from')->nullable();
            $table->date('move_in_to')->nullable();
            $table->timestamps();

            $table->index(['gender_preference', 'min_budget', 'max_budget']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roommate_preferences');
    }
};

