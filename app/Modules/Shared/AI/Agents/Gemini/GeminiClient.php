<?php

declare(strict_types=1);

namespace App\Modules\Shared\AI\Agents\Gemini;

use App\Modules\Shared\AI\Agents\Gemini\Contracts\GeminiClientInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

final readonly class GeminiClient implements GeminiClientInterface
{
    public function __construct(
        private ?string $apiKey,
        private string $model,
    ) {}

    public function generateContent(string $prompt, array $responseSchema): ?string
    {
        if ($this->apiKey === null || $this->apiKey === '') {
            return null;
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";

        try {
            $response = Http::timeout(8)
                ->withHeaders(['x-goog-api-key' => $this->apiKey])
                ->post($url, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'responseSchema' => $responseSchema,
                    ],
                ]);

            if (! $response->successful()) {
                return null;
            }

            $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

            return is_string($text) ? $text : null;
        } catch (ConnectionException) {
            return null;
        }
    }
}
