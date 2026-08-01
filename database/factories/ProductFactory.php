<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
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
        $name = ucfirst(mb_rtrim(fake()->unique()->sentence(random_int(2, 4)), '.'));

        return [
            'uuid' => (string) Str::uuid7(),
            'name' => $name,
            'description' => fake()->paragraph(3),
            'price' => fake()->randomFloat(2, 9.99, 499.99),
            'image' => 'https://picsum.photos/seed/'.Str::slug($name).'/640/480',
        ];
    }
}
