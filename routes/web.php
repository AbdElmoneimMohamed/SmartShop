<?php

declare(strict_types=1);

use App\Modules\Cart\Http\Controllers\CartController;
use App\Modules\Product\Http\Controllers\HomeController;
use App\Modules\Product\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('cart', [CartController::class, 'index'])->name('cart');

require __DIR__.'/auth.php';
