<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, HasUuids, LogsModelActivity;

    protected $guarded = ['id', 'created_at', 'updated_at', 'uuid'];

    /**
     * @return array<array-key, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }
}
