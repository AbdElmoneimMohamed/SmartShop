<?php

declare(strict_types=1);

namespace App\Modules\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        return view('cart.index');
    }
}
