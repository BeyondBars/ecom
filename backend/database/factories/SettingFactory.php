<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->word(),
            'value' => json_encode($this->faker->word()),
            'group' => $this->faker->randomElement(['general', 'email', 'payment', 'shipping', 'social']),
        ];
    }

    /**
     * Indicate that the setting is for site information.
     */
    public function siteInfo(): static
    {
        return $this->state(fn (array $attributes) => [
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
    }

    /**
     * Indicate that the setting is for email configuration.
     */
    public function emailConfig(): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => 'email_config',
            'value' => json_encode([
                'from_name' => 'E-Commerce Store',
                'from_email' => 'noreply@example.com',
                'admin_email' => 'admin@example.com',
            ]),
            'group' => 'email',
        ]);
    }

    /**
     * Indicate that the setting is for payment configuration.
     */
    public function paymentConfig(): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => 'payment_config',
            'value' => json_encode([
                'currency' => 'USD',
                'tax_rate' => 0.1,
                'payment_methods' => ['credit_card', 'paypal', 'bank_transfer'],
            ]),
            'group' => 'payment',
        ]);
    }
}
