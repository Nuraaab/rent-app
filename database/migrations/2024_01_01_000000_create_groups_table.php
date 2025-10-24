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
            $table->enum('meeting_type', ['In Person', 'Online']);
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('online_meeting_url')->nullable();
            $table->date('start_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('timezone');
            $table->enum('repeat', ['None', 'Daily', 'Weekly', 'Monthly']);
            $table->string('group_banner_image')->nullable();
            $table->boolean('admin_approval')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('category');
            $table->index('meeting_type');
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
