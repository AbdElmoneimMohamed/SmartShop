<?php

declare(strict_types=1);

namespace App\Modules\Auth\Repositories\Contracts;

use App\Modules\Auth\Models\User;

interface UserRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User;
}
