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
        Schema::create('subgroups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index('group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subgroups');
    }
};
