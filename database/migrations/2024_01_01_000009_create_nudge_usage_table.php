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
        Schema::create('nudge_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('nudges_used')->default(0);
            $table->integer('nudges_purchased')->default(0);
            $table->date('last_reset_date'); // Track when to reset monthly count
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
        Schema::dropIfExists('nudge_usage');
    }
};
