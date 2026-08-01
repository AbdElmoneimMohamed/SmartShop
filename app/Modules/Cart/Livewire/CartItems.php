<?php

declare(strict_types=1);

namespace App\Modules\Cart\Livewire;

use App\Modules\Cart\Services\CartService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CartItems extends Component
{
    public bool $checkedOut = false;

    public bool $checkoutFailed = false;

    protected CartService $cart;

    /**
     * Livewire re-hydrates components on every request instead of re-running a
     * constructor, so boot() (called on mount and every subsequent request) is
     * where dependencies are injected — the Livewire equivalent of constructor DI.
     */
    public function boot(CartService $cart): void
    {
        $this->cart = $cart;
    }

    public function increment(int $productId): void
    {
        $this->cart->increment($productId);
        $this->dispatch('cart-updated');
    }

    public function decrement(int $productId): void
    {
        $this->cart->decrement($productId);
        $this->dispatch('cart-updated');
    }

    public function remove(int $productId): void
    {
        $this->cart->remove($productId);
        $this->dispatch('cart-updated');
    }

    public function checkout(): void
    {
        if ($this->cart->items()->isEmpty()) {
            return;
        }

        // Simulated payment gateway: ~90% success rate, no real charge is made.
        if (random_int(1, 10) <= 9) {
            $this->cart->clear();
            $this->checkedOut = true;
            $this->checkoutFailed = false;
            $this->dispatch('cart-updated');

            return;
        }

        $this->checkoutFailed = true;
    }

    public function render(): View
    {
        return view('livewire.cart-items', [
            'items' => $this->cart->items(),
            'total' => $this->cart->total(),
        ]);
    }
}
