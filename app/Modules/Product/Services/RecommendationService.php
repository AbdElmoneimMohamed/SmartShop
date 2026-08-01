<?php

declare(strict_types=1);

namespace App\Modules\Product\Services;

use App\Modules\Product\Models\Product;
use App\Modules\Shared\AI\Contracts\AiRecommenderInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class RecommendationService
{
    private const int CACHE_TTL_SECONDS = 600;

    private const int RECOMMENDATION_COUNT = 3;

    public function __construct(
        private readonly AiRecommenderInterface $ai,
    ) {}

    /**
     * Recommend products based on the given viewed product IDs (most recent first).
     *
     * @param  array<int, int>  $viewedProductIds
     * @param  Collection<int, Product>  $productsList
     * @return Collection<int, Product>
     */
    public function recommendForViewed(array $viewedProductIds, Collection $productsList): Collection
    {
        if ($viewedProductIds === []) {
            return $this->randomFallback($productsList);
        }

        $context = Product::query()->whereIn('id', $viewedProductIds)->get();

        if ($context->isEmpty()) {
            return $this->randomFallback($productsList);
        }

        return $this->recommend($context, $productsList);
    }

    /**
     * Recommend products similar to the given product.
     *
     * @param  Collection<int, Product>  $productsList
     * @return Collection<int, Product>
     */
    public function recommendSimilarTo(Product $product, Collection $productsList): Collection
    {
        return $this->recommend(collect([$product]), $productsList);
    }

    /**
     * @param  Collection<int, Product>  $contextProducts
     * @param  Collection<int, Product>  $productsList
     * @return Collection<int, Product>
     */
    private function recommend(Collection $contextProducts, Collection $productsList): Collection
    {
        $contextIds = $contextProducts->pluck('id');
        $candidates = $productsList->reject(fn (Product $product): bool => $contextIds->contains($product->id));

        $ids = $candidates->isEmpty() ? [] : Cache::remember(
            $this->cacheKey($contextProducts, $candidates),
            self::CACHE_TTL_SECONDS,
            fn (): array => $this->resolveIds($contextProducts, $candidates),
        );

        return Product::query()->whereIn('id', $ids)->get();
    }

    /**
     * @param  Collection<int, Product>  $contextProducts
     * @param  Collection<int, Product>  $candidates
     * @return array<int, int>
     */
    private function resolveIds(Collection $contextProducts, Collection $candidates): array
    {
        $suggested = $this->ai->suggestIds($this->buildPrompt($contextProducts, $candidates));
        $ids = $suggested === null ? [] : $candidates->whereIn('id', $suggested)->pluck('id')->all();

        if ($ids === []) {
            $ids = $this->randomFallback($candidates)->pluck('id')->all();
        }

        $typed = [];

        foreach ($ids as $id) {
            if (is_int($id)) {
                $typed[] = $id;
            }
        }

        return $typed;
    }

    /**
     * @param  Collection<int, Product>  $contextProducts
     * @param  Collection<int, Product>  $candidates
     */
    private function buildPrompt(Collection $contextProducts, Collection $candidates): string
    {
        $viewed = $contextProducts
            ->map(fn (Product $product): string => "- {$product->name}: {$product->description}")
            ->implode("\n");

        $catalog = $candidates
            ->map(fn (Product $product): string => "{$product->id}: {$product->name}")
            ->implode("\n");

        $count = self::RECOMMENDATION_COUNT;

        return <<<PROMPT
            Based on these viewed products, suggest {$count} similar ones from this product list:

            Viewed products:
            {$viewed}

            Candidate products (id: name):
            {$catalog}

            Respond with a JSON array of exactly {$count} product IDs from the candidate list above, ordered by relevance. Only use IDs that appear in the candidate list.
            PROMPT;
    }

    /**
     * @param  Collection<int, Product>  $candidates
     * @return Collection<int, Product>
     */
    private function randomFallback(Collection $candidates): Collection
    {
        return $candidates->shuffle()->take(self::RECOMMENDATION_COUNT);
    }

    /**
     * @param  Collection<int, Product>  $contextProducts
     * @param  Collection<int, Product>  $candidates
     */
    private function cacheKey(Collection $contextProducts, Collection $candidates): string
    {
        $contextIds = $contextProducts->pluck('id')->sort()->implode(',');
        $candidateIds = $candidates->pluck('id')->sort()->implode(',');

        return 'recommendations:'.md5($contextIds.'|'.$candidateIds);
    }
}
