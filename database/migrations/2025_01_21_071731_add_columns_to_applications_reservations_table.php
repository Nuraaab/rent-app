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
        Schema::table('applications_reservations', function (Blueprint $table) {
            $table->string('cv')->nullable();
            $table->longText('application_letter')->nullable();
            $table->string('github_link')->nullable();
            $table->string('linkedin_link')->nullable();
            $table->string('portfolio_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications_reservations', function (Blueprint $table) {
            $table->dropColumn(['cv', 'application_letter', 'github_link', 'linkedin_link', 'portfolio_link']);
        });
    }
};
