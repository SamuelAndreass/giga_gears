<?php

namespace Database\Seeders;

use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumTag;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Smartphones',
            'Laptops',
            'Audio',
            'Gaming',
            'Smart Home',
            'Wearables',
            'Accessories',
            'Tips & Tricks',
            'Reviews',
            'Questions'
        ];

        $tagModels = [];
        foreach ($tags as $tagName) {
            $tagModels[$tagName] = ForumTag::firstOrCreate(['name' => $tagName]);
        }

        $users = User::where('role', 'customer')->take(5)->get();
        
        if ($users->isEmpty()) {
            $users = User::take(5)->get();
        }

        if ($users->isEmpty()) {
            $this->command->info('No users found. Creating sample user...');
            $user = User::create([
                'name' => 'Community Member',
                'email' => 'community@gigagears.com',
                'password' => bcrypt('password123'),
                'role' => 'customer',
            ]);
            $users = collect([$user]);
        }

        $posts = [
            [
                'title' => 'Best Budget Smartphones 2025 - My Top Picks!',
                'content' => "Hey everyone! After testing over 20 budget smartphones this year, I wanted to share my top recommendations for those looking for great value.\n\n**Top 3 Picks:**\n1. **Model X Pro** - Amazing camera quality at this price point\n2. **TechPhone Y** - Best battery life in the segment\n3. **ValueMaster Z** - Excellent gaming performance\n\nWhat are your experiences with budget phones? Drop your recommendations below!",
                'tags' => ['Smartphones', 'Reviews'],
                'views' => 1250,
            ],
            [
                'title' => 'How to extend your laptop battery life - Complete Guide',
                'content' => "I've compiled a comprehensive guide on maximizing your laptop battery based on years of experience:\n\n**Quick Tips:**\n- Reduce screen brightness to 50-70%\n- Close unnecessary background apps\n- Use battery saver mode when not plugged in\n- Keep your laptop cool\n- Calibrate your battery every 2-3 months\n\n**Advanced Settings:**\n- Disable keyboard backlighting when not needed\n- Use SSD over HDD for lower power consumption\n- Limit CPU performance in power settings\n\nHope this helps! Let me know if you have any questions.",
                'tags' => ['Laptops', 'Tips & Tricks'],
                'views' => 890,
            ],
            [
                'title' => 'Wireless Earbuds vs Over-Ear Headphones - Which is better for you?',
                'content' => "I get asked this question a lot, so here's my breakdown:\n\n**Wireless Earbuds:**\n- More portable and convenient\n- Great for workouts\n- Usually more affordable\n- Can feel uncomfortable during long sessions\n\n**Over-Ear Headphones:**\n- Superior sound quality\n- Better noise cancellation\n- More comfortable for extended use\n- Bulkier to carry around\n\n**My Verdict:** It depends on your use case! What do you prefer?",
                'tags' => ['Audio', 'Questions'],
                'views' => 567,
            ],
            [
                'title' => 'Just upgraded my gaming setup - Amazing results!',
                'content' => "Finally pulled the trigger on a complete gaming setup upgrade and WOW!\n\n**New Setup:**\n- RTX 4070 Graphics Card\n- 32GB DDR5 RAM\n- 1TB NVMe SSD\n- 144Hz 4K Monitor\n\nGames run incredibly smooth now. Cyberpunk 2077 at max settings is a dream come true!\n\nShare your gaming setups below. Always looking for inspiration!",
                'tags' => ['Gaming', 'Reviews'],
                'views' => 2100,
            ],
            [
                'title' => 'Smart Home Beginners Guide - Where to Start?',
                'content' => "New to smart home tech? Here's my recommended starting point:\n\n**Essential Devices:**\n1. Smart Speaker (Google Home or Amazon Echo)\n2. Smart Plugs - Control any device remotely\n3. Smart Lights - Start with living room\n4. Smart Thermostat - Energy savings!\n\n**Tips for Beginners:**\n- Start small and expand gradually\n- Stick to one ecosystem (Google, Amazon, or Apple)\n- Consider security implications\n- Check device compatibility before buying\n\nWhat was your first smart home device?",
                'tags' => ['Smart Home', 'Tips & Tricks', 'Questions'],
                'views' => 1450,
            ],
            [
                'title' => 'My experience with the new Galaxy Watch 6 - 3 Month Review',
                'content' => "Been using the Galaxy Watch 6 for 3 months now. Here's my honest review:\n\n**Pros:**\n- Beautiful AMOLED display\n- Accurate health tracking\n- 2-day battery life\n- Smooth performance\n\n**Cons:**\n- Samsung Pay issues occasionally\n- Some third-party apps are buggy\n- Could be lighter\n\n**Overall Score: 8.5/10**\n\nDefinitely recommend for Samsung users. Worth the upgrade from Watch 4!",
                'tags' => ['Wearables', 'Reviews'],
                'views' => 780,
            ],
        ];

        $comments = [
            'Great post! Really helpful information.',
            'I agree with your recommendations!',
            'Thanks for sharing this detailed guide.',
            'Just what I was looking for!',
            'Can you elaborate more on this topic?',
            'I had a similar experience.',
            'Excellent tips, will definitely try these!',
            'This helped me make my decision.',
        ];

        foreach ($posts as $index => $postData) {
            $user = $users[$index % count($users)];
            
            $post = ForumPost::create([
                'user_id' => $user->id,
                'title' => $postData['title'],
                'content' => $postData['content'],
                'views' => $postData['views'],
            ]);

            $tagIds = [];
            foreach ($postData['tags'] as $tagName) {
                if (isset($tagModels[$tagName])) {
                    $tagIds[] = $tagModels[$tagName]->id;
                }
            }
            $post->tags()->attach($tagIds);

            $numComments = rand(2, 5);
            for ($i = 0; $i < $numComments; $i++) {
                $commentUser = $users->random();
                ForumComment::create([
                    'post_id' => $post->id,
                    'user_id' => $commentUser->id,
                    'content' => $comments[array_rand($comments)],
                ]);
            }

            $numLikes = rand(5, 50);
            $likedUsers = $users->random(min($numLikes, count($users)));
            foreach ($likedUsers as $likedUser) {
                $post->likes()->create(['user_id' => $likedUser->id]);
            }
        }

        $this->command->info('Forum seeder completed successfully!');
        $this->command->info('Created ' . count($posts) . ' posts with comments and likes.');
    }
}
