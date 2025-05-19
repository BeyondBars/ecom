<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all categories and brands
        $categories = Category::all();
        $brands = Brand::all();
        
        // Create some predefined products
        $products = [
            [
                'name' => 'iPhone 13 Pro',
                'description' => 'The latest iPhone with advanced camera system and A15 Bionic chip.',
                'price' => 999.99,
                'category' => 'Smartphones',
                'brand' => 'Apple',
            ],
            [
                'name' => 'Samsung Galaxy S21',
                'description' => 'Flagship Android smartphone with 120Hz display and powerful camera.',
                'price' => 799.99,
                'category' => 'Smartphones',
                'brand' => 'Samsung',
            ],
            [
                'name' => 'MacBook Pro 16"',
                'description' => 'Powerful laptop for professionals with M1 Pro chip and stunning display.',
                'price' => 2399.99,
                'category' => 'Laptops',
                'brand' => 'Apple',
            ],
            [
                'name' => 'Dell XPS 15',
                'description' => 'Premium Windows laptop with InfinityEdge display and powerful performance.',
                'price' => 1799.99,
                'category' => 'Laptops',
                'brand' => 'Dell',
            ],
            [
                'name' => 'Sony WH-1000XM4',
                'description' => 'Industry-leading noise cancelling wireless headphones.',
                'price' => 349.99,
                'category' => 'Audio',
                'brand' => 'Sony',
            ],
        ];
        
        foreach ($products as $productData) {
            // Find or create category
            $category = $categories->where('name', $productData['category'])->first();
            if (!$category) {
                $category = Category::create([
                    'name' => $productData['category'],
                    'slug' => Str::slug($productData['category']),
                    'status' => 'active',
                ]);
                $categories->push($category);
            }
            
            // Find or create brand
            $brand = $brands->where('name', $productData['brand'])->first();
            if (!$brand) {
                $brand = Brand::create([
                    'name' => $productData['brand'],
                    'slug' => Str::slug($productData['brand']),
                    'status' => 'active',
                ]);
                $brands->push($brand);
            }
            
            // Create product
            Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'description' => $productData['description'],
                'price' => $productData['price'],
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'sku' => strtoupper(substr($productData['brand'], 0, 3)) . rand(1000, 9999),
                'quantity' => rand(10, 100),
                'status' => 'active',
                'featured' => true,
            ]);
        }
        
        // Create random products
        Product::factory()->count(50)->create();
        
        // Create some featured products
        Product::factory()->count(10)->featured()->create();
        
        // Create some products on sale
        Product::factory()->count(15)->onSale()->create();
        
        // Create some out of stock products
        Product::factory()->count(5)->outOfStock()->create();
    }
}
