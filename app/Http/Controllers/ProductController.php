<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\TracksViewedProducts;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\RecommendationService;
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
