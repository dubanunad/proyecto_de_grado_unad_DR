<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nit' => fake()->unique()->numerify('##########'),
            'name' => fake()->unique()->city(),
            'country' => fake()->country(),
            'department' => fake()->state(),
            'municipality' => fake()->city(),
            'address' => fake()->address(),
            'number_phone' => fake()->phoneNumber(),
            'additional_number' => fake()->optional()->phoneNumber(),
            'image' => fake()->optional()->imageUrl(640, 480, 'business', true, 'Faker'),
            'moving_price' => fake()->optional()->randomFloat(2, 10, 500),
            'reconnection_price' => fake()->optional()->randomFloat(2, 10, 500),
            'message_custom_invoice' => fake()->optional()->sentence(),
            'observation' => fake()->optional()->paragraph(),
        ];
    }
}
