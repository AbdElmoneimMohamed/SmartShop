<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Smpita\ConfigAs\ConfigAs;
use Throwable;

final class RecommendationService
{
    private const int CACHE_TTL_SECONDS = 600;

    private const int RECOMMENDATION_COUNT = 3;

    /**
     * Recommend products based on the given viewed product IDs (most recent first).
     *
     * @param  array<int, int>  $viewedProductIds
     * @param  Collection<int, Product>  $catalog
     * @return Collection<int, Product>
     */
    public function recommendForViewed(array $viewedProductIds, Collection $catalog): Collection
    {
        if ($viewedProductIds === []) {
            return $this->randomFallback($catalog);
        }

        $context = Product::query()->whereIn('id', $viewedProductIds)->get();

        if ($context->isEmpty()) {
            return $this->randomFallback($catalog);
        }

        return $this->recommend($context, $catalog);
    }

    /**
     * Recommend products similar to the given product.
     *
     * @param  Collection<int, Product>  $catalog
     * @return Collection<int, Product>
     */
    public function recommendSimilarTo(Product $product, Collection $catalog): Collection
    {
        return $this->recommend(collect([$product]), $catalog);
    }

    /**
     * @param  Collection<int, Product>  $contextProducts
     * @param  Collection<int, Product>  $catalog
     * @return Collection<int, Product>
     */
    private function recommend(Collection $contextProducts, Collection $catalog): Collection
    {
        $contextIds = $contextProducts->pluck('id');
        $candidates = $catalog->reject(fn (Product $product): bool => $contextIds->contains($product->id));

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
        $suggested = $this->askGemini($contextProducts, $candidates);
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
     * @return array<int, int>|null
     */
    private function askGemini(Collection $contextProducts, Collection $candidates): ?array
    {
        $apiKey = ConfigAs::nullableString('services.gemini.key');

        if ($apiKey === null || $apiKey === '') {
            return null;
        }

        $model = ConfigAs::string('services.gemini.model', default: 'gemini-2.0-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        try {
            $response = Http::timeout(8)
                ->withHeaders(['x-goog-api-key' => $apiKey])
                ->post($url, [
                    'contents' => [
                        ['parts' => [['text' => $this->buildPrompt($contextProducts, $candidates)]]],
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'responseSchema' => [
                            'type' => 'ARRAY',
                            'items' => ['type' => 'INTEGER'],
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                return null;
            }

            $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

            if (! is_string($text)) {
                return null;
            }

            $decoded = json_decode($text, true);

            if (! is_array($decoded)) {
                return null;
            }

            return array_values(array_filter(array_map(
                fn (mixed $id): ?int => is_numeric($id) ? (int) $id : null,
                $decoded,
            )));
        } catch (Throwable) {
            return null;
        }
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
