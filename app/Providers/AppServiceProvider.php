<?php

declare(strict_types=1);

namespace App\Providers;

use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Auth\Repositories\UserRepository;
use App\Modules\Cart\Livewire\AddToCartButton;
use App\Modules\Cart\Livewire\CartBadge;
use App\Modules\Cart\Livewire\CartItems;
use App\Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use App\Modules\Product\Repositories\ProductRepository;
use App\Modules\Shared\AI\Agents\Gemini\Contracts\GeminiClientInterface;
use App\Modules\Shared\AI\Agents\Gemini\GeminiClient;
use App\Modules\Shared\AI\Agents\Gemini\GeminiRecommender;
use App\Modules\Shared\AI\Contracts\AiRecommenderInterface;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Smpita\ConfigAs\ConfigAs;
use Smpita\TypeAs\TypeAs;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AiRecommenderInterface::class, GeminiRecommender::class);
        $this->app->bind(GeminiClientInterface::class, GeminiClient::class);

        $this->app->when(GeminiClient::class)
            ->needs('$apiKey')
            ->give(fn (): ?string => ConfigAs::nullableString('services.gemini.key'));

        $this->app->when(GeminiClient::class)
            ->needs('$model')
            ->give(fn (): string => ConfigAs::string('services.gemini.model', default: 'gemini-2.0-flash'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());
        Model::automaticallyEagerLoadRelationships();

        Vite::useAggressivePrefetching();
        Vite::macro('image', fn (string $asset) => TypeAs::class(\Illuminate\Foundation\Vite::class, $this)->asset("resources/images/{$asset}"));

        Date::use(CarbonImmutable::class);

        Livewire::component('add-to-cart-button', AddToCartButton::class);
        Livewire::component('cart-badge', CartBadge::class);
        Livewire::component('cart-items', CartItems::class);
    }
}
