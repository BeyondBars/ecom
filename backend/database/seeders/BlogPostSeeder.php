<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin and manager users
        $authors = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'manager']);
        })->get();
        
        // Create some predefined blog posts
        $blogPosts = [
            [
                'title' => 'Welcome to Our New Online Store',
                'content' => 'We are excited to announce the launch of our new online store. We have been working hard to bring you the best shopping experience possible. Our new store features a clean, modern design, improved navigation, and a streamlined checkout process. We hope you enjoy shopping with us!',
                'status' => 'published',
            ],
            [
                'title' => 'Top 10 Tech Gadgets for 2023',
                'content' => 'Technology is constantly evolving, and it can be hard to keep up with the latest and greatest gadgets. In this post, we\'ll take a look at the top 10 tech gadgets that are making waves in 2023. From smartphones to smart home devices, we\'ve got you covered.',
                'status' => 'published',
            ],
            [
                'title' => 'How to Choose the Right Laptop for Your Needs',
                'content' => 'Buying a new laptop can be overwhelming with so many options available. In this guide, we\'ll walk you through the key factors to consider when choosing a laptop, including performance, display quality, battery life, and more. Whether you\'re a student, professional, or casual user, we\'ll help you find the perfect laptop for your needs.',
                'status' => 'published',
            ],
            [
                'title' => 'The Future of E-Commerce: Trends to Watch',
                'content' => 'E-commerce is constantly evolving, and staying ahead of the curve is essential for businesses. In this post, we\'ll explore the emerging trends that are shaping the future of e-commerce, including augmented reality shopping, voice commerce, and sustainable practices. Stay informed and prepare your business for the future of online retail.',
                'status' => 'published',
            ],
            [
                'title' => 'Customer Spotlight: Success Stories',
                'content' => 'We love hearing from our customers about how our products have made a difference in their lives. In this post, we\'ll share some inspiring success stories from our customers. From small businesses to individual users, these stories showcase the real-world impact of our products.',
                'status' => 'published',
            ],
        ];
        
        foreach ($blogPosts as $index => $postData) {
            // Get author (cycle through available authors)
            $author = $authors[$index % count($authors)];
            
            // Create blog post
            BlogPost::create([
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'content' => $postData['content'],
                'excerpt' => Str::limit(strip_tags($postData['content']), 150),
                'author_id' => $author->id,
                'status' => $postData['status'],
                'published_at' => now()->subDays(rand(1, 30)),
                'tags' => json_encode(['ecommerce', 'blog', 'news']),
            ]);
        }
        
        // Create random blog posts
        BlogPost::factory()->count(20)->published()->create([
            'author_id' => fn() => $authors->random()->id,
        ]);
        
        // Create some draft blog posts
        BlogPost::factory()->count(5)->draft()->create([
            'author_id' => fn() => $authors->random()->id,
        ]);
        
        // Create some scheduled blog posts
        BlogPost::factory()->count(5)->scheduled()->create([
            'author_id' => fn() => $authors->random()->id,
        ]);
    }
}
