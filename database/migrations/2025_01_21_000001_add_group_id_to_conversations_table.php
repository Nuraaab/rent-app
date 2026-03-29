<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Only proceed if the column doesn't already exist
        if (!Schema::hasColumn('conversations', 'group_id')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->unsignedBigInteger('group_id')->nullable()->after('user2_id');
                $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            });
        } else {
            // Column exists, but check if we need to update the unique constraint
            Schema::table('conversations', function (Blueprint $table) {
                // Check if old constraint exists and drop it
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexesFound = $sm->listTableIndexes('conversations');

                $hasOldConstraint = false;
                foreach ($indexesFound as $index) {
                    $columns = $index->getColumns();
                    if (
                        $index->isUnique() &&
                        count($columns) == 2 &&
                        in_array('user1_id', $columns) &&
                        in_array('user2_id', $columns)
                    ) {
                        $hasOldConstraint = true;
                        break;
                    }
                }


                // Check if new constraint exists
                $hasNewConstraint = false;
                foreach ($indexesFound as $index) {
                    $columns = $index->getColumns();
                    if (
                        $index->isUnique() &&
                        count($columns) == 3 &&
                        in_array('user1_id', $columns) &&
                        in_array('user2_id', $columns) &&
                        in_array('group_id', $columns)
                    ) {
                        $hasNewConstraint = true;
                        break;
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }
};
