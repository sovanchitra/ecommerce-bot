<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Dress', 'T-Shirt', 'Pants', 'Pajama', 'Socks', 'Underwear']),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'image_url' => "https://placehold.co/600x400/png",
            'stock' => $this->faker->randomNumber(2),
        ];
    }
}
