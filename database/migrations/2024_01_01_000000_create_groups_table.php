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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->string('cover_image')->nullable();
            $table->enum('privacy', ['open', 'closed'])->default('open');
            $table->unsignedBigInteger('created_by');
            $table->integer('member_count')->default(0);
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('category');
            $table->index('privacy');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
