<?php

declare(strict_types=1);

namespace App\Modules\Shared\AI\Agents\Gemini\Contracts;

interface GeminiClientInterface
{
    /**
     * Call Gemini's generateContent endpoint and return the model's raw text reply.
     *
     * @param  array<string, mixed>  $responseSchema
     */
    public function generateContent(string $prompt, array $responseSchema): ?string;
}
