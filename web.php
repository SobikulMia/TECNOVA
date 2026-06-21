<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TechNova Storefront Routes
|--------------------------------------------------------------------------
| Clean, SEO-friendly, slug-based routing. No SPA — Blade + Tailwind +
| light AJAX for cart/checkout interactions only.
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Shop / product browsing
Route::get('/shop', [ProductController::class, 'index'])->name('products.index');
Route::get('/shop/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Cart — AJAX only, JSON responses
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
});

// Express 1-page checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Order confirmation (thank-you page), keyed by order_number
Route::get('/order/confirmation/{order:order_number}', [OrderController::class, 'confirmation'])
    ->name('orders.confirmation');
