<?php

declare(strict_types=1);

namespace App\Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Controllers\Concerns\TracksViewedProducts;
use App\Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use App\Modules\Product\Services\RecommendationService;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    use TracksViewedProducts;

    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly RecommendationService $recommendations,
    ) {}

    public function show(int $product): View
    {
        $product = $this->products->findOrFail($product);

        $this->rememberViewed($product);

        $similar = $this->recommendations->recommendSimilarTo($product, $this->products->all());

        return view('products.show', [
            'product' => $product,
            'similar' => $similar,
        ]);
    }
}
