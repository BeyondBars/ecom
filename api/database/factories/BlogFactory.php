<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Support\Enums\BlogStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(6),
            'slug' => $this->faker->slug,
            'content' => $this->faker->paragraph(5),
            'meta_description' => $this->faker->sentence(10),
            'meta_keywords' => $this->faker->words(5, true),
            'status' => $this->faker->randomElement(BlogStatus::cases()), // Random status
        ];
    }
}
