<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['info', 'success', 'warning', 'error'];
        $isRead = $this->faker->boolean(30);

        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'message' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement($types),
            'icon' => $this->faker->randomElement(['bell', 'info', 'check', 'alert-triangle', 'x-circle', null]),
            'link' => $this->faker->boolean(70) ? $this->faker->url() : null,
            'is_read' => $isRead,
            'read_at' => $isRead ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
        ];
    }

    /**
     * Indicate that the notification is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'read_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the notification is of type info.
     */
    public function info(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'info',
            'icon' => 'info',
        ]);
    }

    /**
     * Indicate that the notification is of type success.
     */
    public function success(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'success',
            'icon' => 'check',
        ]);
    }

    /**
     * Indicate that the notification is of type warning.
     */
    public function warning(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'warning',
            'icon' => 'alert-triangle',
        ]);
    }

    /**
     * Indicate that the notification is of type error.
     */
    public function error(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'error',
            'icon' => 'x-circle',
        ]);
    }
}
