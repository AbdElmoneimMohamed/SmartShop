<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()->count(25)->create();
    }
}
