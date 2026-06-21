<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    // Flat-rate delivery charges by zone — adjust freely or later pull from a settings table.
    protected const DELIVERY_CHARGE_DHAKA = 70.00;

    protected const DELIVERY_CHARGE_OUTSIDE_DHAKA = 130.00;

    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected WarehouseApiService $warehouseApiService,
    ) {
    }

    /**
     * Create an order from validated checkout input + the cart payload.
     *
     * @param array $customerData  Validated fields: customer_name, customer_phone, district, delivery_address, etc.
     * @param array $cartItems     Array of ['product_id' => int, 'quantity' => int]
     */
    public function placeOrder(array $customerData, array $cartItems): Order
    {
        return DB::transaction(function () use ($customerData, $cartItems) {
            $products = Product::active()
                ->whereIn('id', collect($cartItems)->pluck('product_id'))
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $orderItemsPayload = [];

            foreach ($cartItems as $line) {
                $product = $products->get($line['product_id']);

                if (! $product) {
                    continue; // skip items that vanished/became inactive between view and checkout
                }

                $quantity = max(1, (int) $line['quantity']);
                $unitPrice = $product->final_price;
                $lineTotal = $unitPrice * $quantity;
                $subtotal += $lineTotal;

                $orderItemsPayload[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            $deliveryCharge = $this->calculateDeliveryCharge($customerData['district'] ?? '');
            $totalAmount = $subtotal + $deliveryCharge;

            $order = $this->orderRepository->create([
                'customer_name' => $customerData['customer_name'],
                'customer_phone' => $customerData['customer_phone'],
                'customer_email' => $customerData['customer_email'] ?? null,
                'district' => $customerData['district'],
                'delivery_address' => $customerData['delivery_address'],
                'order_notes' => $customerData['order_notes'] ?? null,
                'subtotal' => $subtotal,
                'delivery_charge' => $deliveryCharge,
                'total_amount' => $totalAmount,
                'payment_method' => $customerData['payment_method'] ?? 'cod',
                'payment_status' => 'pending',
                'shipping_status' => 'processing',
                'warehouse_sync_status' => 'not_synced',
            ]);

            $order->items()->createMany($orderItemsPayload);

            return $order->load('items.product');
        });
    }

    protected function calculateDeliveryCharge(string $district): float
    {
        return strcasecmp(trim($district), 'Dhaka') === 0
            ? self::DELIVERY_CHARGE_DHAKA
            : self::DELIVERY_CHARGE_OUTSIDE_DHAKA;
    }

    /**
     * Attempt to push a freshly placed order to the warehouse immediately.
     * Safe to call even when the Warehouse API isn't configured yet — it will
     * simply mark the order as not_synced and log a notice instead of failing the request.
     */
    public function syncOrderToWarehouse(Order $order): void
    {
        if (! $this->warehouseApiService->isConfigured()) {
            Log::info("[OrderService] Warehouse API not configured — order {$order->order_number} stays queued.");

            return;
        }

        $result = $this->warehouseApiService->pushOrderToWarehouse($order);

        if ($result['success']) {
            $this->orderRepository->markWarehouseSynced(
                $order,
                $result['data']['warehouse_order_id'] ?? $result['data']['id'] ?? 'unknown'
            );
        } else {
            $this->orderRepository->markWarehouseSyncFailed($order, $result['message'] ?? 'Unknown error');
        }
    }
}
