<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CartBadge extends Component
{
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

    #[On('cart-updated')]
    public function refresh(): void
    {
        // Re-renders in response to the cart-updated event.
    }

    public function render(): View
    {
        return view('livewire.cart-badge', [
            'count' => $this->cart->count(),
        ]);
    }
}
