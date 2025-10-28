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
        Schema::create('networking_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('skills')->nullable(); // Array of skills
            $table->string('industry')->nullable();
            $table->json('project_interests')->nullable(); // Array of interests
            $table->string('availability')->nullable(); // e.g., "Full-time", "Part-time", "Open to opportunities"
            $table->string('location')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('industry');
        });
        
        // Create connection requests table (like group members but simpler)
        Schema::create('networking_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('networking_profile_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
            
            // Unique constraint: a user can only connect to a profile once
            $table->unique(['user_id', 'networking_profile_id']);
            
            // Indexes
            $table->index('networking_profile_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('networking_connections');
        Schema::dropIfExists('networking_profiles');
    }
};
