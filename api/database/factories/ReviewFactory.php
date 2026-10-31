<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'rating' => 5,
            'comment' => 'Beautiful fit and lovely fabric.',
            'status' => 'approved',
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }
}
