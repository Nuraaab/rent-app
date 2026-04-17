<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('community_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');
            $table->unsignedBigInteger('target_id'); 
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('invite_token')->nullable()->unique();
            $table->string('status')->default('pending'); 
            $table->timestamps();
            $table->index(['type', 'target_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('community_invites');
    }
};
