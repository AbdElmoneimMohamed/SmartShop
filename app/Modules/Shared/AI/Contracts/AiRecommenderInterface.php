<?php

declare(strict_types=1);

namespace App\Modules\Shared\AI\Contracts;

interface AiRecommenderInterface
{
    /**
     * Send a prompt to the AI provider and return the suggested IDs it replies with.
     *
     * Implementations must return a JSON array of integers, or null when the
     * provider is unreachable, unconfigured, or returns something unusable.
     *
     * @return array<int, int>|null
     */
    public function suggestIds(string $prompt): ?array;
}
