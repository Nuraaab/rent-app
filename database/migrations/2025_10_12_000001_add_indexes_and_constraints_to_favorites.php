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
        Schema::table('favorites', function (Blueprint $table) {
            // Add composite unique constraint to prevent duplicate favorites
            // User can only favorite the same rental or job once
            $table->unique(['user_id', 'rental_id'], 'unique_user_rental_favorite');
            $table->unique(['user_id', 'job_position_id'], 'unique_user_job_favorite');
            
            // Add indexes for better query performance
            $table->index(['user_id', 'rental_id'], 'idx_user_rental');
            $table->index(['user_id', 'job_position_id'], 'idx_user_job');
            $table->index('user_id', 'idx_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropUnique('unique_user_rental_favorite');
            $table->dropUnique('unique_user_job_favorite');
            $table->dropIndex('idx_user_rental');
            $table->dropIndex('idx_user_job');
            $table->dropIndex('idx_user');
        });
    }
};

