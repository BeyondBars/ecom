<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all customers
        $customers = User::whereHas('roles', function ($query) {
            $query->where('name', 'customer');
        })->get();
        
        // Get all products
        $products = Product::where('status', 'active')->get();
        
        // Create orders for each customer
        foreach ($customers as $customer) {
            // Create 1-5 orders per customer
            $orderCount = rand(1, 5);
            
            for ($i = 0; $i < $orderCount; $i++) {
                // Create order
                $order = Order::factory()->create([
                    'user_id' => $customer->id,
                ]);
                
                // Create 1-5 order items per order
                $itemCount = rand(1, 5);
                $subtotal = 0;
                
                for ($j = 0; $j < $itemCount; $j++) {
                    // Get random product
                    $product = $products->random();
                    $quantity = rand(1, 3);
                    $price = $product->sale_price ?? $product->price;
                    $itemSubtotal = $price * $quantity;
                    
                    // Create order item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'price' => $price,
                        'quantity' => $quantity,
                        'subtotal' => $itemSubtotal,
                    ]);
                    
                    $subtotal += $itemSubtotal;
                }
                
                // Update order totals
                $tax = $subtotal * 0.1; // 10% tax
                $shipping = rand(5, 20);
                $total = $subtotal + $tax + $shipping;
                
                $order->update([
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'shipping' => $shipping,
                    'total' => $total,
                ]);
            }
        }
        
        // Create some additional random orders
        Order::factory()->count(20)->create()->each(function ($order) use ($products) {
            // Create 1-5 order items per order
            $itemCount = rand(1, 5);
            $subtotal = 0;
            
            for ($j = 0; $j < $itemCount; $j++) {
                // Get random product
                $product = $products->random();
                $quantity = rand(1, 3);
                $price = $product->sale_price ?? $product->price;
                $itemSubtotal = $price * $quantity;
                
                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $itemSubtotal,
                ]);
                
                $subtotal += $itemSubtotal;
            }
            
            // Update order totals
            $tax = $subtotal * 0.1; // 10% tax
            $shipping = rand(5, 20);
            $total = $subtotal + $tax + $shipping;
            
            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
            ]);
        });
    }
}
