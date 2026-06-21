<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;

    public function findByOrderNumber(string $orderNumber): ?Order;

    public function pendingWarehouseSync(int $limit = 50): Collection;

    public function markWarehouseSynced(Order $order, string $warehouseOrderRef): Order;

    public function markWarehouseSyncFailed(Order $order, string $errorMessage): Order;
}
