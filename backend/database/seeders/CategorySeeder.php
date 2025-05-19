<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create parent categories
        $parentCategories = [
            'Electronics' => 'Electronic devices and accessories',
            'Clothing' => 'Apparel and fashion items',
            'Home & Garden' => 'Home decor and gardening supplies',
            'Books' => 'Books, e-books, and audiobooks',
            'Sports & Outdoors' => 'Sports equipment and outdoor gear',
        ];
        
        foreach ($parentCategories as $name => $description) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
                'status' => 'active',
            ]);
        }
        
        // Create child categories for Electronics
        $electronicsId = Category::where('name', 'Electronics')->first()->id;
        
        $electronicsSubcategories = [
            'Smartphones' => 'Mobile phones and accessories',
            'Laptops' => 'Notebook computers and accessories',
            'Tablets' => 'Tablet computers and accessories',
            'Audio' => 'Headphones, speakers, and audio equipment',
            'Cameras' => 'Digital cameras and photography equipment',
        ];
        
        foreach ($electronicsSubcategories as $name => $description) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
                'parent_id' => $electronicsId,
                'status' => 'active',
            ]);
        }
        
        // Create child categories for Clothing
        $clothingId = Category::where('name', 'Clothing')->first()->id;
        
        $clothingSubcategories = [
            'Men' => 'Men\'s clothing and accessories',
            'Women' => 'Women\'s clothing and accessories',
            'Kids' => 'Children\'s clothing and accessories',
            'Shoes' => 'Footwear for all ages',
            'Accessories' => 'Bags, belts, and other accessories',
        ];
        
        foreach ($clothingSubcategories as $name => $description) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
                'parent_id' => $clothingId,
                'status' => 'active',
            ]);
        }
        
        // Create additional random categories
        Category::factory()->count(10)->create();
        
        // Create some child categories
        Category::factory()->count(15)->child()->create();
    }
}
