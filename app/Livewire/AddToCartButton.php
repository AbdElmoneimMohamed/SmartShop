<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AddToCartButton extends Component
{
    public Product $product;

    public bool $addedToCart = false;

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

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function addToCart(): void
    {
        $this->cart->add($this->product);

        $this->addedToCart = true;
        $this->dispatch('cart-updated');
    }

    public function render(): View
    {
        return view('livewire.add-to-cart-button');
    }
}
