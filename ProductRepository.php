<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(protected Product $model)
    {
    }

    public function paginateActive(int $perPage = 12, ?int $categoryId = null): LengthAwarePaginator
    {
        return $this->model
            ->with('category')
            ->active()
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->latest()
            ->paginate($perPage);
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->model
            ->with('category')
            ->active()
            ->where('slug', $slug)
            ->first();
    }

    public function featured(int $limit = 8): Collection
    {
        return $this->model
            ->with('category')
            ->active()
            ->featured()
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function search(string $term, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model
            ->with('category')
            ->active()
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('short_description', 'like', "%{$term}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->refresh();
    }

    public function findByWarehouseProductId(string $warehouseProductId): ?Product
    {
        return $this->model
            ->where('warehouse_product_id', $warehouseProductId)
            ->first();
    }

    public function updateStockFromWarehouse(string $warehouseProductId, int $quantity, string $status): bool
    {
        $product = $this->findByWarehouseProductId($warehouseProductId);

        if (! $product) {
            return false;
        }

        return $product->update([
            'stock_quantity' => $quantity,
            'stock_status' => $status,
            'warehouse_synced_at' => now(),
        ]);
    }
}
