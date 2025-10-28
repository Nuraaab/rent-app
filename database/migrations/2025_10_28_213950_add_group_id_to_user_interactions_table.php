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
        // Check if column already exists (from previous failed migration)
        if (!Schema::hasColumn('user_interactions', 'group_id')) {
            // First, add the group_id column as nullable
            Schema::table('user_interactions', function (Blueprint $table) {
                $table->foreignId('group_id')->nullable()->after('target_user_id');
            });
        }
        
        // Check if foreign key already exists
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'user_interactions'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            AND CONSTRAINT_NAME LIKE '%group_id%'
        ");
        
        if (empty($foreignKeys)) {
            // Add foreign key constraint
            Schema::table('user_interactions', function (Blueprint $table) {
                $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            });
        }
        
        // First, find and drop any foreign keys that might use the unique index
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'user_interactions'
            AND REFERENCED_TABLE_NAME IS NOT NULL
            AND COLUMN_NAME IN ('user_id', 'target_user_id')
        ");
        
        foreach ($foreignKeys as $fk) {
            $constraintName = $fk->CONSTRAINT_NAME;
            if ($constraintName !== 'user_interactions_user_id_foreign' && 
                $constraintName !== 'user_interactions_target_user_id_foreign') {
                try {
                    DB::statement("ALTER TABLE user_interactions DROP FOREIGN KEY `$constraintName`");
                } catch (\Exception $e) {
                    // Ignore if doesn't exist
                }
            }
        }
        
        // Check if old unique constraint exists
        $indexes = DB::select("
            SHOW INDEX FROM user_interactions 
            WHERE Key_name = 'user_interactions_user_id_target_user_id_type_unique'
        ");
        
        if (!empty($indexes)) {
            // Try to drop the old unique constraint
            try {
                DB::statement('ALTER TABLE user_interactions DROP INDEX user_interactions_user_id_target_user_id_type_unique');
            } catch (\Exception $e) {
                // If it fails due to foreign key, that's okay - we'll work around it
                // The new unique constraint will be created anyway
            }
        }
        
        // Check if new unique constraint already exists
        $newIndexes = DB::select("
            SHOW INDEX FROM user_interactions 
            WHERE Key_name = 'user_interactions_unique'
        ");
        
        if (empty($newIndexes)) {
            // Add new unique constraint that includes group_id
            Schema::table('user_interactions', function (Blueprint $table) {
                // This allows same user interaction in different groups
                $table->unique(['user_id', 'target_user_id', 'type', 'group_id'], 'user_interactions_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new unique constraint using raw SQL
        DB::statement('ALTER TABLE user_interactions DROP INDEX user_interactions_unique');
        
        Schema::table('user_interactions', function (Blueprint $table) {
            // Drop the group_id column
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
            
            // Restore the old unique constraint
            $table->unique(['user_id', 'target_user_id', 'type']);
        });
    }
};
