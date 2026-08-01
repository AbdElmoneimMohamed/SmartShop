<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\TracksViewedProducts;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\RecommendationService;
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
