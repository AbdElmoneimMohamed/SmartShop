<?php

declare(strict_types=1);

namespace App\Modules\Product\Repositories;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
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
