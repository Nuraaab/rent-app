<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum column to include 'super_like'
        // MySQL requires dropping and recreating the column to modify enum values
        if (Schema::hasTable('user_interactions')) {
            // Check if super_like already exists in the enum
            $enumValues = DB::select("SHOW COLUMNS FROM user_interactions WHERE Field = 'type'");
            if (!empty($enumValues)) {
                $enumStr = $enumValues[0]->Type;
                if (strpos($enumStr, 'super_like') === false) {
                    // Drop the unique constraint first
                    Schema::table('user_interactions', function (Blueprint $table) {
                        $table->dropUnique(['user_id', 'target_user_id', 'type']);
                    });
                    
                    // Modify the enum column
                    DB::statement("ALTER TABLE user_interactions MODIFY COLUMN type ENUM('like', 'nudge', 'super_like') DEFAULT 'like'");
                    
                    // Recreate the unique constraint
                    Schema::table('user_interactions', function (Blueprint $table) {
                        $table->unique(['user_id', 'target_user_id', 'type']);
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to enum without 'super_like'
        if (Schema::hasTable('user_interactions')) {
            // Drop the unique constraint first
            Schema::table('user_interactions', function (Blueprint $table) {
                $table->dropUnique(['user_id', 'target_user_id', 'type']);
            });
            
            // Modify the enum column back
            DB::statement("ALTER TABLE user_interactions MODIFY COLUMN type ENUM('like', 'nudge') DEFAULT 'like'");
            
            // Recreate the unique constraint
            Schema::table('user_interactions', function (Blueprint $table) {
                $table->unique(['user_id', 'target_user_id', 'type']);
            });
        }
    }
};
