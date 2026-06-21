<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'district',
        'delivery_address',
        'order_notes',
        'subtotal',
        'delivery_charge',
        'total_amount',
        'payment_method',
        'payment_status',
        'bkash_transaction_id',
        'shipping_status',
        'warehouse_sync_status',
        'warehouse_order_ref',
        'warehouse_synced_at',
        'warehouse_sync_error',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'warehouse_synced_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        return 'TN-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopePendingWarehouseSync(Builder $query): Builder
    {
        return $query->whereIn('warehouse_sync_status', ['not_synced', 'failed']);
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }
}
