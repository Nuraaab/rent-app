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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->longText("description");
            $table->string("max_number_of_gusts");
            $table->string("number_of_bedrooms");
            $table->string("number_of_baths");
            $table->string("phone_number");
            $table->string("address");
            $table->string("latitude");
            $table->string("longtiude");
            $table->string("price");
            $table->string("check_in_time")->nullable();
            $table->string("check_out_time")->nullable();
            $table->string("start_date")->nullable();
            $table->string("end_date")->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
