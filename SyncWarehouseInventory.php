<?php

namespace App\Console\Commands;

use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\WarehouseApiService;
use Illuminate\Console\Command;

class SyncWarehouseInventory extends Command
{
    protected $signature = 'warehouse:sync-inventory';

    protected $description = 'Check the 3rd-party Warehouse API for stock/inventory updates and sync them locally.';

    public function __construct(
        protected WarehouseApiService $warehouseApiService,
        protected ProductRepositoryInterface $productRepository,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Checking Warehouse API for inventory updates...');

        if (! $this->warehouseApiService->isConfigured()) {
            $this->warn('Warehouse API is not configured yet. Set WAREHOUSE_API_BASE_URL and WAREHOUSE_API_KEY in .env to enable real syncing.');
            $this->line('Skipping this run — no changes made.');

            return self::SUCCESS;
        }

        $result = $this->warehouseApiService->updateStock();

        if (! $result['success']) {
            $this->error('Warehouse inventory sync failed: '.($result['message'] ?? 'Unknown error'));

            return self::FAILURE;
        }

        $updatedCount = 0;

        // TODO: Adjust this loop to match the real shape of your warehouse provider's response.
        // Expected item shape (example): ['warehouse_product_id' => 'WH123', 'quantity' => 42, 'status' => 'in_stock']
        foreach ($result['data'] as $item) {
            $synced = $this->productRepository->updateStockFromWarehouse(
                $item['warehouse_product_id'] ?? '',
                (int) ($item['quantity'] ?? 0),
                $item['status'] ?? 'in_stock',
            );

            if ($synced) {
                $updatedCount++;
            }
        }

        $this->info("Inventory sync complete. {$updatedCount} product(s) updated.");

        return self::SUCCESS;
    }
}
