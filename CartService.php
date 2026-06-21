<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

/**
 * Lightweight session-based cart. No DB table needed for a dropshipping
 * storefront with express checkout — keeps the architecture simple while
 * the Warehouse API integration is pending.
 */
class CartService
{
    protected const SESSION_KEY = 'technova_cart';

    public function add(int $productId, int $quantity = 1): void
    {
        $cart = $this->raw();
        $cart[$productId] = ($cart[$productId] ?? 0) + max(1, $quantity);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function update(int $productId, int $quantity): void
    {
        $cart = $this->raw();

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->raw();
        unset($cart[$productId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function raw(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    /**
     * Hydrated cart lines with live product data + computed totals.
     */
    public function items(): Collection
    {
        $cart = $this->raw();

        if (empty($cart)) {
            return collect();
        }

        $products = Product::active()->whereIn('id', array_keys($cart))->get()->keyBy('id');

        return collect($cart)->map(function ($quantity, $productId) use ($products) {
            $product = $products->get($productId);

            if (! $product) {
                return null;
            }

            return [
                'product' => $product,
                'quantity' => $quantity,
                'line_total' => $product->final_price * $quantity,
            ];
        })->filter()->values();
    }

    public function subtotal(): float
    {
        return (float) $this->items()->sum('line_total');
    }
}
