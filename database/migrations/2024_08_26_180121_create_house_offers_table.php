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
        Schema::create('house_offers', function (Blueprint $table) {
            $table->id();
            $table->string("offer_name")->nullable();
            $table->unsignedBigInteger('rental_id')->nullable();
            $table->foreign('rental_id')->references('id')->on('rentals')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('house_offers');
    }
};
