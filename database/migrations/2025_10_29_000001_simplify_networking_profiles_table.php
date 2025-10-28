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
        Schema::table('networking_profiles', function (Blueprint $table) {
            // Remove unnecessary columns
            if (Schema::hasColumn('networking_profiles', 'skills')) {
                $table->dropColumn('skills');
            }
            if (Schema::hasColumn('networking_profiles', 'industry')) {
                $table->dropColumn('industry');
            }
            if (Schema::hasColumn('networking_profiles', 'project_interests')) {
                $table->dropColumn('project_interests');
            }
            if (Schema::hasColumn('networking_profiles', 'availability')) {
                $table->dropColumn('availability');
            }
            if (Schema::hasColumn('networking_profiles', 'location')) {
                $table->dropColumn('location');
            }
            
            // Add privacy column if it doesn't exist
            if (!Schema::hasColumn('networking_profiles', 'privacy')) {
                $table->enum('privacy', ['open', 'closed'])->default('open')->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('networking_profiles', function (Blueprint $table) {
            // Add back the columns
            $table->json('skills')->nullable();
            $table->string('industry')->nullable();
            $table->json('project_interests')->nullable();
            $table->string('availability')->nullable();
            $table->string('location')->nullable();
            
            // Remove privacy
            if (Schema::hasColumn('networking_profiles', 'privacy')) {
                $table->dropColumn('privacy');
            }
        });
    }
};

