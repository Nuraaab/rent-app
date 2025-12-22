<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, convert existing string values to JSON format
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $profileImagePath = $user->profile_image_path;
            if ($profileImagePath && $profileImagePath !== 'default.jpg') {
                // Convert single image to JSON array format
                $images = [
                    'current' => $profileImagePath,
                    'previous' => []
                ];
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['profile_image_path' => json_encode($images)]);
            } else {
                // Set default JSON structure
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['profile_image_path' => json_encode(['current' => null, 'previous' => []])]);
            }
        }

        // Change column type to JSON
        Schema::table('users', function (Blueprint $table) {
            $table->json('profile_image_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert JSON back to string (take current image)
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $profileImagePath = $user->profile_image_path;
            if ($profileImagePath) {
                $images = json_decode($profileImagePath, true);
                $currentImage = $images['current'] ?? 'default.jpg';
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['profile_image_path' => $currentImage]);
            }
        }

        // Change column type back to string
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image_path')->default('default.jpg')->change();
        });
    }
};
