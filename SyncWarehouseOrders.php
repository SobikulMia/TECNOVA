<?php

namespace App\Console\Commands;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;
use Illuminate\Console\Command;

class SyncWarehouseOrders extends Command
{
    protected $signature = 'warehouse:sync-orders';

    protected $description = 'Push any locally placed orders that have not yet been sent to the Warehouse API.';

    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected OrderService $orderService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $pendingOrders = $this->orderRepository->pendingWarehouseSync(50);

        if ($pendingOrders->isEmpty()) {
            $this->info('No pending orders to sync with the warehouse.');

            return self::SUCCESS;
        }

        $this->info("Found {$pendingOrders->count()} order(s) pending warehouse sync...");

        foreach ($pendingOrders as $order) {
            $this->line("Syncing order {$order->order_number}...");
            $this->orderService->syncOrderToWarehouse($order);
        }

        $this->info('Warehouse order sync run complete.');

        return self::SUCCESS;
    }
}
