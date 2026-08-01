<?php

declare(strict_types=1);

namespace App\Modules\Shared\AI\Agents\Gemini;

use App\Modules\Shared\AI\Agents\Gemini\Contracts\GeminiClientInterface;
use App\Modules\Shared\AI\Contracts\AiRecommenderInterface;

final readonly class GeminiRecommender implements AiRecommenderInterface
{
    public function __construct(
        private GeminiClientInterface $client,
    ) {}

    public function suggestIds(string $prompt): ?array
    {
        $text = $this->client->generateContent($prompt, [
            'type' => 'ARRAY',
            'items' => ['type' => 'INTEGER'],
        ]);

        if ($text === null) {
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
    }
}
