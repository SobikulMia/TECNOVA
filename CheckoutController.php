<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService,
    ) {
    }

    /**
     * Express 1-page checkout view.
     */
    public function index(): View|RedirectResponse
    {
        $items = $this->cartService->items();

        if ($items->isEmpty()) {
            return redirect()->route('products.index')
                ->with('error', 'Your cart is empty. Please add a product before checking out.');
        }

        return view('checkout.index', [
            'items' => $items,
            'subtotal' => $this->cartService->subtotal(),
        ]);
    }

    /**
     * Handle AJAX order submission from the express checkout form.
     */
    public function store(PlaceOrderRequest $request): JsonResponse
    {
        $cartItems = $this->cartService->items()
            ->map(fn ($line) => [
                'product_id' => $line['product']->id,
                'quantity' => $line['quantity'],
            ])
            ->toArray();

        if (empty($cartItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.',
            ], 422);
        }

        $order = $this->orderService->placeOrder($request->validated(), $cartItems);

        // Fire-and-forget warehouse push attempt; failures are logged, never block the customer.
        $this->orderService->syncOrderToWarehouse($order);

        $this->cartService->clear();

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully!',
            'order_number' => $order->order_number,
            'redirect_url' => route('orders.confirmation', $order->order_number),
        ]);
    }
}
