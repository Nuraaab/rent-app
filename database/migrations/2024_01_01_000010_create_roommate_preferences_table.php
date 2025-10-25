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
            $table->unsignedBigInteger('user_id');
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->enum('gender_preference', ['male', 'female', 'no_preference'])->default('no_preference');
            $table->string('location_preference')->nullable();
            $table->json('lifestyle_tags')->nullable(); // e.g., ['clean', 'night_owl', 'pets']
            $table->json('amenities')->nullable(); // e.g., ['parking', 'gym', 'pool']
            $table->text('about')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id');
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
