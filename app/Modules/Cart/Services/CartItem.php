<?php

declare(strict_types=1);

namespace App\Modules\Cart\Services;

use App\Modules\Product\Models\Product;

final readonly class CartItem
{
    public function __construct(
        public Product $product,
        public int $quantity,
    ) {}

    public function subtotal(): float
    {
        return (float) $this->product->price * $this->quantity;
    }
}
