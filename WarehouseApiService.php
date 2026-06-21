<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\ConnectionException;

/**
 * WarehouseApiService
 *
 * Single integration boundary between TechNova and the 3rd-party Warehouse/Dropshipping API.
 * All warehouse-related HTTP calls MUST go through this service — controllers, jobs, and
 * console commands should never call Http:: directly for warehouse operations.
 *
 * To go live: fill in WAREHOUSE_API_BASE_URL and WAREHOUSE_API_KEY in your .env file,
 * then implement the TODO sections below using your warehouse provider's real API docs.
 */
class WarehouseApiService
{
    protected string $baseUrl;

    protected string $apiKey;

    protected int $timeoutSeconds;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.warehouse.base_url', ''), '/');
        $this->apiKey = config('services.warehouse.api_key', '');
        $this->timeoutSeconds = (int) config('services.warehouse.timeout', 15);
    }

    /**
     * Base HTTP client pre-configured with auth headers, timeout, and base URL.
     */
    protected function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->withToken($this->apiKey)
            ->acceptJson()
            ->timeout($this->timeoutSeconds)
            ->retry(2, 200);
    }

    /**
     * Fetch the full (or paginated) product catalog from the warehouse provider.
     *
     * TODO: Replace the endpoint/query params with your provider's actual catalog endpoint.
     * Expected use: a scheduled job calls this, then upserts into the local `products` table
     * via ProductRepositoryInterface, keeping `warehouse_product_id` as the link key.
     *
     * @return array{success: bool, data: array, message: ?string}
     */
    public function fetchProducts(int $page = 1, int $perPage = 100): array
    {
        if (! $this->isConfigured()) {
            return $this->notConfiguredResponse();
        }

        try {
            // TODO: swap '/v1/products' for your real endpoint.
            $response = $this->client()->get('/v1/products', [
                'page' => $page,
                'per_page' => $perPage,
            ]);

            return $this->handleResponse($response, 'fetchProducts');
        } catch (ConnectionException $e) {
            return $this->connectionErrorResponse('fetchProducts', $e);
        }
    }

    /**
     * Pull live stock levels for a specific warehouse product, or for all products
     * if no ID is given (provider-dependent).
     *
     * TODO: Replace the endpoint with your provider's stock/inventory endpoint.
     *
     * @return array{success: bool, data: array, message: ?string}
     */
    public function updateStock(?string $warehouseProductId = null): array
    {
        if (! $this->isConfigured()) {
            return $this->notConfiguredResponse();
        }

        try {
            // TODO: swap '/v1/inventory' for your real endpoint.
            $response = $this->client()->get('/v1/inventory', array_filter([
                'product_id' => $warehouseProductId,
            ]));

            return $this->handleResponse($response, 'updateStock');
        } catch (ConnectionException $e) {
            return $this->connectionErrorResponse('updateStock', $e);
        }
    }

    /**
     * Push a confirmed local order to the warehouse for fulfillment/shipping.
     *
     * TODO: Map the payload below to match your warehouse provider's required order schema
     * (field names, required customer fields, item structure, etc. will vary by provider).
     *
     * @return array{success: bool, data: array, message: ?string}
     */
    public function pushOrderToWarehouse(Order $order): array
    {
        if (! $this->isConfigured()) {
            return $this->notConfiguredResponse();
        }

        $payload = [
            'reference_id' => $order->order_number,
            'customer' => [
                'name' => $order->customer_name,
                'phone' => $order->customer_phone,
                'address' => $order->delivery_address,
                'district' => $order->district,
            ],
            'items' => $order->items->map(fn ($item) => [
                'warehouse_product_id' => $item->product?->warehouse_product_id,
                'sku_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->price,
            ])->toArray(),
            'payment_method' => $order->payment_method,
            'total_amount' => (float) $order->total_amount,
        ];

        try {
            // TODO: swap '/v1/orders' for your real endpoint.
            $response = $this->client()->post('/v1/orders', $payload);

            return $this->handleResponse($response, 'pushOrderToWarehouse');
        } catch (ConnectionException $e) {
            return $this->connectionErrorResponse('pushOrderToWarehouse', $e);
        }
    }

    /**
     * Quick check used by the scheduler / health checks before attempting real calls.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->baseUrl) && ! empty($this->apiKey);
    }

    protected function notConfiguredResponse(): array
    {
        Log::warning('[WarehouseApiService] Skipped call — warehouse API credentials are not configured yet.');

        return [
            'success' => false,
            'data' => [],
            'message' => 'Warehouse API is not configured. Set WAREHOUSE_API_BASE_URL and WAREHOUSE_API_KEY in .env.',
        ];
    }

    protected function handleResponse(Response $response, string $context): array
    {
        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $response->json('data', $response->json() ?? []),
                'message' => null,
            ];
        }

        Log::error("[WarehouseApiService] {$context} failed", [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return [
            'success' => false,
            'data' => [],
            'message' => "Warehouse API request failed with status {$response->status()}.",
        ];
    }

    protected function connectionErrorResponse(string $context, ConnectionException $e): array
    {
        Log::error("[WarehouseApiService] {$context} connection error: ".$e->getMessage());

        return [
            'success' => false,
            'data' => [],
            'message' => 'Could not connect to the Warehouse API. Please try again later.',
        ];
    }
}
