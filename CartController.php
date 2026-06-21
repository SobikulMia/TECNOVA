<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    public function index(): View
    {
        return view('cart.index', [
            'items' => $this->cartService->items(),
            'subtotal' => $this->cartService->subtotal(),
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $this->cartService->add($validated['product_id'], $validated['quantity'] ?? 1);

        return response()->json([
            'success' => true,
            'message' => 'Added to cart.',
            'cart_count' => $this->cartService->count(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:0', 'max:20'],
        ]);

        $this->cartService->update($validated['product_id'], $validated['quantity']);

        return response()->json([
            'success' => true,
            'cart_count' => $this->cartService->count(),
            'subtotal' => $this->cartService->subtotal(),
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
        ]);

        $this->cartService->remove($validated['product_id']);

        return response()->json([
            'success' => true,
            'cart_count' => $this->cartService->count(),
            'subtotal' => $this->cartService->subtotal(),
        ]);
    }
}
