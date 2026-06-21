<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function paginateActive(int $perPage = 12, ?int $categoryId = null): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Product;

    public function featured(int $limit = 8): Collection;

    public function search(string $term, int $perPage = 12): LengthAwarePaginator;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    /**
     * Used by the Warehouse sync job to find a product by its 3rd-party identifier.
     */
    public function findByWarehouseProductId(string $warehouseProductId): ?Product;

    public function updateStockFromWarehouse(string $warehouseProductId, int $quantity, string $status): bool;
}
