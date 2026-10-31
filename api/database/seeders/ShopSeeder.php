<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo catalogue matching the storefront's sample data, so both apps show the same shop.
 */
class ShopSeeder extends Seeder
{
    protected const IMAGES = [
        'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1576566588028-4147f3842f27?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1604695573706-53170668f6a6?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1583496661160-fb5886a0aaaa?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1565084888279-aca607ecce0c?auto=format&fit=crop&w=800&q=80',
    ];

    public function run(?User $author = null): void
    {
        $categories = collect(['Women', 'Men', 'Accessories', 'Shoes', 'Jewelry'])
            ->mapWithKeys(fn ($name, $i) => [$name => Category::create(['name' => $name, 'sort_order' => $i])]);

        $brands = collect(['Elegance', 'Urban Style', 'Luxe', 'Minimalist', 'Vintage'])
            ->map(fn ($name) => Brand::create(['name' => $name]));

        $products = [
            ['Slim Fit Cotton Shirt', 'Men'], ['Floral Print Maxi Dress', 'Women'], ['Tailored Wool Blazer', 'Women'],
            ['High-Waisted Skinny Jeans', 'Women'], ['Cashmere Knit Sweater', 'Women'], ['Leather Crossbody Bag', 'Accessories'],
            ['Oversized Linen Shirt', 'Men'], ['Pleated Midi Skirt', 'Women'], ['Structured Shoulder Bag', 'Accessories'],
            ['Relaxed Fit Chinos', 'Men'], ['Silk Wrap Blouse', 'Women'], ['Straight Leg Denim Jeans', 'Men'],
            ['Leather Ankle Boots', 'Shoes'], ['Gold Hoop Earrings', 'Jewelry'], ['Minimalist Watch', 'Accessories'],
            ['Wool Blend Coat', 'Women'], ['Satin Slip Dress', 'Women'], ['Chunky Knit Cardigan', 'Women'],
            ['Canvas Sneakers', 'Shoes'], ['Statement Necklace', 'Jewelry'],
        ];

        foreach ($products as $i => [$name, $category]) {
            $id = $i + 1;
            $price = 499 + $id * 250;
            $clothing = in_array($category, ['Women', 'Men'], true);

            $product = Product::create([
                'name' => $name,
                'sku' => sprintf('LFC-%04d', $id),
                'category_id' => $categories[$category]->id,
                'brand_id' => $brands[$id % $brands->count()]->id,
                'description' => 'This premium '.strtolower($name).' features a modern design with exceptional comfort and style.',
                'price' => $price,
                'sale_price' => $id % 3 === 0 ? round($price * 0.8 / 10) * 10 - 1 : null,
                'cost' => round($price * 0.45),
                // Every 7th product is sold out and a few run low, to show stock states
                'stock' => $id % 7 === 0 ? 0 : ($id % 6 === 0 ? 3 : 5 + $id * 3),
                'status' => 'active',
                'is_featured' => $id % 4 === 0,
                'options' => $clothing
                    ? [['name' => 'Color', 'values' => ['Black', 'White', 'Navy', 'Beige']], ['name' => 'Size', 'values' => ['XS', 'S', 'M', 'L', 'XL']]]
                    : ($category === 'Shoes' ? [['name' => 'Size', 'values' => ['UK 4', 'UK 5', 'UK 6', 'UK 7', 'UK 8']]] : []),
                'specifications' => [
                    ['name' => 'Material', 'value' => $clothing ? '100% Cotton' : 'Premium'],
                    ['name' => 'Care', 'value' => 'See label'],
                ],
            ]);

            $product->images()->create(['url' => self::IMAGES[$id % count(self::IMAGES)], 'sort_order' => 0]);
            $product->images()->create(['url' => self::IMAGES[($id + 1) % count(self::IMAGES)], 'sort_order' => 1]);
        }

        Coupon::create(['code' => 'WELCOME10', 'description' => '10% off your order', 'type' => 'percent', 'value' => 10, 'min_subtotal' => 999, 'usage_limit_per_user' => 1]);
        Coupon::create(['code' => 'FLAT200', 'description' => '₹200 off orders above ₹1,999', 'type' => 'fixed', 'value' => 200, 'min_subtotal' => 1999]);
        Coupon::create(['code' => 'FREESHIP', 'description' => 'Free shipping on any order', 'type' => 'free_shipping', 'value' => 0]);

        Banner::create([
            'title' => 'The Festive Edit',
            'subtitle' => 'New arrivals in rich jewel tones',
            'image_url' => self::IMAGES[1],
            'link_url' => '/categories/women',
            'button_text' => 'Shop Women',
            'placement' => 'home_hero',
        ]);
        Banner::create([
            'title' => 'Up to 20% off',
            'subtitle' => 'Selected styles, while stocks last',
            'image_url' => self::IMAGES[4],
            'link_url' => '/sale',
            'button_text' => 'Shop the Sale',
            'placement' => 'home_hero',
            'sort_order' => 1,
        ]);

        $post = new BlogPost([
            'title' => '5 Ways to Style a Maxi Dress',
            'excerpt' => 'From brunch to evening, one dress, five looks.',
            'content' => "A maxi dress is the most versatile piece in your wardrobe.\n\nPair it with sneakers for the day, add a blazer for the office, or dress it up with statement earrings for the evening.",
            'cover_image_url' => self::IMAGES[1],
            'status' => 'published',
        ]);
        $post->author()->associate($author)->save();
    }
}
