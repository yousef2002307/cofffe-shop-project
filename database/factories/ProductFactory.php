<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word() . ' ' . $this->faker->word(), // Combine two words for a product name, // Random product name
            'description' => $this->faker->paragraph(), // Random description
            'price' => $this->faker->randomFloat(2, 5, 100), // Random price between 5 and 100
            'stock' => $this->faker->numberBetween(0, 200), // Random stock quantity between 0 and 200
        ];
    }
}