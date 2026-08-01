<?php

declare(strict_types=1);

namespace App\Modules\Cart\Services;

use App\Modules\Product\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

final class CartService
{
    private const string SESSION_KEY = 'cart';

    public function add(Product $product, int $quantity = 1): void
    {
        $cart = $this->raw();
        $cart[$product->id] = ($cart[$product->id] ?? 0) + $quantity;
        $this->put($cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->raw();
        unset($cart[$productId]);
        $this->put($cart);
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($productId);

            return;
        }

        $cart = $this->raw();

        if (! isset($cart[$productId])) {
            return;
        }

        $cart[$productId] = $quantity;
        $this->put($cart);
    }

    public function increment(int $productId): void
    {
        $cart = $this->raw();

        if (! isset($cart[$productId])) {
            return;
        }

        $cart[$productId]++;
        $this->put($cart);
    }

    public function decrement(int $productId): void
    {
        $cart = $this->raw();

        if (! isset($cart[$productId])) {
            return;
        }

        $this->updateQuantity($productId, $cart[$productId] - 1);
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function items(): Collection
    {
        $cart = $this->raw();

        $products = Product::query()->whereIn('id', array_keys($cart))->get()->keyBy('id');

        /** @var Collection<int, CartItem> $items */
        $items = collect();

        foreach ($cart as $productId => $quantity) {
            $product = $products->get($productId);

            if ($product === null) {
                continue;
            }

            $items->push(new CartItem($product, $quantity));
        }

        return $items;
    }

    public function total(): float
    {
        return $this->items()->sum(fn (CartItem $item): float => $item->subtotal());
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * @return array<int, int>
     */
    private function raw(): array
    {
        /** @var array<int, int> $cart */
        $cart = Session::get(self::SESSION_KEY, []);

        return $cart;
    }

    /**
     * @param  array<int, int>  $cart
     */
    private function put(array $cart): void
    {
        Session::put(self::SESSION_KEY, $cart);
    }
}
