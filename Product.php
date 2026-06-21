<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'specifications_json',
        'regular_price',
        'sale_price',
        'buying_price',
        'stock_status',
        'stock_quantity',
        'images_json',
        'is_featured',
        'is_active',
        'warehouse_product_id',
        'warehouse_synced_at',
    ];

    protected $casts = [
        'specifications_json' => 'array',
        'images_json' => 'array',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'buying_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'warehouse_synced_at' => 'datetime',
    ];

    // Never expose internal cost price to the storefront/API responses
    protected $hidden = [
        'buying_price',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock_status', '!=', 'out_of_stock');
    }

    /**
     * The price actually charged to the customer — sale price if present, else regular price.
     */
    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->regular_price);
    }

    public function getHasDiscountAttribute(): bool
    {
        return ! is_null($this->sale_price) && (float) $this->sale_price < (float) $this->regular_price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if (! $this->has_discount) {
            return 0;
        }

        return (int) round((($this->regular_price - $this->sale_price) / $this->regular_price) * 100);
    }

    /**
     * First image, with a safe placeholder fallback for the UI.
     */
    public function getMainImageAttribute(): string
    {
        $images = $this->images_json ?? [];

        return $images[0] ?? 'images/placeholder-product.svg';
    }

    public function getStockLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'in_stock' => 'In Stock',
            'low_stock' => 'Low Stock',
            'out_of_stock' => 'Out of Stock',
            default => 'Unknown',
        };
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
