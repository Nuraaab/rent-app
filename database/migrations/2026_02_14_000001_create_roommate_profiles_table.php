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
        Schema::create('roommate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('occupation')->nullable();
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->json('preferred_locations')->nullable();
            $table->json('lifestyle_tags')->nullable();
            $table->json('amenity_preferences')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->date('move_in_date')->nullable();
            $table->boolean('is_smoker')->nullable();
            $table->boolean('has_pets')->nullable();
            $table->string('sleep_schedule')->nullable();
            $table->unsignedTinyInteger('cleanliness_level')->nullable();
            $table->unsignedTinyInteger('social_level')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'city']);
            $table->index(['budget_min', 'budget_max']);
            $table->index('move_in_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roommate_profiles');
    }
};

