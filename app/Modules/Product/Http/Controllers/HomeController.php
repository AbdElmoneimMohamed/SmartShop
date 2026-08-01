<?php

declare(strict_types=1);

namespace App\Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Controllers\Concerns\TracksViewedProducts;
use App\Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use App\Modules\Product\Services\RecommendationService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    use TracksViewedProducts;

    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly RecommendationService $recommendations,
    ) {}

    public function __invoke(): View
    {
        $productList = $this->products->all();

        $recommended = $this->recommendations->recommendForViewed(
            $this->viewedProductIds(),
            $productList,
        );

        return view('home', [
            'products' => $productList,
            'recommended' => $recommended,
        ]);
    }
}
