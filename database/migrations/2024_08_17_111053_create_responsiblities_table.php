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
        Schema::create('responsiblities', function (Blueprint $table) {
            $table->id();
            $table->string("responsiblity")->nullable();
            $table->unsignedBigInteger('job_position_id')->nullable();
            $table->foreign('job_position_id')->references('id')->on('job_positions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responsiblities');
    }
};