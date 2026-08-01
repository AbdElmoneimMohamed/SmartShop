<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Services\RecommendationService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecommendationServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
    }

    #[Test]
    public function it_returns_the_ai_suggested_products_when_the_api_responds_successfully(): void
    {
        $viewed = Product::factory()->create();
        $candidates = Product::factory()->count(5)->create();
        $expected = $candidates->take(3);

        config(['services.gemini.key' => 'test-key']);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => json_encode($expected->pluck('id')->all())],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $recommendations = app(RecommendationService::class)->recommendSimilarTo($viewed, $candidates);

        $this->assertSame(
            $expected->pluck('id')->sort()->values()->all(),
            $recommendations->pluck('id')->sort()->values()->all(),
        );
    }

    #[Test]
    public function it_falls_back_to_random_candidates_when_the_ai_api_call_fails(): void
    {
        $viewed = Product::factory()->create();
        $candidates = Product::factory()->count(5)->create();

        config(['services.gemini.key' => 'test-key']);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([], 500),
        ]);

        $recommendations = app(RecommendationService::class)->recommendSimilarTo($viewed, $candidates);

        $this->assertCount(3, $recommendations);
        $this->assertEmpty($recommendations->pluck('id')->diff($candidates->pluck('id')));
    }

    #[Test]
    public function it_falls_back_to_random_candidates_when_no_api_key_is_configured(): void
    {
        $viewed = Product::factory()->create();
        $candidates = Product::factory()->count(5)->create();

        config(['services.gemini.key' => null]);

        $recommendations = app(RecommendationService::class)->recommendSimilarTo($viewed, $candidates);

        $this->assertCount(3, $recommendations);
    }

    #[Test]
    public function it_excludes_the_viewed_product_itself_from_recommendations(): void
    {
        $viewed = Product::factory()->create();
        $candidates = Product::factory()->count(4)->create();

        config(['services.gemini.key' => null]);

        $recommendations = app(RecommendationService::class)->recommendSimilarTo($viewed, $candidates->push($viewed));

        $this->assertNotContains($viewed->id, $recommendations->pluck('id')->all());
    }
}
