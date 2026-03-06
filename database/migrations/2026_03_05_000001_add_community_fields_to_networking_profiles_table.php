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
            $table->string('height', 20)->nullable()->after('privacy');
            $table->string('pets', 20)->nullable()->after('height');
            $table->string('children', 40)->nullable()->after('pets');
            $table->string('politics', 20)->nullable()->after('children');
            $table->string('faith_identity', 60)->nullable()->after('politics');
            $table->string('education', 40)->nullable()->after('faith_identity');
            $table->string('body_type', 20)->nullable()->after('education');
            $table->string('exercise', 20)->nullable()->after('body_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('networking_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'height',
                'pets',
                'children',
                'politics',
                'faith_identity',
                'education',
                'body_type',
                'exercise',
            ]);
        });
    }
};

