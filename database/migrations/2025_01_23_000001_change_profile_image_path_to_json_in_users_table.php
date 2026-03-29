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

        // Drop the inherited string default before converting to JSON.
        DB::statement("ALTER TABLE users ALTER profile_image_path DROP DEFAULT");
        DB::statement("ALTER TABLE users MODIFY profile_image_path JSON NULL");
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

        // Restore the original string column and default.
        DB::statement("ALTER TABLE users MODIFY profile_image_path VARCHAR(255) NOT NULL DEFAULT 'default.jpg'");
    }
};
