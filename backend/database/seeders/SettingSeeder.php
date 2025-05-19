<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General settings
        Setting::create([
            'key' => 'site_info',
            'value' => json_encode([
                'name' => 'E-Commerce Store',
                'description' => 'Your one-stop shop for all your needs',
                'email' => 'info@example.com',
                'phone' => '+1 (555) 123-4567',
                'address' => '123 Main St, Anytown, USA',
            ]),
            'group' => 'general',
        ]);
        
        Setting::create([
            'key' => 'logo',
            'value' => json_encode([
                'url' => '/images/logo.png',
                'alt' => 'E-Commerce Store Logo',
            ]),
            'group' => 'general',
        ]);
        
        Setting::create([
            'key' => 'favicon',
            'value' => json_encode([
                'url' => '/images/favicon.ico',
            ]),
            'group' => 'general',
        ]);
        
        // Email settings
        Setting::create([
            'key' => 'email_config',
            'value' => json_encode([
                'from_name' => 'E-Commerce Store',
                'from_email' => 'noreply@example.com',
                'admin_email' => 'admin@example.com',
            ]),
            'group' => 'email',
        ]);
        
        Setting::create([
            'key' => 'email_templates',
            'value' => json_encode([
                'order_confirmation' => [
                    'subject' => 'Order Confirmation - {order_number}',
                    'body' => 'Thank you for your order. Your order number is {order_number}.',
                ],
                'shipping_confirmation' => [
                    'subject' => 'Your Order Has Shipped - {order_number}',
                    'body' => 'Your order {order_number} has been shipped.',
                ],
            ]),
            'group' => 'email',
        ]);
        
        // Payment settings
        Setting::create([
            'key' => 'payment_config',
            'value' => json_encode([
                'currency' => 'USD',
                'tax_rate' => 0.1,
                'payment_methods' => ['credit_card', 'paypal', 'bank_transfer'],
            ]),
            'group' => 'payment',
        ]);
        
        Setting::create([
            'key' => 'stripe_config',
            'value' => json_encode([
                'public_key' => 'pk_test_example',
                'secret_key' => 'sk_test_example',
                'webhook_secret' => 'whsec_example',
            ]),
            'group' => 'payment',
        ]);
        
        Setting::create([
            'key' => 'paypal_config',
            'value' => json_encode([
                'client_id' => 'client_id_example',
                'client_secret' => 'client_secret_example',
                'environment' => 'sandbox',
            ]),
            'group' => 'payment',
        ]);
        
        // Shipping settings
        Setting::create([
            'key' => 'shipping_config',
            'value' => json_encode([
                'methods' => [
                    'standard' => [
                        'name' => 'Standard Shipping',
                        'price' => 5.99,
                        'delivery_time' => '3-5 business days',
                    ],
                    'express' => [
                        'name' => 'Express Shipping',
                        'price' => 12.99,
                        'delivery_time' => '1-2 business days',
                    ],
                    'next_day' => [
                        'name' => 'Next Day Delivery',
                        'price' => 19.99,
                        'delivery_time' => 'Next business day',
                    ],
                ],
                'free_shipping_threshold' => 50,
            ]),
            'group' => 'shipping',
        ]);
        
        // Social media settings
        Setting::create([
            'key' => 'social_media',
            'value' => json_encode([
                'facebook' => 'https://facebook.com/ecommercestore',
                'twitter' => 'https://twitter.com/ecommercestore',
                'instagram' => 'https://instagram.com/ecommercestore',
                'youtube' => 'https://youtube.com/ecommercestore',
            ]),
            'group' => 'social',
        ]);
        
        // Analytics settings
        Setting::create([
            'key' => 'analytics',
            'value' => json_encode([
                'google_analytics_id' => 'UA-XXXXXXXX-X',
                'facebook_pixel_id' => 'XXXXXXXXXX',
            ]),
            'group' => 'analytics',
        ]);
    }
}
