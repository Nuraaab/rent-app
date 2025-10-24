<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users to create posts
        $users = User::limit(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please seed users first.');
            return;
        }

        // Create sample posts
        $posts = [
            [
                'content' => 'Just moved to a new neighborhood! Looking forward to meeting new people and exploring the area. Any recommendations for good coffee shops nearby? ☕',
            ],
            [
                'content' => 'Had an amazing networking event today! Met so many talented professionals in the tech industry. The connections made were invaluable. #networking #tech',
            ],
            [
                'content' => 'Beautiful sunset from my apartment window today. Sometimes it\'s the simple moments that remind us how lucky we are. 🌅',
            ],
            [
                'content' => 'Started a new book club in our building! We\'re reading "Atomic Habits" this month. Anyone interested in joining? We meet every Tuesday evening.',
            ],
            [
                'content' => 'Working from home has its perks, but I miss the office camaraderie. Anyone else feeling the same way? Looking for virtual coffee chats!',
            ],
            [
                'content' => 'Just finished renovating my kitchen! The new granite countertops look amazing. Home improvement projects are so satisfying when they\'re done.',
            ],
            [
                'content' => 'Found a great local farmers market today. The fresh produce is incredible and the prices are reasonable. Supporting local farmers feels good!',
            ],
            [
                'content' => 'Attended a community cleanup event this weekend. It\'s amazing how much we can accomplish when we work together. Proud of our neighborhood!',
            ],
        ];

        foreach ($posts as $index => $postData) {
            $user = $users->random();
            
            $post = Post::create([
                'user_id' => $user->id,
                'content' => $postData['content'],
                'likes_count' => rand(0, 25),
                'comments_count' => rand(0, 10),
            ]);

            // Create some comments for this post
            $commentCount = rand(0, 5);
            for ($i = 0; $i < $commentCount; $i++) {
                $commentUser = $users->random();
                Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $commentUser->id,
                    'content' => $this->getRandomComment(),
                ]);
            }

            // Update the actual comments count
            $post->update(['comments_count' => $commentCount]);
        }

        $this->command->info('Posts and comments seeded successfully!');
    }

    private function getRandomComment(): string
    {
        $comments = [
            'That\'s awesome! Congratulations! 🎉',
            'I totally agree with you on this.',
            'Thanks for sharing this with us!',
            'This is really helpful information.',
            'I had a similar experience recently.',
            'Great post! Looking forward to more.',
            'This made my day! 😊',
            'I\'m so happy for you!',
            'This is exactly what I needed to hear.',
            'Amazing! Keep up the great work!',
            'I can relate to this so much.',
            'Thank you for the inspiration!',
            'This is really insightful.',
            'I love this! ❤️',
            'So true! Couldn\'t agree more.',
        ];

        return $comments[array_rand($comments)];
    }
}
