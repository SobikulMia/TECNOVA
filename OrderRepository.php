<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(protected Order $model)
    {
    }

    public function create(array $data): Order
    {
        return $this->model->create($data);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->model
            ->with('items.product')
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function pendingWarehouseSync(int $limit = 50): Collection
    {
        return $this->model
            ->with('items')
            ->pendingWarehouseSync()
            ->oldest()
            ->limit($limit)
            ->get();
    }

    public function markWarehouseSynced(Order $order, string $warehouseOrderRef): Order
    {
        $order->update([
            'warehouse_sync_status' => 'synced',
            'warehouse_order_ref' => $warehouseOrderRef,
            'warehouse_synced_at' => now(),
            'warehouse_sync_error' => null,
        ]);

        return $order->refresh();
    }

    public function markWarehouseSyncFailed(Order $order, string $errorMessage): Order
    {
        $order->update([
            'warehouse_sync_status' => 'failed',
            'warehouse_sync_error' => $errorMessage,
        ]);

        return $order->refresh();
    }
}
