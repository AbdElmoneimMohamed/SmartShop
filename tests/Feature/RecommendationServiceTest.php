<?php

declare(strict_types=1);

use App\Models\Product;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Http;

it('returns the AI-suggested products when the API responds successfully', function () {
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

    expect($recommendations->pluck('id')->sort()->values()->all())
        ->toBe($expected->pluck('id')->sort()->values()->all());
});

it('falls back to random candidates when the AI API call fails', function () {
    $viewed = Product::factory()->create();
    $candidates = Product::factory()->count(5)->create();

    config(['services.gemini.key' => 'test-key']);

    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([], 500),
    ]);

    $recommendations = app(RecommendationService::class)->recommendSimilarTo($viewed, $candidates);

    expect($recommendations)->toHaveCount(3);
    expect($candidates->pluck('id'))->toContain(...$recommendations->pluck('id')->all());
});

it('falls back to random candidates when no API key is configured', function () {
    $viewed = Product::factory()->create();
    $candidates = Product::factory()->count(5)->create();

    config(['services.gemini.key' => null]);

    $recommendations = app(RecommendationService::class)->recommendSimilarTo($viewed, $candidates);

    expect($recommendations)->toHaveCount(3);
});

it('excludes the viewed product itself from recommendations', function () {
    $viewed = Product::factory()->create();
    $candidates = Product::factory()->count(4)->create();

    config(['services.gemini.key' => null]);

    $recommendations = app(RecommendationService::class)->recommendSimilarTo($viewed, $candidates->push($viewed));

    expect($recommendations->pluck('id'))->not->toContain($viewed->id);
});
