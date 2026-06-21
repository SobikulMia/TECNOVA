<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique(); // e.g. TN-20260620-0001

            // Customer info — kept minimal & BD-optimized (no forced account creation)
            $table->string('customer_name', 150);
            $table->string('customer_phone', 20);
            $table->string('customer_email')->nullable();
            $table->string('district', 100);
            $table->text('delivery_address');
            $table->text('order_notes')->nullable();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);

            $table->enum('payment_method', ['cod', 'bkash', 'nagad'])->default('cod');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('bkash_transaction_id')->nullable();

            $table->enum('shipping_status', [
                'processing', 'confirmed', 'shipped', 'delivered', 'cancelled', 'returned',
            ])->default('processing');

            // Warehouse API readiness — for pushing this order out to the 3rd-party fulfillment system
            $table->enum('warehouse_sync_status', ['not_synced', 'queued', 'synced', 'failed'])
                ->default('not_synced');
            $table->string('warehouse_order_ref')->nullable();
            $table->timestamp('warehouse_synced_at')->nullable();
            $table->text('warehouse_sync_error')->nullable();

            $table->timestamps();

            $table->index('payment_status');
            $table->index('shipping_status');
            $table->index('warehouse_sync_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
