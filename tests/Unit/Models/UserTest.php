<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Concerns\LogsModelActivity;
use App\Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[Test]
    public function it_returns_correct_unique_ids(): void
    {
        $this->assertSame(['uuid'], (new User)->uniqueIds());
    }

    #[Test]
    public function it_has_uuid_column_on_the_users_table(): void
    {
        $this->assertSame('users', (new User)->getTable());
        $this->assertTrue(Schema::hasColumn('users', 'uuid'));
    }

    #[Test]
    public function it_uses_required_traits(): void
    {
        $traits = class_uses_recursive(User::class);

        $this->assertContains(HasFactory::class, $traits);
        $this->assertContains(HasUuids::class, $traits);
        $this->assertContains(LogsModelActivity::class, $traits);
        $this->assertContains(Notifiable::class, $traits);
    }

    #[Test]
    public function it_has_guarded_attributes(): void
    {
        $this->assertSame([
            'id',
            'created_at',
            'updated_at',
            'uuid',
        ], (new User)->getGuarded());
    }

    #[Test]
    public function it_generates_a_uuid_when_created(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->uuid);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'uuid' => $user->uuid,
        ]);
    }
}
