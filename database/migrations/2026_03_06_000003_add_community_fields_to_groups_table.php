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
        if (!Schema::hasTable('groups')) {
            return;
        }

        Schema::table('groups', function (Blueprint $table) {
            if (!Schema::hasColumn('groups', 'height')) {
                $table->string('height', 20)->nullable()->after('admin_approval');
            }
            if (!Schema::hasColumn('groups', 'pets')) {
                $table->string('pets', 20)->nullable()->after('height');
            }
            if (!Schema::hasColumn('groups', 'children')) {
                $table->string('children', 40)->nullable()->after('pets');
            }
            if (!Schema::hasColumn('groups', 'politics')) {
                $table->string('politics', 20)->nullable()->after('children');
            }
            if (!Schema::hasColumn('groups', 'faith_identity')) {
                $table->string('faith_identity', 60)->nullable()->after('politics');
            }
            if (!Schema::hasColumn('groups', 'education')) {
                $table->string('education', 40)->nullable()->after('faith_identity');
            }
            if (!Schema::hasColumn('groups', 'body_type')) {
                $table->string('body_type', 20)->nullable()->after('education');
            }
            if (!Schema::hasColumn('groups', 'exercise')) {
                $table->string('exercise', 20)->nullable()->after('body_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('groups')) {
            return;
        }

        Schema::table('groups', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach ([
                'height',
                'pets',
                'children',
                'politics',
                'faith_identity',
                'education',
                'body_type',
                'exercise',
            ] as $column) {
                if (Schema::hasColumn('groups', $column)) {
                    $columnsToDrop[] = $column;
                }
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
