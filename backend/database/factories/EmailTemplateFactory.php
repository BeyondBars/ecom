<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailTemplate>
 */
class EmailTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['transactional', 'marketing', 'notification'];
        $modules = ['user', 'product', 'order', 'cart', 'wishlist', 'coupon', 'payment', 'shipping', 'review', 'system'];
        $triggers = [
            'user.register', 'user.password_reset', 'user.welcome',
            'order.created', 'order.shipped', 'order.delivered', 'order.cancelled', 'order.refunded',
            'product.back_in_stock', 'product.price_drop',
            'cart.abandoned',
            'wishlist.price_drop',
            'coupon.new', 'coupon.expiring',
            'payment.success', 'payment.failed',
            'shipping.update',
            'review.thank_you',
            'system.maintenance'
        ];

        return [
            'name' => $this->faker->words(3, true),
            'subject' => $this->faker->sentence(),
            'body' => '<div>' . $this->faker->paragraphs(3, true) . '</div>',
            'type' => $this->faker->randomElement($types),
            'module' => $this->faker->randomElement($modules),
            'triggered_by' => $this->faker->randomElement($triggers),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
