<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user as the creator (assuming you have users in your database)
        $user = User::first();
        
        if (!$user) {
            // Create a sample user if none exists
            $user = User::create([
                'name' => 'Sample User',
                'email' => 'sample@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        $groups = [
            [
                'title' => 'Tech Enthusiasts',
                'description' => 'A community for technology lovers to share knowledge and network',
                'category' => 'Technology',
                'meeting_type' => 'Online',
                'online_meeting_url' => 'https://zoom.us/j/123456789',
                'start_date' => Carbon::now()->addDays(7),
                'start_time' => '19:00:00',
                'end_time' => '21:00:00',
                'timezone' => 'Eastern',
                'repeat' => 'Weekly',
                'admin_approval' => true,
                'created_by' => $user->id,
            ],
            [
                'title' => 'Fitness Buddies',
                'description' => 'Join us for regular workouts and healthy lifestyle discussions',
                'category' => 'Sport & Fitness',
                'meeting_type' => 'In Person',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'start_date' => Carbon::now()->addDays(5),
                'start_time' => '18:00:00',
                'end_time' => '20:00:00',
                'timezone' => 'Eastern',
                'repeat' => 'Weekly',
                'admin_approval' => true,
                'created_by' => $user->id,
            ],
            [
                'title' => 'Art & Culture Club',
                'description' => 'Exploring art, culture, and creative expressions together',
                'category' => 'Art & Culture',
                'meeting_type' => 'In Person',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip_code' => '90210',
                'start_date' => Carbon::now()->addDays(10),
                'start_time' => '19:30:00',
                'end_time' => '21:30:00',
                'timezone' => 'Pacific',
                'repeat' => 'Monthly',
                'admin_approval' => true,
                'created_by' => $user->id,
            ],
            [
                'title' => 'Business Networking',
                'description' => 'Professional networking and business development opportunities',
                'category' => 'Career & Business',
                'meeting_type' => 'Online',
                'online_meeting_url' => 'https://teams.microsoft.com/l/meetup-join/123456789',
                'start_date' => Carbon::now()->addDays(14),
                'start_time' => '12:00:00',
                'end_time' => '13:00:00',
                'timezone' => 'Eastern',
                'repeat' => 'Weekly',
                'admin_approval' => true,
                'created_by' => $user->id,
            ],
            [
                'title' => 'Food & Wine Lovers',
                'description' => 'Discover new cuisines, share recipes, and explore local restaurants',
                'category' => 'Food & Drink',
                'meeting_type' => 'In Person',
                'city' => 'San Francisco',
                'state' => 'CA',
                'zip_code' => '94102',
                'start_date' => Carbon::now()->addDays(21),
                'start_time' => '18:30:00',
                'end_time' => '21:00:00',
                'timezone' => 'Pacific',
                'repeat' => 'Monthly',
                'admin_approval' => true,
                'created_by' => $user->id,
            ],
            [
                'title' => 'Mental Health Support',
                'description' => 'A safe space for discussing mental health and wellness',
                'category' => 'Health & Wellness',
                'meeting_type' => 'Online',
                'online_meeting_url' => 'https://zoom.us/j/987654321',
                'start_date' => Carbon::now()->addDays(3),
                'start_time' => '20:00:00',
                'end_time' => '21:30:00',
                'timezone' => 'Central',
                'repeat' => 'Weekly',
                'admin_approval' => true,
                'created_by' => $user->id,
            ],
            [
                'title' => 'Photography Enthusiasts',
                'description' => 'Share your photos, learn new techniques, and explore the world through a lens',
                'category' => 'Hobbies & Passion',
                'meeting_type' => 'In Person',
                'city' => 'Chicago',
                'state' => 'IL',
                'zip_code' => '60601',
                'start_date' => Carbon::now()->addDays(28),
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'timezone' => 'Central',
                'repeat' => 'Monthly',
                'admin_approval' => true,
                'created_by' => $user->id,
            ],
            [
                'title' => 'Language Learning Exchange',
                'description' => 'Practice different languages and learn from native speakers',
                'category' => 'Learning & Education',
                'meeting_type' => 'Online',
                'online_meeting_url' => 'https://zoom.us/j/456789123',
                'start_date' => Carbon::now()->addDays(1),
                'start_time' => '19:00:00',
                'end_time' => '20:30:00',
                'timezone' => 'Eastern',
                'repeat' => 'Daily',
                'admin_approval' => false,
                'created_by' => $user->id,
            ],
            [
                'title' => 'Community Service Volunteers',
                'description' => 'Making a positive impact in our community through volunteer work',
                'category' => 'Social Activities',
                'meeting_type' => 'In Person',
                'city' => 'Austin',
                'state' => 'TX',
                'zip_code' => '73301',
                'start_date' => Carbon::now()->addDays(7),
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
                'timezone' => 'Central',
                'repeat' => 'Weekly',
                'admin_approval' => true,
                'created_by' => $user->id,
            ],
            [
                'title' => 'Adventure Travelers',
                'description' => 'Plan exciting trips and share travel experiences with fellow adventurers',
                'category' => 'Travel & Adventure',
                'meeting_type' => 'Online',
                'online_meeting_url' => 'https://zoom.us/j/789123456',
                'start_date' => Carbon::now()->addDays(14),
                'start_time' => '20:00:00',
                'end_time' => '21:00:00',
                'timezone' => 'Mountain',
                'repeat' => 'Monthly',
                'admin_approval' => true,
                'created_by' => $user->id,
            ],
        ];

        foreach ($groups as $groupData) {
            Group::create($groupData);
        }
    }
}
