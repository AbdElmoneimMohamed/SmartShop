<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Models\Product;

trait TracksViewedProducts
{
    /**
     * @return array<int, int>
     */
    private function viewedProductIds(): array
    {
        $viewed = session('viewed_products', []);

        return is_array($viewed) ? array_values(array_filter($viewed, 'is_int')) : [];
    }

    private function rememberViewed(Product $product): void
    {
        $viewed = array_slice(array_values(array_unique([$product->id, ...$this->viewedProductIds()])), 0, 3);

        session(['viewed_products' => $viewed]);
    }
}
