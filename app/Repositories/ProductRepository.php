<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Collection;

final class ProductRepository implements ProductRepositoryInterface
{
    public function all(): Collection
    {
        return Product::query()->orderBy('name')->get();
    }

    public function findOrFail(int $id): Product
    {
        return Product::query()->findOrFail($id);
    }
}
