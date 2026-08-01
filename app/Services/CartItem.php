<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;

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
