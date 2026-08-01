<?php

declare(strict_types=1);

namespace App\Modules\Product\Repositories\Contracts;

use App\Modules\Product\Models\Product;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    /**
     * @return Collection<int, Product>
     */
    public function all(): Collection;

    public function findOrFail(int $id): Product;
}
