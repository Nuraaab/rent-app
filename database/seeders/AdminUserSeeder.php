<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists
        $adminExists = User::where('email', 'admin@spacegig.com')->first();

        if (!$adminExists) {
            User::create([
                'first_name' => 'Admin',
                'last_name' => 'SpaceGig',
                'email' => 'admin@spacegig.com',
                'password' => Hash::make('admin123'), // Change this password after first login!
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);

            $this->command->info('✅ Admin user created successfully!');
            $this->command->info('📧 Email: admin@spacegig.com');
            $this->command->info('🔑 Password: admin123');
            $this->command->warn('⚠️  Please change the password after first login!');
        } else {
            $this->command->warn('⚠️  Admin user already exists!');
        }
    }
}

